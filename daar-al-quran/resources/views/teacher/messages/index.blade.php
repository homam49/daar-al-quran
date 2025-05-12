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
                                <i class="fas fa-chevron-down me-1"></i>
                                {{ $message->subject ?? 'بدون عنوان' }}
                            </h5>
                            <small>{{ $message->created_at->format('Y-m-d') }} ({{ $message->created_at->diffForHumans() }})</small>
                        </div>
                        
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            <small class="text-muted">
                                @if($message->sender_type === 'teacher' && $message->sender_id === auth()->id())
                                    @if($message->type === 'personal' && $message->student)
                                        <i class="fas fa-paper-plane text-success me-1"></i> إلى: {{ $message->student->full_name ?? 'الطالب المحدد' }}
                                    @elseif($message->type === 'class' && $message->classRoom)
                                        @php
                                            $studentCount = $message->classRoom->students()->count();
                                        @endphp
                                        <i class="fas fa-users text-success me-1"></i> إلى: كل طلاب {{ $message->classRoom->name ?? 'الفصل' }} ({{ $studentCount }} طالب)
                                    @endif
                                @elseif($message->sender_type === 'student' && $message->sender)
                                    <i class="fas fa-user-graduate text-info me-1"></i> من: {{ $message->sender->full_name ?? 'طالب' }}
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
    // Mark as read in database via AJAX
    fetch('/teacher/messages/' + messageId + '/mark-read', {
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
    })
    .catch(error => {
        console.error('Error marking message as read:', error);
    });
}
</script>
@endsection 