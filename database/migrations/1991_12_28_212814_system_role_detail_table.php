<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemRoleDetailTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_role_detail');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_role_detail', function (Blueprint $table) {
            $table->string('id_role', 100);
            $table->string('id_detail', 100);
            $table->dateTime('created_at')->useCurrent();
            $table->foreign('id_role')->references('id')->on('sys_role');
            $table->foreign('id_detail')->references('id')->on('sys_role');
            $table->primary(['id_role', 'id_detail']);
        });
    }
}
