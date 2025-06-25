<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemorizationProgress;
use App\Models\Student;
use App\Models\User;

class MemorizationProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get some students to add memorization progress for
        $students = Student::take(3)->get();
        
        if ($students->count() == 0) {
            $this->command->info('No students found. Skipping memorization progress seeding.');
            return;
        }

        // Get a teacher to assign as the verifying teacher
        $teacher = User::whereHas('role', function($query) {
            $query->where('name', 'teacher');
        })->first();

        if (!$teacher) {
            $this->command->info('No teacher found. Skipping memorization progress seeding.');
            return;
        }

        // Sample data for some students
        $sampleProgress = [
            // Student 1: Advanced student
            [
                'surahs' => [
                    1 => ['status' => 'memorized', 'notes' => 'ممتاز في الحفظ والتجويد'],
                    112 => ['status' => 'memorized', 'notes' => 'حفظ جيد'],
                    113 => ['status' => 'memorized', 'notes' => 'حفظ جيد'],
                    114 => ['status' => 'memorized', 'notes' => 'حفظ جيد'],
                    110 => ['status' => 'reviewed', 'notes' => 'يحتاج مراجعة دورية'],
                    111 => ['status' => 'in_progress', 'notes' => 'في منتصف الحفظ'],
                ]
            ],
            // Student 2: Intermediate student
            [
                'surahs' => [
                    1 => ['status' => 'memorized', 'notes' => 'حفظ متقن'],
                    112 => ['status' => 'memorized', 'notes' => 'جيد'],
                    113 => ['status' => 'in_progress', 'notes' => 'يحتاج متابعة'],
                    114 => ['status' => 'not_started', 'notes' => ''],
                ]
            ],
            // Student 3: Beginner student
            [
                'surahs' => [
                    1 => ['status' => 'in_progress', 'notes' => 'طالب مجتهد'],
                    112 => ['status' => 'not_started', 'notes' => ''],
                ]
            ]
        ];

        foreach ($students as $index => $student) {
            if (isset($sampleProgress[$index])) {
                $studentProgress = $sampleProgress[$index];
                
                foreach ($studentProgress['surahs'] as $surahNumber => $progress) {
                    $surahName = MemorizationProgress::getSurahName($surahNumber);
                    
                    MemorizationProgress::create([
                        'student_id' => $student->id,
                        'surah_number' => $surahNumber,
                        'surah_name' => $surahName,
                        'status' => $progress['status'],
                        'teacher_id' => $teacher->id,
                        'notes' => $progress['notes'],
                        'started_at' => $progress['status'] != 'not_started' ? now()->subDays(rand(10, 60)) : null,
                        'completed_at' => $progress['status'] === 'memorized' ? now()->subDays(rand(1, 30)) : null,
                        'last_reviewed_at' => $progress['status'] === 'reviewed' ? now()->subDays(rand(1, 15)) : null,
                    ]);
                }
                
                $this->command->info("Added memorization progress for student: {$student->name}");
            }
        }

        $this->command->info('Memorization progress seeding completed.');
    }
}
