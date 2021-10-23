<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterStructureTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_structure');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_structure', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_organization', 100);
            $table->string('id_employee', 100);
            $table->string('id_position', 100);
            $table->integer('status')->default(1);
            $table->dateTime('actived_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index('id_organization', 'mst_structure_id_organization_idx');
            $table->index('id_employee', 'mst_structure_id_employee_idx');
            $table->index('id_position', 'mst_structure_id_position_idx');
            $table->index('status', 'mst_structure_status_idx');
            $table->index('created_by', 'mst_structure_created_by_idx');
            $table->index('created_at', 'mst_structure_created_at_idx');
            $table->index('actived_at', 'mst_structure_actived_at_idx');
            $table->index('expired_at', 'mst_structure_expired_at_idx');

            $table->foreign('id_organization')->references('id')->on('mst_organization');
            $table->foreign('id_employee')->references('id')->on('mst_employee');
            $table->foreign('id_position')->references('id')->on('mst_position');
        });
    }
}
