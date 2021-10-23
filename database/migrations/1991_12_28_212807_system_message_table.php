<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemMessageTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_message');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_message', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('subject');
            $table->text('body');
            $table->string('sender');
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();

            $table->index('subject', 'sys_message_subject_idx');
            $table->index('body', 'sys_message_body_idx');
            $table->index('sender', 'sys_message_sender_idx');
            $table->index('created_by', 'sys_message_created_by_idx');
            $table->index('created_at', 'sys_message_created_at_idx');
        });
    }
}
