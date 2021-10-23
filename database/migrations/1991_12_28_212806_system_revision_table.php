<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemRevisionTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_revision');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_revision', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_target', 100);
            $table->string('id_object', 100);
            $table->string('reference');
            $table->integer('order')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();

            $table->index('id_target', 'sys_revision_id_target_idx');
            $table->index('id_object', 'sys_revision_id_object_idx');
            $table->index('reference', 'sys_revision_reference_idx');
            $table->index('order', 'sys_revision_order_idx');
            $table->index('created_by', 'sys_revision_created_by_idx');
            $table->index('created_at', 'sys_revision_created_at_idx');
        });
    }
}
