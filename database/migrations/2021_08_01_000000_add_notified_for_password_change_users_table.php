<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotifiedForPasswordChangeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'notified_for_password_change')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('notified_for_password_change')->default(0)->nullable();
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
        if (Schema::hasColumn('users', 'notified_for_password_change')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('notified_for_password_change');
            });
        }
    }
}
