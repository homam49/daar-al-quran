<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\User;
use App\Models\Role;

class EnsureAdminHasSingleSchool extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration ensures each admin has at most one school.
     * For each admin with multiple schools, it keeps only the most recently created school
     * and deletes all other schools along with associated data.
     *
     * @return void
     */
    public function up()
    {
        // Step 1: Get the admin role id
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        
        if (!$adminRoleId) {
            return;
        }

        // Step 2: Find all admins with more than one school
        $adminsWithMultipleSchools = DB::table('schools')
            ->select('admin_id', DB::raw('COUNT(*) as school_count'))
            ->where('admin_id', '!=', null)
            ->groupBy('admin_id')
            ->having('school_count', '>', 1)
            ->get();

        foreach ($adminsWithMultipleSchools as $admin) {
            // Step 3: For each admin, keep only the most recently created school
            $schoolsToKeep = DB::table('schools')
                ->where('admin_id', $admin->admin_id)
                ->orderBy('created_at', 'desc')
                ->limit(1)
                ->pluck('id');

            $schoolsToDelete = DB::table('schools')
                ->where('admin_id', $admin->admin_id)
                ->whereNotIn('id', $schoolsToKeep)
                ->pluck('id');

            // Step 4: For each school to delete, remove related data
            foreach ($schoolsToDelete as $schoolId) {
                // Get classroom IDs for this school
                $classroomIds = DB::table('class_rooms')
                    ->where('school_id', $schoolId)
                    ->pluck('id');

                // Delete student-classroom relationships
                DB::table('student_class_rooms')
                    ->whereIn('class_room_id', $classroomIds)
                    ->delete();

                // Delete classroom schedules
                DB::table('class_schedules')
                    ->whereIn('class_room_id', $classroomIds)
                    ->delete();

                // Delete class sessions
                $sessionIds = DB::table('class_sessions')
                    ->whereIn('class_room_id', $classroomIds)
                    ->pluck('id');

                // Delete attendances for these sessions
                DB::table('attendances')
                    ->whereIn('class_session_id', $sessionIds)
                    ->delete();

                // Delete the sessions
                DB::table('class_sessions')
                    ->whereIn('id', $sessionIds)
                    ->delete();

                // Delete school-teacher relationships
                DB::table('school_teacher')
                    ->where('school_id', $schoolId)
                    ->delete();

                // Delete students for this school
                DB::table('students')
                    ->where('school_id', $schoolId)
                    ->delete();

                // Delete classrooms
                DB::table('class_rooms')
                    ->whereIn('id', $classroomIds)
                    ->delete();

                // Delete the school
                DB::table('schools')
                    ->where('id', $schoolId)
                    ->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // There's no way to reverse this operation as data has been deleted
    }
}
