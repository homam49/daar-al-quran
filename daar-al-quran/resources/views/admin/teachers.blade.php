@extends('layouts.admin')

@section('admin-content')
<style>
.dropdown-unbound {
    /* position: fixed !important; */
    z-index: 9999 !important;
    right: 20px !important;
    min-width: 300px;
    max-width: 90vw;
    max-height: 80vh;
    overflow-y: auto;
    background: #fff;
    box-shadow: 0 2px 16px rgba(0,0,0,0.2);
    border-radius: 0.5rem;
    direction: rtl;
}
</style>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>إدارة المعلمين</h4>
        </div>
    </div>
</div>

@if(isset($pendingTeachers) && count($pendingTeachers) > 0)
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-warning">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>طلبات انضمام المعلمين</h5>
                <span class="badge bg-dark">{{ count($pendingTeachers) }} طلب</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المعلم</th>
                                <th>البريد الإلكتروني</th>
                                <th>المدرسة</th>
                                <th>تاريخ الطلب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingTeachers as $index => $teacher)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $teacher->user_name }}</td>
                                <td>{{ $teacher->user_email }}</td>
                                <td>{{ $teacher->school_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($teacher->joined_at)->format('Y-m-d') }}</td>
                                <td>
                                    <form action="{{ route('admin.teachers.approve-school') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $teacher->user_id }}">
                                        <input type="hidden" name="school_id" value="{{ $teacher->school_id }}">
                                        <button type="submit" class="btn btn-sm btn-success" title="الموافقة على انضمام المعلم للمدرسة">
                                            <i class="fas fa-check"></i> قبول
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.teachers.reject-school') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $teacher->user_id }}">
                                        <input type="hidden" name="school_id" value="{{ $teacher->school_id }}">
                                        <button type="submit" class="btn btn-sm btn-danger" title="رفض طلب الانضمام" 
                                            onclick="return confirm('هل أنت متأكد من رفض طلب انضمام هذا المعلم؟')">
                                            <i class="fas fa-times"></i> رفض
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة المعلمين في مدارسك</h5>
            </div>
            <div class="card-body">
                
                
                
                @if(count($teachers) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المعلم</th>
                                <th>البريد الإلكتروني</th>
                                <th>المدرسة</th>
                                <th>حالة الموافقة</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $index => $teacher)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $teacher->name }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>{{ $teacher->school_name ?? 'غير محدد' }}</td>
                                <td>
                                    @if($teacher->is_approved)
                                        <span class="badge bg-success">تمت الموافقة</span>
                                    @else
                                        <span class="badge bg-warning">في انتظار الموافقة</span>
                                    @endif
                                </td>
                                <td>{{ $teacher->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="btn btn-sm btn-primary" title="عرض تفاصيل المعلم">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($teacher->is_approved)
                                        <button type="button" class="btn btn-sm btn-info open-classroom-modal" data-teacher-id="{{ $teacher->id }}" title="إدارة صلاحيات الفصول">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    @endif
                                    @if(!$teacher->is_approved)
                                        <form action="{{ route('admin.teachers.approve', $teacher->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="الموافقة على المعلم">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.teachers.delete', $teacher->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="إزالة المعلم من المدرسة وحذف فصوله" 
                                            onclick="return confirm('هل أنت متأكد من إزالة هذا المعلم من المدرسة؟ سيتم حذف جميع الفصول والجلسات التي أنشأها.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-user-graduate text-muted fa-3x mb-3"></i>
                    <p class="mb-0">لا يوجد معلمين في مدارسك حتى الآن</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Classroom Access Modal -->
<div class="modal fade" id="classroomAccessModal" tabindex="-1" aria-labelledby="classroomAccessModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="classroomAccessModalLabel">صلاحيات الفصول للمعلم</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>
      <div class="modal-body">
        <div id="modalClassroomsList">
          <div class="text-center text-muted">جاري التحميل...</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal logic
    let classroomAccessModal = new bootstrap.Modal(document.getElementById('classroomAccessModal'));
    let modalClassroomsList = document.getElementById('modalClassroomsList');
    let currentTeacherId = null;

    document.querySelectorAll('.open-classroom-modal').forEach(function(btn) {
        btn.addEventListener('click', function() {
            currentTeacherId = btn.getAttribute('data-teacher-id');
            modalClassroomsList.innerHTML = '<div class="text-center text-muted">جاري التحميل...</div>';
            classroomAccessModal.show();
            // Load classroom access data
            fetch(`/admin/teachers/classroom-access/${currentTeacherId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success === false) {
                        modalClassroomsList.innerHTML = `<div class='text-danger'>${data.message}</div>`;
                        return;
                    }
                    if (data.classrooms && data.classrooms.length > 0) {
                        let html = '<ul class="list-group">';
                        data.classrooms.forEach(classroom => {
                            const isChecked = classroom.has_access ? 'checked' : '';
                            html += `
                                <li class="list-group-item d-flex align-items-center justify-content-between">
                                    <span>
                                        <strong>${classroom.name}</strong><br>
                                        <small class="text-muted">${classroom.school_name}</small>
                                    </span>
                                    <input type="checkbox" class="form-check-input classroom-access-checkbox" data-classroom-id="${classroom.id}" ${isChecked}>
                                </li>
                            `;
                        });
                        html += '</ul>';
                        modalClassroomsList.innerHTML = html;
                        // Add event listeners to checkboxes
                        modalClassroomsList.querySelectorAll('.classroom-access-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                handleAccessChange(currentTeacherId, this.getAttribute('data-classroom-id'), this.checked);
                            });
                        });
                    } else {
                        modalClassroomsList.innerHTML = '<div class="text-muted">لا توجد فصول في المدرسة</div>';
                    }
                })
                .catch(error => {
                    modalClassroomsList.innerHTML = `<div class='text-danger'>خطأ: ${error.message}</div>`;
                });
        });
    });

    function handleAccessChange(teacherId, classroomId, hasAccess) {
        const url = hasAccess ? '/admin/teachers/classroom-access/grant' : '/admin/teachers/classroom-access/revoke';
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                teacher_id: teacherId,
                classroom_id: classroomId
            })
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.success ? 'success' : 'danger', data.message);
        })
        .catch(error => {
            showToast('danger', 'حدث خطأ أثناء تحديث الصلاحيات');
        });
    }

    // Toast function
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 4000);
    }
});
</script>
@endpush
@endsection 