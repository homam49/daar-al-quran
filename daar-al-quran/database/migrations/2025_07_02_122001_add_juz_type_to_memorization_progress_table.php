<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddJuzTypeToMemorizationProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For MySQL, modify the enum to include 'juz'
        DB::statement("ALTER TABLE memorization_progress MODIFY COLUMN type ENUM('page', 'surah', 'juz') NOT NULL DEFAULT 'page'");
        
        echo "Added 'juz' type to memorization_progress.type enum\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove 'juz' type from enum (only if no juz records exist)
        $juzCount = DB::table('memorization_progress')->where('type', 'juz')->count();
        
        if ($juzCount > 0) {
            throw new Exception("Cannot reverse migration: {$juzCount} juz progress records exist. Delete them first.");
        }
        
        DB::statement("ALTER TABLE memorization_progress MODIFY COLUMN type ENUM('page', 'surah') NOT NULL DEFAULT 'page'");
        
        echo "Removed 'juz' type from memorization_progress.type enum\n";
    }
}
