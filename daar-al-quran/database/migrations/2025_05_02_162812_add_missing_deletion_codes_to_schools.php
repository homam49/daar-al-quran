<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddMissingDeletionCodesToSchools extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get all schools with null or empty deletion_code
        $schools = DB::table('schools')
            ->whereNull('deletion_code')
            ->orWhere('deletion_code', '')
            ->get();
        
        foreach ($schools as $school) {
            $deletionCode = Str::random(8);
            
            DB::table('schools')
                ->where('id', $school->id)
                ->update(['deletion_code' => $deletionCode]);
            
            // Log for debugging
            \Illuminate\Support\Facades\Log::info('Updated school with missing deletion code', [
                'school_id' => $school->id,
                'school_name' => $school->name,
                'new_deletion_code' => $deletionCode
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration cannot be rolled back since we can't know which schools
        // originally had no deletion code
    }
}
