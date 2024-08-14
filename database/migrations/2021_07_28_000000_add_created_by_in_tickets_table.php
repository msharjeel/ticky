<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByInTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('tickets', 'created_by')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->bigInteger('created_by')->nullable()->after('agent_id');
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
        if (Schema::hasColumn('tickets', 'created_by')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }
    }
}
