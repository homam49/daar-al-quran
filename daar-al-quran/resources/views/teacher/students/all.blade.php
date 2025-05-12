@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i>جميع الطلاب</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('classrooms.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-chalkboard me-1"></i> عرض الفصول
            </a>
        </div>
    </div>
</div>





<div class="card">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">قائمة الطلاب ({{ $students->count() }})</h5>
            </div>
            <div class="col-auto">
                <div class="d-flex">
                    <select class="form-select form-select-sm me-2" id="classroomFilter">
                        <option value="all">جميع الفصول</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" id="schoolFilter">
                        <option value="all">جميع المدارس</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($students->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="studentsTable">
                    <thead>
                        <tr>
                            <th>اسم الطالب</th>
                            <th>العمر</th>
                            <th>الفصول</th>
                            <th>المدرسة</th>
                            <th>معدل الحضور</th>
                            <th>تاريخ الإضافة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr 
                            data-school-id="{{ $student->school_id }}"
                            data-search-text="{{ $student->full_name }} {{ $student->age }} {{ $student->school->name ?? '' }} {{ $student->username ?? '' }}"
                        >
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->age }} سنة</td>
                            <td>
                                @if($student->classRooms->count() > 0)
                                    @foreach($student->classRooms as $classRoom)
                                        <span class="badge bg-primary me-1" data-classroom-id="{{ $classRoom->id }}">{{ $classRoom->name }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">لا يوجد فصول</span>
                                @endif
                            </td>
                            <td>{{ $student->school->name ?? 'غير محدد' }}</td>
                            <td>
                                @php
                                    $attendances = $student->attendances;
                                    $totalAttendances = $attendances->count();
                                    $presentCount = $attendances->where('status', 'present')->count();
                                    $lateCount = $attendances->where('status', 'late')->count();
                                    $attendanceRate = $totalAttendances > 0
                                        ? round((($presentCount + $lateCount) / $totalAttendances) * 100)
                                        : 0;
                                @endphp
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 5px;">
                                        <div class="progress-bar bg-{{ $attendanceRate >= 75 ? 'success' : ($attendanceRate >= 50 ? 'warning' : 'danger') }}" 
                                            role="progressbar" style="width: {{ $attendanceRate }}%" 
                                            aria-valuenow="{{ $attendanceRate }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span>{{ $attendanceRate }}%</span>
                                </div>
                            </td>
                            <td>{{ $student->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="noResultsMessage" class="alert alert-info mt-3" style="display: none;">لا توجد طلاب مطابقة للبحث</div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-user-graduate text-muted fa-3x mb-3"></i>
                <p class="mb-0">لا يوجد طلاب مضافين حتى الآن</p>
            </div>
        @endif
    </div>
</div>

<!-- Credentials Modal -->
<div class="modal fade" id="credentialsModal" tabindex="-1" aria-labelledby="credentialsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="credentialsModalLabel">بيانات دخول الطالب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                    <h5 id="studentName"></h5>
                </div>
                <div class="form-group mb-3">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="username" readonly>
                        <button class="btn btn-outline-secondary copy-btn" data-copy="username">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="password" readonly>
                        <button class="btn btn-outline-secondary copy-btn" data-copy="password">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get filter elements
    const classroomFilter = document.getElementById('classroomFilter');
    const schoolFilter = document.getElementById('schoolFilter');
    
    if (classroomFilter && schoolFilter) {
        // Define the filter function - simplified version
        function applyFilter() {
            const classroomValue = classroomFilter.value;
            const schoolValue = schoolFilter.value;
            
            // Get all rows
            const tableRows = document.querySelectorAll('#studentsTable tbody tr');
            let visibleRows = 0;
            
            // Filter each row manually
            tableRows.forEach(function(row) {
                let showRow = true;
                
                // Apply only the active filter
                if (classroomValue !== 'all') {
                    // Classroom filter is active
                    const classroomSpans = row.querySelectorAll('span[data-classroom-id]');
                    let hasClassroom = false;
                    
                    classroomSpans.forEach(function(span) {
                        if (span.getAttribute('data-classroom-id') === classroomValue) {
                            hasClassroom = true;
                        }
                    });
                    
                    if (!hasClassroom) {
                        showRow = false;
                    }
                } else if (schoolValue !== 'all') {
                    // School filter is active
                    const rowSchool = row.getAttribute('data-school-id');
                    if (rowSchool !== schoolValue) {
                        showRow = false;
                    }
                }
                
                // Show or hide row
                row.style.display = showRow ? '' : 'none';
                if (showRow) visibleRows++;
            });
            
            // Show/hide no results message
            const noResults = document.getElementById('noResultsMessage');
            if (noResults) {
                noResults.style.display = visibleRows === 0 ? 'block' : 'none';
            }
        }
        
        // Add event listeners
        classroomFilter.addEventListener('change', function() {
            if (this.value !== 'all') {
                // Reset school filter
                schoolFilter.value = 'all';
            }
            applyFilter();
        });
        
        schoolFilter.addEventListener('change', function() {
            if (this.value !== 'all') {
                // Reset classroom filter
                classroomFilter.value = 'all';
            }
            applyFilter();
        });
        
        // Run initial filter
        applyFilter();
    }
    
    // Credentials modal setup
    const viewButtons = document.querySelectorAll('.view-credentials');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const studentNameElement = document.getElementById('studentName');
    const copyButtons = document.querySelectorAll('.copy-btn');
    const credentialsModal = document.getElementById('credentialsModal');
    
    if (typeof bootstrap !== 'undefined' && credentialsModal) {
        const modal = new bootstrap.Modal(credentialsModal);
        
        viewButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const username = this.getAttribute('data-username');
                const studentName = this.getAttribute('data-student-name');
                
                usernameInput.value = username;
                passwordInput.value = username;
                studentNameElement.textContent = studentName;
                
                modal.show();
            });
        });
    }
    
    // Copy functionality
    copyButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const field = this.getAttribute('data-copy');
            const input = document.getElementById(field);
            
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand('copy');
            
            const icon = this.querySelector('i');
            icon.className = 'fas fa-check';
            
            setTimeout(function() {
                icon.className = 'fas fa-copy';
            }, 1500);
        });
    });
});
</script>
@endsection 