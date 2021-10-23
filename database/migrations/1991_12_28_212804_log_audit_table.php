<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogAuditTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_audit');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_audit', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_request', 100);
            $table->string('id_session', 100);
            $table->string('id_object', 100);
            $table->string('entity');
            $table->string('action');
            $table->string('address');
            $table->text('browser');
            $table->text('url');
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();

            $table->index('id_request', 'log_audit_id_request_idx');
            $table->index('id_session', 'log_audit_id_session_idx');
            $table->index('id_object', 'log_audit_id_object_idx');
            $table->index('entity', 'log_audit_entity_idx');
            $table->index('action', 'log_audit_action_idx');
            $table->index('address', 'log_audit_address_idx');
            $table->index('browser', 'log_audit_browser_idx');
            $table->index('url', 'log_audit_url_idx');
            $table->index('created_by', 'log_audit_created_by_idx');
            $table->index('created_at', 'log_audit_created_at_idx');
        });
    }
}
