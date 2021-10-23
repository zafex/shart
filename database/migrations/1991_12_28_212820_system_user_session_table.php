<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemUserSessionTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user_session');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user_session', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_user', 100);
            $table->string('provider');
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->dateTime('expired_at')->nullable();

            $table->index('id_user', 'sys_user_session_id_user_idx');
            $table->index('provider', 'sys_user_session_provider_idx');
            $table->index('status', 'sys_user_session_status_idx');
            $table->index('created_by', 'sys_user_session_created_by_idx');
            $table->index('created_at', 'sys_user_session_created_at_idx');
            $table->index('expired_at', 'sys_user_session_expired_at_idx');

            $table->foreign('id_user')->references('id')->on('sys_user');
        });
    }
}
