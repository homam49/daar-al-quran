@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="sidebar p-3">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('img/logo.png') }}" alt="دار القرآن" class="me-2" style="height: 40px;">
                    <h5 class="mb-0 fw-bold">لوحة تحكم المعلم</h5>
                </div>
                <div class="nav flex-column">
                    <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> الرئيسية
                    </a>
                    <a href="{{ route('teacher.join-school.form') }}" class="nav-link {{ request()->routeIs('teacher.join-school.form') ? 'active' : '' }}">
                        <i class="fas fa-school"></i> الانضمام إلى مدرسة
                    </a>
                    <a href="{{ route('teacher.schools') }}" class="nav-link {{ request()->routeIs('teacher.schools') ? 'active' : '' }}">
                        <i class="fas fa-building"></i> المدارس
                    </a>
                    <a href="{{ route('classrooms.index') }}" class="nav-link {{ request()->routeIs('classrooms.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard"></i> الفصول الدراسية
                    </a>
                    <a href="{{ route('teacher.students.index') }}" class="nav-link {{ request()->routeIs('teacher.students.*') || request()->routeIs('memorization.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> الطلاب والحفظ
                    </a>
                    <a href="{{ route('teacher.messages') }}" class="nav-link {{ request()->routeIs('teacher.messages*') ? 'active' : '' }}">
                        <i class="fas fa-envelope"></i> الرسائل
                        @php
                            $unreadCount = \App\Models\Message::where('recipient_id', Auth::id())
                                ->where('sender_type', 'student')
                                ->whereNull('read_at')
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('teacher.profile') }}" class="nav-link {{ request()->routeIs('teacher.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i> الملف الشخصي
                    </a>
                    
                    <hr class="my-2">
                    
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            @yield('teacher-content')
        </div>
    </div>
</div>
@endsection 