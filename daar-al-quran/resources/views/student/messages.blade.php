@extends('layouts.student')

@section('student-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-envelope"></i></div>
        الرسائل
    </h1>
    <a href="{{ route('student.messages.compose') }}" class="btn btn-primary">
        <i class="fas fa-paper-plane me-1"></i> إرسال رسالة جديدة
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">صندوق الرسائل</h5>
        
        <div class="d-flex flex-wrap gap-2">
            <div class="btn-group" role="group">
                <a href="{{ route('student.messages', ['filter' => 'all']) }}" class="btn btn-sm {{ $filter === 'all' || !isset($filter) ? 'btn-primary' : 'btn-outline-primary' }}">الكل</a>
                <a href="{{ route('student.messages', ['filter' => 'sent']) }}" class="btn btn-sm {{ $filter === 'sent' ? 'btn-primary' : 'btn-outline-primary' }}">المرسلة</a>
                <a href="{{ route('student.messages', ['filter' => 'received']) }}" class="btn btn-sm {{ $filter === 'received' ? 'btn-primary' : 'btn-outline-primary' }}">الواردة</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(count($messages) > 0)
            <div class="list-group">
                @foreach($messages as $message)
                    <div class="list-group-item {{ $message->read_at ? '' : 'bg-light' }} mb-2 shadow-sm">
                        <div class="d-flex w-100 justify-content-between" 
                             data-bs-toggle="collapse" 
                             href="#messageContent{{ $message->id }}" 
                             role="button" 
                             aria-expanded="false" 
                             aria-controls="messageContent{{ $message->id }}"
                             data-message-id="{{ $message->id }}"
                             onclick="markAsRead(this, {{ $message->id }})"
                             style="cursor: pointer;">
                            <h5 class="mb-1 text-primary">
                                @if(!$message->read_at && !($message->sender_type == 'student' && $message->sender_id == $student->id))
                                    <span class="badge bg-primary me-2">جديد</span>
                                @endif
                                <i class="fas fa-chevron-down me-1"></i>
                                {{ $message->subject ?? $message->title ?? 'رسالة بدون عنوان' }}
                            </h5>
                            <small>{{ $message->created_at->format('Y-m-d') }} ({{ $message->created_at->diffForHumans() }})</small>
                        </div>
                        <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                            <small class="text-muted">
                                @php
                                    $teacherName = 'إشعار نظام';
                                    if (isset($message->sender)) {
                                        $senderClass = get_class($message->sender);
                                        if ($senderClass == 'App\\Models\\User') {
                                            $teacherName = $message->sender->name;
                                        }
                                    }
                                @endphp
                                
                                @if($message->sender_type == 'student' && $message->sender_id == $student->id)
                                    <i class="fas fa-paper-plane text-success me-1"></i> إلى: {{ $message->recipient->name ?? 'معلم' }}
                                @elseif($message->sender_type == 'teacher' || isset($message->sender))
                                    <i class="fas fa-user-tie text-primary me-1"></i> من: {{ $teacherName }}
                                @else
                                    <i class="fas fa-bell text-secondary me-1"></i> {{ $teacherName }}
                                @endif
                            </small>
                        </div>
                        
                        <div class="collapse mt-3" id="messageContent{{ $message->id }}">
                            <div class="card card-body">
                                <div class="p-2 bg-light rounded mb-3">
                                    {!! nl2br(e($message->content)) !!}
                                </div>
                                
                                @if($message->related_type && $message->related_id)
                                    <div class="alert alert-info mb-0">
                                        <div class="d-flex">
                                            <div class="me-2">
                                                <i class="fas fa-link"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">هذه الرسالة مرتبطة بـ:</h6>
                                                @if($message->related_type == 'class_session')
                                                    <p class="mb-0">
                                                        جلسة دراسية بتاريخ {{ $message->related->session_date }}
                                                        <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-outline-primary mt-1">
                                                            <i class="fas fa-calendar-check me-1"></i> عرض سجل الحضور
                                                        </a>
                                                    </p>
                                                @elseif($message->related_type == 'classroom')
                                                    <p class="mb-0">
                                                        الفصل الدراسي: {{ $message->related->name }}
                                                        <a href="{{ route('student.classrooms') }}" class="btn btn-sm btn-outline-primary mt-1">
                                                            <i class="fas fa-chalkboard me-1"></i> عرض الفصول
                                                        </a>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-3">
                {{ $messages->appends(['filter' => $filter])->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-envelope-open text-muted fa-4x mb-3"></i>
                <h5>لا توجد رسائل</h5>
                <p class="text-muted">
                    @if($filter === 'sent')
                        لا توجد رسائل مرسلة
                    @elseif($filter === 'received')
                        لا توجد رسائل واردة
                    @else
                        لا توجد أي رسائل في صندوق الرسائل الخاص بك
                    @endif
                </p>
                <a href="{{ route('student.messages.compose') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-paper-plane me-1"></i> إرسال رسالة جديدة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 

@section('scripts')
<script>
    // Global function to mark message as read - defined in the head
    window.markAsRead = function(element, messageId) {
        // Find the parent list item
        const messageItem = element.closest('.list-group-item');
        
        // Check if it's an unread message (only make API calls for unread messages)
        if (messageItem && messageItem.classList.contains('bg-light')) {
            // Mark as read in database via AJAX
            fetch('/student/messages/' + messageId + '/mark-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ _token: '{{ csrf_token() }}' }),
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Message marked as read:', data);
                
                // Remove unread styling only after successful response
                if (messageItem) {
                    // Remove unread styling
                    messageItem.classList.remove('bg-light');
                    
                    // Find and remove the "new" badge
                    const newBadge = messageItem.querySelector('.badge.bg-primary');
                    if (newBadge) {
                        newBadge.remove();
                    }
                }
            })
            .catch(error => {
                console.error('Error marking message as read:', error);
            });
        }
    };

    // When document is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Setup event listeners for BS collapse events
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(element) {
            element.addEventListener('shown.bs.collapse', function() {
                const messageId = this.getAttribute('data-message-id');
                if (messageId) {
                    markAsRead(this, messageId);
                }
            });
        });
    });
</script>
@endsection 