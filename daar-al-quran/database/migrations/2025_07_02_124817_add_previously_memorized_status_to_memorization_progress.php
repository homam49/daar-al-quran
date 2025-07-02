<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPreviouslyMemorizedStatusToMemorizationProgress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For MySQL, modify the enum to include 'previously_memorized'
        DB::statement("ALTER TABLE memorization_progress MODIFY COLUMN status ENUM('not_started', 'in_progress', 'memorized', 'reviewed', 'previously_memorized') NOT NULL DEFAULT 'not_started'");
        
        echo "Added 'previously_memorized' status to memorization_progress.status enum\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove 'previously_memorized' status from enum (only if no records with this status exist)
        $previouslyMemorizedCount = DB::table('memorization_progress')->where('status', 'previously_memorized')->count();
        
        if ($previouslyMemorizedCount > 0) {
            throw new Exception("Cannot reverse migration: {$previouslyMemorizedCount} previously_memorized progress records exist. Delete them first.");
        }
        
        DB::statement("ALTER TABLE memorization_progress MODIFY COLUMN status ENUM('not_started', 'in_progress', 'memorized', 'reviewed') NOT NULL DEFAULT 'not_started'");
        
        echo "Removed 'previously_memorized' status from memorization_progress.status enum\n";
    }
}
