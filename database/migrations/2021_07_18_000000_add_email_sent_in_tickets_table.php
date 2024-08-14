<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailSentInTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('tickets', 'email_sent')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedInteger('email_sent')->default(0)->nullable();
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
        if (Schema::hasColumn('tickets', 'email_sent')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('email_sent');
            });
        }
    }
}
