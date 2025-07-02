<?php

namespace App\Services;

use App\Models\MemorizationProgress;
use App\Models\Student;

class MemorizationService
{
    /**
     * Get memorization progress data for a student.
     *
     * @param Student $student
     * @return array
     */
    public function getProgressData(Student $student): array
    {
        // Get all content items (pages + surahs)
        $contentItems = MemorizationProgress::getAllContent();
        
        // Get existing progress records
        $progressRecords = MemorizationProgress::where('student_id', $student->id)->get();
        
        // Create a keyed array for quick lookup
        $progressLookup = [];
        foreach ($progressRecords as $progress) {
            if ($progress->type === 'page') {
                $progressLookup["page_{$progress->page_number}"] = $progress;
            } elseif ($progress->type === 'surah') {
                $progressLookup["surah_{$progress->surah_number}"] = $progress;
            } elseif ($progress->type === 'juz') {
                $progressLookup["juz_{$progress->content_details}"] = $progress;
            }
        }

        return [
            'contentItems' => $contentItems,
            'progressRecords' => $progressRecords,
            'progressLookup' => $progressLookup,
            'statistics' => $this->calculateStatistics($progressRecords)
        ];
    }

    /**
     * Calculate memorization statistics.
     *
     * @param \Illuminate\Database\Eloquent\Collection $progressRecords
     * @return array
     */
    public function calculateStatistics($progressRecords): array
    {
        $totalItems = 581 + 37 + 30; // Pages + Surahs + Juz
        $memorizedCount = $progressRecords->where('status', 'memorized')->count();
        $inProgressCount = $progressRecords->where('status', 'in_progress')->count();
        $completionPercentage = $totalItems > 0 ? round(($memorizedCount / $totalItems) * 100, 1) : 0;

        // Separate statistics for pages, surahs, and juz
        $pageProgress = $progressRecords->where('type', 'page');
        $surahProgress = $progressRecords->where('type', 'surah');
        $juzProgress = $progressRecords->where('type', 'juz');

        return [
            'total' => $totalItems,
            'memorized' => $memorizedCount,
            'in_progress' => $inProgressCount,
            'completion_percentage' => $completionPercentage,
            'pages' => [
                'total' => 581,
                'memorized' => $pageProgress->where('status', 'memorized')->count(),
                'in_progress' => $pageProgress->where('status', 'in_progress')->count(),
            ],
            'surahs' => [
                'total' => 37, // Surahs 78-114
                'memorized' => $surahProgress->where('status', 'memorized')->count(),
                'in_progress' => $surahProgress->where('status', 'in_progress')->count(),
            ],
            'juz' => [
                'total' => 30, // Juz 1-30
                'memorized' => $juzProgress->where('status', 'memorized')->count(),
                'in_progress' => $juzProgress->where('status', 'in_progress')->count(),
            ]
        ];
    }

    /**
     * Update memorization progress for a student.
     *
     * @param Student $student
     * @param array $data
     * @param int $teacherId
     * @return MemorizationProgress
     */
    public function updateProgress(Student $student, array $data, int $teacherId): MemorizationProgress
    {
        $type = $data['type'];
        $contentName = '';
        $whereClause = ['student_id' => $student->id, 'type' => $type];
        $updateData = [
            'status' => $data['status'],
            'teacher_id' => $teacherId,
            'notes' => $data['notes'] ?? null,
            'started_at' => $data['status'] != 'not_started' ? now() : null,
            'completed_at' => $data['status'] === 'memorized' ? now() : null,
        ];

        if ($type === 'page') {
            $pageNumber = $data['page_number'];
            $contentName = "صفحة {$pageNumber}";
            $whereClause['page_number'] = $pageNumber;
            $updateData['page_number'] = $pageNumber;
            $updateData['content_name'] = $contentName;
        } elseif ($type === 'surah') {
            $surahNumber = $data['surah_number'];
            $surahName = MemorizationProgress::getSurahName($surahNumber);
            $contentName = $surahName;
            $whereClause['surah_number'] = $surahNumber;
            $updateData['surah_number'] = $surahNumber;
            $updateData['surah_name'] = $surahName;
            $updateData['content_name'] = $contentName;
        } else { // juz
            $juzNumber = $data['juz_number'];
            $juzNames = MemorizationProgress::getAllJuz();
            $contentName = $juzNames[$juzNumber] ?? "الجزء {$juzNumber}";
            $whereClause['content_details'] = $juzNumber;
            $updateData['content_details'] = $juzNumber;
            $updateData['content_name'] = $contentName;
        }
        
        // Find or create progress record
        return MemorizationProgress::updateOrCreate($whereClause, $updateData);
    }

    /**
     * Get specific progress information.
     *
     * @param Student $student
     * @param string $type
     * @param int $number
     * @return array
     */
    public function getProgressInfo(Student $student, string $type, int $number): array
    {
        $query = MemorizationProgress::where('student_id', $student->id)
            ->where('type', $type);
            
        if ($type === 'page') {
            $query->where('page_number', $number);
        } elseif ($type === 'surah') {
            $query->where('surah_number', $number);
        } else { // juz
            $query->where('content_details', $number);
        }
        
        $progress = $query->first();

        if (!$progress) {
            return [
                'status' => 'not_started',
                'notes' => '',
                'teacher' => null
            ];
        }

        return [
            'status' => $progress->status,
            'notes' => $progress->notes,
            'teacher' => $progress->teacher ? $progress->teacher->name : null,
            'started_at' => $progress->started_at ? $progress->started_at->format('d/m/Y') : null,
            'completed_at' => $progress->completed_at ? $progress->completed_at->format('d/m/Y') : null,
            'last_reviewed_at' => $progress->last_reviewed_at ? $progress->last_reviewed_at->format('d/m/Y') : null,
        ];
    }
} 