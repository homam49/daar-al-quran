<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role;

class TruncateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate {--force : Force the operation to run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate all application tables to start with a fresh database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('WARNING: This will delete ALL data except moderator accounts. Are you sure you want to continue?')) {
            $this->info('Operation canceled.');
            return 1;
        }

        $this->info('Starting database truncation...');

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        try {
            // Tables to truncate
            $tables = [
                'schools',
                'class_rooms',
                'students',
                'class_sessions',
                'attendances',
                'messages',
                'class_schedules',
                'student_class_rooms',
                'school_teacher'
            ];
            
            // Truncate tables
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->info("Table '{$table}' truncated successfully.");
                } else {
                    $this->warn("Table '{$table}' does not exist, skipping.");
                }
            }
            
            // Get the moderator role ID
            $moderatorRole = Role::where('name', 'moderator')->first();
            $moderatorRoleId = $moderatorRole ? $moderatorRole->id : null;
            
            if ($moderatorRoleId) {
                // Delete all users except moderators
                $deletedUsers = User::where('role_id', '!=', $moderatorRoleId)->delete();
                $this->info("Deleted {$deletedUsers} non-moderator users.");
            } else {
                $this->warn("Moderator role not found, skipping user deletion.");
            }
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            $this->info('Database truncation completed successfully!');
            
            return 0;
        } catch (\Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
            
            // Re-enable foreign key checks even if there was an error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            return 1;
        }
    }
} 