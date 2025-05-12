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
                    <a href="{{ route('teacher.messages') }}" class="nav-link {{ request()->routeIs('teacher.messages*') ? 'active' : '' }}">
                        <i class="fas fa-envelope"></i> الرسائل
                    </a>
                    <a href="{{ route('teacher.profile') }}" class="nav-link {{ request()->routeIs('teacher.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i> الملف الشخصي
                    </a>
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