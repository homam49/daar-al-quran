<?php

namespace App\Console\Commands;

use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSchoolDeletionCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:generate-deletion-code {school_code : The unique code of the school}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a deletion code for a school';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schoolCode = $this->argument('school_code');
        
        $school = School::where('code', $schoolCode)->first();
        
        if (!$school) {
            $this->error("School with code '{$schoolCode}' not found.");
            return 1;
        }
        
        // Generate a random 8-character deletion code
        $deletionCode = strtoupper(Str::random(8));
        
        // Save the deletion code to the school
        $school->deletion_code = $deletionCode;
        $school->save();
        
        $this->info("Deletion code generated successfully for school: {$school->name}");
        $this->info("School Code: {$school->code}");
        $this->info("Deletion Code: {$deletionCode}");
        $this->warn("Keep this code secure. It can be used to delete the school and all associated data.");
        
        return 0;
    }
} 