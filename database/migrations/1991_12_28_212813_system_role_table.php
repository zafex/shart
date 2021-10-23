<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SystemRoleTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sys_role');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_role', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('identity');
            $table->string('label');
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index('identity', 'sys_role_identity_idx');
            $table->index('label', 'sys_role_label_idx');
            $table->index('description', 'sys_role_description_idx');
            $table->index('status', 'sys_role_status_idx');
            $table->index('created_by', 'sys_role_created_by_idx');
            $table->index('created_at', 'sys_role_created_at_idx');

            $table->unique(['identity'], 'sys_role_00_unique');
        });
    }
}
