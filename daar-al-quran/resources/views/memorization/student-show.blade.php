@extends('layouts.student')

@section('title', 'متابعة حفظ القرآن')

@section('student-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>تتبع حفظ القرآن - {{ $student->full_name }}</h4>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-right"></i> العودة للوحة التحكم
                    </a>
                </div>

                <div class="card-body">
                    <!-- Pages and Surahs Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">إحصائيات الصفحات (1-581)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <h5 class="text-info">{{ $statistics['pages']['total'] }}</h5>
                                            <small>إجمالي الصفحات</small>
                                        </div>
                                        <div class="col-4">
                                            <h5 class="text-success">{{ $statistics['pages']['memorized'] }}</h5>
                                            <small>محفوظة</small>
                                        </div>
                                        <div class="col-4">
                                            <h5 class="text-warning">{{ $statistics['pages']['in_progress'] }}</h5>
                                            <small>قيد الحفظ</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">إحصائيات السور (78-114)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <h5 class="text-success">{{ $statistics['surahs']['total'] }}</h5>
                                            <small>إجمالي السور</small>
                                        </div>
                                        <div class="col-4">
                                            <h5 class="text-success">{{ $statistics['surahs']['memorized'] }}</h5>
                                            <small>محفوظة</small>
                                        </div>
                                        <div class="col-4">
                                            <h5 class="text-warning">{{ $statistics['surahs']['in_progress'] }}</h5>
                                            <small>قيد الحفظ</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs" id="memorizationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pages-tab" data-bs-toggle="tab" data-bs-target="#pages" type="button" role="tab" style="color: #495057 !important; background-color: white !important; border: 1px solid #dee2e6 !important;">
                                <i class="fa fa-file-text" style="color: inherit !important;"></i> الصفحات (1-581)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="surahs-tab" data-bs-toggle="tab" data-bs-target="#surahs" type="button" role="tab" style="color: #495057 !important; background-color: white !important; border: 1px solid #dee2e6 !important;">
                                <i class="fa fa-book" style="color: inherit !important;"></i> السور الأخيرة (78-114)
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="memorizationTabContent">
                        <!-- Pages Tab -->
                        <div class="tab-pane fade show active" id="pages" role="tabpanel">
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>صفحات القرآن الكريم (1-581)</h5>
                                    <div class="alert alert-info mb-0" role="alert">
                                        <small><i class="fa fa-info-circle"></i> انقر على أي صفحة لعرض الملاحظات (إن وجدت)</small>
                                    </div>
                                </div>
                                
                                <!-- Status Legend -->
                                <div class="mb-3 p-2 bg-light rounded">
                                    <div class="d-flex justify-content-center gap-4">
                                        <span class="d-flex align-items-center gap-1">
                                            <div class="legend-box bg-light border"></div>
                                            <small>لم يبدأ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-1">
                                            <div class="legend-box bg-warning"></div>
                                            <small>قيد الحفظ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-1">
                                            <div class="legend-box bg-success"></div>
                                            <small>محفوظ</small>
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Pages Grid -->
                                <div class="pages-grid" style="max-height: 600px; overflow-y: auto;">
                                    <div class="row">
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
                                                    case 'in_progress':
                                                        $statusClass = 'bg-warning text-dark';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-light border';
                                                        break;
                                                }
                                                if ($hasNotes) {
                                                    $statusClass .= ' has-notes';
                                                }
                                            @endphp
                                            <div class="col-lg-1 col-md-2 col-sm-3 col-4 mb-2">
                                                <div class="card content-card {{ $statusClass }} h-100 cursor-pointer" 
                                                     data-type="page" 
                                                     data-number="{{ $page }}" 
                                                     data-name="صفحة {{ $page }}"
                                                     data-current-status="{{ $status }}"
                                                     onclick="showProgressInfo('page', {{ $page }}, 'صفحة {{ $page }}')">
                                                    <div class="card-body p-2 text-center">
                                                        <div class="small">{{ $page }}</div>
                                                        @if($hasNotes)
                                                            <div class="small">📝</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Surahs Tab -->
                        <div class="tab-pane fade" id="surahs" role="tabpanel">
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>السور الأخيرة (78-114)</h5>
                                    <div class="alert alert-info mb-0" role="alert">
                                        <small><i class="fa fa-info-circle"></i> انقر على أي سورة لعرض الملاحظات (إن وجدت)</small>
                                    </div>
                                </div>
                                
                                <!-- Status Legend -->
                                <div class="mb-3 p-2 bg-light rounded">
                                    <div class="d-flex justify-content-center gap-4">
                                        <span class="d-flex align-items-center gap-1">
                                            <div class="legend-box bg-light border"></div>
                                            <small>لم يبدأ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-1">
                                            <div class="legend-box bg-warning"></div>
                                            <small>قيد الحفظ</small>
                                        </span>
                                        <span class="d-flex align-items-center gap-1">
                                            <div class="legend-box bg-success"></div>
                                            <small>محفوظ</small>
                                        </span>
                                    </div>
                                </div>

                                <!-- Surahs Grid -->
                                <div class="surahs-grid" style="max-height: 600px; overflow-y: auto;">
                                    <div class="row">
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
                                                    case 'in_progress':
                                                        $statusClass = 'bg-warning text-dark';
                                                        break;
                                                    default:
                                                        $statusClass = 'bg-light border';
                                                        break;
                                                }
                                                if ($hasNotes) {
                                                    $statusClass .= ' has-notes';
                                                }
                                            @endphp
                                            <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                                <div class="card content-card {{ $statusClass }} h-100 cursor-pointer" 
                                                     data-type="surah" 
                                                     data-number="{{ $surah }}" 
                                                     data-name="{{ $surahName }}"
                                                     data-current-status="{{ $status }}"
                                                     onclick="showProgressInfo('surah', {{ $surah }}, '{{ $surahName }}')">
                                                    <div class="card-body p-2">
                                                        <div class="small fw-bold">{{ $surah }}. {{ $surahName }}</div>
                                                        @if($hasNotes)
                                                            <div class="text-center mt-1">📝</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endfor
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="progressInfoModalLabel">معلومات الحفظ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-4"><strong>العنصر:</strong></div>
                    <div class="col-8" id="modalContentName"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-4"><strong>الحالة:</strong></div>
                    <div class="col-8" id="modalStatus"></div>
                </div>

                <div class="row mb-3" id="modalStartedRow" style="display: none;">
                    <div class="col-4"><strong>تاريخ البدء:</strong></div>
                    <div class="col-8" id="modalStarted"></div>
                </div>
                <div class="row mb-3" id="modalCompletedRow" style="display: none;">
                    <div class="col-4"><strong>تاريخ الإتمام:</strong></div>
                    <div class="col-8" id="modalCompleted"></div>
                </div>
                <div class="row mb-3" id="modalNotesRow" style="display: none;">
                    <div class="col-4"><strong>الملاحظات:</strong></div>
                    <div class="col-8" id="modalNotes"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<style>
.legend-box {
    width: 15px;
    height: 15px;
    border-radius: 3px;
    display: inline-block;
}

.content-card {
    transition: all 0.2s ease;
    min-height: 50px;
}

.content-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.content-card.has-notes {
    position: relative;
}

.content-card.has-notes::after {
    content: '';
    position: absolute;
    top: 5px;
    right: 5px;
    width: 8px;
    height: 8px;
    background-color: #007bff;
    border-radius: 50%;
}

.cursor-pointer {
    cursor: pointer;
}

/* Tab styling improvements */
.nav-tabs .nav-link {
    color: #495057 !important;
    background-color: white !important;
    border: 1px solid #dee2e6 !important;
}

.nav-tabs .nav-link.active {
    background-color: #f8f9fa !important;
    border-bottom-color: transparent !important;
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
                case 'in_progress':
                    statusText = 'قيد الحفظ';
                    statusClass = 'badge bg-warning text-dark';
                    break;
                default:
                    statusText = 'لم يبدأ';
                    statusClass = 'badge bg-light text-dark';
                    break;
            }
            document.getElementById('modalStatus').innerHTML = `<span class="${statusClass}">${statusText}</span>`;
            

            
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