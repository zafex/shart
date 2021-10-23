<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shart\Type;

class MasterPositionTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_position');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_position', function (Blueprint $table) {
            migrate_add_primary($table, 'id');
            $table->string('id_organization', 100);
            $table->string('type')->default(Type::FUNCTIONAL);
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('level')->default(0);
            $table->integer('status')->default(1);
            $table->string('created_by')->default('system:anonymous');
            $table->dateTime('created_at')->useCurrent();
            $table->string('updated_by')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index('id_organization', 'mst_position_id_organization_idx');
            $table->index('type', 'mst_position_type_idx');
            $table->index('code', 'mst_position_code_idx');
            $table->index('name', 'mst_position_name_idx');
            $table->index('description', 'mst_position_description_idx');
            $table->index('level', 'mst_position_level_idx');
            $table->index('status', 'mst_position_status_idx');
            $table->index('created_by', 'mst_position_created_by_idx');
            $table->index('created_at', 'mst_position_created_at_idx');

            $table->foreign('id_organization')->references('id')->on('mst_organization');
        });
    }
}
