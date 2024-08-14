<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreventiveMaintenanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('preventive_maintenance')) {
            Schema::create('preventive_maintenance', function (Blueprint $table) {
                $table->increments('id');
                
                
                $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
                $table->foreignId('sub_department_id')->nullable()->constrained('departments')->nullOnDelete();

                $table->foreignId('department_location_id')->nullable()->constrained('department_locations')->nullOnDelete();

                $table->foreignId('department_sub_location_id')->nullable()->constrained('department_locations')->nullOnDelete();
                
                
                
                $table->date('prev_maint_1_date')->nullable();
                $table->tinyInteger('prev_maint_1_done')->default(0)->nullable();

                $table->date('prev_maint_2_date')->nullable();
                $table->tinyInteger('prev_maint_2_done')->default(0)->nullable();

                $table->date('prev_maint_3_date')->nullable();
                $table->tinyInteger('prev_maint_3_done')->default(0)->nullable();

                $table->date('prev_maint_4_date')->nullable();
                $table->tinyInteger('prev_maint_4_done')->default(0)->nullable();

                
                $table->timestamps();
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
        Schema::dropIfExists('preventive_maintenance');
    }
}
