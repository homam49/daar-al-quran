@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon">
            <i class="fas fa-school"></i>
        </div>
        المدارس
    </h1>
    <a href="{{ route('teacher.join-school.form') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> الانضمام إلى مدرسة
    </a>
</div>

@if(isset($pendingSchools) && $pendingSchools->count() > 0)
<div class="card shadow-sm border-warning mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>طلبات الانضمام للمدارس المعلقة</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <p><i class="fas fa-info-circle me-2"></i>لديك {{ $pendingSchools->count() }} طلب انضمام للمدارس في انتظار موافقة المدير. سيتم إشعارك عند الموافقة على طلبك.</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم المدرسة</th>
                        <th>تاريخ الطلب</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingSchools as $index => $school)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $school->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($school->created_at)->format('Y-m-d') }}</td>
                        <td><span class="badge bg-warning text-dark">في انتظار الموافقة</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-muted">قائمة المدارس </h6>
    </div>
    <div class="card-body">
        @if(count($schools) > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>اسم المدرسة</th>
                            <th>العنوان</th>
                            <th>عدد الفصول</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schools as $school)
                            <tr>
                                <td>{{ $school->name }}</td>
                                <td>{{ $school->address }}</td>
                                <td>
                                    {{ App\Models\ClassRoom::where('school_id', $school->id)
                                         ->where('user_id', Auth::id())->count() }}
                                </td>
                                <td>
                                    <a href="{{ route('classrooms.create', ['school_id' => $school->id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> إضافة فصل
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <p class="lead">لا توجد مدارس مرتبطة</p>
                <p>يمكنك الانضمام إلى مدرسة باستخدام رمز المدرسة</p>
                <a href="{{ route('teacher.join-school.form') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> الانضمام إلى مدرسة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 