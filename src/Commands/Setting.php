<?php

declare(strict_types=1);

namespace Shart\Commands;

use Illuminate\Console\Command;
use Shart\Services\SettingService;

class Setting extends Command
{
    /**
     * @var string
     */
    protected $description = 'Get Setting';

    /**
     * @var string
     */
    protected $signature = 'shart:setting {key}';

    public function handle()
    {
        $key = $this->argument('key');
        $value = app(SettingService::class)->fetch($key);
        $this->info($value);
    }
}
