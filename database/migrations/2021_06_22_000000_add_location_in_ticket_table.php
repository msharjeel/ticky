<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationInTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('tickets', 'department_location_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                // $table->foreignId('department_location_id')->constrained('department_locations')->nullOnDelete()->default(0);
                $table->foreignId('department_location_id')->nullable()->constrained('department_locations')->nullOnDelete()->default(0);
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
        if (Schema::hasColumn('tickets', 'department_location_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('department_location_id');
            });
        }
    }
}
