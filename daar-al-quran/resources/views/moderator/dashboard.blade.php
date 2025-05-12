@extends('layouts.moderator')

@section('moderator-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>لوحة التحكم</h4>
        </div>
    </div>
</div>

<div class="row dashboard-stats">
    <!-- Schools Card -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-primary mb-2">
                    <i class="fas fa-school"></i>
                </div>
                <h3 class="font-weight-bold">{{ $schoolsCount }}</h3>
                <p class="mb-0">المدارس</p>
            </div>
            <div class="card-footer bg-primary text-white text-center py-2">
                <a href="{{ route('moderator.schools') }}" class="text-white text-decoration-none">عرض التفاصيل</a>
            </div>
        </div>
    </div>
    
    <!-- Unverified Users Card -->
    <!-- <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-box bg-info text-white me-3">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h5 class="mb-0">بانتظار التحقق</h5>
                </div>
                <h3 class="mb-3">{{ $unverifiedUsers }}</h3>
                <p class="text-muted">مستخدمين بانتظار تأكيد البريد الإلكتروني</p>
            </div>
        </div>
    </div> -->
    
    <!-- Registration Requests Card -->

    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-warning mb-2">
                    <i class="fas fa-user-clock"></i>
                </div>
                <h3 class="font-weight-bold">{{ $pendingUsers }}</h3>
                <p class="mb-0">طلبات التسجيل</p>
            </div>
            <div class="card-footer bg-warning text-white text-center py-2">
                <a href="{{ route('moderator.pending-users') }}" class="text-white text-decoration-none">عرض التفاصيل</a>
            </div>
        </div>
    </div>
    <!-- Total Users Card -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card bg-white text-dark h-100">
            <div class="card-body text-center">
                <div class="display-4 text-success mb-2">
                    <i class="fas fa-user-clock"></i>
                </div>
                <h3 class="font-weight-bold">{{ $totalUsers }}</h3>
                <p class="mb-0">المستخدمين</p>
            </div>
            <div class="card-footer bg-success text-white text-center py-2">
                <a href="{{ route('moderator.users') }}" class="text-white text-decoration-none">عرض التفاصيل</a>
            </div>
        </div>
    </div>
</div>

@endsection 