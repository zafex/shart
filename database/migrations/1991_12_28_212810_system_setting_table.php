<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemSettingTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_setting');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_setting', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('identity');
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('identity', 'sys_setting_identity_idx');
            $table->index('label', 'sys_setting_value_idx');
            $table->index('status', 'sys_setting_status_idx');
            $table->index('created_by', 'sys_setting_created_by_idx');
            $table->index('created_at', 'sys_setting_created_at_idx');

            $table->unique(['identity'], 'sys_setting_00_unique');
        });
    }
}
