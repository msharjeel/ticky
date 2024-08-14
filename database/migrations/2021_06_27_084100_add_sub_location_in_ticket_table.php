<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubLocationInTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (true || !Schema::hasColumn('tickets', 'department_sub_location_id')) {
            Schema::table('tickets', function (Blueprint $table) {

                $table->unsignedInteger('department_sub_location_id')->nullable();            
                
                // // $table->foreign('department_sub_location_id')->references('id')->on('department_locations')->nullOnDelete();
                // $table->foreign('department_sub_location_id')->constrained('tickets_department_sub_location_id_foreign')->nullOnDelete()->references('id')->on('department_locations')->default(0);
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
        if (Schema::hasColumn('tickets', 'department_sub_location_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('department_sub_location_id');
            });
        }
    }
}
