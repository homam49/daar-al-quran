<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MemorizationProgress;
use App\Models\Student;
use App\Models\User;

class MemorizationPagesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        // Get all students and a teacher
        $students = Student::all();
        $teacher = User::whereHas('role', function($query) {
            $query->where('name', 'teacher');
        })->first();

        if ($students->isEmpty() || !$teacher) {
            $this->command->info('No students or teachers found. Please run other seeders first.');
            return;
        }

        $this->command->info('Creating memorization progress data for pages and surahs...');

        foreach ($students as $student) {
            $this->createProgressForStudent($student, $teacher);
        }

        $this->command->info('Memorization progress data created successfully!');
    }

    private function createProgressForStudent($student, $teacher)
    {
        // Create progress for some pages (1-581)
        $pagesData = [
            // Student has memorized some early pages
            ['page' => 1, 'status' => 'memorized'],
            ['page' => 2, 'status' => 'memorized'],
            ['page' => 3, 'status' => 'memorized'],
            ['page' => 4, 'status' => 'memorized'],
            ['page' => 5, 'status' => 'reviewed'],
            ['page' => 6, 'status' => 'reviewed'],
            ['page' => 7, 'status' => 'in_progress'],
            ['page' => 8, 'status' => 'in_progress'],
            ['page' => 9, 'status' => 'not_started'],
            ['page' => 10, 'status' => 'not_started'],
            
            // Some middle pages
            ['page' => 50, 'status' => 'memorized'],
            ['page' => 51, 'status' => 'memorized'],
            ['page' => 52, 'status' => 'in_progress'],
            ['page' => 100, 'status' => 'memorized'],
            ['page' => 150, 'status' => 'reviewed'],
            ['page' => 200, 'status' => 'in_progress'],
            ['page' => 250, 'status' => 'not_started'],
            ['page' => 300, 'status' => 'memorized'],
            ['page' => 350, 'status' => 'in_progress'],
            ['page' => 400, 'status' => 'not_started'],
            
            // Near the end
            ['page' => 570, 'status' => 'memorized'],
            ['page' => 571, 'status' => 'memorized'],
            ['page' => 572, 'status' => 'in_progress'],
            ['page' => 573, 'status' => 'in_progress'],
            ['page' => 574, 'status' => 'not_started'],
            ['page' => 575, 'status' => 'not_started'],
            ['page' => 580, 'status' => 'memorized'],
            ['page' => 581, 'status' => 'memorized'],
        ];

        foreach ($pagesData as $pageData) {
            MemorizationProgress::create([
                'student_id' => $student->id,
                'type' => 'page',
                'page_number' => $pageData['page'],
                'content_name' => "صفحة {$pageData['page']}",
                'status' => $pageData['status'],
                'teacher_id' => $teacher->id,
                'started_at' => in_array($pageData['status'], ['in_progress', 'memorized', 'reviewed']) ? now()->subDays(rand(1, 30)) : null,
                'completed_at' => $pageData['status'] === 'memorized' ? now()->subDays(rand(1, 15)) : null,
                'last_reviewed_at' => $pageData['status'] === 'reviewed' ? now()->subDays(rand(1, 7)) : null,
                'notes' => $this->getRandomNote($pageData['status']),
            ]);
        }

        // Create progress for some surahs (103-114)
        $surahsData = [
            ['surah' => 103, 'name' => 'العصر', 'status' => 'memorized'],
            ['surah' => 104, 'name' => 'الهمزة', 'status' => 'memorized'],
            ['surah' => 105, 'name' => 'الفيل', 'status' => 'memorized'],
            ['surah' => 106, 'name' => 'قريش', 'status' => 'reviewed'],
            ['surah' => 107, 'name' => 'الماعون', 'status' => 'reviewed'],
            ['surah' => 108, 'name' => 'الكوثر', 'status' => 'in_progress'],
            ['surah' => 109, 'name' => 'الكافرون', 'status' => 'in_progress'],
            ['surah' => 110, 'name' => 'النصر', 'status' => 'memorized'],
            ['surah' => 111, 'name' => 'المسد', 'status' => 'memorized'],
            ['surah' => 112, 'name' => 'الإخلاص', 'status' => 'memorized'],
            ['surah' => 113, 'name' => 'الفلق', 'status' => 'reviewed'],
            ['surah' => 114, 'name' => 'الناس', 'status' => 'memorized'],
        ];

        foreach ($surahsData as $surahData) {
            MemorizationProgress::create([
                'student_id' => $student->id,
                'type' => 'surah',
                'surah_number' => $surahData['surah'],
                'surah_name' => $surahData['name'],
                'content_name' => $surahData['name'],
                'status' => $surahData['status'],
                'teacher_id' => $teacher->id,
                'started_at' => in_array($surahData['status'], ['in_progress', 'memorized', 'reviewed']) ? now()->subDays(rand(1, 20)) : null,
                'completed_at' => $surahData['status'] === 'memorized' ? now()->subDays(rand(1, 10)) : null,
                'last_reviewed_at' => $surahData['status'] === 'reviewed' ? now()->subDays(rand(1, 5)) : null,
                'notes' => $this->getRandomNote($surahData['status']),
            ]);
        }
    }

    private function getRandomNote($status)
    {
        $notes = [
            'memorized' => [
                'ممتاز! حفظ متقن',
                'أداء رائع في الحفظ',
                'حفظ جيد مع تجويد ممتاز',
                'حفظ كامل بدون أخطاء',
                'أحسنت، حفظ متقن ومتأن',
            ],
            'in_progress' => [
                'يحتاج لمزيد من المراجعة',
                'حفظ جيد لكن يحتاج تثبيت',
                'في طريقه للإتقان',
                'يراجع بانتظام',
                'يحتاج تركيز أكثر في بعض المواضع',
            ],
            'reviewed' => [
                'مراجعة ممتازة',
                'تثبيت جيد للحفظ',
                'أداء متميز في المراجعة',
                'حافظ على مستوى الحفظ',
                'مراجعة منتظمة ومفيدة',
            ],
            'not_started' => [
                null, null, null // Most not started won't have notes
            ]
        ];

        $statusNotes = $notes[$status] ?? [null];
        return $statusNotes[array_rand($statusNotes)];
    }
}
