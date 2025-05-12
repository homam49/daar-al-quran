@extends('layouts.student')

@section('student-content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <div class="page-header-icon"><i class="fas fa-envelope-open"></i></div>
            {{ $message->title }}
        </h1>
        <a href="{{ route('student.messages') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right me-1"></i> العودة للرسائل
        </a>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                @if($message->sender_type == 'teacher' && $message->sender)
                    <span class="badge bg-info text-white me-2">من المعلم</span> {{ $message->sender->name }}
                @elseif($message->sender_type == 'system')
                    <span class="badge bg-secondary me-2">إشعار نظام</span>
                @endif
            </div>
            <div>
                <small class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i> {{ $message->created_at instanceof \DateTime ? $message->created_at->format('Y-m-d') : \Carbon\Carbon::parse($message->created_at)->format('Y-m-d') }}
                    <i class="fas fa-clock ms-2 me-1"></i> {{ $message->created_at instanceof \DateTime ? $message->created_at->format('h:i A') : \Carbon\Carbon::parse($message->created_at)->format('h:i A') }}
                </small>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="message-content mb-4">
            <div class="p-3 bg-light rounded">
                {!! nl2br(e($message->content)) !!}
            </div>
        </div>
        
        @if($message->related_type && $message->related_id)
            <div class="alert alert-info">
                <div class="d-flex">
                    <div class="me-2">
                        <i class="fas fa-link fa-2x"></i>
                    </div>
                    <div>
                        <h6>هذه الرسالة مرتبطة بـ:</h6>
                        @if($message->related_type == 'class_session')
                            <p class="mb-0">
                                جلسة دراسية في {{ $message->related->classroom->name }} بتاريخ {{ $message->related->date instanceof \DateTime ? $message->related->date->format('Y-m-d') : \Carbon\Carbon::parse($message->related->date)->format('Y-m-d') }}
                                @if($attendance = $message->related->attendances->where('student_id', auth('student')->id())->first())
                                    <br>
                                    <span class="badge 
                                        @if($attendance->status == 'present') bg-success 
                                        @elseif($attendance->status == 'late') bg-warning text-dark 
                                        @elseif($attendance->status == 'absent') bg-danger 
                                        @else bg-secondary @endif">
                                        {{ $attendance->status == 'present' ? 'حاضر' : ($attendance->status == 'late' ? 'متأخر' : 'غائب') }}
                                    </span>
                                @endif
                                
                                <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-calendar-check me-1"></i> عرض سجل الحضور
                                </a>
                            </p>
                        @elseif($message->related_type == 'classroom')
                            <p class="mb-0">
                                الفصل الدراسي: {{ $message->related->name }}
                                <a href="{{ route('student.classrooms') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="fas fa-chalkboard me-1"></i> عرض الفصول
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        
        @if($message->read_at)
            <div class="text-muted mt-3">
                <small><i class="fas fa-check-double me-1"></i> تمت القراءة في {{ $message->read_at instanceof \DateTime ? $message->read_at->format('Y-m-d h:i A') : \Carbon\Carbon::parse($message->read_at)->format('Y-m-d h:i A') }}</small>
            </div>
        @endif
    </div>
</div>

@if(count($otherMessages) > 0)
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">رسائل أخرى</h5>
    </div>
    <div class="card-body">
        <div class="list-group">
            @foreach($otherMessages as $otherMessage)
                <a href="{{ route('student.messages.view', $otherMessage->id) }}" class="list-group-item list-group-item-action {{ $otherMessage->read_at ? '' : 'bg-light' }}">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">
                            @if(!$otherMessage->read_at)
                                <span class="badge bg-primary me-2">جديد</span>
                            @endif
                            {{ $otherMessage->title }}
                        </h6>
                        <small>{{ $otherMessage->created_at instanceof \DateTime ? $otherMessage->created_at->format('Y-m-d') : \Carbon\Carbon::parse($otherMessage->created_at)->format('Y-m-d') }}</small>
                    </div>
                    <small>
                        @if($otherMessage->sender_type == 'teacher' && $otherMessage->sender)
                            <span class="text-muted">من المعلم: {{ $otherMessage->sender->name }}</span>
                        @elseif($otherMessage->sender_type == 'system')
                            <span class="text-muted">إشعار نظام</span>
                        @endif
                    </small>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection 