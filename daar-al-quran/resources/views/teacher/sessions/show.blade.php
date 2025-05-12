@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-calendar-check"></i></div>
        تفاصيل الجلسة
    </h1>
    <div>
        <a href="{{ route('classroom.sessions.edit', [$classroom->id, $session->id]) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> تعديل الجلسة
        </a>
        <a href="{{ route('classrooms.show', $classroom->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للفصل
        </a>
    </div>
</div>

<div class="row">
    <!-- Session Information -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>معلومات الجلسة</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>الفصل:</strong> {{ $classroom->name }}
                </div>
                <div class="mb-3">
                    <strong>المدرسة:</strong> {{ $classroom->school->name }}
                </div>
                <div class="mb-3">
                    <strong>تاريخ الجلسة:</strong> {{ $session->session_date->format('Y-m-d') }}
                </div>
                <div class="mb-3">
                    <strong>وقت الجلسة:</strong> {{ $session->start_time }} - {{ $session->end_time }}
                </div>
                @if($session->description)
                <div class="mb-3">
                    <strong>وصف الجلسة:</strong>
                    <p class="mt-2">{{ $session->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance Summary -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>ملخص الحضور</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="p-3 rounded bg-success bg-opacity-10">
                            <h3 class="">{{ $session->attendances->where('status', 'present')->count() }}</h3>
                            <p class="mb-0">حاضر</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 rounded bg-warning bg-opacity-10">
                            <h3 class="">{{ $session->attendances->where('status', 'late')->count() }}</h3>
                            <p class="mb-0">متأخر</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3 rounded bg-danger bg-opacity-10">
                            <h3 class="">{{ $session->attendances->where('status', 'absent')->count() }}</h3>
                            <p class="mb-0">غائب</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>نسبة الحضور</h6>
                    @php
                        $totalStudents = $session->attendances->count();
                        $presentCount = $session->attendances->where('status', 'present')->count();
                        $lateCount = $session->attendances->where('status', 'late')->count();
                        $presentPercentage = $totalStudents > 0 ? round((($presentCount + $lateCount) / $totalStudents) * 100) : 0;
                    @endphp
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $presentCount / $totalStudents * 100 }}%;" 
                             aria-valuenow="{{ $presentCount }}" aria-valuemin="0" aria-valuemax="{{ $totalStudents }}">
                            {{ $presentCount }}
                        </div>
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: {{ $lateCount / $totalStudents * 100 }}%;" 
                             aria-valuenow="{{ $lateCount }}" aria-valuemin="0" aria-valuemax="{{ $totalStudents }}">
                            {{ $lateCount }}
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <strong>{{ $presentPercentage }}%</strong> نسبة الحضور
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Details -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>تفاصيل الحضور</h5>
    </div>
    <div class="card-body">
        @if($session->attendances->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الطالب</th>
                        <th>الحالة</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($session->attendances as $index => $attendance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $attendance->student->full_name }}</td>
                        <td>
                            @if($attendance->status == 'present')
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> حاضر</span>
                            @elseif($attendance->status == 'late')
                                <span class="badge bg-warning"><i class="fas fa-clock me-1"></i> متأخر</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i> غائب</span>
                            @endif
                        </td>
                        <td>{{ $attendance->note ?: 'لا توجد ملاحظات' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            لم يتم تسجيل الحضور لهذه الجلسة بعد.
        </div>
        @endif
    </div>
</div>

<!-- Session Actions -->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">إجراءات</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <a href="{{ route('classroom.sessions.edit', [$classroom->id, $session->id]) }}" class="btn btn-primary w-100">
                    <i class="fas fa-edit me-2"></i> تعديل الجلسة والحضور
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <form action="{{ route('classroom.sessions.destroy', [$classroom->id, $session->id]) }}" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟ سيتم حذف كافة سجلات الحضور المرتبطة بها.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-trash me-2"></i> حذف الجلسة
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection 