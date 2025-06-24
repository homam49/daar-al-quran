@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>لوحة التحكم</h4>
        </div>
        <div class="col-auto">
            <span class="text-muted">{{ \Carbon\Carbon::now()->format('Y-m-d') }}</span>
        </div>
    </div>
</div>

<div class="row dashboard-stats">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-primary mb-2">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <h3 class="font-weight-bold">{{ $classrooms_count }}</h3>
                <p class="mb-0">عدد الفصول</p>
            </div>
            <div class="card-footer bg-primary text-white text-center py-2">
                <a href="{{ route('classrooms.index') }}" class="text-white text-decoration-none">
                    <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-success mb-2">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3 class="font-weight-bold">{{ $students_count }}</h3>
                <p class="mb-0">عدد الطلاب</p>
            </div>
            <div class="card-footer bg-success text-white text-center py-2">
                                        <a href="{{ route('teacher.students.index') }}" class="text-white text-decoration-none">
                    <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-warning mb-2">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="font-weight-bold">{{ $sessions_count }}</h3>
                <p class="mb-0">عدد الجلسات</p>
            </div>
            <div class="card-footer bg-warning text-dark text-center py-2">
                <a href="{{ route('sessions.index') }}" class="text-dark text-decoration-none">
                    <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-info mb-2">
                    <i class="fas fa-school"></i>
                </div>
                <h3 class="font-weight-bold">{{ $schools_count }}</h3>
                <p class="mb-0">عدد المدارس</p>
            </div>
            <div class="card-footer bg-info text-white text-center py-2">
                <a href="{{ route('teacher.schools') }}" class="text-white text-decoration-none">
                    <small>عرض التفاصيل <i class="fas fa-arrow-left ms-1"></i></small>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Memorization Statistics Row -->
<div class="row mt-4">
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-quran me-2"></i>إحصائيات حفظ القرآن الكريم</h5>
            </div>
            <!-- <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="bg-success text-white rounded p-3">
                            <h3 class="mb-1">{{ $memorization_stats['total_memorized'] ?? 0 }}</h3>
                            <p class="mb-0">محفوظ إجمالي</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="bg-warning text-white rounded p-3">
                            <h3 class="mb-1">{{ $memorization_stats['in_progress'] ?? 0 }}</h3>
                            <p class="mb-0">قيد الحفظ</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="bg-info text-white rounded p-3">
                            <h3 class="mb-1">{{ $memorization_stats['total_content_items'] ?? 0 }}</h3>
                            <p class="mb-0">صفحة وسورة مُسجلة</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="bg-primary text-white rounded p-3">
                            <h3 class="mb-1">{{ $memorization_stats['total_students_tracking'] ?? 0 }}</h3>
                            <p class="mb-0">طالب يتم متابعة حفظه</p>
                        </div>
                    </div>
                </div>
                
                <div class="row text-center">
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-file-text me-1"></i>إحصائيات الصفحات</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <h4 class="text-success">{{ $memorization_stats['pages_memorized'] ?? 0 }}</h4>
                                        <small>صفحة محفوظة</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-warning">{{ $memorization_stats['pages_in_progress'] ?? 0 }}</h4>
                                        <small>صفحة قيد الحفظ</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-book me-1"></i>إحصائيات السور</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <h4 class="text-success">{{ $memorization_stats['surahs_memorized'] ?? 0 }}</h4>
                                        <small>سورة محفوظة</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-warning">{{ $memorization_stats['surahs_in_progress'] ?? 0 }}</h4>
                                        <small>سورة قيد الحفظ</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($students_count > 0)
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        يمكنك متابعة حفظ طلابك من قائمة الطلاب في كل فصل
                    </small>
                </div>
                @endif
            </div> -->
        </div>
    </div>
</div>

@if(isset($pendingSchools) && $pendingSchools->count() > 0)
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>طلبات الانضمام للمدارس المعلقة</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <p><i class="fas fa-info-circle me-2"></i>لديك {{ $pendingSchools->count() }} طلب انضمام للمدارس في انتظار موافقة المدير. سيتم إشعارك عند الموافقة على طلبك.</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المدرسة</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingSchools as $index => $school)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $school->name }}</td>
                                <td><span class="badge bg-warning text-dark">في انتظار الموافقة</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i>جلسات هذا اليوم</h5>
                <span class="badge bg-primary">{{ $today_sessions->count() }} جلسة</span>
            </div>
            <div class="card-body">
                @if(count($today_sessions) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الفصل</th>
                                    <th>وقت البدء</th>
                                    <th>وقت الانتهاء</th>
                                    <th>المدرسة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($today_sessions as $session)
                                <tr>
                                    <td>{{ $session->classRoom->name }}</td>
                                    <td>{{ $session->formatted_start_time ?? $session->start_time }}</td>
                                    <td>{{ $session->formatted_end_time ?? $session->end_time }}</td>
                                    <td>{{ $session->classRoom->school->name }}</td>
                                    <td>
                                        <a href="{{ route('classroom.sessions.show', ['classroom' => $session->class_room_id, 'session' => $session->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('classroom.sessions.edit', ['classroom' => $session->class_room_id, 'session' => $session->id]) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-user-check"></i> الحضور
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times text-muted fa-3x mb-3"></i>
                        <p class="mb-0">لا توجد جلسات لهذا اليوم</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>أحدث الرسائل</h5>
                <a href="{{ route('teacher.messages') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                @php
                    // Use the existing $messages or fetch it from the controller
                    $fetchedMessages = App\Models\Message::where(function($query) {
                        $query->where('sender_id', Auth::id())
                              ->where('sender_type', 'teacher');
                    })->orWhere(function($query) {
                        $query->where('recipient_id', Auth::id())
                              ->where('sender_type', 'student');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
                    // Group class messages by class_room_id, subject, content and created_at
                    $recentMessages = collect();
                    $groupedClassMessages = [];
                    
                    // Process messages to deduplicate class messages
                    foreach ($fetchedMessages as $message) {
                        if ($message->sender_type === 'teacher' && $message->type === 'class') {
                            // Create a unique key for this class message
                            $key = $message->class_room_id . '_' . $message->subject . '_' . 
                                   md5($message->content) . '_' . $message->created_at->format('Y-m-d H:i:s');
                            
                            // Only add this class message if we haven't seen it before
                            if (!isset($groupedClassMessages[$key])) {
                                $groupedClassMessages[$key] = $message;
                                $recentMessages->push($message);
                            }
                        } else {
                            // For personal messages and received messages, add them all
                            $recentMessages->push($message);
                        }
                    }
                    
                    // Get only the most recent 2 messages after deduplication
                    $latestMessages = $recentMessages->sortByDesc('created_at')->take(2);
                @endphp
                
                @if($latestMessages->count() > 0)
                    <div class="list-group">
                        @foreach($latestMessages as $message)
                            <div class="list-group-item {{ $message->is_read || $message->read_at ? '' : 'bg-light' }} mb-2">
                                <div class="d-flex w-100 justify-content-between" 
                                     data-bs-toggle="collapse" 
                                     href="#dashboardMessageContent{{ $message->id }}" 
                                     role="button" 
                                     aria-expanded="false" 
                                     aria-controls="dashboardMessageContent{{ $message->id }}"
                                     style="cursor: pointer;">
                                    <h6 class="mb-1 text-primary">
                                        @if(!$message->is_read && $message->sender_type == 'student')
                                            <span class="badge bg-primary me-2">جديد</span>
                                        @endif
                                        @if($message->sender_type === 'teacher' && $message->sender_id === auth()->id())
                                            <span class="badge bg-success me-2">مرسلة</span>
                                        @endif
                                        <i class="fas fa-chevron-down me-1"></i>
                                        {{ $message->subject ?? 'بدون عنوان' }}
                                    </h6>
                                    <small>{{ $message->created_at->diffForHumans() }}</small>
                                </div>
                                <small class="text-muted">
                                    @if($message->sender_type === 'teacher' && $message->sender_id === auth()->id())
                                        @if($message->type === 'personal' && $message->student)
                                            <i class="fas fa-paper-plane text-success me-1"></i> إلى: {{ $message->student->full_name ?? 'الطالب المحدد' }}
                                        @elseif($message->type === 'class' && $message->classRoom)
                                            <i class="fas fa-users text-success me-1"></i> إلى: كل طلاب {{ $message->classRoom->name ?? 'الفصل' }}
                                        @endif
                                    @elseif($message->sender_type === 'student' && $message->sender)
                                        <i class="fas fa-user-graduate text-info me-1"></i> من: {{ $message->sender->full_name ?? 'طالب' }}
                                    @endif
                                </small>
                                
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
            <div class="card-footer text-center">
                <a href="{{ route('teacher.messages') }}" class="btn btn-sm btn-outline-primary">عرض جميع الرسائل</a>
                <a href="{{ route('teacher.messages.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> إرسال رسالة
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>آخر الجلسات</h5>
            </div>
            <div class="card-body">
                @if(count($recent_sessions) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الفصل</th>
                                    <th>التاريخ</th>
                                    <th>وقت البدء</th>
                                    <th>وقت الانتهاء</th>
                                    <th>عدد الحضور</th>
                                    <th>نسبة الحضور</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_sessions as $session)
                                <tr>
                                    <td>{{ $session->classRoom->name }}</td>
                                    <td>{{ $session->formatted_date ?? $session->session_date }}</td>
                                    <td>{{ $session->formatted_start_time ?? $session->start_time }}</td>
                                    <td>{{ $session->formatted_end_time ?? $session->end_time }}</td>
                                    <td>{{ $session->attendance_count }} / {{ $session->classRoom->students->count() }}</td>
                                    <td>
                                        @php
                                            $attendancePercentage = $session->classRoom->students->count() > 0
                                                ? round(($session->attendance_count / $session->classRoom->students->count()) * 100)
                                                : 0;
                                        @endphp
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendancePercentage }}%"></div>
                                        </div>
                                        <small>{{ $attendancePercentage }}%</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('classroom.sessions.show', ['classroom' => $session->class_room_id, 'session' => $session->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history text-muted fa-3x mb-3"></i>
                        <p class="mb-0">لا توجد جلسات سابقة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 