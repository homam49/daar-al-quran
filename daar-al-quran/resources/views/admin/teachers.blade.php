@extends('layouts.admin')

@section('admin-content')
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
@endsection 