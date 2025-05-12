@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="sidebar p-3">
                <h5 class="mb-3 fw-bold">لوحة تحكم المشرف</h5>
                <div class="nav flex-column">
                    <a href="{{ route('moderator.dashboard') }}" class="nav-link {{ request()->routeIs('moderator.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> الرئيسية
                    </a>
                    <a href="{{ route('moderator.users') }}" class="nav-link {{ request()->routeIs('moderator.users*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i> إدارة المستخدمين
                    </a>
                    <a href="{{ route('moderator.pending-users') }}" class="nav-link {{ request()->routeIs('moderator.pending-users*') ? 'active' : '' }}">
                        <i class="fas fa-user-clock"></i> طلبات التسجيل
                    </a>
                    <a href="{{ route('moderator.schools') }}" class="nav-link {{ request()->routeIs('moderator.schools*') ? 'active' : '' }}">
                        <i class="fas fa-school"></i> إدارة المدارس
                    </a>
                    <a href="{{ route('moderator.profile') }}" class="nav-link {{ request()->routeIs('moderator.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-cog"></i> الملف الشخصي
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            @yield('moderator-content')
        </div>
    </div>
</div>
@endsection 