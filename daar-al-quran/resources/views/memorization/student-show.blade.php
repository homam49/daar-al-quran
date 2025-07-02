@extends('layouts.student')

@section('title', 'متابعة حفظ القرآن')

@section('student-content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Page Header -->
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title">
                    <!-- <div class="page-header-icon"><i class="fas fa-book-quran"></i></div> -->
                    تتبع حفظ القرآن - {{ $student->name }}
                </h1>
                <div>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <!-- <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-info shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>إحصائيات الصفحات (1-581)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h5 class="text-info">{{ $statistics['pages']['total'] }}</h5>
                                    <small class="text-muted">إجمالي الصفحات</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="text-success">{{ $statistics['pages']['memorized'] }}</h5>
                                    <small class="text-muted">محفوظة</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="text-warning">{{ $statistics['pages']['in_progress'] }}</h5>
                                    <small class="text-muted">قيد الحفظ</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $pageCompletionPercentage = $statistics['pages']['total'] > 0 ? 
                                            round(($statistics['pages']['memorized'] / $statistics['pages']['total']) * 100, 1) : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $pageCompletionPercentage }}%"
                                         title="{{ $pageCompletionPercentage }}% مكتمل">
                                    </div>
                                </div>
                                <small class="text-muted d-block text-center mt-1">{{ $pageCompletionPercentage }}% مكتمل</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-success shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-book me-2"></i>إحصائيات السور (78-114)</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h5 class="text-success">{{ $statistics['surahs']['total'] }}</h5>
                                    <small class="text-muted">إجمالي السور</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="text-success">{{ $statistics['surahs']['memorized'] }}</h5>
                                    <small class="text-muted">محفوظة</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="text-warning">{{ $statistics['surahs']['in_progress'] }}</h5>
                                    <small class="text-muted">قيد الحفظ</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="progress" style="height: 8px;">
                                    @php
                                        $surahCompletionPercentage = $statistics['surahs']['total'] > 0 ? 
                                            round(($statistics['surahs']['memorized'] / $statistics['surahs']['total']) * 100, 1) : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $surahCompletionPercentage }}%"
                                         title="{{ $surahCompletionPercentage }}% مكتمل">
                                    </div>
                                </div>
                                <small class="text-muted d-block text-center mt-1">{{ $surahCompletionPercentage }}% مكتمل</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Overall Progress Card -->
            <!-- <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>التقدم الإجمالي في حفظ القرآن الكريم</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="p-3 rounded bg-primary bg-opacity-10">
                                        <h4 class="text-primary">{{ $statistics['total'] }}</h4>
                                        <small class="text-muted">إجمالي المحتوى</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 rounded bg-success bg-opacity-10">
                                        <h4 class="text-success">{{ $statistics['memorized'] }}</h4>
                                        <small class="text-muted">محفوظ</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 rounded bg-warning bg-opacity-10">
                                        <h4 class="text-warning">{{ $statistics['in_progress'] }}</h4>
                                        <small class="text-muted">قيد الحفظ</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="p-3 rounded bg-info bg-opacity-10">
                                        <h4 class="text-info">{{ number_format($statistics['completion_percentage'], 1) }}%</h4>
                                        <small class="text-muted">نسبة الإنجاز</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Navigation Tabs -->
            <div class="card shadow-sm">
                <div class="card-header p-0">
                    <ul class="nav nav-tabs card-header-tabs" id="memorizationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pages-tab" data-bs-toggle="tab" data-bs-target="#pages" type="button" role="tab">
                                <i class="fas fa-file-alt me-2"></i>الصفحات (1-581)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="surahs-tab" data-bs-toggle="tab" data-bs-target="#surahs" type="button" role="tab">
                                <i class="fas fa-book me-2"></i>السور الأخيرة (78-114)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="juz-tab" data-bs-toggle="tab" data-bs-target="#juz" type="button" role="tab">
                                <i class="fas fa-bookmark me-2"></i>الأجزاء (1-30)
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="card-body">
                    <div class="tab-content" id="memorizationTabContent">
                        <!-- Pages Tab -->
                        <div class="tab-pane fade show active" id="pages" role="tabpanel">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">صفحات القرآن الكريم (1-581)</h5>
                                    <div class="alert alert-info py-2 px-3 mb-0">
                                        <small><i class="fas fa-info-circle me-1"></i>انقر على أي صفحة لعرض الملاحظات (إن وجدت)</small>
                                    </div>
                                </div>
                                
                                <!-- Status Legend -->
                                <div class="mb-3 p-3 bg-light rounded">
                                    <div class="d-flex justify-content-center gap-4 flex-wrap">
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box bg-light border" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                            <small>لم يبدأ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box bg-warning" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                            <small>قيد الحفظ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box bg-success" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                            <small>محفوظ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box bg-info" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                            <small>محفوظ سابقا</small>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Pages Grid -->
                                <div class="pages-grid" style="max-height: 500px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px;">
                                    <div class="row g-2">
                                        @for($page = 1; $page <= 581; $page++)
                                            @php
                                                $pageId = "page_{$page}";
                                                $progress = $progressLookup[$pageId] ?? null;
                                                $status = $progress ? $progress->status : 'not_started';
                                                $hasNotes = $progress && !empty($progress->notes);
                                                switch($status) {
                                                    case 'memorized':
                                                        $statusClass = 'bg-success text-white';
                                                        break;
                                                    case 'previously_memorized':
                                                        $statusClass = 'bg-info text-white';
                                                        break;
                                                    case 'reviewed':
                                                        $statusClass = 'bg-primary text-white';
                                                        break;
                                                    case 'in_progress':
                                                        $statusClass = 'bg-warning text-dark';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-light border';
                                                        break;
                                                }
                                            @endphp
                                            <div class="col-lg-1 col-md-2 col-sm-3 col-4">
                                                <div class="card {{ $statusClass }} h-100 position-relative" 
                                                     style="min-height: 45px; cursor: pointer;"
                                                     @if($hasNotes) title="{{ $progress->notes }}" @endif
                                                     onclick="showProgressInfo('page', {{ $page }}, 'صفحة {{ $page }}')">
                                                    <div class="card-body p-2 text-center d-flex align-items-center justify-content-center">
                                                        <small class="fw-bold">{{ $page }}</small>
                                                    </div>
                                                    @if($hasNotes)
                                                        <i class="fas fa-sticky-note position-absolute" 
                                                           style="top: 2px; right: 2px; font-size: 12px; color: #dc3545; z-index: 10;" 
                                                           title="يحتوي على ملاحظات"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Surahs Tab -->
                        <div class="tab-pane fade" id="surahs" role="tabpanel">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">السور الأخيرة (78-114) - 37 سورة</h5>
                                    <div class="alert alert-info py-2 px-3 mb-0">
                                        <small><i class="fas fa-info-circle me-1"></i>انقر على أي سورة لعرض الملاحظات (إن وجدت)</small>
                                    </div>
                                </div>
                                
                                <!-- Status Legend -->
                                <div class="mb-3 p-3 bg-light rounded">
                                    <div class="d-flex justify-content-center gap-4 flex-wrap">
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box bg-light border" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                            <small>لم يبدأ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box bg-warning" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                            <small>قيد الحفظ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box bg-success" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                            <small>محفوظ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box bg-info" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                            <small>محفوظ سابقا</small>
                                        </span>
                                    </div>
                                </div>

                                <!-- Surahs Grid -->
                                <div class="surahs-grid" style="max-height: 500px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px;">
                                    <div class="row g-3">
                                        @php
                                            $surahNames = [
                                                78 => 'النبأ', 79 => 'النازعات', 80 => 'عبس', 81 => 'التكوير', 82 => 'الانفطار',
                                                83 => 'المطففين', 84 => 'الانشقاق', 85 => 'البروج', 86 => 'الطارق', 87 => 'الأعلى',
                                                88 => 'الغاشية', 89 => 'الفجر', 90 => 'البلد', 91 => 'الشمس', 92 => 'الليل',
                                                93 => 'الضحى', 94 => 'الشرح', 95 => 'التين', 96 => 'العلق', 97 => 'القدر',
                                                98 => 'البينة', 99 => 'الزلزلة', 100 => 'العاديات', 101 => 'القارعة', 102 => 'التكاثر',
                                                103 => 'العصر', 104 => 'الهمزة', 105 => 'الفيل', 106 => 'قريش', 107 => 'الماعون',
                                                108 => 'الكوثر', 109 => 'الكافرون', 110 => 'النصر', 111 => 'المسد', 112 => 'الإخلاص',
                                                113 => 'الفلق', 114 => 'الناس'
                                            ];
                                        @endphp
                                        @for($surah = 78; $surah <= 114; $surah++)
                                            @php
                                                $surahId = "surah_{$surah}";
                                                $progress = $progressLookup[$surahId] ?? null;
                                                $status = $progress ? $progress->status : 'not_started';
                                                $hasNotes = $progress && !empty($progress->notes);
                                                $surahName = $surahNames[$surah];
                                                switch($status) {
                                                    case 'memorized':
                                                        $statusClass = 'bg-success text-white';
                                                        break;
                                                    case 'previously_memorized':
                                                        $statusClass = 'bg-info text-white';
                                                        break;
                                                    case 'reviewed':
                                                        $statusClass = 'bg-primary text-white';
                                                        break;
                                                    case 'in_progress':
                                                        $statusClass = 'bg-warning text-dark';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-light border';
                                                        break;
                                                }
                                            @endphp
                                            <div class="col-lg-3 col-md-4 col-sm-6">
                                                <div class="card {{ $statusClass }} h-100 position-relative" 
                                                     style="cursor: pointer;"
                                                     @if($hasNotes) title="{{ $progress->notes }}" @endif
                                                     onclick="showProgressInfo('surah', {{ $surah }}, '{{ $surahName }}')">
                                                    <div class="card-body p-3 text-center">
                                                        <h6 class="card-title mb-1">{{ $surahName }}</h6>
                                                        <small class="opacity-75">سورة {{ $surah }}</small>
                                                    </div>
                                                    @if($hasNotes)
                                                        <i class="fas fa-sticky-note position-absolute" 
                                                           style="top: 8px; right: 8px; font-size: 14px; color: #dc3545; z-index: 10;" 
                                                           title="يحتوي على ملاحظات"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Juz Tab -->
                        <div class="tab-pane fade" id="juz" role="tabpanel">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">الأجزاء (1-30) - 30 جزء</h5>
                                    <div class="alert alert-info py-2 px-3 mb-0">
                                        <small><i class="fas fa-info-circle me-1"></i>انقر على أي جزء لعرض الملاحظات (إن وجدت)</small>
                                    </div>
                                </div>
                                
                                <!-- Status Legend for Juz -->
                                <div class="mb-3 p-3 bg-light rounded">
                                    <div class="d-flex justify-content-center gap-4 flex-wrap">
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box bg-light border" style="width: 20px; height: 20px; border-radius: 4px;"></div>
                                            <small>لم يبدأ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box" style="width: 20px; height: 20px; border-radius: 4px; background-color: #ffc0cb; border: 1px solid #e91e63;"></div>
                                            <small>قيد الحفظ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-2">
                                            <div class="legend-box" style="width: 20px; height: 20px; border-radius: 4px; background-color: #e91e63;"></div>
                                            <small>محفوظ</small>
                                        </span>
                                    </div>
                                </div>

                                <!-- Juz Grid -->
                                <div class="juz-grid" style="max-height: 500px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px;">
                                    <div class="row g-3">
                                    @foreach(\App\Models\MemorizationProgress::getAllJuz() as $juzNumber => $juzName)
                                        @php
                                            $juzId = "juz_{$juzNumber}";
                                            $progress = $progressLookup[$juzId] ?? null;
                                            $status = $progress ? $progress->status : 'not_started';
                                            $hasNotes = $progress && !empty($progress->notes);
                                            switch($status) {
                                                case 'memorized':
                                                    $statusClass = 'text-white';
                                                    $bgStyle = 'background-color: #e91e63;';
                                                    break;
                                                case 'reviewed':
                                                    $statusClass = 'text-white';
                                                    $bgStyle = 'background-color: #e91e63;';
                                                    break;
                                                case 'in_progress':
                                                    $statusClass = 'text-dark';
                                                    $bgStyle = 'background-color: #ffc0cb; border: 1px solid #e91e63;';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-light border';
                                                    $bgStyle = '';
                                                    break;
                                            }
                                        @endphp
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="card {{ $statusClass }} h-100 position-relative" 
                                                 style="cursor: pointer; {{ $bgStyle }}"
                                                 @if($hasNotes) title="{{ $progress->notes }}" @endif
                                                 onclick="showProgressInfo('juz', {{ $juzNumber }}, '{{ $juzName }}')">
                                                <div class="card-body p-3 text-center">
                                                    <h6 class="card-title mb-1">الجزء {{ $juzNumber }}</h6>
                                                    <small class="opacity-75">{{ $juzName }}</small>
                                                </div>
                                                @if($hasNotes)
                                                    <i class="fas fa-sticky-note position-absolute" 
                                                       style="top: 8px; right: 8px; font-size: 14px; color: #dc3545; z-index: 10;" 
                                                       title="يحتوي على ملاحظات"></i>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Info Modal -->
<div class="modal fade" id="progressInfoModal" tabindex="-1" aria-labelledby="progressInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="progressInfoModalLabel">
                    <i class="fas fa-info-circle me-2"></i>معلومات الحفظ
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-4"><strong>العنصر:</strong></div>
                        <div class="col-8" id="modalContentName"></div>
                    </div>
                </div>
                
                <div class="mb-3 p-3 border rounded">
                    <div class="row">
                        <div class="col-4"><strong>الحالة:</strong></div>
                        <div class="col-8" id="modalStatus"></div>
                    </div>
                </div>

                <div class="mb-3 p-3 border rounded" id="modalStartedRow" style="display: none;">
                    <div class="row">
                        <div class="col-4"><strong><i class="fas fa-play-circle me-1"></i>تاريخ البدء:</strong></div>
                        <div class="col-8" id="modalStarted"></div>
                    </div>
                </div>
                
                <div class="mb-3 p-3 border rounded" id="modalCompletedRow" style="display: none;">
                    <div class="row">
                        <div class="col-4"><strong><i class="fas fa-check-circle me-1"></i>تاريخ الإتمام:</strong></div>
                        <div class="col-8" id="modalCompleted"></div>
                    </div>
                </div>
                
                <div class="mb-3 p-3 bg-info bg-opacity-10 rounded" id="modalNotesRow" style="display: none;">
                    <div class="row">
                        <div class="col-4"><strong><i class="fas fa-sticky-note me-1"></i>الملاحظات:</strong></div>
                        <div class="col-8" id="modalNotes"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.legend-box {
    display: inline-block;
    border: 1px solid #dee2e6;
}

.page-header-icon {
    display: inline-block;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    border-radius: 8px;
    text-align: center;
    line-height: 40px;
    color: white;
    margin-right: 15px;
}

.card {
    transition: all 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.pages-grid .card, .surahs-grid .card, .juz-grid .card {
    transition: transform 0.1s ease;
}

.pages-grid .card:hover, .surahs-grid .card:hover, .juz-grid .card:hover {
    transform: scale(1.05);
}

.nav-tabs .nav-link {
    color: #495057;
    border: 1px solid transparent;
    border-bottom-color: #dee2e6;
}

.nav-tabs .nav-link.active {
    color: #007bff;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.page-title {
    display: flex;
    align-items: center;
    font-size: 1.75rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

@media (max-width: 768px) {
    .pages-grid {
        max-height: 400px;
    }
    
    .surahs-grid {
        max-height: 400px;
    }
    
    .juz-grid {
        max-height: 400px;
    }
    
    .page-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
}

.cursor-pointer {
    cursor: pointer;
}

.legend-box {
    width: 16px;
    height: 16px;
    border-radius: 3px;
    display: inline-block;
}

.content-card {
    position: relative;
    user-select: none;
}

.content-card.notes-mode {
    border: 2px dashed #007bff !important;
}

.content-card.has-notes::after {
    content: "📝";
    position: absolute;
    top: 2px;
    right: 2px;
    font-size: 10px;
    z-index: 1;
}

.status-cycling {
    transition: all 0.2s ease;
}

.btn-notes-active {
    background-color: #007bff;
    color: white;
}

#memorizationTabs .nav-link {
    color: #495057 !important;
    background-color: white !important;
    border: 1px solid #dee2e6 !important;
    border-bottom: 1px solid #dee2e6 !important;
}

#memorizationTabs .nav-link:hover {
    color: #007bff !important;
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
}

#memorizationTabs .nav-link.active {
    background-color: #007bff !important;
    color: white !important;
    border-color: #007bff !important;
    border-bottom-color: #007bff !important;
}

#memorizationTabs .nav-link.active:hover {
    color: white !important;
    background-color: #007bff !important;
}

/* Additional specific overrides for text visibility - Override navbar styles */
#memorizationTabs .nav-link,
#memorizationTabs .nav-link i,
#memorizationTabs .nav-link span {
    color: #495057 !important;
    opacity: 1 !important;
}

#memorizationTabs .nav-link:hover,
#memorizationTabs .nav-link:hover i,
#memorizationTabs .nav-link:hover span {
    color: #007bff !important;
    opacity: 1 !important;
}

#memorizationTabs .nav-link.active,
#memorizationTabs .nav-link.active i,
#memorizationTabs .nav-link.active span,
#memorizationTabs .nav-link.active:hover,
#memorizationTabs .nav-link.active:hover i,
#memorizationTabs .nav-link.active:hover span {
    color: white !important;
    opacity: 1 !important;
}

/* Very specific override for the navbar style inheritance */
.tab-content #memorizationTabs .nav-link {
    color: #495057 !important;
    background-color: white !important;
}

.tab-content #memorizationTabs .nav-link.active {
    color: white !important;
    background-color: #007bff !important;
}
</style>

<script>
function showProgressInfo(type, number, name) {
    // Fetch progress information via AJAX
    fetch(`{{ url('/student/memorization') }}/${type}/${number}`)
        .then(response => response.json())
        .then(data => {
            // Update modal content
            document.getElementById('modalContentName').textContent = name;
            
            // Update status
            let statusText = '';
            let statusClass = '';
            switch(data.status) {
                case 'memorized':
                    statusText = 'محفوظ';
                    statusClass = 'badge bg-success';
                    break;
                case 'previously_memorized':
                    statusText = 'محفوظ سابقا';
                    statusClass = 'badge bg-info';
                    break;
                case 'reviewed':
                    statusText = 'تمت المراجعة';
                    statusClass = 'badge bg-primary';
                    break;
                case 'in_progress':
                    statusText = 'قيد الحفظ';
                    statusClass = 'badge bg-warning text-dark';
                    break;
                default:
                    statusText = 'لم يبدأ';
                    statusClass = 'badge bg-light text-dark';
                    break;
            }
            document.getElementById('modalStatus').innerHTML = `<span class="${statusClass}"><i class="fas fa-circle me-1"></i>${statusText}</span>`;
            

            
            // Show/hide and update started date
            if (data.started_at) {
                document.getElementById('modalStarted').textContent = data.started_at;
                document.getElementById('modalStartedRow').style.display = 'flex';
            } else {
                document.getElementById('modalStartedRow').style.display = 'none';
            }
            
            // Show/hide and update completed date
            if (data.completed_at) {
                document.getElementById('modalCompleted').textContent = data.completed_at;
                document.getElementById('modalCompletedRow').style.display = 'flex';
            } else {
                document.getElementById('modalCompletedRow').style.display = 'none';
            }
            
            // Show/hide and update notes
            if (data.notes && data.notes.trim() !== '') {
                document.getElementById('modalNotes').textContent = data.notes;
                document.getElementById('modalNotesRow').style.display = 'flex';
            } else {
                document.getElementById('modalNotesRow').style.display = 'none';
            }
            
            // Show the modal
            new bootstrap.Modal(document.getElementById('progressInfoModal')).show();
        })
        .catch(error => {
            console.error('Error fetching progress:', error);
            alert('حدث خطأ في تحميل المعلومات');
        });
}
</script>
@endsection 