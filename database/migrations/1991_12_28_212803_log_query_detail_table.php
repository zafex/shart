<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LogQueryDetailTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_query_detail');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_query_detail', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_query', 100);
            $table->string('param');
            $table->integer('order')->default(0);

            $table->index('id_query', 'log_query_detail_id_query_idx');
            $table->index('param', 'log_query_detail_param_idx');
            $table->index('order', 'log_query_detail_order_idx');

            $table->foreign('id_query')->references('id')->on('log_query');
        });
    }
}
