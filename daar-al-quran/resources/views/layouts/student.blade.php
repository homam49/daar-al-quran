@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="sidebar p-3">
                <div class="d-flex align-items-center mb-4">
                    <img src="{{ asset('img/logo.png') }}" alt="دار القرآن" class="me-2" style="height: 40px;">
                    <div>
                        <h5 class="mb-0 fw-bold">صفحة الطالب</h5>
                        @if(Auth::guard('student')->check())
                            <small class="text-muted">{{ Auth::guard('student')->user()->first_name }} {{ Auth::guard('student')->user()->last_name }}</small>
                        @endif
                    </div>
                </div>
                <div class="nav flex-column">
                    <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt me-2"></i> الرئيسية
                    </a>
                    <a href="{{ route('student.attendance') }}" class="nav-link {{ request()->routeIs('student.attendance*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check me-2"></i> سجل الحضور
                    </a>
                    <a href="{{ route('student.memorization') }}" class="nav-link {{ request()->routeIs('student.memorization*') ? 'active' : '' }}">
                        <i class="fas fa-book-quran me-2"></i> سجل الحفظ
                    </a>
                    <a href="{{ route('student.messages') }}" class="nav-link {{ request()->routeIs('student.messages*') ? 'active' : '' }}">
                        <i class="fas fa-envelope me-2"></i> الرسائل
                        @if(Auth::guard('student')->check() && Auth::guard('student')->user()->messages()->whereNull('read_at')->count() > 0)
                            <span class="badge bg-danger rounded-pill ms-1">{{ Auth::guard('student')->user()->messages()->whereNull('read_at')->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('student.classrooms') }}" class="nav-link {{ request()->routeIs('student.classrooms*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard me-2"></i> الفصول الدراسية
                    </a>
                    <a href="{{ route('student.profile') }}" class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-cog me-2"></i> الملف الشخصي
                    </a>
                    
                    <hr class="my-2">
                    
                    <form action="{{ route('student.logout') }}" method="POST" class="mt-2">
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
            @yield('student-content')
        </div>
    </div>
</div>
@endsection 