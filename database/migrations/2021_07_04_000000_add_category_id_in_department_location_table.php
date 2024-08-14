<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdInDepartmentLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('department_locations', 'location_category')) {
            Schema::table('department_locations', function (Blueprint $table) {
                $table->unsignedInteger('location_category')->nullable();
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
        if (Schema::hasColumn('department_locations', 'location_category')) {
            Schema::table('department_locations', function (Blueprint $table) {
                $table->dropColumn('location_category');
            });
        }
    }
}
