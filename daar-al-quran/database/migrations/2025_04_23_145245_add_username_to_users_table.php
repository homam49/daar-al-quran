<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUsernameToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
            // Add unique index but allow null values
            $table->unique('username');
        });

        // Generate usernames for existing users based on their emails
        DB::table('users')->whereNull('username')->cursor()->each(function ($user) {
            $emailParts = explode('@', $user->email);
            $baseUsername = $emailParts[0];
            
            // Attempt with original email prefix
            $username = $baseUsername;
            $counter = 1;
            
            // Check if username exists and generate a unique one
            while (DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }
            
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
}
