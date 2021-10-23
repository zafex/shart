<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shart\Type;

class SystemUserTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('username');
            $table->string('fullname');
            $table->string('email');
            $table->string('type')->default(Type::USER_STANDARD);
            $table->text('avatar')->nullable();
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index('username', 'sys_user_username_idx');
            $table->index('fullname', 'sys_user_fullname_idx');
            $table->index('email', 'sys_user_email_idx');
            $table->index('type', 'sys_user_type_idx');
            $table->index('status', 'sys_user_status_idx');
            $table->index('created_by', 'sys_user_created_by_idx');
            $table->index('created_at', 'sys_user_created_at_idx');

            $table->unique('username', 'sys_user_00_unique');
        });
    }
}
