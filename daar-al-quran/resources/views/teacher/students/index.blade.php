@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-user-graduate"></i></div>
        قائمة الطلاب - {{ $classroom->name }}
    </h1>
    <div>
        <a href="{{ route('classroom.students.create', $classroom->id) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة طالب
        </a>
        <a href="{{ route('classrooms.show', $classroom->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للفصل
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>جميع الطلاب المسجلين في الفصل</h5>
        @if(count($students) > 0)
        <div class="btn-group">
            <button type="button" class="btn btn-light btn-sm" onclick="generateSelectedPdf()" disabled id="pdfSelectedBtn">
                <i class="fas fa-file-pdf"></i> PDF للمختارين
            </button>
            <form action="{{ route('classroom.students.credentials.pdf', $classroom->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="fas fa-file-pdf"></i> PDF لجميع الطلاب
                </button>
            </form>
        </div>
        @endif
    </div>
    <div class="card-body">
        @if(count($students) > 0)
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>ملاحظة:</strong> بيانات الدخول للطلاب هي نفسها لاسم المستخدم وكلمة المرور. يمكن للطلاب تغيير كلمة المرور بعد تسجيل الدخول الأول.
        </div>
        
        <!-- Search Bar -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="studentSearchInput" placeholder="ابحث عن طالب بالاسم، الهاتف، اسم المستخدم أو العنوان..." oninput="searchStudents()">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                        <i class="fas fa-times"></i> مسح
                    </button>
                </div>
                <small class="text-muted"><i class="fas fa-info-circle"></i> يتم البحث في جميع البيانات المتاحة للطلاب</small>
            </div>
            <div class="col-md-6 text-end">
                <span id="searchResults" class="text-muted"></span>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="studentsTable">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">
                            <input type="checkbox" id="selectAll" onclick="toggleAllStudents()" title="تحديد الكل">
                        </th>
                        <th width="25%">اسم الطالب</th>
                        <th>العمر</th>
                        <th>رقم الهاتف</th>
                        <th>بيانات الدخول</th>
                        <th>العنوان</th>
                        <th width="15%">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php
                        $searchText = trim(
                            ($student->first_name ?? '') . ' ' .
                            ($student->middle_name ?? '') . ' ' .
                            ($student->last_name ?? '') . ' ' .
                            ($student->email ?? '') . ' ' .
                            ($student->phone ?? '') . ' ' .
                            ($student->username ?? '') . ' ' .
                            ($student->address ?? '')
                        );
                    @endphp
                    <tr data-search-text="{{ $searchText }}">
                        <td>
                            <input type="checkbox" class="student-checkbox" value="{{ $student->id }}" onclick="updateSelectedCount()" title="اختيار الطالب">
                        </td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->age }} سنة</td>
                        <td>{{ $student->phone ?? 'غير متوفر' }}</td>
                        <td><strong>{{ $student->username ?? 'غير متوفر' }}</strong></td>
                        <td>{{ $student->address ?? 'غير متوفر' }}</td>
                        <td class="text-center">
                            <div class="btn-group-vertical" role="group">
                                <a href="{{ route('teacher.memorization.show', $student->id) }}" class="btn btn-sm btn-success mb-1" title="متابعة حفظ القرآن">
                                    <i class="fas fa-book"></i> 📖
                                </a>
                                <a href="{{ route('classroom.students.edit', [$classroom->id, $student->id]) }}" class="btn btn-sm btn-primary mb-1" title="تعديل بيانات الطالب">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-info mb-1 view-credentials" data-student-id="{{ $student->id }}" data-student-name="{{ $student->name }}" title="عرض بيانات الدخول">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-secondary mb-1 send-note-btn" data-student-id="{{ $student->id }}" data-student-name="{{ $student->name }}" title="إرسال رسالة">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                                <form action="{{ route('classroom.students.remove', [$classroom->id, $student->id]) }}" method="POST" class="d-inline delete-form" onsubmit="return confirm('هل أنت متأكد من إزالة هذا الطالب من الفصل؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mb-1" title="إزالة من الفصل">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="noSearchResults" class="alert alert-warning mt-3" style="display: none;">
                <i class="fas fa-search"></i> لا توجد طلاب مطابقة لبحثك. جرب مصطلحات بحث مختلفة.
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-user-graduate text-muted fa-4x"></i>
            </div>
            <p class="lead">لا يوجد طلاب مسجلين في هذا الفصل</p>
            <a href="{{ route('classroom.students.create', $classroom->id) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة طالب جديد
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Credentials Modal for each student -->
@foreach($students as $student)
<div class="modal fade" id="credentialsModal{{ $student->id }}" tabindex="-1" aria-labelledby="credentialsModalLabel{{ $student->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="credentialsModalLabel{{ $student->id }}">بيانات تسجيل الدخول - {{ $student->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeCredentialsModal('{{ $student->id }}')"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="username{{ $student->id }}" class="form-label">اسم المستخدم</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="username{{ $student->id }}" value="{{ $student->username }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('username{{ $student->id }}')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password{{ $student->id }}" class="form-label">كلمة المرور</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="password{{ $student->id }}" value="{{ $student->username }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('password{{ $student->id }}')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> اسم المستخدم وكلمة المرور متطابقان
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCredentialsModal('{{ $student->id }}')">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Send Note Modal -->
<div class="modal fade" id="sendNoteModal" tabindex="-1" aria-labelledby="sendNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="sendNoteModalLabel">إرسال رسالة للطالب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('classroom.students.note', $classroom->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="studentIdForNote">
                    <div class="mb-3">
                        <label for="studentNameForNote" class="form-label">اسم الطالب</label>
                        <input type="text" class="form-control" id="studentNameForNote" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="note_subject" class="form-label">عنوان الرسالة</label>
                        <input type="text" class="form-control" id="note_subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="note_content" class="form-label">محتوى الرسالة</label>
                        <textarea class="form-control" id="note_content" name="content" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إرسال الرسالة</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Search functionality
function searchStudents() {
    const searchInput = document.getElementById('studentSearchInput');
    const searchTerm = searchInput.value.toLowerCase().trim();
    const tableRows = document.querySelectorAll('#studentsTable tbody tr');
    const noResultsDiv = document.getElementById('noSearchResults');
    const searchResults = document.getElementById('searchResults');
    
    let visibleRows = 0;
    const totalRows = tableRows.length;
    
    tableRows.forEach(function(row) {
        const searchText = row.getAttribute('data-search-text') || '';
        
        if (searchTerm === '' || searchText.toLowerCase().includes(searchTerm)) {
            row.style.display = '';
            visibleRows++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update search results text
    if (searchTerm === '') {
        searchResults.textContent = `عرض جميع الطلاب (${totalRows})`;
        noResultsDiv.style.display = 'none';
    } else {
        searchResults.textContent = `عرض ${visibleRows} من ${totalRows} طالب`;
        noResultsDiv.style.display = visibleRows === 0 ? 'block' : 'none';
    }
    
    // Reset checkbox states after search
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    }
    updateSelectedCount();
}

function clearSearch() {
    const searchInput = document.getElementById('studentSearchInput');
    searchInput.value = '';
    searchStudents();
    searchInput.focus();
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle view credentials buttons
    const viewCredentialsButtons = document.querySelectorAll('.view-credentials');
    viewCredentialsButtons.forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            openCredentialsModal(studentId);
        });
    });
    
    // Handle send note buttons
    const sendNoteButtons = document.querySelectorAll('.send-note-btn');
    sendNoteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const studentName = this.getAttribute('data-student-name');
            
            document.getElementById('studentIdForNote').value = studentId;
            document.getElementById('studentNameForNote').value = studentName;
            
            const modal = new bootstrap.Modal(document.getElementById('sendNoteModal'));
            modal.show();
        });
    });
    
    // Functions for credential modals
    function openCredentialsModal(studentId) {
        const modal = new bootstrap.Modal(document.getElementById(`credentialsModal${studentId}`));
        modal.show();
    }
    
    function closeCredentialsModal(studentId) {
        const modalElement = document.getElementById(`credentialsModal${studentId}`);
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }
    
    function copyToClipboard(fieldId) {
        const field = document.getElementById(fieldId);
        field.select();
        document.execCommand('copy');
        
        // Show a temporary "copied" message
        const button = field.nextElementSibling;
        const originalHTML = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-check"></i> تم النسخ';
        setTimeout(function() {
            button.innerHTML = originalHTML;
        }, 1500);
    }
    
    // Make functions globally accessible
    window.openCredentialsModal = openCredentialsModal;
    window.closeCredentialsModal = closeCredentialsModal;
    window.copyToClipboard = copyToClipboard;
});

// PDF Generation Functions (Global scope for onclick handlers)
function toggleAllStudents() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    
    if (selectAll && checkboxes.length > 0) {
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        updateSelectedCount();
    }
}

function updateSelectedCount() {
    const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
    const generateBtn = document.getElementById('pdfSelectedBtn');
    
    if (generateBtn) {
        const count = selectedCheckboxes.length;
        if (count > 0) {
            generateBtn.innerHTML = '<i class="fas fa-file-pdf"></i> PDF للمختارين (' + count + ')';
            generateBtn.disabled = false;
            generateBtn.classList.remove('btn-light');
            generateBtn.classList.add('btn-success');
        } else {
            generateBtn.innerHTML = '<i class="fas fa-file-pdf"></i> PDF للمختارين';
            generateBtn.disabled = true;
            generateBtn.classList.remove('btn-success');
            generateBtn.classList.add('btn-light');
        }
    }
}

function generateSelectedPdf() {
    const selectedCheckboxes = document.querySelectorAll('.student-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('يرجى اختيار طالب واحد على الأقل');
        return;
    }
    
    const studentIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("classroom.students.credentials.pdf", $classroom->id) }}';
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    // Add student IDs
    studentIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    
    // Clean up
    setTimeout(() => {
        document.body.removeChild(form);
    }, 1000);
}

// Add event listeners after page load
window.addEventListener('load', function() {
    
    // Add change event listener to all student checkboxes
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Add change event listener to select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', toggleAllStudents);
    }
    
    // Initial count update
    updateSelectedCount();
    
    // Initialize search results text
    const totalRows = document.querySelectorAll('#studentsTable tbody tr').length;
    const searchResults = document.getElementById('searchResults');
    if (searchResults) {
        searchResults.textContent = `عرض جميع الطلاب (${totalRows})`;
    }
});
</script>
@endsection 