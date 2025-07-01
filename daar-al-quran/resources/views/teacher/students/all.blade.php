@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i>جميع الطلاب</h4>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                @if($students->count() > 0)
                <button type="button" class="btn btn-primary btn-sm" onclick="generateSelectedPdf()" disabled id="pdfSelectedBtn">
                    <i class="fas fa-file-pdf"></i> PDF للمختارين
                </button>
                @endif
                <a href="{{ route('classrooms.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-chalkboard me-1"></i> عرض الفصول
                </a>
            </div>
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
                            @if($students->count() > 0)
                            <th width="5%">
                                <input type="checkbox" id="selectAllStudents" onclick="toggleAllStudents()" title="تحديد الكل">
                            </th>
                            @endif
                            <th>اسم الطالب</th>
                            <th>العمر</th>
                            <th>الفصول</th>
                            <th>المدرسة</th>
                            <th>معدل الحضور</th>
                            <th>تاريخ الإضافة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr 
                            data-school-id="{{ $student->school_id }}"
                            data-search-text="{{ $student->name }} {{ $student->age }} {{ $student->school->name ?? '' }} {{ $student->username ?? '' }}"
                        >
                            <td>
                                <input type="checkbox" class="student-checkbox" value="{{ $student->id }}" onclick="updateSelectedCount()" title="اختيار الطالب">
                            </td>
                            <td>{{ $student->name }}</td>
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
                            <td class="text-center">
                                <a href="{{ route('teacher.memorization.show', $student->id) }}" class="btn btn-sm btn-success" title="متابعة حفظ القرآن">
                                    <i class="fas fa-book"></i> 📖
                                </a>
                            </td>
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
            
            // Reset select all checkbox and update count after filtering
            const selectAllCheckbox = document.getElementById('selectAllStudents');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            
            // Update the selected count display
            updateSelectedCount();
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
    
    // Initialize checkbox state on page load
    updateSelectedCount();
    
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

// Global functions for PDF generation and checkbox handling
function toggleAllStudents() {
    console.log('toggleAllStudents called');
    const selectAllCheckbox = document.getElementById('selectAllStudents');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox:not([style*="display: none"])');
    
    if (!selectAllCheckbox) {
        console.error('Select all checkbox not found');
        return;
    }
    
    console.log('Select all checkbox checked:', selectAllCheckbox.checked);
    console.log('Found', studentCheckboxes.length, 'visible student checkboxes');
    
    // Only toggle checkboxes for visible rows (not filtered out)
    studentCheckboxes.forEach(function(checkbox) {
        const row = checkbox.closest('tr');
        if (row && row.style.display !== 'none') {
        checkbox.checked = selectAllCheckbox.checked;
        }
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    console.log('updateSelectedCount called');
    const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
    const selectedCount = selectedCheckboxes.length;
    const pdfButton = document.getElementById('pdfSelectedBtn');
    
    console.log('Selected count:', selectedCount);
    console.log('PDF button found:', !!pdfButton);
    
    if (pdfButton) {
        if (selectedCount > 0) {
            pdfButton.disabled = false;
            pdfButton.classList.remove('btn-secondary');
            pdfButton.classList.add('btn-primary');
            pdfButton.textContent = `PDF للمختارين (${selectedCount})`;
        } else {
            pdfButton.disabled = true;
            pdfButton.classList.remove('btn-primary');
            pdfButton.classList.add('btn-secondary');
            pdfButton.innerHTML = '<i class="fas fa-file-pdf"></i> PDF للمختارين';
        }
    }
    
    // Update select all checkbox state
    const selectAllCheckbox = document.getElementById('selectAllStudents');
    const visibleCheckboxes = document.querySelectorAll('.student-checkbox');
    const visibleCheckedBoxes = document.querySelectorAll('.student-checkbox:checked');
    
    if (selectAllCheckbox && visibleCheckboxes.length > 0) {
        // Only consider visible checkboxes for the select all state
        const visibleCount = Array.from(visibleCheckboxes).filter(cb => {
            const row = cb.closest('tr');
            return row && row.style.display !== 'none';
        }).length;
        
        const visibleSelectedCount = Array.from(visibleCheckedBoxes).filter(cb => {
            const row = cb.closest('tr');
            return row && row.style.display !== 'none';
        }).length;
        
        selectAllCheckbox.checked = visibleSelectedCount === visibleCount && visibleCount > 0;
        selectAllCheckbox.indeterminate = visibleSelectedCount > 0 && visibleSelectedCount < visibleCount;
    }
}

function generateSelectedPdf() {
    console.log('generateSelectedPdf called');
    const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    console.log('Selected student IDs:', selectedIds);
    
    if (selectedIds.length === 0) {
        alert('يرجى اختيار طالب واحد على الأقل');
        return;
    }
    
    // Show loading state
    const pdfButton = document.getElementById('pdfSelectedBtn');
    const originalText = pdfButton.innerHTML;
    pdfButton.disabled = true;
    pdfButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري إنشاء الملف...';
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("teacher.students.credentials.pdf") }}';
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    // Add selected student IDs
    selectedIds.forEach(function(id) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    // Reset button state after a short delay
    setTimeout(function() {
        pdfButton.disabled = false;
        pdfButton.innerHTML = originalText;
    }, 3000);
}
</script>
@endsection 
