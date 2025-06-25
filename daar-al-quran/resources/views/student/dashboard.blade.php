@extends('layouts.student')

@section('student-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>لوحة معلومات الطالب</h4>
        </div>
        <div class="col-auto">
            <span class="text-muted">{{ \Carbon\Carbon::now()->format('Y-m-d') }}</span>
        </div>
    </div>
</div>

<div class="row">
    <!-- <div class="col-lg-4 mb-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="mb-3">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <i class="fas fa-user-graduate fa-3x text-primary"></i>
                    </div>
                    <h5>{{ $student->name }}</h5>
                    <p class="text-muted mb-0">{{ $student->username }}</p>
                </div>
                <hr>
                <div class="row text-start">
                    <div class="col-6 mb-2">
                        <small class="text-muted d-block">المدرسة:</small>
                        <span>{{ $student->school->name }}</span>
                    </div>
                    <div class="col-6 mb-2">
                        <small class="text-muted d-block">عدد الفصول:</small>
                        <span>{{ $student->classRooms->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div class="col-lg-12 mb-4">
        <div class="row dashboard-stats">
            <div class="col-md-3 mb-4">
                <div class="card bg-white text-dark h-100">
                    <div class="card-body text-center">
                        <div class="display-4 text-success mb-2">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="font-weight-bold">{{ $attendance_count }}</h3>
                        <p class="mb-0">الحضور</p>
                    </div>
                    <div class="card-footer bg-success text-white text-center py-2">
                        <a href="{{ route('student.attendance') }}" class="text-white text-decoration-none">
                            <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card bg-white text-dark h-100">
                    <div class="card-body text-center">
                        <div class="display-4 text-primary mb-2">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <h3 class="font-weight-bold">{{ $classroom_count }}</h3>
                        <p class="mb-0">الفصول</p>
                    </div>
                    <div class="card-footer bg-primary text-white text-center py-2">
                        <a href="{{ route('student.classrooms') }}" class="text-white text-decoration-none">
                            <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card bg-white text-dark h-100">
                    <div class="card-body text-center">
                        <div class="display-4 text-info mb-2">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="font-weight-bold">{{ $unread_messages }}</h3>
                        <p class="mb-0">رسائل جديدة</p>
                    </div>
                    <div class="card-footer bg-info text-white text-center py-2">
                        <a href="{{ route('student.messages') }}" class="text-white text-decoration-none">
                            <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card bg-white text-dark h-100">
                    <div class="card-body text-center">
                        <div class="display-4 text-warning mb-2">
                            <i class="fas fa-quran"></i>
                        </div>
                        <h3 class="font-weight-bold">
                            @php
                                // Calculate memorization progress
                                $memorizedCount = 0;
                                if (isset($student)) {
                                    $memorizedCount = \App\Models\MemorizationProgress::where('student_id', $student->id)
                                        ->where('status', 'memorized')
                                        ->count();
                                }
                            @endphp
                            {{ $memorizedCount }}
                        </h3>
                        <p class="mb-0">سجل الحفظ</p>
                    </div>
                    <div class="card-footer bg-warning text-white text-center py-2">
                        <a href="{{ route('student.memorization') }}" class="text-white text-decoration-none">
                            <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>معدل الحضور</h5>
            </div>
            <div class="card-body">
                <div class="attendance-chart mb-3">
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendance_percentage }}%;" aria-valuenow="{{ $attendance_percentage }}" aria-valuemin="0" aria-valuemax="100">{{ $attendance_percentage }}%</div>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="attendance-present">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h6>حاضر</h6>
                        <h5>{{ $present_count }}</h5>
                    </div>
                    <div class="col-4">
                        <div class="attendance-late">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h6>متأخر</h6>
                        <h5>{{ $late_count }}</h5>
                    </div>
                    <div class="col-4">
                        <div class="attendance-absent">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <h6>غائب</h6>
                        <h5>{{ $absent_count }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i>جلسات اليوم</h5>
                <span class="badge bg-primary">{{ $today_sessions->count() }}</span>
            </div>
            <div class="card-body">
                @if(count($today_sessions) > 0)
                    <div class="list-group">
                        @foreach($today_sessions as $session)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $session->classRoom ? $session->classRoom->name : 'فصل غير معروف' }}</h6>
                                    <small class="time-display">{{ $session->start_time }} - {{ $session->end_time }}</small>
                                </div>
                                <div class="d-flex w-100 justify-content-between">
                                    <p class="mb-1">{{ $session->classRoom && $session->classRoom->school ? $session->classRoom->school->name : 'مدرسة غير معروفة' }}</p>
                                    <small>المعلم: {{ $session->classRoom && $session->classRoom->teacher ? $session->classRoom->teacher->name : 'معلم غير معروف' }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times text-muted fa-3x mb-3"></i>
                        <p class="mb-0">لا توجد جلسات لهذا اليوم</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>أحدث الرسائل</h5>
                <a href="{{ route('student.messages') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                @if(count($messages) > 0)
                    <div class="list-group">
                        @foreach($messages as $message)
                            @php
                                // Get the proper teacher name based on the actual class
                                $teacherName = 'تطبيق دار القرآن';
                                
                                if (isset($message->sender)) {
                                    $senderClass = get_class($message->sender);
                                    if ($senderClass == 'App\\Models\\User') {
                                        // Direct User model
                                        $teacherName = $message->sender->name;
                                    } 
                                }
                            @endphp
                            <div class="list-group-item {{ $message->read_at ? '' : 'bg-light' }} mb-2">
                                <div class="d-flex w-100 justify-content-between" 
                                     data-message-id="{{ $message->id }}"
                                     data-bs-toggle="collapse" 
                                     href="#dashboardMessageContent{{ $message->id }}" 
                                     role="button" 
                                     aria-expanded="false" 
                                     aria-controls="dashboardMessageContent{{ $message->id }}"
                                     style="cursor: pointer;"
                                     onclick="markDashboardMessageAsRead({{ $message->id }})">
                                    <h6 class="mb-1 text-primary">
                                        @if(!$message->read_at)
                                            <span class="badge bg-primary me-2">جديد</span>
                                        @endif
                                        <i class="fas fa-chevron-down me-1"></i>
                                        {{ $message->subject ?? $message->title ?? 'رسالة بدون عنوان' }}
                                    </h6>
                                    <small>{{ $message->created_at->diffForHumans() }}</small>
                                </div>
                                <small>من: {{ $teacherName }}</small>
                                
                                <div class="collapse mt-2" id="dashboardMessageContent{{ $message->id }}">
                                    <div class="card card-body p-2 bg-light">
                                        <div class="mb-2">
                                            {!! nl2br(e($message->content)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-envelope-open-text text-muted fa-3x mb-3"></i>
                        <p class="mb-0">لا توجد رسائل حديثة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> -->
@endsection 

<script>
function markDashboardMessageAsRead(messageId) {
    // Find the message header and badge elements
    const messageHeader = document.querySelector(`[data-message-id="${messageId}"]`);
    const newBadge = messageHeader?.querySelector('.badge.bg-primary');
    
    // Only proceed if there's a "جديد" badge to remove
    if (!newBadge || newBadge.textContent.trim() !== 'جديد') {
        return;
    }
    
    // Mark as read in database via AJAX
    fetch('/student/messages/' + messageId + '/mark-read', {
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
            throw new Error('Network response was not ok');
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
            
            // Remove the light background from the message item
            const messageItem = messageHeader.closest('.list-group-item');
            if (messageItem && messageItem.classList.contains('bg-light')) {
                messageItem.classList.remove('bg-light');
            }
            
            // Update the sidebar notification badge count
            updateSidebarNotificationCount();
            
            // Update the dashboard unread messages counter
            updateDashboardUnreadCounter();
            
            console.log('Dashboard message marked as read:', data);
        }
    })
    .catch(error => {
        console.error('Error marking dashboard message as read:', error);
    });
}

function updateSidebarNotificationCount() {
    // Find the sidebar notification badge
    const sidebarBadge = document.querySelector('.nav-link[href*="student.messages"] .badge.bg-danger');
    
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

function updateDashboardUnreadCounter() {
    // Find the unread messages counter in the dashboard
    const unreadCounter = document.querySelector('h3.font-weight-bold');
    if (unreadCounter) {
        const currentCount = parseInt(unreadCounter.textContent.trim()) || 0;
        const newCount = Math.max(0, currentCount - 1);
        
        // Update with animation
        unreadCounter.style.transition = 'transform 0.2s ease-out';
        unreadCounter.style.transform = 'scale(1.1)';
        unreadCounter.textContent = newCount;
        
        setTimeout(() => {
            unreadCounter.style.transform = 'scale(1)';
        }, 200);
    }
}
</script> 