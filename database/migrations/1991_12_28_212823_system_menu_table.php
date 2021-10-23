<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemMenuTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_menu');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_menu', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_setting_item', 100);
            $table->string('id_parent', 100)->nullable();
            $table->string('id_role', 100)->nullable();
            $table->string('label', 100);
            $table->string('url');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->integer('order')->default(1);
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index('id_setting_item', 'sys_menu_id_setting_item_idx');
            $table->index('id_parent', 'sys_menu_id_parent_idx');
            $table->index('id_role', 'sys_menu_id_role_idx');
            $table->index('url', 'sys_menu_url_idx');
            $table->index('label', 'sys_menu_label_idx');
            $table->index('description', 'sys_menu_description_idx');
            $table->index('order', 'sys_menu_order_idx');
            $table->index('status', 'sys_menu_status_idx');
            $table->index('created_by', 'sys_menu_created_by_idx');
            $table->index('created_at', 'sys_menu_created_at_idx');

            $table->foreign('id_setting_item')->references('id')->on('sys_setting_item');
            $table->foreign('id_role')->references('id')->on('sys_role');
        });

        Schema::table('sys_menu', function (Blueprint $table) {
            $table->foreign('id_parent')->references('id')->on('sys_menu');
        });
    }
}
