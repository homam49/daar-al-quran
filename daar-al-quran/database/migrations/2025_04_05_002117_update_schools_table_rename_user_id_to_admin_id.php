<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSchoolsTableRenameUserIdToAdminId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['user_id']);
            
            // Rename the column
            $table->renameColumn('user_id', 'admin_id');
            
            // Add the foreign key constraint back with the new column name
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['admin_id']);
            
            // Rename the column back
            $table->renameColumn('admin_id', 'user_id');
            
            // Add the foreign key constraint back with the original column name
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
