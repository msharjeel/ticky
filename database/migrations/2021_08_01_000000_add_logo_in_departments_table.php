<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogoInDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('departments', 'logo')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->string('logo')->nullable();
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
        if (Schema::hasColumn('departments', 'logo')) {
            Schema::table('departments', function (Blueprint $table) {
                $table->dropColumn('logo');
            });
        }
    }
}
