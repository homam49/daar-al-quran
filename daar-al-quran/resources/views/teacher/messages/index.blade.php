@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-envelope"></i></div>
        الرسائل
    </h1>
    <a href="{{ route('teacher.messages.create') }}" class="btn btn-primary">
        <i class="fas fa-paper-plane me-1"></i> إرسال رسالة جديدة
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">صندوق الرسائل</h5>
        <div class="btn-group" role="group">
            <a href="{{ route('teacher.messages', ['filter' => 'all']) }}" class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">الكل</a>
            <a href="{{ route('teacher.messages', ['filter' => 'sent']) }}" class="btn btn-sm {{ $filter === 'sent' ? 'btn-primary' : 'btn-outline-primary' }}">المرسلة</a>
            <a href="{{ route('teacher.messages', ['filter' => 'received']) }}" class="btn btn-sm {{ $filter === 'received' ? 'btn-primary' : 'btn-outline-primary' }}">الواردة</a>
        </div>
    </div>
    
    <div class="card-body">
        @if(count($messages) > 0)
            <div class="list-group">
                @foreach($messages as $message)
                    <div class="list-group-item mb-3 shadow-sm">
                        <div class="d-flex w-100 justify-content-between message-header" 
                             data-message-id="{{ $message->id }}"
                             data-bs-toggle="collapse" 
                             href="#messageContent{{ $message->id }}" 
                             role="button" 
                             aria-expanded="false" 
                             aria-controls="messageContent{{ $message->id }}"
                             style="cursor: pointer;"
                             onclick="markAsRead({{ $message->id }})">
                            <h5 class="mb-1 text-primary">
                                @if(!$message->read_at && $message->sender_type == 'student' && $message->recipient_id == auth()->id())
                                    <span class="badge bg-primary me-2">جديد</span>
                                @endif
                                <i class="fas fa-chevron-down me-1"></i>
                                {{ $message->subject ?? 'بدون عنوان' }}
                            </h5>
                            <small>{{ $message->created_at->format('Y-m-d') }} ({{ $message->created_at->diffForHumans() }})</small>
                        </div>
                        
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            <small class="text-muted">
                                @if($message->sender_type === 'teacher' && $message->sender_id === auth()->id())
                                    @if($message->type === 'personal' && $message->student)
                                        <i class="fas fa-paper-plane text-success me-1"></i> إلى: {{ $message->student->name ?? 'الطالب المحدد' }}
                                    @elseif($message->type === 'class' && $message->classRoom)
                                        @php
                                            $studentCount = $message->classRoom->students()->count();
                                        @endphp
                                        <i class="fas fa-users text-success me-1"></i> إلى: كل طلاب {{ $message->classRoom->name ?? 'الفصل' }} ({{ $studentCount }} طالب)
                                    @endif
                                @elseif($message->sender_type === 'student' && $message->sender)
                                                                          <i class="fas fa-user-graduate text-info me-1"></i> من: {{ $message->sender->name ?? 'طالب' }}
                                @endif
                            </small>
                            
                            <!-- @if($message->type === 'class')
                                <span class="badge bg-secondary">إعلان فصل</span>
                            @endif -->
                        </div>
                        
                        <div class="collapse mt-3" id="messageContent{{ $message->id }}">
                            <div class="card card-body">
                                <div class="p-3 bg-light rounded mb-3">
                                    {!! nl2br(e($message->content)) !!}
                                </div>
                                
                                @if($message->sender_type === 'student')
                                    <div class="text-start">
                                        <a href="{{ route('teacher.messages.reply', $message->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-reply me-1"></i> الرد
                                        </a>
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
                        لا توجد رسائل في صندوق الرسائل الخاص بك
                    @endif
                </p>
                <a href="{{ route('teacher.messages.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-paper-plane me-1"></i> إرسال رسالة جديدة
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function markAsRead(messageId) {
    // Find the message header and badge elements
    const messageHeader = document.querySelector(`[data-message-id="${messageId}"]`);
    const newBadge = messageHeader?.querySelector('.badge.bg-primary');
    
    // Only proceed if there's a "جديد" badge to remove
    if (!newBadge || newBadge.textContent.trim() !== 'جديد') {
        return;
    }
    
    // Mark as read in database via AJAX
    fetch('/teacher/messages/' + messageId + '/mark-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Remove the "جديد" badge with smooth animation
            newBadge.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            newBadge.style.opacity = '0';
            newBadge.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                newBadge.remove();
            }, 300);
            
            // Update the sidebar notification badge count
            updateSidebarNotificationCount();
        }
    })
    .catch(error => {
        console.error('Error marking message as read:', error);
    });
}

function updateSidebarNotificationCount() {
    // Find the sidebar notification badge
    const sidebarBadge = document.querySelector('.nav-link[href*="teacher.messages"] .badge.bg-danger');
    
    if (sidebarBadge) {
        const currentCount = parseInt(sidebarBadge.textContent.trim()) || 0;
        const newCount = Math.max(0, currentCount - 1);
        
        if (newCount === 0) {
            // Remove the badge entirely if count reaches 0
            sidebarBadge.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            sidebarBadge.style.opacity = '0';
            sidebarBadge.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                sidebarBadge.remove();
            }, 300);
        } else {
            // Update the count with a subtle animation
            sidebarBadge.style.transition = 'transform 0.2s ease-out';
            sidebarBadge.style.transform = 'scale(1.2)';
            sidebarBadge.textContent = newCount;
            
            setTimeout(() => {
                sidebarBadge.style.transform = 'scale(1)';
            }, 200);
        }
    }
}
</script>
@endsection 