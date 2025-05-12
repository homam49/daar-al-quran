@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-chalkboard me-2"></i>إدارة الفصول</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة الفصول</h5>
        </div>
    </div>
    <div class="card-body">
        @if(count($classrooms) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الفصل</th>
                            <th>المعلم</th>
                            <th>عدد الطلاب</th>
                            <th>أيام الدراسة</th>
                            <th>توقيت الدراسة</th>
                            <th>تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classrooms as $index => $classroom)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $classroom->name }}</td>
                                <td>{{ $classroom->teacher->name }}</td>
                                <td>{{ $classroom->students_count }}</td>
                                <td>
                                    <small>
                                        @php
                                            $days = $classroom->schedules->pluck('day')->toArray();
                                            echo implode('، ', $days);
                                        @endphp
                                    </small>
                                </td>
                                <td>
                                    @if($classroom->schedules->count() > 0)
                                        <small>
                                            {{ date('h:i A', strtotime($classroom->schedules->first()->start_time)) }} 
                                            - 
                                            {{ date('h:i A', strtotime($classroom->schedules->first()->end_time)) }}
                                        </small>
                                    @else
                                        <small class="text-muted">غير محدد</small>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($classroom->created_at)->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-chalkboard text-muted fa-4x"></i>
                </div>
                <p class="lead">لا يوجد فصول مسجلة في المدرسة الخاصة بك</p>
                <p>قم بتشجيع المعلمين على إنشاء فصول في المدرسة الخاصة بك</p>
            </div>
        @endif
    </div>
</div>
@endsection 