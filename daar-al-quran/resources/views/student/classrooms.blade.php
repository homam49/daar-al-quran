@extends('layouts.student')

@section('student-content')
<div class="page-header">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-chalkboard"></i></div>
        الفصول الدراسية
    </h1>
</div>

<div class="row">
    @if(count($classrooms) > 0)
        @foreach($classrooms as $classroom)
            <div class="col-md-6 mb-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $classroom->name }}</h5>
                            <span class="badge bg-primary">{{ $classroom->students->count() }} طالب</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-school text-primary me-2"></i> {{ $classroom->school ? $classroom->school->name : 'مدرسة غير معروفة' }}
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-user-tie text-primary me-2"></i> المعلم: {{ $classroom->teacher ? $classroom->teacher->name : 'معلم غير معروف' }}
                        </div>
                        @if(isset($classroom->days))
                        <div class="mb-3">
                            <i class="fas fa-calendar-alt text-primary me-2"></i> 
                            أيام الدراسة: 
                            @if(count($classroom->days) > 0)
                                @foreach($classroom->days as $day)
                                    <span class="me-1">{{ $day }}</span>
                                @endforeach
                            @else   
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </div>
                        @endif
                        @if(isset($classroom->start_time) && isset($classroom->end_time))
                        <div class="mb-3">
                            <i class="fas fa-clock text-primary me-2"></i> 
                            @php
                                try {
                                    if (strlen($classroom->start_time) > 8) {
                                        $startTime = \Carbon\Carbon::parse($classroom->start_time)->format('h:i A');
                                        $endTime = \Carbon\Carbon::parse($classroom->end_time)->format('h:i A');
                                    } else {
                                        $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $classroom->start_time)->format('h:i A');
                                        $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $classroom->end_time)->format('h:i A');
                                    }
                                } catch (\Exception $e) {
                                    $startTime = date('h:i A', strtotime($classroom->start_time));
                                    $endTime = date('h:i A', strtotime($classroom->end_time));
                                }
                            @endphp
                            وقت الدراسة: <span class="time-display">{{ $startTime }} - {{ $endTime }}</span>
                        </div>
                        @endif
                        @if($classroom->description)
                            <div class="mt-3">
                                <h6>وصف الفصل:</h6>
                                <p class="text-muted">{{ $classroom->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                لم يتم تسجيلك في أي فصول دراسية حتى الآن. يرجى التواصل مع المدرسة أو المعلم للانضمام إلى الفصول.
            </div>
        </div>
    @endif
</div>
@endsection 