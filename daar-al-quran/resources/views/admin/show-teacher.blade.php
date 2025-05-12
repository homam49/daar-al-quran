@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-user me-2"></i>تفاصيل المعلم</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.teachers') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-right"></i> العودة إلى قائمة المعلمين
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">المعلومات الشخصية</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar avatar-xl">
                        <span class="avatar-text rounded-circle bg-primary">
                            {{ substr($user->name, 0, 1) }}
                        </span>
                    </div>
                    <h4 class="mt-3">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <span class="badge bg-success">{{ $user->role->name }}</span>
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">البريد الإلكتروني</span>
                            <span>{{ $user->email }}</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">حالة الموافقة</span>
                            <span>
                                @if($user->is_approved)
                                    <span class="badge bg-success">تمت الموافقة</span>
                                @else
                                    <span class="badge bg-warning">في انتظار الموافقة</span>
                                @endif
                            </span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">تاريخ الانضمام</span>
                            <span>{{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                    </li>
                </ul>
                
                <div class="mt-3 d-flex gap-2">
                    @if(!$user->is_approved)
                        <form action="{{ route('admin.teachers.approve', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-1"></i> الموافقة على المعلم
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.teachers.delete', $user->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من إزالة هذا المعلم من مدارسك؟ سيتم حذف جميع الفصول والجلسات التي أنشأها.')">
                            <i class="fas fa-trash me-1"></i> إزالة من المدارس
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">إحصائيات</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4 mb-3">
                        <h5>{{ $teacherSchools->count() }}</h5>
                        <span class="text-muted">عدد المدارس</span>
                    </div>
                    <div class="col-4 mb-3">
                        <h5>{{ $classrooms->count() }}</h5>
                        <span class="text-muted">عدد الفصول</span>
                    </div>
                    <div class="col-4 mb-3">
                        <h5>{{ $classrooms->sum(function($classroom) { return $classroom->students->count(); }) }}</h5>
                        <span class="text-muted">عدد الطلاب</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="mb-0">المدارس المنضم إليها</h5>
            </div>
            <div class="card-body">
                @if($teacherSchools->count() > 0)
                <ul class="list-group">
                    @foreach($teacherSchools as $school)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $school->name }}
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="text-center py-3">
                    <p class="mb-0">لم ينضم المعلم إلى أي من مدارسك بعد</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">فصول المعلم</h5>
            </div>
            <div class="card-body">
                @if($classrooms->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الفصل</th>
                                <th>المدرسة</th>
                                <th>عدد الطلاب</th>
                                <th>تاريخ الإنشاء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classrooms as $index => $classroom)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $classroom->name }}</td>
                                <td>{{ $classroom->school->name }}</td>
                                <td>{{ $classroom->students->count() }}</td>
                                <td>{{ $classroom->created_at->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-chalkboard text-muted fa-3x mb-3"></i>
                    <p class="mb-0">لا يوجد فصول لهذا المعلم في مدارسك</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 