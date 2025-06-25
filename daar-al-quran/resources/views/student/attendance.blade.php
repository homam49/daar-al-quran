@extends('layouts.student')

@section('student-content')
<div class="page-header">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-calendar-check"></i></div>
        سجل الحضور
    </h1>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">إحصائيات الحضور</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="attendance-present mb-2">
                            <i class="fas fa-check-circle fa-3x"></i>
                        </div>
                        <h4>{{ $present_count }}</h4>
                        <p class="text-muted">حاضر</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="attendance-late mb-2">
                            <i class="fas fa-clock fa-3x"></i>
                        </div>
                        <h4>{{ $late_count }}</h4>
                        <p class="text-muted">متأخر</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="attendance-absent mb-2">
                            <i class="fas fa-times-circle fa-3x"></i>
                        </div>
                        <h4>{{ $absent_count }}</h4>
                        <p class="text-muted">غائب</p>
                    </div>
                </div>
                
                <div class="attendance-chart">
                    <h6 class="text-center mb-2">نسبة الحضور الإجمالية</h6>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: {{ $attendance_percentage }}%;" 
                            aria-valuenow="{{ $attendance_percentage }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">{{ $attendance_percentage }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">سجل الحضور التفصيلي</h5>
        <form method="GET" action="{{ route('student.attendance') }}" class="d-flex">
            <select name="classroom_id" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                <option value="">كل الفصول</option>
                @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ request('classroom_id') == $classroom->id ? 'selected' : '' }}>
                        {{ $classroom->name }}
                    </option>
                @endforeach
            </select>
            <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">كل الأشهر</option>
                @foreach($months as $key => $month)
                    <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>
                        {{ $month }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body">
        @if(count($attendances) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>الفصل</th>
                            <th>الوقت</th>
                            <th>الحالة</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->classSession->session_date->format('Y-m-d') }}</td>
                                <td>{{ $attendance->classSession->classRoom ? $attendance->classSession->classRoom->name : 'فصل غير معروف' }}</td>
                                <td><span class="time-display">{{ $attendance->classSession->start_time }} - {{ $attendance->classSession->end_time }}</span></td>
                                <td>
                                    @if($attendance->status == 'present')
                                        <span class="badge bg-success">حاضر</span>
                                    @elseif($attendance->status == 'late')
                                        <span class="badge bg-warning text-dark">متأخر</span>
                                    @elseif($attendance->status == 'absent')
                                        <span class="badge bg-danger">غائب</span>
                                    @else
                                        <span class="badge bg-secondary">غير معروف</span>
                                    @endif
                                </td>
                                <td>{{ $attendance->note ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $attendances->appends(request()->except('page'))->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-calendar-times text-muted fa-3x mb-3"></i>
                <p>لا توجد سجلات حضور متاحة{{ request('classroom_id') || request('month') ? ' وفقًا للمعايير المحددة' : '' }}</p>
                @if(request('classroom_id') || request('month'))
                    <a href="{{ route('student.attendance') }}" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-sync-alt me-1"></i> إعادة ضبط المعايير
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection 