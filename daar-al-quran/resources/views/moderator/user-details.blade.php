@extends('layouts.moderator')

@section('moderator-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-user me-2"></i>تفاصيل المستخدم</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('moderator.users') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right me-1"></i> العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">معلومات المستخدم</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>الاسم:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $user->name }}
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>البريد الإلكتروني:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $user->email }}
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>الدور:</strong>
                    </div>
                    <div class="col-md-9">
                        <span class="badge bg-info">{{ $user->role->name }}</span>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>حالة الحساب:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($user->is_approved)
                            <span class="badge bg-success">معتمد</span>
                        @else
                            <span class="badge bg-warning text-dark">بانتظار الموافقة</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>تاريخ التسجيل:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $user->created_at->format('Y-m-d H:i') }}
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>آخر تحديث:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $user->updated_at->format('Y-m-d H:i') }}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <div>
                        @if(!$user->is_approved && $user->role->name != 'moderator')
                            <form method="POST" action="{{ route('moderator.users.approve', $user->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-1"></i> الموافقة
                                </button>
                            </form>
                        @endif
                    </div>
                    <div>
                        @if($user->role->name != 'moderator')
                            <form method="POST" action="{{ route('moderator.users.delete', $user->id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                    <i class="fas fa-trash-alt me-1"></i> حذف المستخدم
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 