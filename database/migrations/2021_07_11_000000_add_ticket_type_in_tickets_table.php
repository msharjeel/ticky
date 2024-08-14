<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTicketTypeInTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (true || !Schema::hasColumn('tickets', 'ticket_type_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedInteger('ticket_type_id')->nullable();
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
        if (Schema::hasColumn('tickets', 'ticket_type_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('ticket_type_id');
            });
        }
    }
}
