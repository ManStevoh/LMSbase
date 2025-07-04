<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSelectedUsersToNotificationsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This updates the ENUM values to include 'selected_users'
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('single', 'all_users', 'students', 'instructors', 'organizations', 'group', 'course_students', 'selected_users')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This removes 'selected_users' from the ENUM values
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('single', 'all_users', 'students', 'instructors', 'organizations', 'group', 'course_students')");
    }
}
