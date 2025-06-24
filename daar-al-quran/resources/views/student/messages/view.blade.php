@extends('layouts.student')

@section('student-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-envelope-open"></i></div>
        عرض الرسالة
    </h1>
    <div>
        <a href="{{ route('student.messages') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right me-1"></i> العودة للرسائل
        </a>
        <a href="{{ route('student.messages.compose') }}" class="btn btn-primary">
            <i class="fas fa-paper-plane me-1"></i> رسالة جديدة
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5 class="mb-1">{{ $message->subject ?? 'رسالة بدون عنوان' }}</h5>
                <small class="text-muted">
                    @if($message->sender)
                        <i class="fas fa-user-tie text-primary me-1"></i> من: {{ $message->sender->name }}
                    @else
                        <i class="fas fa-bell text-secondary me-1"></i> إشعار نظام
                    @endif
                </small>
            </div>
            <div class="text-end">
                <small class="text-muted">
                    {{ $message->created_at->format('Y-m-d H:i') }}<br>
                    <span class="badge bg-secondary">{{ $message->created_at->diffForHumans() }}</span>
                </small>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="message-content p-3 bg-light rounded">
            {!! nl2br(e($message->content)) !!}
        </div>
        
        @if($message->read_at)
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-check text-success me-1"></i>
                    تم قراءة الرسالة في {{ $message->read_at->format('Y-m-d H:i') }}
                </small>
            </div>
        @endif
    </div>
</div>
@endsection 