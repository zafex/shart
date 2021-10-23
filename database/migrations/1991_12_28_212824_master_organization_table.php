<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterOrganizationTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_organization');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_organization', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_parent', 100)->nullable();
            $table->string('code');
            $table->string('name');
            $table->string('path');
            $table->text('description')->nullable();
            $table->integer('level')->default(0);
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index('id_parent', 'mst_organization_id_parent_idx');
            $table->index('code', 'mst_organization_code_idx');
            $table->index('name', 'mst_organization_name_idx');
            $table->index('path', 'mst_organization_path_idx');
            $table->index('description', 'mst_organization_description_idx');
            $table->index('level', 'mst_organization_level_idx');
            $table->index('status', 'mst_organization_status_idx');
            $table->index('created_by', 'mst_organization_created_by_idx');
            $table->index('created_at', 'mst_organization_created_at_idx');
        });
    }
}
