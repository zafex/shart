<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterEmployeeTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_employee');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_employee', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_organization', 100);
            $table->string('code');
            $table->string('name');
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('birthday')->nullable();
            $table->integer('level')->default(0);
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index('id_organization', 'mst_employee_id_organization_idx');
            $table->index('code', 'mst_employee_code_idx');
            $table->index('name', 'mst_employee_name_idx');
            $table->index('email', 'mst_employee_email_idx');
            $table->index('description', 'mst_employee_description_idx');
            $table->index('birthday', 'mst_employee_birthday_idx');
            $table->index('level', 'mst_employee_level_idx');
            $table->index('status', 'mst_employee_status_idx');
            $table->index('created_by', 'mst_employee_created_by_idx');
            $table->index('created_at', 'mst_employee_created_at_idx');

            $table->foreign('id_organization')->references('id')->on('mst_organization');
        });
    }
}
