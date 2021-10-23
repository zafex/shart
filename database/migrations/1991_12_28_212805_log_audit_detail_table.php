<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogAuditDetailTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_audit_detail');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_audit_detail', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_audit', 100);
            $table->string('name');
            $table->text('value');

            $table->index('id_audit', 'log_audit_detail_id_audit_idx');
            $table->index('name', 'log_audit_detail_name_idx');
            $table->index('value', 'log_audit_detail_value_idx');

            $table->foreign('id_audit')->references('id')->on('log_audit');
        });
    }
}
