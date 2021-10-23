<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemMessageTargetTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_message_target');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_message_target', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_message', 100)->nullable();
            $table->string('id_object', 100)->nullable();
            $table->string('reference')->nullable();
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();

            $table->index('id_message', 'sys_message_target_id_message_idx');
            $table->index('id_object', 'sys_message_target_id_object_idx');
            $table->index('reference', 'sys_message_target_reference_idx');
            $table->index('created_by', 'sys_message_target_created_by_idx');
            $table->index('created_at', 'sys_message_target_created_at_idx');

            $table->foreign('id_message')->references('id')->on('sys_message');
        });
    }
}
