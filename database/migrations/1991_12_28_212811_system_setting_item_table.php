<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemSettingItemTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_setting_item');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_setting_item', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_setting', 100);
            $table->string('identity');
            $table->string('value');
            $table->string('label');
            $table->integer('visibility')->default(1);
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('id_setting', 'sys_setting_item_id_setting_idx');
            $table->index('identity', 'sys_setting_item_identity_idx');
            $table->index('value', 'sys_setting_item_value_idx');
            $table->index('label', 'sys_setting_item_label_idx');
            $table->index('visibility', 'sys_setting_item_visibility_idx');
            $table->index('status', 'sys_setting_item_status_idx');
            $table->index('created_by', 'sys_setting_item_created_by_idx');
            $table->index('created_at', 'sys_setting_item_created_at_idx');

            $table->foreign('id_setting')->references('id')->on('sys_setting');
            $table->unique(['identity', 'id_setting'], 'sys_setting_item_00_unique');
        });
    }
}
