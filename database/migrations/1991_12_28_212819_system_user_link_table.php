<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemUserLinkTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_user_link');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_user_link', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_user', 100);
            $table->string('id_object', 100);
            $table->string('reference');
            $table->string('provider')->default('application');
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();

            $table->index('id_user', 'sys_user_link_id_user_idx');
            $table->index('id_object', 'sys_user_link_id_object_idx');
            $table->index('reference', 'sys_user_link_reference_idx');
            $table->index('provider', 'sys_user_link_provider_idx');

            $table->unique(['id_object', 'reference', 'provider'], 'sys_user_link_00_unique');
            $table->foreign('id_user')->references('id')->on('sys_user');
        });
    }
}
