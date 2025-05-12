@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-school me-2"></i>تفاصيل المدرسة</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل المدرسة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">معلومات المدرسة</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">اسم المدرسة</span>
                            <span>{{ $school->name }}</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">العنوان</span>
                            <span>{{ $school->address }}</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">رمز الانضمام</span>
                            <span>{{ $school->code }}</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">تاريخ الإنشاء</span>
                            <span>{{ $school->created_at->format('Y-m-d') }}</span>
                        </div>
                    </li>
                </ul>
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
                    <div class="col-6 mb-3">
                        <h5>{{ $school->teachers()->count() }}</h5>
                        <span class="text-muted">عدد المعلمين</span>
                    </div>
                    <div class="col-6 mb-3">
                        <h5>{{ $school->classRooms()->count() }}</h5>
                        <span class="text-muted">عدد الفصول</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة المعلمين</h5>
            </div>
            <div class="card-body">
                @php $teachers = $school->teachers; @endphp
                
                @if($teachers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم المعلم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>تاريخ الانضمام</th>
                                    <th>عدد الفصول</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teachers as $index => $teacher)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $teacher->name }}</td>
                                        <td>{{ $teacher->email }}</td>
                                        <td>{{ ($teacher->pivot && $teacher->pivot->created_at) ? $teacher->pivot->created_at->format('Y-m-d') : 'غير معروف' }}</td>
                                        <td>{{ $teacher->classRooms()->where('school_id', $school->id)->count() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-tie text-muted fa-3x mb-3"></i>
                        <p class="mb-0">لا يوجد معلمين في هذه المدرسة حالياً</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 