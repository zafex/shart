<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemUserRoleTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user_role');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user_role', function (Blueprint $table) {
            $table->string('id_role', 100);
            $table->string('id_user', 100);
            $table->dateTime('created_at')->useCurrent();
            $table->foreign('id_role')->references('id')->on('sys_role');
            $table->foreign('id_user')->references('id')->on('sys_user');
            $table->primary(['id_role', 'id_user']);
        });
    }
}
