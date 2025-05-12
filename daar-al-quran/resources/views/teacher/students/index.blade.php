@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-user-graduate"></i></div>
        قائمة الطلاب - {{ $classroom->name }}
    </h1>
    <div>
        <a href="{{ route('teacher.classroom.students.create', $classroom->id) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة طالب
        </a>
        <a href="{{ route('classrooms.show', $classroom->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للفصل
        </a>
    </div>
</div>





<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>جميع الطلاب المسجلين في الفصل</h5>
    </div>
    <div class="card-body">
        @if(count($students) > 0)
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>ملاحظة:</strong> بيانات الدخول للطلاب هي نفسها لاسم المستخدم وكلمة المرور. يمكن للطلاب تغيير كلمة المرور بعد تسجيل الدخول الأول.
        </div>
        
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="studentsTable">
                <thead class="bg-light">
                    <tr>
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
                    <tr data-search-text="{{ $student->first_name }} {{ $student->middle_name ?? '' }} {{ $student->last_name }} {{ $student->phone ?? '' }} {{ $student->username ?? '' }} {{ $student->address ?? '' }}">
                        <td>{{ $student->first_name }} {{ $student->middle_name ?? '' }} {{ $student->last_name }}</td>
                        <td>{{ $student->age }} سنة</td>
                        <td>{{ $student->phone ?? 'غير متوفر' }}</td>
                        <td><strong>{{ $student->username ?? 'غير متوفر' }}</strong></td>
                        <td>{{ $student->address ?? 'غير متوفر' }}</td>
                        <td class="text-center">
                            <a href="{{ route('teacher.classroom.students.edit', [$classroom->id, $student->id]) }}" class="btn btn-sm btn-primary mb-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('teacher.classroom.students.remove', [$classroom->id, $student->id]) }}" method="POST" class="d-inline delete-form" onsubmit="return confirm('هل أنت متأكد من إزالة هذا الطالب من الفصل؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <button class="btn btn-sm btn-info mb-1 view-credentials" data-student-id="{{ $student->id }}" data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-secondary mb-1 send-note-btn" data-student-id="{{ $student->id }}" data-student-name="{{ $student->first_name }} {{ $student->last_name }}">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-user-graduate text-muted fa-4x"></i>
            </div>
            <p class="lead">لا يوجد طلاب مسجلين في هذا الفصل</p>
            <a href="{{ route('teacher.classroom.students.create', $classroom->id) }}" class="btn btn-primary">
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
                <h5 class="modal-title" id="credentialsModalLabel{{ $student->id }}">بيانات تسجيل الدخول - {{ $student->full_name }}</h5>
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
                        <label for="note_title" class="form-label">عنوان الرسالة</label>
                        <input type="text" class="form-control" id="note_title" name="title" required>
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
        
        // Add global functions so they can be called from HTML
        window.openCredentialsModal = openCredentialsModal;
        window.closeCredentialsModal = closeCredentialsModal;
        window.copyToClipboard = copyToClipboard;
    });
</script>
@endsection 