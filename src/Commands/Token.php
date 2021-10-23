<?php

declare(strict_types=1);

namespace Shart\Commands;

use Illuminate\Console\Command;
use Shart\Models\User;
use Shart\Services\TokenService;

class Token extends Command
{
    /**
     * @var string
     */
    protected $description = 'Generate Token';

    /**
     * @var string
     */
    protected $signature = 'shart:token {username}';

    public function handle()
    {
        $username = $this->argument('username');
        $user = User::where('username', $username)->first();
        $payload = array_merge($user->getAttributes(), [
            'links' => $user->links,
            'roles' => $user->roles->pluck('identity'),
        ]);
        $token = app(TokenService::class)->generate($payload);
        $this->info($token);
    }
}
