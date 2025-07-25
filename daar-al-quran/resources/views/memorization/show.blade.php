@extends('layouts.teacher')

@section('title', 'متابعة حفظ القرآن - ' . $student->name)

@section('teacher-content')
            <!-- Page Header -->
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title">
                    <!-- <div class="page-header-icon"><i class="fas fa-book-quran"></i></div> -->
                    تتبع حفظ القرآن - {{ $student->name }}
                </h1>
                <div>
                    <a href="{{ route('teacher.students.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للطلاب
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
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="alert alert-info py-2 px-3 mb-0">
                                            <small><i class="fas fa-mouse-pointer me-1"></i>انقر لتغيير الحالة • كليك يمين للملاحظات</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="toggleNotesMode()">
                                            <i class="fas fa-sticky-note"></i> وضع الملاحظات
                                        </button>
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
                                
                                <!-- Memorized Pages Filter -->
                                <div class="mb-3 d-flex align-items-center gap-3">
                                    <label for="memorizedDaysInput" class="form-label mb-0">عدد الصفحات المحفوظة في آخر</label>
                                    <input type="number" id="memorizedDaysInput" class="form-control form-control-sm" value="7" min="1" style="width: 70px;">
                                    <span>يوم</span>
                                    <span class="badge bg-success" id="memorizedCountDisplay">...</span>
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
                                                     data-type="page" 
                                                     data-number="{{ $page }}" 
                                                     data-name="صفحة {{ $page }}"
                                                     data-current-status="{{ $status }}"
                                                     @if($hasNotes) title="{{ $progress->notes }}" @endif
                                                     onclick="handleCardClick(this, 'page', {{ $page }}, 'صفحة {{ $page }}')"
                                                     oncontextmenu="openNotesModal(event, 'page', {{ $page }}, 'صفحة {{ $page }}'); return false;">
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
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="alert alert-info py-2 px-3 mb-0">
                                            <small><i class="fas fa-mouse-pointer me-1"></i>انقر لتغيير الحالة • كليك يمين للملاحظات</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="toggleNotesMode()">
                                            <i class="fas fa-sticky-note"></i> وضع الملاحظات
                                        </button>
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
                                
                                <!-- Memorized Pages Filter -->
                                <div class="mb-3 d-flex align-items-center gap-3">
                                    <label for="memorizedSurahsDaysInput" class="form-label mb-0">عدد الصفحات المحفوظة في آخر</label>
                                    <input type="number" id="memorizedSurahsDaysInput" class="form-control form-control-sm" value="7" min="1" style="width: 70px;">
                                    <span>يوم</span>
                                    <span class="badge bg-success" id="memorizedSurahsCountDisplay">...</span>
                                </div>
                                
                                <!-- Surahs Grid -->
                                <div class="surahs-grid" style="max-height: 500px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px;">
                                    <div class="row g-3">
                                    @foreach(\App\Models\MemorizationProgress::getRemainingSurahs() as $surahNumber => $surahName)
                                        @php
                                            $surahId = "surah_{$surahNumber}";
                                            $progress = $progressLookup[$surahId] ?? null;
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
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="card {{ $statusClass }} h-100 position-relative" 
                                                 style="cursor: pointer;"
                                                 data-type="surah" 
                                                 data-number="{{ $surahNumber }}" 
                                                 data-name="{{ $surahName }}"
                                                 data-current-status="{{ $status }}"
                                                 @if($hasNotes) title="{{ $progress->notes }}" @endif
                                                 onclick="handleCardClick(this, 'surah', {{ $surahNumber }}, '{{ $surahName }}')"
                                                 oncontextmenu="openNotesModal(event, 'surah', {{ $surahNumber }}, '{{ $surahName }}'); return false;">
                                                <div class="card-body p-3 text-center">
                                                    <h6 class="card-title mb-1">{{ $surahName }}</h6>
                                                    <small class="opacity-75">سورة {{ $surahNumber }}</small>
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

                        <!-- Juz Tab -->
                        <div class="tab-pane fade" id="juz" role="tabpanel">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">الأجزاء (1-30) - 30 جزء</h5>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="alert alert-info py-2 px-3 mb-0">
                                            <small><i class="fas fa-mouse-pointer me-1"></i>انقر لتغيير الحالة • كليك يمين للملاحظات</small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary" onclick="toggleNotesMode()">
                                            <i class="fas fa-sticky-note"></i> وضع الملاحظات
                                        </button>
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
                                                 data-type="juz" 
                                                 data-number="{{ $juzNumber }}" 
                                                 data-name="{{ $juzName }}"
                                                 data-current-status="{{ $status }}"
                                                 @if($hasNotes) title="{{ $progress->notes }}" @endif
                                                 onclick="handleCardClick(this, 'juz', {{ $juzNumber }}, '{{ $juzName }}')"
                                                 oncontextmenu="openNotesModal(event, 'juz', {{ $juzNumber }}, '{{ $juzName }}'); return false;">
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

<!-- Notes Modal for Teachers -->
<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalLabel">إضافة ملاحظات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="notesForm">
                    @csrf
                    <input type="hidden" id="notesContentType" name="type">
                    <input type="hidden" id="notesPageNumber" name="page_number">
                    <input type="hidden" id="notesSurahNumber" name="surah_number">
                    <input type="hidden" id="notesJuzNumber" name="juz_number">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" id="notesContentLabel"></label>
                    </div>

                    <!-- Progress Status Section -->
                    <div class="mb-3 p-3 border rounded bg-light" id="progressInfoSection" style="display: none;">
                        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>معلومات الحفظ</h6>
                        
                        <div class="row mb-2" id="statusRow" style="display: none;">
                            <div class="col-4"><strong>الحالة:</strong></div>
                            <div class="col-8" id="currentStatus"></div>
                        </div>
                        
                        <div class="row mb-2" id="startedRow" style="display: none;">
                            <div class="col-4"><strong><i class="fas fa-play-circle me-1"></i>تاريخ البدء:</strong></div>
                            <div class="col-8" id="startedDate"></div>
                        </div>
                        
                        <div class="row mb-2" id="completedRow" style="display: none;">
                            <div class="col-4"><strong><i class="fas fa-check-circle me-1"></i>تاريخ الإتمام:</strong></div>
                            <div class="col-8" id="completedDate"></div>
                        </div>
                        
                        <div class="row mb-0" id="teacherRow" style="display: none;">
                            <div class="col-4"><strong><i class="fas fa-user-tie me-1"></i>المعلم:</strong></div>
                            <div class="col-8" id="teacherName"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notesText" class="form-label">الملاحظات</label>
                        <textarea class="form-control" id="notesText" name="notes" rows="4" placeholder="اكتب ملاحظاتك هنا..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i>
                            يمكنك أيضاً النقر بالزر الأيمن على أي صفحة أو سورة لإضافة ملاحظات مباشرة
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="saveNotes()">حفظ الملاحظات</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Modal (kept for backward compatibility) -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">تحديث حالة الحفظ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateForm">
                    @csrf
                    <input type="hidden" id="contentType" name="type">
                    <input type="hidden" id="pageNumber" name="page_number">
                    <input type="hidden" id="surahNumber" name="surah_number">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" id="contentLabel"></label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">حالة الحفظ</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="not_started">لم يبدأ</option>
                            <option value="in_progress">قيد الحفظ</option>
                            <option value="memorized">محفوظ</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="ملاحظات إضافية..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="updateProgress()">حفظ التغييرات</button>
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
let currentStudent = {{ $student->id }};
let notesMode = false;
let pendingUpdates = [];
let notesCache = {};
const LS_KEY = `memorization_pending_${currentStudent}`;

// On page load, check for unsent updates in localStorage and send them
window.addEventListener('DOMContentLoaded', function() {
    const unsent = localStorage.getItem(LS_KEY);
    const savingIndicator = document.getElementById('savingIndicator');
    if (unsent) {
        if (savingIndicator) savingIndicator.style.display = 'block';
        try {
            const changes = JSON.parse(unsent);
            if (Array.isArray(changes) && changes.length > 0) {
                const url = `/teacher/students/${currentStudent}/memorization/batch`;
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('changes', JSON.stringify(changes));
                fetch(url, { method: 'POST', body: formData, credentials: 'include' })
                    .then(() => {
                        // Add delay before hiding indicator to give database time to update
                        setTimeout(() => {
                            localStorage.removeItem(LS_KEY);
                            if (savingIndicator) savingIndicator.style.display = 'none';
                        }, 1000); // 1 second delay
                    });
            } else {
                if (savingIndicator) savingIndicator.style.display = 'none';
            }
        } catch (e) {
            localStorage.removeItem(LS_KEY);
            if (savingIndicator) savingIndicator.style.display = 'none';
        }
    } else {
        if (savingIndicator) savingIndicator.style.display = 'none';
    }
});

// Status cycle for pages and surahs: not_started -> in_progress -> memorized -> previously_memorized -> not_started
// Status cycle for juz: not_started -> in_progress -> memorized -> not_started
const statusCycleDefault = ['not_started', 'in_progress', 'memorized', 'previously_memorized'];
const statusCycleJuz = ['not_started', 'in_progress', 'memorized'];
const statusClasses = {
    'not_started': 'bg-light border',
    'in_progress': 'bg-warning text-dark',
    'memorized': 'bg-success text-white',
    'previously_memorized': 'bg-info text-white'
};

function handleCardClick(cardElement, type, number, name) {
    if (notesMode) {
        openNotesModal(null, type, number, name);
        return;
    }
    const currentStatus = cardElement.dataset.currentStatus || 'not_started';
    const statusCycle = type === 'juz' ? statusCycleJuz : statusCycleDefault;
    const currentIndex = statusCycle.indexOf(currentStatus);
    const nextIndex = (currentIndex + 1) % statusCycle.length;
    const nextStatus = statusCycle[nextIndex];
    cardElement.classList.add('status-cycling');
    // Update UI immediately
    updateCardAppearance(cardElement, nextStatus);
    cardElement.dataset.currentStatus = nextStatus;
    setTimeout(() => cardElement.classList.remove('status-cycling'), 200);
    showBriefSuccess(cardElement);
    // Store change locally
    let update = { type, status: nextStatus };
    if (type === 'page') update.page_number = number;
    else if (type === 'surah') update.surah_number = number;
    else update.juz_number = number;
    // Attach notes if cached
    if (notesCache[type + '_' + number]) {
        update.notes = notesCache[type + '_' + number];
    }
    // Remove any previous update for this item
    pendingUpdates = pendingUpdates.filter(u => {
        if (u.type !== type) return true;
        if (type === 'page' && u.page_number == number) return false;
        if (type === 'surah' && u.surah_number == number) return false;
        if (type === 'juz' && u.juz_number == number) return false;
        return true;
    });
    pendingUpdates.push(update);
    // Save to localStorage on every change
    localStorage.setItem(LS_KEY, JSON.stringify(pendingUpdates));
}

window.addEventListener('beforeunload', function(e) {
    if (pendingUpdates.length === 0) return;
    localStorage.setItem(LS_KEY, JSON.stringify(pendingUpdates));
    navigator.sendBeacon = navigator.sendBeacon || function(url, data) {
        fetch(url, { method: 'POST', body: data, credentials: 'include' });
    };
    const url = `/teacher/students/${currentStudent}/memorization/batch`;
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    formData.append('changes', JSON.stringify(pendingUpdates));
    navigator.sendBeacon(url, formData);
});

function updateStatusDirectly(type, number, status, cardElement) {
    // First, get existing notes before updating
    fetch(`/teacher/students/${currentStudent}/memorization/${type}/${number}`)
        .then(response => response.json())
        .then(existingData => {
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            formData.append('type', type);
            
            if (type === 'page') {
                formData.append('page_number', number);
                formData.append('surah_number', '');
                formData.append('juz_number', '');
            } else if (type === 'surah') {
                formData.append('surah_number', number);
                formData.append('page_number', '');
                formData.append('juz_number', '');
            } else { // juz
                formData.append('juz_number', number);
                formData.append('page_number', '');
                formData.append('surah_number', '');
            }
            
            formData.append('status', status);
            formData.append('notes', existingData.notes || ''); // Preserve existing notes
            
            fetch(`/teacher/students/${currentStudent}/memorization`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update card appearance
                    updateCardAppearance(cardElement, status);
                    cardElement.dataset.currentStatus = status;
                    
                    // Preserve notes indicator if notes exist
                    if (existingData.notes && existingData.notes.trim()) {
                        cardElement.classList.add('has-notes');
                    }
                    
                    // Remove animation class
                    setTimeout(() => {
                        cardElement.classList.remove('status-cycling');
                    }, 200);
                    
                    // Show brief success indicator
                    showBriefSuccess(cardElement);
                } else {
                    console.error('Failed to update status:', data.message);
                    cardElement.classList.remove('status-cycling');
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                cardElement.classList.remove('status-cycling');
            });
        })
        .catch(error => {
            console.error('Error fetching existing notes:', error);
            cardElement.classList.remove('status-cycling');
        });
}

function updateCardAppearance(cardElement, status) {
    const type = cardElement.dataset.type;
    
    // Remove all status classes and inline styles
    cardElement.classList.remove('bg-success', 'bg-warning', 'bg-light', 'text-white', 'text-dark', 'border');
    cardElement.removeAttribute('style');
    
    // Add new status classes based on type
    if (type === 'juz') {
        // Pink color scheme for juz
        switch(status) {
            case 'memorized':
                cardElement.style.backgroundColor = '#e91e63';
                cardElement.classList.add('text-white');
                break;
            case 'in_progress':
                cardElement.style.backgroundColor = '#ffc0cb';
                cardElement.style.border = '1px solid #e91e63';
                cardElement.classList.add('text-dark');
                break;
            default:
                cardElement.classList.add('bg-light', 'border');
                break;
        }
    } else {
        // Default colors for pages and surahs
    const newClasses = statusClasses[status].split(' ');
    newClasses.forEach(cls => cardElement.classList.add(cls));
    }
    
    // Update icon
    const iconContainer = cardElement.querySelector('.mt-1, .mt-2');
    if (iconContainer) {
        updateIconDisplay(iconContainer, status, type);
    }
}

function updateIconDisplay(iconContainer, status, type) {
    if (status === 'not_started') {
        if (type === 'surah') {
            iconContainer.innerHTML = '<div class="text-muted small">لم يبدأ</div>';
            iconContainer.style.display = 'block';
        } else {
            iconContainer.style.display = 'none';
        }
    } else {
        iconContainer.style.display = 'block';
        let textHTML = '';
        
        if (type === 'surah') {
            switch(status) {
                case 'memorized':
                    textHTML = '<div class="small">محفوظة</div>';
                    break;
                case 'in_progress':
                    textHTML = '<div class="small">قيد الحفظ</div>';
                    break;
            }
        }
        
        iconContainer.innerHTML = textHTML;
    }
}

function showBriefSuccess(cardElement) {
    const originalTransform = cardElement.style.transform;
    cardElement.style.transform = 'scale(1.1)';
    setTimeout(() => {
        cardElement.style.transform = originalTransform;
    }, 150);
}

function toggleNotesMode() {
    notesMode = !notesMode;
    const button = event.target.closest('button');
    
    if (notesMode) {
        button.classList.add('btn-notes-active');
        button.innerHTML = '<i class="fa fa-sticky-note"></i> إلغاء وضع الملاحظات';
        document.querySelectorAll('.content-card').forEach(card => {
            card.classList.add('notes-mode');
        });
    } else {
        button.classList.remove('btn-notes-active');
        button.innerHTML = '<i class="fa fa-sticky-note"></i> وضع الملاحظات';
        document.querySelectorAll('.content-card').forEach(card => {
            card.classList.remove('notes-mode');
        });
    }
}

function openNotesModal(event, type, number, name) {
    if (event) event.preventDefault();
    
    document.getElementById('notesContentType').value = type;
    document.getElementById('notesContentLabel').textContent = name;
    
    if (type === 'page') {
        document.getElementById('notesPageNumber').value = number;
        document.getElementById('notesSurahNumber').value = '';
        document.getElementById('notesJuzNumber').value = '';
    } else if (type === 'surah') {
        document.getElementById('notesSurahNumber').value = number;
        document.getElementById('notesPageNumber').value = '';
        document.getElementById('notesJuzNumber').value = '';
    } else { // juz
        document.getElementById('notesJuzNumber').value = number;
        document.getElementById('notesPageNumber').value = '';
        document.getElementById('notesSurahNumber').value = '';
    }
    
    // Load existing progress information and notes
    fetch(`/teacher/students/${currentStudent}/memorization/${type}/${number}`)
        .then(response => response.json())
        .then(data => {
            // Update notes field
            document.getElementById('notesText').value = data.notes || '';
            notesCache[type + '_' + number] = data.notes; // Cache notes
            
            // Update progress information
            populateProgressInfo(data);
        })
        .catch(error => {
            console.error('Error loading progress:', error);
        });
    
    new bootstrap.Modal(document.getElementById('notesModal')).show();
}

function populateProgressInfo(data) {
    const progressSection = document.getElementById('progressInfoSection');
    
    // Show progress section if we have any meaningful data
    const hasProgressData = data.status !== 'not_started' || data.started_at || data.completed_at || data.teacher;
    
    if (hasProgressData) {
        progressSection.style.display = 'block';
        
        // Update status
        if (data.status && data.status !== 'not_started') {
            let statusText = '';
            let statusClass = '';
            switch(data.status) {
                case 'memorized':
                    statusText = 'محفوظ';
                    statusClass = 'badge bg-success';
                    break;
                case 'previously_memorized':
                    statusText = 'محفوظ سابقاً';
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
            document.getElementById('currentStatus').innerHTML = `<span class="${statusClass}"><i class="fas fa-circle me-1"></i>${statusText}</span>`;
            document.getElementById('statusRow').style.display = 'flex';
        } else {
            document.getElementById('statusRow').style.display = 'none';
        }
        
        // Update started date
        if (data.started_at) {
            document.getElementById('startedDate').textContent = data.started_at;
            document.getElementById('startedRow').style.display = 'flex';
        } else {
            document.getElementById('startedRow').style.display = 'none';
        }
        
        // Update completed date
        if (data.completed_at) {
            document.getElementById('completedDate').textContent = data.completed_at;
            document.getElementById('completedRow').style.display = 'flex';
        } else {
            document.getElementById('completedRow').style.display = 'none';
        }
        
        // Update teacher name
        if (data.teacher) {
            document.getElementById('teacherName').textContent = data.teacher;
            document.getElementById('teacherRow').style.display = 'flex';
        } else {
            document.getElementById('teacherRow').style.display = 'none';
        }
    } else {
        progressSection.style.display = 'none';
    }
}

function saveNotes() {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const type = document.getElementById('notesContentType').value;
    const pageNumber = document.getElementById('notesPageNumber').value;
    const surahNumber = document.getElementById('notesSurahNumber').value;
    const juzNumber = document.getElementById('notesJuzNumber').value;
    const notes = document.getElementById('notesText').value;
    
    formData.append('type', type);
    formData.append('page_number', pageNumber);
    formData.append('surah_number', surahNumber);
    formData.append('juz_number', juzNumber);
    formData.append('notes', notes);
    
    // Keep current status - we only want to update notes
    const number = pageNumber || surahNumber || juzNumber;
    const card = document.querySelector(`[data-type="${type}"][data-number="${number}"]`);
    const currentStatus = card ? card.dataset.currentStatus || 'not_started' : 'not_started';
    formData.append('status', currentStatus);
    
    fetch(`/teacher/students/${currentStudent}/memorization`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('notesModal')).hide();
            
            // Add notes indicator to card
            if (card && notes.trim()) {
                card.classList.add('has-notes');
            } else if (card) {
                card.classList.remove('has-notes');
            }
            
            showSuccessMessage('تم حفظ الملاحظات بنجاح!');
        } else {
            alert('خطأ في حفظ الملاحظات: ' + (data.message || 'يرجى المحاولة مرة أخرى.'));
        }
    })
    .catch(error => {
        console.error('Error saving notes:', error);
        alert('خطأ في حفظ الملاحظات. يرجى المحاولة مرة أخرى.');
    });
}

// Keep the old modal function for backward compatibility
function openUpdateModal(type, number, name) {
    // This is kept for any remaining references
    handleCardClick(event.target.closest('.content-card'), type, number, name);
}

function updateProgress() {
    const form = document.getElementById('updateForm');
    const formData = new FormData();
    
    // Add CSRF token
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    // Add form fields
    const type = document.getElementById('contentType').value;
    const pageNumber = document.getElementById('pageNumber').value;
    const surahNumber = document.getElementById('surahNumber').value;
    const status = document.getElementById('status').value;
    const notes = document.getElementById('notes').value;
    
    formData.append('type', type);
    formData.append('page_number', pageNumber);
    formData.append('surah_number', surahNumber);
    formData.append('status', status);
    formData.append('notes', notes);
    
    // Debug logging
    console.log('Submitting data:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    fetch(`/teacher/students/${currentStudent}/memorization`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('updateModal')).hide();
            
            // Update UI dynamically without page refresh
            updateCardUI(type, pageNumber || surahNumber, status);
            
            // Show success message
            showSuccessMessage('تم تحديث حالة الحفظ بنجاح!');
        } else {
            alert('خطأ في التحديث: ' + (data.message || 'يرجى المحاولة مرة أخرى.'));
        }
    })
    .catch(error => {
        console.error('Error updating progress:', error);
        alert('خطأ في التحديث. يرجى المحاولة مرة أخرى.');
    });
}

function updateCardUI(type, number, status) {
    // Find the card element
    const card = document.querySelector(`[data-type="${type}"][data-number="${number}"]`);
    if (!card) return;
    
    // Remove all existing status classes
    card.classList.remove('bg-success', 'bg-warning', 'bg-info', 'bg-light', 'text-white', 'text-dark', 'border');
    
    // Apply new status classes
    switch(status) {
        case 'memorized':
            card.classList.add('bg-success', 'text-white');
            break;
        case 'in_progress':
            card.classList.add('bg-warning', 'text-dark');
            break;
        default:
            card.classList.add('bg-light', 'border');
            break;
    }
    
    // Update the icon inside the card
    const iconContainer = card.querySelector('.mt-1, .mt-2');
    if (iconContainer) {
        if (status === 'not_started') {
            iconContainer.style.display = 'none';
        } else {
            iconContainer.style.display = 'block';
            let iconHTML = '';
            let textHTML = '';
            
            switch(status) {
                case 'memorized':
                    iconHTML = '<i class="fa fa-check-circle' + (type === 'page' ? ' fa-sm' : '') + '"></i>';
                    if (type === 'surah') {
                        textHTML = '<div class="small">محفوظة</div>';
                    }
                    break;
                case 'in_progress':
                    iconHTML = '<i class="fa fa-clock' + (type === 'page' ? ' fa-sm' : '') + '"></i>';
                    if (type === 'surah') {
                        textHTML = '<div class="small">قيد الحفظ</div>';
                    }
                    break;
            }
            
            iconContainer.innerHTML = iconHTML + textHTML;
        }
    }
    
    // If it's a surah card and status is not_started, show "لم يبدأ"
    if (type === 'surah' && status === 'not_started') {
        const statusText = card.querySelector('.text-muted.small');
        if (statusText) {
            statusText.textContent = 'لم يبدأ';
        }
    }
}

function showSuccessMessage(message) {
    // Create and show a temporary success message
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed';
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

// Initialize Bootstrap tabs
document.addEventListener('DOMContentLoaded', function() {
    var triggerTabList = [].slice.call(document.querySelectorAll('#memorizationTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
            updateTabStyles()
        })
    })
    
    // Apply initial styles
    updateTabStyles()
})

function updateTabStyles() {
    const tabs = document.querySelectorAll('#memorizationTabs .nav-link')
    
    tabs.forEach(function(tab) {
        if (tab.classList.contains('active')) {
            // Active tab styles
            tab.style.cssText = 'color: white !important; background-color: #007bff !important; border-color: #007bff !important;'
            const icon = tab.querySelector('i')
            if (icon) {
                icon.style.color = 'white !important'
            }
        } else {
            // Inactive tab styles
            tab.style.cssText = 'color: #495057 !important; background-color: white !important; border: 1px solid #dee2e6 !important;'
            const icon = tab.querySelector('i')
            if (icon) {
                icon.style.color = '#495057 !important'
            }
        }
        
        // Add hover events
        tab.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active')) {
                this.style.cssText = 'color: #007bff !important; background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important;'
                const icon = this.querySelector('i')
                if (icon) {
                    icon.style.color = '#007bff !important'
                }
            }
        })
        
        tab.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.cssText = 'color: #495057 !important; background-color: white !important; border: 1px solid #dee2e6 !important;'
                const icon = this.querySelector('i')
                if (icon) {
                    icon.style.color = '#495057 !important'
                }
            }
        })
    })
}

// --- Memorized Pages Filter ---
const memorizedDaysInput = document.getElementById('memorizedDaysInput');
const memorizedCountDisplay = document.getElementById('memorizedCountDisplay');

function fetchMemorizedCount() {
    const days = parseInt(memorizedDaysInput.value) || 7;
    fetch(`/teacher/students/${currentStudent}/memorized-count?days=${days}`)
        .then(response => response.json())
        .then(data => {
            memorizedCountDisplay.textContent = data.count;
        })
        .catch(() => {
            memorizedCountDisplay.textContent = '...';
        });
}

memorizedDaysInput.addEventListener('change', fetchMemorizedCount);
document.addEventListener('DOMContentLoaded', fetchMemorizedCount);

// --- Memorized Surahs Filter ---
const memorizedSurahsDaysInput = document.getElementById('memorizedSurahsDaysInput');
const memorizedSurahsCountDisplay = document.getElementById('memorizedSurahsCountDisplay');

function fetchMemorizedSurahsCount() {
    const days = parseInt(memorizedSurahsDaysInput.value) || 7;
    fetch(`/teacher/students/${currentStudent}/memorized-count?days=${days}`)
        .then(response => response.json())
        .then(data => {
            // Show total count (same as pages tab)
            memorizedSurahsCountDisplay.textContent = data.count;
        })
        .catch(() => {
            memorizedSurahsCountDisplay.textContent = '...';
        });
}

memorizedSurahsDaysInput.addEventListener('change', fetchMemorizedSurahsCount);
document.addEventListener('DOMContentLoaded', fetchMemorizedSurahsCount);
</script>
@endsection 