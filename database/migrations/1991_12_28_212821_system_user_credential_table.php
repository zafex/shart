<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemUserCredentialTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user_credential');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user_credential', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_user', 100);
            $table->string('password');
            $table->text('note')->nullable();
            $table->integer('status')->default(1);
            $table->dateTime('expired_at')->nullable();
            $table->dateTime('lastused')->nullable();
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index('id_user', 'sys_user_credential_id_user_idx');
            $table->index('password', 'sys_user_credential_password_idx');
            $table->index('note', 'sys_user_credential_note_idx');
            $table->index('status', 'sys_user_credential_status_idx');
            $table->index('lastused', 'sys_user_credential_lastused_idx');
            $table->index('expired_at', 'sys_user_credential_expired_at_idx');
            $table->index('created_by', 'sys_user_credential_created_by_idx');
            $table->index('created_at', 'sys_user_credential_created_at_idx');

            $table->foreign('id_user')->references('id')->on('sys_user');
        });
    }
}
