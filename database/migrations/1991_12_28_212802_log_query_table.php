<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogQueryTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_query');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_query', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_request', 100);
            $table->string('id_session', 100);
            $table->text('sql');
            $table->double('time');
            $table->string('address')->nullable();
            $table->text('browser')->nullable();
            $table->text('url')->nullable();
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();

            $table->index('id_request', 'log_query_id_request_idx');
            $table->index('id_session', 'log_query_id_session_idx');
            $table->index('sql', 'log_query_sql_idx');
            $table->index('time', 'log_query_time_idx');
            $table->index('address', 'log_query_address_idx');
            $table->index('browser', 'log_query_browser_idx');
            $table->index('url', 'log_query_url_idx');
            $table->index('created_by', 'log_query_created_by_idx');
            $table->index('created_at', 'log_query_created_at_idx');
        });
    }
}
