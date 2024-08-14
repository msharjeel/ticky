<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdInDepartmentLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('department_locations', 'parent_id')) {
            Schema::table('department_locations', function (Blueprint $table) {
                $table->foreignId('parent_id')->nullable()->constrained('parent_location')->deleteOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('department_locations', 'parent_id')) {
            Schema::table('department_locations', function (Blueprint $table) {
                $table->dropColumn('parent_id');
            });
        }
    }
}
