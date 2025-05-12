@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>لوحة تحكم المسؤول</h4>
        </div>
        <div class="col-auto">
            <span class="text-muted">{{ \Carbon\Carbon::now()->format('Y-m-d') }}</span>
        </div>
    </div>
</div>

<div class="row dashboard-stats">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-success mb-2">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3 class="font-weight-bold">{{ $teachers_count }}</h3>
                <p class="mb-0">عدد المعلمين</p>
            </div>
            <div class="card-footer bg-success text-white text-center py-2">
                <a href="{{ route('admin.teachers') }}" class="text-white text-decoration-none">
                    <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-warning mb-2">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3 class="font-weight-bold">{{ $students_count }}</h3>
                <p class="mb-0">عدد الطلاب</p>
            </div>
            <div class="card-footer bg-warning text-dark text-center py-2">
                <a href="{{ route('admin.students') }}" class="text-dark text-decoration-none">
                    <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-info mb-2">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <h3 class="font-weight-bold">{{ $classrooms_count }}</h3>
                <p class="mb-0">عدد الفصول</p>
            </div>
            <div class="card-footer bg-info text-white text-center py-2">
                <a href="{{ route('admin.classrooms') }}" class="text-white text-decoration-none">
                    <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>طلبات التسجيل الجديدة</h5>
                @php
                    $totalPendingCount = count($pending_teachers['system']) + count($pending_teachers['school']);
                @endphp
                @if($totalPendingCount > 0)
                <span class="badge bg-dark">{{ $totalPendingCount }} طلب</span>
                @endif
            </div>
            <div class="card-body">
                @if($totalPendingCount > 0)
                    @if(count($pending_teachers['system']) > 0)
                        <div class="table-responsive mb-4">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>تاريخ التسجيل</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pending_teachers['system'] as $teacher)
                                    <tr>
                                        <td>{{ $teacher->name }}</td>
                                        <td>{{ $teacher->email }}</td>
                                        <td>{{ $teacher->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="btn btn-sm btn-primary" title="عرض تفاصيل المعلم">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.teachers.approve', $teacher->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="الموافقة على المعلم">
                                                    <i class="fas fa-check"></i> قبول
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(count($pending_teachers['school']) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>تاريخ الطلب</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pending_teachers['school'] as $teacher)
                                    <tr>
                                        <td>{{ $teacher->user_name }}</td>
                                        <td>{{ $teacher->user_email }}</td>
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
                                                <button type="submit" class="btn btn-sm btn-danger" title="رفض طلب الانضمام">
                                                    <i class="fas fa-times"></i> رفض
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="mb-0">لا توجد طلبات تسجيل جديدة</p>
                    </div>
                @endif
            </div>
            @if($totalPendingCount > 0)
                <div class="card-footer text-center">
                    <a href="{{ route('admin.teachers') }}" class="btn btn-sm btn-outline-primary">عرض جميع الطلبات</a>
                </div>
            @endif
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100" id="school-details">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-school me-2"></i>معلومات المدرسة</h5>
            </div>
            <div class="card-body">
                @if($school)
                    <div class="p-3">
                        <h4 class="text-center mb-4">{{ $school->name }}</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">العنوان</span>
                                    <span>{{ $school->address }}</span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">رمز الانضمام</span>
                                    <span><code>{{ $school->code }}</code></span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">تاريخ الإنشاء</span>
                                    <span>{{ $school->created_at->format('Y-m-d') }}</span>
                                </div>
                            </li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit me-1"></i> تعديل المدرسة
                            </a>
                            <form method="GET" action="{{ route('admin.schools.deletion-form') }}" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash me-1"></i> حذف المدرسة
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-school text-muted fa-3x mb-3"></i>
                        <p class="mb-0">لم تقم بإنشاء مدرسة بعد</p>
                        <a href="{{ route('admin.schools.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-1"></i> إنشاء مدرسة جديدة
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection 