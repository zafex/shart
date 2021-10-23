<?php

declare(strict_types=1);

namespace Shart\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Support\Arr;
use Shart\Authority;
use Shart\Type;
use Shart\Helpers\Notifier;
use Shart\Models\Permission;
use Shart\Models\Role;
use Shart\Models\Setting;
use Shart\Models\User;
use Shart\Models\UserCredential;
use Shart\Services\MessageService;

class Installer extends Command
{
    /**
     * @var string
     */
    protected $description = 'Installer';

    /**
     * @var array
     */
    protected $roles = [
        [
            'identity' => 'role-1',
            'label' => 'Role 1',
        ],
        [
            'identity' => 'role-2',
            'label' => 'Role 2',
        ],
        [
            'identity' => 'role-3',
            'label' => 'Role 3',
        ],
        [
            'identity' => 'role-4',
            'label' => 'Role 4',
        ],
    ];

    /**
     * @var array
     */
    protected $settings = [
        [
            'identity' => 'application',
            'label' => 'Application',
            'items' => [
                [
                    'identity' => 'name',
                    'value' => 'Viloveul',
                    'label' => 'Application Name',
                    'visibility' => 1,
                ],
            ],
        ],
        [
            'identity' => 'menu',
            'label' => 'Menu',
            'items' => [
                [
                    'identity' => 'main',
                    'value' => 'main',
                    'label' => 'Main Menu',
                    'visibility' => 1,
                ],
            ],
        ],
        [
            'identity' => 'oauth',
            'label' => 'Oauth',
        ],
        [
            'identity' => 'employment',
            'label' => 'Employment',
        ],
    ];

    /**
     * @var string
     */
    protected $signature = 'shart:install {--username=} {--fullname=} {--email=} {--password=} {--sample} {--yes}';

    /**
     * @return null
     */
    public function handle()
    {
        $options = $this->prefill();

        if (!$this->option('yes')) {
            $rows = [];

            foreach ($options as $key => $value) {
                $rows[] = [$key, $value];
            }

            $this->table(['OPTION', 'VALUE'], $rows);

            if (!$this->confirm('Do you wish to continue ?')) {
                return;
            }
        }

        Authority::setSelected(Authority::INSTALLER);

        $su = app('db')->transaction(function () use ($options) {

            $services = config('services');
            $registeredPermissions = config('permissions');
            $ctasks = \count($this->roles) + count($this->settings) + count($services) + count($registeredPermissions) + 2;

            $bar = $this->output->createProgressBar($ctasks);
            $bar->start();

            foreach ($this->settings as $value) {
                $setting = Setting::updateOrCreate(
                    [
                        'identity' => $value['identity'],
                    ],
                    [
                        'label' => $value['label'],
                    ]
                );

                if (isset($value['items'])) {
                    foreach ($value['items'] as $item) {
                        $setting->items()->updateOrCreate(
                            [
                                'identity' => $item['identity'],
                            ],
                            Arr::only($item, ['value', 'label', 'visibility'])
                        );
                    }
                }

                $bar->advance();
            }

            $oauth = Setting::where('identity', 'oauth')->first();

            foreach ($services as $identity => $config) {
                if (\array_key_exists('client_id', $config)) {
                    $label = ucwords(implode(' ', explode('_', $identity)));

                    $oauth->items()->updateOrCreate(
                        [
                            'identity' => sprintf('%s.client_secret', $identity),
                        ],
                        [
                            'value' => $config['client_secret'],
                            'label' => $label.' Client Secret',
                            'visibility' => 0,
                        ]
                    );

                    $oauth->items()->updateOrCreate(
                        [
                            'identity' => sprintf('%s.client_id', $identity),
                        ],
                        [
                            'value' => $config['client_id'],
                            'label' => $label.' Client ID',
                            'visibility' => 1,
                        ]
                    );

                    $oauth->items()->updateOrCreate(
                        [
                            'identity' => sprintf('%s.url', $identity),
                        ],
                        [
                            'value' => $config['url'],
                            'label' => $label.' Auth URL',
                            'visibility' => 1,
                        ]
                    );

                    $oauth->items()->updateOrCreate(
                        [
                            'identity' => sprintf('%s.redirect', $identity),
                        ],
                        [
                            'value' => $config['redirect'],
                            'label' => $label.' Redirect URL',
                            'visibility' => 1,
                        ]
                    );
                }

                $bar->advance();
            }

            $permissions = [];
            foreach ($this->roles as $value) {
                Role::updateOrCreate(
                    [
                        'identity' => $value['identity'],
                    ],
                    [
                        'label' => $value['label'],
                    ]
                );
                $bar->advance();
            }

            User::updateOrCreate(
                [
                    'username' => Type::SYS_ANONYMOUS,
                ],
                [
                    'fullname' => 'Guest User',
                    'email' => $options['email'],
                    'type' => Type::USER_STANDARD,
                    'status' => 1,
                ]
            );
            $bar->advance();

            $su = User::updateOrCreate(
                [
                    'username' => $options['username'],
                ],
                [
                    'fullname' => $options['fullname'],
                    'email' => $options['email'],
                    'type' => Type::USER_SUPER,
                    'status' => 1,
                ]
            );
            UserCredential::where('id_user', $su->getKey())->delete();
            $su->credentials()->create([
                'id_user' => $su->getKey(),
                'password' => app('hash')->make($options['password']),
                'status' => 1,
            ]);
            $bar->advance();

            foreach ($registeredPermissions as $identity => $object) {
                $p = Permission::updateOrCreate(
                    [
                        'identity' => $identity,
                    ],
                    Arr::except($object, 'abilities')
                );

                foreach (Arr::get($object, 'abilities', []) as $key => $value) {
                    $permission = $p->abilities()->updateOrCreate(
                        [
                            'identity' => sprintf('%s:%s', $identity, $key),
                        ],
                        $value
                    );
                    $permissions[] = $permission->getKey();
                }
                $bar->advance();
            }
            $bar->finish();
            $bar->clear();

            return $su;
        });

        app(Factory::class)->store()->clear();

        $message = app(MessageService::class)->newMessage(
            'System Information',
            'Installation Complete ...',
            'Installer'
        );
        $notification = app(MessageService::class)->newNotification($message);
        $notifier = new Notifier($notification);
        $notifier->attach($su);
        $notifier->send();

        $this->info('Installation Complete ...');
    }

    /**
     * @return mixed
     */
    protected function prefill(): array
    {
        $fields = [
            'username' => [
                'default' => 'super',
                'question' => 'Insert your Super User username',
            ],
            'fullname' => [
                'default' => 'Super User',
                'question' => 'Super User display name',
            ],
            'email' => [
                'default' => 'su@viloveul.com',
                'question' => 'email for Super User',
            ],
            'password' => [
                'default' => 'super',
                'question' => 'set a password for Super User',
            ],
        ];
        $options = Arr::only($this->options(), array_keys($fields));

        foreach ($fields as $key => $vals) {
            while (empty($options[$key])) {
                $options[$key] = $this->ask($vals['question'], $vals['default']);
            }
        }

        return $options;
    }
}
