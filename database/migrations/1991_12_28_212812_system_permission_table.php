<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemPermissionTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_permission');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_permission', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_parent', 100)->nullable();
            $table->string('identity');
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();

            $table->index('id_parent', 'sys_permission_id_parent_idx');
            $table->index('identity', 'sys_permission_identity_idx');
            $table->index('label', 'sys_permission_label_idx');
            $table->index('description', 'sys_permission_description_idx');
            $table->index('status', 'sys_permission_status_idx');
            $table->index('created_by', 'sys_permission_created_by_idx');
            $table->index('created_at', 'sys_permission_created_at_idx');

            $table->unique(['identity'], 'sys_permission_00_unique');
        });

        Schema::table('sys_permission', function (Blueprint $table) {
            $table->foreign('id_parent')->references('id')->on('sys_permission');
        });
    }
}
