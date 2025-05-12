@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-key"></i></div>
        بيانات تسجيل دخول الطالب
    </h1>
    <div>
        <a href="{{ route('teacher.classroom.students', $classroom->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة لقائمة الطلاب
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>بيانات الطالب: {{ $student->first_name }} {{ $student->middle_name ?? '' }} {{ $student->last_name }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <table class="table table-bordered">
                    <tr>
                        <th class="bg-light" width="40%">اسم الطالب</th>
                        <td>{{ $student->first_name }} {{ $student->middle_name ?? '' }} {{ $student->last_name }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">اسم المستخدم</th>
                        <td>{{ $student->username ?? 'غير متوفر' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">كلمة المرور</th>
                        <td>{{ $student->username ?? 'غير متوفر' }}</td>
                    </tr>
                </table>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    اسم المستخدم وكلمة المرور هما نفس القيمة. يمكن للطالب تغيير كلمة المرور بعد تسجيل الدخول الأول.
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('teacher.classroom.students', $classroom->id) }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right me-1"></i> العودة لقائمة الطلاب
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 