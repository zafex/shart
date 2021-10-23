<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemNotificationTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_notification');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_notification', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_message', 100);
            $table->string('action')->default('preview');
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index('action', 'sys_notification_action_idx');
            $table->index('status', 'sys_notification_status_idx');
            $table->index('created_by', 'sys_notification_created_by_idx');
            $table->index('created_at', 'sys_notification_created_at_idx');

            $table->foreign('id_message')->references('id')->on('sys_message');
        });
    }
}
