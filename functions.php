<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

if (!function_exists('shart_path')) {
    /**
     * @param string $path
     */
    function shart_path(string $path = '')
    {
        return realpath(__DIR__ . (empty($path) ? '' : DIRECTORY_SEPARATOR . $path));
    }

}

if (!function_exists('migrate_add_primary')) {
    /**
     * @param Blueprint $table
     * @param string    $key
     */
    function migrate_add_primary(Blueprint $table, string $key)
    {

        if (DB::getDriverName() === 'pgsql') {
            $table->string($key, 100)->default(DB::raw('uuid_generate_v4()'))->primary();
        } else {
            $table->string($key, 100)->primary();
        }

    }

}
