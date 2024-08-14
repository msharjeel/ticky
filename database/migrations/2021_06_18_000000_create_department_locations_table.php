<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('department_locations')) {
            Schema::create('department_locations', function (Blueprint $table) {
                $table->increments('id');
                
                $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');

                $table->string('location_name');
                $table->string('location_latitude')->nullable();
                $table->string('location_longitude')->nullable();

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
        Schema::dropIfExists('department_locations');
    }
}
