@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="sidebar p-3">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ asset('img/logo.png') }}" alt="دار القرآن" class="me-2" style="height: 40px;">
                    <h5 class="mb-0 fw-bold">لوحة تحكم المسؤول</h5>
                </div>
                <div class="nav flex-column">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> الرئيسية
                    </a>
                    <a href="{{ route('admin.teachers') }}" class="nav-link {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher"></i> إدارة المعلمين
                    </a>
                    <a href="{{ route('admin.students') }}" class="nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                        <i class="fas fa-user-graduate"></i> إدارة الطلاب
                    </a>
                    <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i> الملف الشخصي
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            @yield('admin-content')
        </div>
    </div>
</div>
@endsection 