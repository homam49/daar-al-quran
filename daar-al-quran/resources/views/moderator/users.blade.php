@extends('layouts.moderator')

@section('moderator-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-users me-2"></i>إدارة المستخدمين</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('moderator.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> إضافة مستخدم
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة المستخدمين</h5>
                <span class="badge bg-primary">{{ count($users) }} مستخدم</span>
            </div>
            <div class="card-body">
                
                
                
                
                @if(count($users) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الدور</th>
                                    <th>الحالة</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->role->name === 'moderator')
                                            <span class="badge bg-danger">مشرف</span>
                                        @elseif($user->role->name === 'admin')
                                            <span class="badge bg-primary">مدير</span>
                                        @elseif($user->role->name === 'teacher')
                                            <span class="badge bg-success">معلم</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $user->role->name }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_approved)
                                            <span class="badge bg-success">معتمد</span>
                                        @else
                                            <span class="badge bg-warning text-dark">بانتظار الموافقة</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('moderator.users.show', $user->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(!$user->is_approved)
                                            <form method="POST" action="{{ route('moderator.users.approve', $user->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($user->role->name !== 'moderator')
                                            <form method="POST" action="{{ route('moderator.users.delete', $user->id) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-users-slash text-muted fa-3x mb-3"></i>
                        <p class="mb-0">لا يوجد مستخدمين</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 