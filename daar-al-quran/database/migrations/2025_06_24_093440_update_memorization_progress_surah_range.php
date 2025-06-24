<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateMemorizationProgressSurahRange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the validation constraint for surah_number to allow range 78-114
        // Note: Laravel doesn't directly support modifying check constraints,
        // but we can update existing data and ensure the application validates correctly
        
        // Update content_name for existing Surahs to ensure consistency
        $surahNames = [
            78 => 'النبأ', 79 => 'النازعات', 80 => 'عبس',
            81 => 'التكوير', 82 => 'الانفطار', 83 => 'المطففين', 84 => 'الانشقاق', 85 => 'البروج',
            86 => 'الطارق', 87 => 'الأعلى', 88 => 'الغاشية', 89 => 'الفجر', 90 => 'البلد',
            91 => 'الشمس', 92 => 'الليل', 93 => 'الضحى', 94 => 'الشرح', 95 => 'التين',
            96 => 'العلق', 97 => 'القدر', 98 => 'البينة', 99 => 'الزلزلة', 100 => 'العاديات',
            101 => 'القارعة', 102 => 'التكاثر', 103 => 'العصر', 104 => 'الهمزة', 105 => 'الفيل',
            106 => 'قريش', 107 => 'الماعون', 108 => 'الكوثر', 109 => 'الكافرون', 110 => 'النصر',
            111 => 'المسد', 112 => 'الإخلاص', 113 => 'الفلق', 114 => 'الناس'
        ];
        
        // Update existing Surah progress records to ensure surah_name and content_name are correct
        foreach ($surahNames as $surahNumber => $surahName) {
            DB::table('memorization_progress')
                ->where('type', 'surah')
                ->where('surah_number', $surahNumber)
                ->update([
                    'surah_name' => $surahName,
                    'content_name' => $surahName,
                    'updated_at' => now()
                ]);
        }
        
        // Log the number of records updated
        $updatedCount = DB::table('memorization_progress')
            ->where('type', 'surah')
            ->whereBetween('surah_number', [78, 114])
            ->count();
            
        if ($updatedCount > 0) {
            echo "Updated {$updatedCount} existing Surah progress records\n";
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This migration doesn't need to be reversed as it only updates data consistency
        // The original data structure remains the same
    }
}
