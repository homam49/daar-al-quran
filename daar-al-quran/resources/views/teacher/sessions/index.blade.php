@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-calendar-check"></i></div>
        جلسات الفصل - {{ $classroom->name }}
    </h1>
    <div>
        <a href="{{ route('classroom.sessions.create', $classroom->id) }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة جلسة جديدة
        </a>
        <a href="{{ route('classrooms.show', $classroom->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للفصل
        </a>
    </div>
</div>





<!-- Sessions calendar view -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>تقويم الجلسات</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            يمكنك هنا رؤية جميع الجلسات التي تم إنشاؤها لهذا الفصل في تقويم شهري.
        </div>
        <!-- Calendar placeholder - would need JavaScript calendar implementation -->
        <div class="text-center py-3">
            <p>سيتم إضافة تقويم هنا في تحديث قادم</p>
        </div>
    </div>
</div>

<!-- Sessions list view -->
<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>قائمة الجلسات</h5>
    </div>
    <div class="card-body">
        @if(count($sessions) > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th width="15%">التاريخ</th>
                        <th width="15%">الوقت</th>
                        <th>الحضور</th>
                        <th>ملاحظات</th>
                        <th width="25%">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                    <tr>
                        <td>{{ $session->session_date->format('Y-m-d') }}</td>
                        <td>{{ $session->start_time }} - {{ $session->end_time }}</td>
                        <td>
                            @php
                                $totalStudents = $session->attendances->count();
                                $presentCount = $session->attendances->where('status', 'present')->count();
                                $lateCount = $session->attendances->where('status', 'late')->count();
                                $absentCount = $session->attendances->where('status', 'absent')->count();
                                $attendanceRate = $totalStudents > 0 ? round((($presentCount + $lateCount) / $totalStudents) * 100) : 0;
                            @endphp
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $totalStudents > 0 ? ($presentCount / $totalStudents) * 100 : 0 }}%;" 
                                     aria-valuenow="{{ $presentCount }}" aria-valuemin="0" aria-valuemax="{{ $totalStudents }}">
                                    {{ $presentCount }}
                                </div>
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: {{ $totalStudents > 0 ? ($lateCount / $totalStudents) * 100 : 0 }}%;" 
                                     aria-valuenow="{{ $lateCount }}" aria-valuemin="0" aria-valuemax="{{ $totalStudents }}">
                                    {{ $lateCount }}
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: {{ $totalStudents > 0 ? ($absentCount / $totalStudents) * 100 : 0 }}%;" 
                                     aria-valuenow="{{ $absentCount }}" aria-valuemin="0" aria-valuemax="{{ $totalStudents }}">
                                    {{ $absentCount }}
                                </div>
                            </div>
                            <small>{{ $attendanceRate }}% نسبة الحضور</small>
                        </td>
                        <td>{{ $session->description ?? 'لا توجد ملاحظات' }}</td>
                        <td class="text-center">
                            <a href="{{ route('classroom.sessions.show', [$classroom->id, $session->id]) }}" class="btn btn-sm btn-info mb-1">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                            <a href="{{ route('classroom.sessions.edit', [$classroom->id, $session->id]) }}" class="btn btn-sm btn-primary mb-1">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <a href="{{ route('teacher.sessions.attendance', $session->id) }}" class="btn btn-sm btn-success mb-1">
                                <i class="fas fa-user-check"></i> الحضور
                            </a>
                            <form action="{{ route('classroom.sessions.destroy', [$classroom->id, $session->id]) }}" method="POST" class="d-inline delete-form" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1">
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
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-calendar-times text-muted fa-4x"></i>
            </div>
            <p class="lead">لا توجد جلسات لهذا الفصل</p>
            <a href="{{ route('classroom.sessions.create', $classroom->id) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة جلسة جديدة
            </a>
        </div>
        @endif
    </div>
</div>

@if(count($sessions) > 0)
<!-- Session statistics -->
<div class="card shadow-sm mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>إحصائيات الجلسات</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3 mb-3">
                <div class="p-3 rounded bg-light">
                    <h3 class="text-primary">{{ count($sessions) }}</h3>
                    <p class="mb-0">إجمالي الجلسات</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3 rounded bg-light">
                    <h3 class="text-success">{{ $sessions->where('session_date', '>=', now()->startOfDay())->count() }}</h3>
                    <p class="mb-0">جلسات قادمة</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3 rounded bg-light">
                    <h3 class="text-info">{{ $sessions->where('session_date', '<', now()->startOfDay())->count() }}</h3>
                    <p class="mb-0">جلسات سابقة</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="p-3 rounded bg-light">
                    @php
                        $allAttendance = $sessions->flatMap(function($session) {
                            return $session->attendances;
                        });
                        $totalAttendances = $allAttendance->count();
                        $presentAttendances = $allAttendance->where('status', 'present')->count();
                        $lateAttendances = $allAttendance->where('status', 'late')->count();
                        $overallRate = $totalAttendances > 0 ? round((($presentAttendances + $lateAttendances) / $totalAttendances) * 100) : 0;
                    @endphp
                    <h3 class="text-warning">{{ $overallRate }}%</h3>
                    <p class="mb-0">متوسط نسبة الحضور</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript for sorting, filtering or calendar functionality here
    });
</script>
@endsection 