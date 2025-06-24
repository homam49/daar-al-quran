<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemorizationProgress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'surah_number',
        'surah_name',
        'type',
        'page_number',
        'content_name',
        'content_details',
        'status',
        'teacher_id',
        'started_at',
        'completed_at',
        'last_reviewed_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'date',
        'completed_at' => 'date',
        'last_reviewed_at' => 'date',
    ];

    /**
     * Get the student that owns the memorization progress.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the teacher who verified the memorization.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the status badge color for display.
     */
    public function getStatusBadgeAttribute()
    {
        switch($this->status) {
            case 'not_started':
                return 'secondary';
            case 'in_progress':
                return 'warning';
            case 'memorized':
                return 'success';
            case 'reviewed':
                return 'primary';
            default:
                return 'secondary';
        }
    }

    /**
     * Get the status text in Arabic.
     */
    public function getStatusTextAttribute()
    {
        switch($this->status) {
            case 'not_started':
                return 'لم يبدأ';
            case 'in_progress':
                return 'قيد الحفظ';
            case 'memorized':
                return 'محفوظة';
            case 'reviewed':
                return 'مراجعة';
            default:
                return 'غير محدد';
        }
    }

    /**
     * Get all pages from 1 to 581.
     */
    public static function getAllPages()
    {
        $pages = [];
        for ($i = 1; $i <= 581; $i++) {
            $pages[$i] = "صفحة {$i}";
        }
        return $pages;
    }

    /**
     * Get remaining Surahs (those on pages 582-604).
     * Based on standard Mushaf, these are Surahs 78-114
     */
    public static function getRemainingSurahs()
    {
        return [
            78 => 'النبأ', 79 => 'النازعات', 80 => 'عبس',
            81 => 'التكوير', 82 => 'الانفطار', 83 => 'المطففين', 84 => 'الانشقاق', 85 => 'البروج',
            86 => 'الطارق', 87 => 'الأعلى', 88 => 'الغاشية', 89 => 'الفجر', 90 => 'البلد',
            91 => 'الشمس', 92 => 'الليل', 93 => 'الضحى', 94 => 'الشرح', 95 => 'التين',
            96 => 'العلق', 97 => 'القدر', 98 => 'البينة', 99 => 'الزلزلة', 100 => 'العاديات',
            101 => 'القارعة', 102 => 'التكاثر', 103 => 'العصر', 104 => 'الهمزة', 105 => 'الفيل',
            106 => 'قريش', 107 => 'الماعون', 108 => 'الكوثر', 109 => 'الكافرون', 110 => 'النصر',
            111 => 'المسد', 112 => 'الإخلاص', 113 => 'الفلق', 114 => 'الناس'
        ];
    }

    /**
     * Get all content items (pages + remaining surahs).
     */
    public static function getAllContent()
    {
        $content = [];
        
        // Add pages 1-581
        for ($i = 1; $i <= 581; $i++) {
            $content[] = [
                'type' => 'page',
                'number' => $i,
                'name' => "صفحة {$i}",
                'id' => "page_{$i}"
            ];
        }
        
        // Add remaining surahs
        $remainingSurahs = self::getRemainingSurahs();
        foreach ($remainingSurahs as $number => $name) {
            $content[] = [
                'type' => 'surah',
                'number' => $number,
                'name' => $name,
                'id' => "surah_{$number}"
            ];
        }
        
        return collect($content);
    }

    /**
     * Get all Quran Surahs data (for reference).
     */
    public static function getAllSurahs()
    {
        return [
            1 => 'الفاتحة', 2 => 'البقرة', 3 => 'آل عمران', 4 => 'النساء', 5 => 'المائدة',
            6 => 'الأنعام', 7 => 'الأعراف', 8 => 'الأنفال', 9 => 'التوبة', 10 => 'يونس',
            11 => 'هود', 12 => 'يوسف', 13 => 'الرعد', 14 => 'إبراهيم', 15 => 'الحجر',
            16 => 'النحل', 17 => 'الإسراء', 18 => 'الكهف', 19 => 'مريم', 20 => 'طه',
            21 => 'الأنبياء', 22 => 'الحج', 23 => 'المؤمنون', 24 => 'النور', 25 => 'الفرقان',
            26 => 'الشعراء', 27 => 'النمل', 28 => 'القصص', 29 => 'العنكبوت', 30 => 'الروم',
            31 => 'لقمان', 32 => 'السجدة', 33 => 'الأحزاب', 34 => 'سبأ', 35 => 'فاطر',
            36 => 'يس', 37 => 'الصافات', 38 => 'ص', 39 => 'الزمر', 40 => 'غافر',
            41 => 'فصلت', 42 => 'الشورى', 43 => 'الزخرف', 44 => 'الدخان', 45 => 'الجاثية',
            46 => 'الأحقاف', 47 => 'محمد', 48 => 'الفتح', 49 => 'الحجرات', 50 => 'ق',
            51 => 'الذاريات', 52 => 'الطور', 53 => 'النجم', 54 => 'القمر', 55 => 'الرحمن',
            56 => 'الواقعة', 57 => 'الحديد', 58 => 'المجادلة', 59 => 'الحشر', 60 => 'الممتحنة',
            61 => 'الصف', 62 => 'الجمعة', 63 => 'المنافقون', 64 => 'التغابن', 65 => 'الطلاق',
            66 => 'التحريم', 67 => 'الملك', 68 => 'القلم', 69 => 'الحاقة', 70 => 'المعارج',
            71 => 'نوح', 72 => 'الجن', 73 => 'المزمل', 74 => 'المدثر', 75 => 'القيامة',
            76 => 'الإنسان', 77 => 'المرسلات', 78 => 'النبأ', 79 => 'النازعات', 80 => 'عبس',
            81 => 'التكوير', 82 => 'الانفطار', 83 => 'المطففين', 84 => 'الانشقاق', 85 => 'البروج',
            86 => 'الطارق', 87 => 'الأعلى', 88 => 'الغاشية', 89 => 'الفجر', 90 => 'البلد',
            91 => 'الشمس', 92 => 'الليل', 93 => 'الضحى', 94 => 'الشرح', 95 => 'التين',
            96 => 'العلق', 97 => 'القدر', 98 => 'البينة', 99 => 'الزلزلة', 100 => 'العاديات',
            101 => 'القارعة', 102 => 'التكاثر', 103 => 'العصر', 104 => 'الهمزة', 105 => 'الفيل',
            106 => 'قريش', 107 => 'الماعون', 108 => 'الكوثر', 109 => 'الكافرون', 110 => 'النصر',
            111 => 'المسد', 112 => 'الإخلاص', 113 => 'الفلق', 114 => 'الناس'
        ];
    }

    /**
     * Get Surah name by number.
     */
    public static function getSurahName($number)
    {
        $surahs = self::getAllSurahs();
        return $surahs[$number] ?? "سورة رقم {$number}";
    }

    /**
     * Get content name based on type and number.
     */
    public static function getContentName($type, $number)
    {
        if ($type === 'page') {
            return "صفحة {$number}";
        } else {
            return self::getSurahName($number);
        }
    }
}
