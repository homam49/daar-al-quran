@extends('layouts.app')

@section('title', 'متابعة حفظ القرآن - ' . $student->full_name)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>تتبع حفظ القرآن - {{ $student->name }}</h4>
                    <a href="{{ route('teacher.students.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-right"></i> العودة للطلاب
                    </a>
                </div>

                <div class="card-body">
                    <!-- Statistics Dashboard -->
                    <!-- <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="row">
                                Overall Statistics
                                    <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">الإجمالي</h5>
                                            <h3>{{ $statistics['total'] }}</h3>
                                            <small>صفحة وسورة</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">محفوظ</h5>
                                            <h3>{{ $statistics['memorized'] }}</h3>
                                            <small>{{ number_format($statistics['completion_percentage'], 1) }}%</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">قيد الحفظ</h5>
                                            <h3>{{ $statistics['in_progress'] }}</h3>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div> -->

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
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">انقر لتغيير الحالة: أبيض → أصفر → أخضر</small>
                                        <button class="btn btn-sm btn-outline-primary" onclick="toggleNotesMode()">
                                            <i class="fa fa-sticky-note"></i> وضع الملاحظات
                                        </button>
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
                                                     onclick="handleCardClick(this, 'page', {{ $page }}, 'صفحة {{ $page }}')"
                                                     oncontextmenu="openNotesModal(event, 'page', {{ $page }}, 'صفحة {{ $page }}'); return false;">
                                                    <div class="card-body p-2 text-center">
                                                        <div class="small">{{ $page }}</div>

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
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">انقر لتغيير الحالة: أبيض → أصفر → أخضر</small>
                                        <button class="btn btn-sm btn-outline-primary" onclick="toggleNotesMode()">
                                            <i class="fa fa-sticky-note"></i> وضع الملاحظات
                                        </button>
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
                                <div class="row">
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
                                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                            <div class="card content-card {{ $statusClass }} h-100 cursor-pointer" 
                                                 data-type="surah" 
                                                 data-number="{{ $surahNumber }}" 
                                                 data-name="{{ $surahName }}"
                                                 data-current-status="{{ $status }}"
                                                 onclick="handleCardClick(this, 'surah', {{ $surahNumber }}, '{{ $surahName }}')"
                                                 oncontextmenu="openNotesModal(event, 'surah', {{ $surahNumber }}, '{{ $surahName }}'); return false;">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title mb-2">{{ $surahName }}</h6>
                                                    <div class="text-muted small mb-2">سورة {{ $surahNumber }}</div>
                                                    @if($status !== 'not_started')
                                                        <div class="mt-2">
                                                            @switch($status)
                                                                @case('memorized')
                                                                    <div class="small">محفوظة</div>
                                                                    @break
                                                                @case('in_progress')
                                                                    <div class="small">قيد الحفظ</div>
                                                                    @break
                                                            @endswitch
                                                        </div>
                                                    @else
                                                        <div class="text-muted small">لم يبدأ</div>
                                                    @endif
                                                </div>
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

<!-- Notes Modal -->
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
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" id="notesContentLabel"></label>
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
.content-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.content-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.pages-grid {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 15px;
    background-color: #f8f9fa;
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

// Status cycle: not_started -> in_progress -> memorized -> not_started
const statusCycle = ['not_started', 'in_progress', 'memorized'];
const statusClasses = {
    'not_started': 'bg-light border',
    'in_progress': 'bg-warning text-dark',
    'memorized': 'bg-success text-white'
};

function handleCardClick(cardElement, type, number, name) {
    if (notesMode) {
        openNotesModal(null, type, number, name);
        return;
    }
    
    // Get current status
    const currentStatus = cardElement.dataset.currentStatus || 'not_started';
    
    // Get next status in cycle
    const currentIndex = statusCycle.indexOf(currentStatus);
    const nextIndex = (currentIndex + 1) % statusCycle.length;
    const nextStatus = statusCycle[nextIndex];
    
    // Add cycling animation
    cardElement.classList.add('status-cycling');
    
    // Update status via API
    updateStatusDirectly(type, number, nextStatus, cardElement);
}

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
            } else {
                formData.append('surah_number', number);
                formData.append('page_number', '');
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
    // Remove all status classes
    cardElement.classList.remove('bg-success', 'bg-warning', 'bg-light', 'text-white', 'text-dark', 'border');
    
    // Add new status classes
    const newClasses = statusClasses[status].split(' ');
    newClasses.forEach(cls => cardElement.classList.add(cls));
    
    // Update icon
    const iconContainer = cardElement.querySelector('.mt-1, .mt-2');
    if (iconContainer) {
        updateIconDisplay(iconContainer, status, cardElement.dataset.type);
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
    } else {
        document.getElementById('notesSurahNumber').value = number;
        document.getElementById('notesPageNumber').value = '';
    }
    
    // Load existing notes
    fetch(`/teacher/students/${currentStudent}/memorization/${type}/${number}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('notesText').value = data.notes || '';
        })
        .catch(error => {
            console.error('Error loading notes:', error);
        });
    
    new bootstrap.Modal(document.getElementById('notesModal')).show();
}

function saveNotes() {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const type = document.getElementById('notesContentType').value;
    const pageNumber = document.getElementById('notesPageNumber').value;
    const surahNumber = document.getElementById('notesSurahNumber').value;
    const notes = document.getElementById('notesText').value;
    
    formData.append('type', type);
    formData.append('page_number', pageNumber);
    formData.append('surah_number', surahNumber);
    formData.append('notes', notes);
    
    // Keep current status - we only want to update notes
    const number = pageNumber || surahNumber;
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
</script>
@endsection 