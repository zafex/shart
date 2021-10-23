<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemUserNotificationTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user_notification');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user_notification', function (Blueprint $table) {
            $table->string('id_user', 100);
            $table->string('id_notification', 100);
            $table->dateTime('created_at')->useCurrent();
            $table->foreign('id_user')->references('id')->on('sys_user');
            $table->foreign('id_notification')->references('id')->on('sys_notification');
            $table->primary(['id_user', 'id_notification']);
        });
    }
}
