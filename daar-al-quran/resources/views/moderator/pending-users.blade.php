@extends('layouts.moderator')

@section('moderator-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-user-clock me-2"></i>طلبات التسجيل</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('moderator.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right me-1"></i> العودة للوحة التحكم
            </a>
        </div>
    </div>
</div>

<!-- Email verification notice card -->
@if($unverifiedUsers > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-light border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="fas fa-info-circle text-info fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="mb-2">مستخدمين بانتظار تأكيد البريد الإلكتروني</h5>
                        <p class="mb-0">
                            هناك {{ $unverifiedUsers }} مستخدم مسجل لم يقم بتأكيد بريده الإلكتروني بعد.
                            لن تظهر هذه الطلبات في القائمة أدناه حتى يتم التحقق من عنوان البريد الإلكتروني.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة طلبات التسجيل بانتظار الموافقة</h5>
                <span class="badge bg-warning text-dark">{{ count($pendingUsers) }} طلب</span>
            </div>
            <div class="card-body">
                
                
                @if(count($pendingUsers) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الدور</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingUsers as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>
                                        {{ $user->email }}
                                        <span class="badge bg-success">تم التحقق</span>
                                    </td>
                                    <td>
                                        @if($user->role->name === 'admin')
                                            <span class="badge bg-primary">مدير</span>
                                        @elseif($user->role->name === 'teacher')
                                            <span class="badge bg-success">معلم</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $user->role->name }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('moderator.users.show', $user->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('moderator.users.approve', $user->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> موافقة
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('moderator.users.reject', $user->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من رفض هذا المستخدم؟')">
                                                <i class="fas fa-times"></i> رفض
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="mb-0">لا توجد طلبات تسجيل جديدة بانتظار الموافقة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 