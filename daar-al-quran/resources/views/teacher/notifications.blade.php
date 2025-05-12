@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-bell"></i></div>
        التنبيهات
    </h1>
</div>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">جميع التنبيهات</h5>
        
        <div class="d-flex">
            <div class="dropdown me-2">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter me-1"></i> تصفية
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item {{ request('type') == '' || !request('type') ? 'active' : '' }}" href="{{ route('teacher.notifications', ['type' => '']) }}">كل التنبيهات</a></li>
                    <li><a class="dropdown-item {{ request('type') == 'message' ? 'active' : '' }}" href="{{ route('teacher.notifications', ['type' => 'message']) }}">رسائل</a></li>
                    <li><a class="dropdown-item {{ request('type') == 'attendance' ? 'active' : '' }}" href="{{ route('teacher.notifications', ['type' => 'attendance']) }}">حضور</a></li>
                    <li><a class="dropdown-item {{ request('type') == 'system' ? 'active' : '' }}" href="{{ route('teacher.notifications', ['type' => 'system']) }}">النظام</a></li>
                </ul>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-sort me-1"></i> ترتيب
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li><a class="dropdown-item {{ request('sort') != 'oldest' ? 'active' : '' }}" href="{{ route('teacher.notifications', ['sort' => 'newest', 'type' => request('type')]) }}">الأحدث أولاً</a></li>
                    <li><a class="dropdown-item {{ request('sort') == 'oldest' ? 'active' : '' }}" href="{{ route('teacher.notifications', ['sort' => 'oldest', 'type' => request('type')]) }}">الأقدم أولاً</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        @if(count($notifications) > 0)
            <div class="list-group">
                @foreach($notifications as $notification)
                    <div class="list-group-item notification-item py-3">
                        <div class="d-flex">
                            <div class="notification-icon me-3">
                                @if($notification->type == 'message')
                                    <div class="rounded-circle bg-info text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                @elseif($notification->type == 'attendance')
                                    <div class="rounded-circle bg-warning text-dark p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                @elseif($notification->type == 'system')
                                    <div class="rounded-circle bg-secondary text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                @else
                                    <div class="rounded-circle bg-primary text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1">
                                        @if($notification->type == 'message')
                                            <span class="badge bg-info text-white me-2">رسالة</span> 
                                        @elseif($notification->type == 'attendance')
                                            <span class="badge bg-warning text-dark me-2">حضور</span>
                                        @elseif($notification->type == 'system')
                                            <span class="badge bg-secondary me-2">النظام</span>
                                        @else
                                            <span class="badge bg-primary me-2">تنبيه</span>
                                        @endif
                                        
                                        {{ $notification->message }}
                                    </h6>
                                    <small class="text-muted">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-bell-slash text-muted fa-4x mb-3"></i>
                <h5>لا توجد تنبيهات</h5>
                <p class="text-muted">ستظهر هنا التنبيهات والإشعارات الخاصة بك</p>
            </div>
        @endif
    </div>
</div>
@endsection 