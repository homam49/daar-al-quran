@extends('layouts.' . $layout)

@section($contentSection)
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-user-cog me-2"></i>الملف الشخصي</h4>
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
                @if(isset($user))
                <!-- Regular User Profile -->
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
                        <strong>اسم المستخدم:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $user->username }}
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
                        @if($user->role->name === 'admin')
                            <span class="badge bg-primary">مدير</span>
                        @elseif($user->role->name === 'teacher')
                            <span class="badge bg-success">معلم</span>
                        @elseif($user->role->name === 'moderator')
                            <span class="badge bg-success">مشرف</span>
                        @else
                            <span class="badge bg-secondary">{{ $user->role->name }}</span>
                        @endif
                    </div>
                </div>
                
                @if(isset($user->phone) && $user->phone)
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>رقم الهاتف:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $user->phone }}
                    </div>
                </div>
                @else
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>رقم الهاتف:</strong>
                    </div>
                    <div class="col-md-9">
                        <em class="text-muted">غير محدد</em>
                    </div>
                </div>
                @endif
                
                @if(isset($user->address) && $user->address)
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>العنوان:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $user->address }}
                    </div>
                </div>
                @else
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>العنوان:</strong>
                    </div>
                    <div class="col-md-9">
                        <em class="text-muted">غير محدد</em>
                    </div>
                </div>
                @endif
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>حالة الحساب:</strong>
                    </div>
                    <div class="col-md-9">
                        <span class="badge bg-success">معتمد</span>
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
                @elseif(isset($student))
                <!-- Student Profile -->
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>الاسم الكامل:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>اسم المستخدم:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $student->username }}
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>البريد الإلكتروني:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $student->email }}
                        @if(!$student->email_verified_at && $student->email)
                            <span class="badge bg-warning text-dark ms-2">غير مؤكد</span>
                        @elseif($student->email_verified_at)
                            <span class="badge bg-success ms-2">مؤكد</span>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>سنة الميلاد:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ date('Y') - $student->birth_year }} سنة ({{ $student->birth_year }})
                    </div>
                </div>
                
                @if(isset($student->phone) && $student->phone)
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>رقم الهاتف:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $student->phone }}
                    </div>
                </div>
                @else
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>رقم الهاتف:</strong>
                    </div>
                    <div class="col-md-9">
                        <em class="text-muted">غير محدد</em>
                    </div>
                </div>
                @endif
                
                @if(isset($student->address) && $student->address)
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>العنوان:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $student->address }}
                    </div>
                </div>
                @else
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>العنوان:</strong>
                    </div>
                    <div class="col-md-9">
                        <em class="text-muted">غير محدد</em>
                    </div>
                </div>
                @endif
                
                <!-- <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>حالة البريد الإلكتروني:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($student->email_verified_at)
                            <span class="badge bg-success">مؤكد</span>
                            <small class="text-muted">تم التأكيد في {{ $student->email_verified_at->format('Y-m-d H:i') }}</small>
                        @else
                            <span class="badge bg-warning text-dark">غير مؤكد</span>
                            @if($student->email)
                                <div class="mt-2">
                                    <form method="POST" action="{{ route('student.verification.resend') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">إعادة إرسال رابط التأكيد</button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                </div> -->
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>تاريخ التسجيل:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $student->created_at->format('Y-m-d H:i') }}
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-3 text-md-end">
                        <strong>آخر تحديث:</strong>
                    </div>
                    <div class="col-md-9">
                        {{ $student->updated_at->format('Y-m-d H:i') }}
                    </div>
                </div>
                @endif
            </div>
            <div class="card-footer d-flex gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fas fa-key me-1"></i> تغيير كلمة المرور
                </button>
                
                @if(isset($user))
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#changePersonalInfoModal">
                    <i class="fas fa-user-edit me-1"></i> تعديل البيانات الشخصية
                </button>
                @elseif(isset($student))
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#changeStudentInfoModal">
                    <i class="fas fa-user-edit me-1"></i> تعديل البيانات الشخصية
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">تغيير كلمة المرور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if(isset($user))
            <!-- User Password Form -->
            <form method="POST" action="{{ $passwordUpdateRoute }}">
                @csrf
                @if($user->role->name != 'moderator')
                    <!-- Admin and teacher use POST -->
                @else
                    <!-- Moderator uses PUT -->
                    @method('PUT')
                @endif
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                </div>
            </form>
            @elseif(isset($student))
            <!-- Student Password Form -->
            <form method="POST" action="{{ $passwordUpdateRoute }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

@if(isset($user))
<!-- Change Personal Info Modal for regular users -->
<div class="modal fade" id="changePersonalInfoModal" tabindex="-1" aria-labelledby="changePersonalInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePersonalInfoModalLabel">تعديل البيانات الشخصية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('profile.update.info') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="personal_name" class="form-label">الاسم</label>
                        <input type="text" class="form-control" id="personal_name" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="personal_phone" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control" id="personal_phone" name="phone" value="{{ $user->phone }}" placeholder="05xxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label for="personal_address" class="form-label">العنوان</label>
                        <textarea class="form-control" id="personal_address" name="address" rows="3">{{ $user->address }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="current_password_for_info" class="form-label">كلمة المرور الحالية للتأكيد</label>
                        <input type="password" class="form-control" id="current_password_for_info" name="current_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@elseif(isset($student))
<!-- Change Personal Info Modal for students -->
<div class="modal fade" id="changeStudentInfoModal" tabindex="-1" aria-labelledby="changeStudentInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStudentInfoModalLabel">تعديل البيانات الشخصية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('student.profile.info.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_phone" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="student_phone" name="phone" value="{{ $student->phone }}" placeholder="05xxxxxxxx">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="student_address" class="form-label">العنوان</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="student_address" name="address" rows="3">{{ $student->address }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="current_password" class="form-label">كلمة المرور الحالية للتأكيد</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required placeholder="أدخل كلمة المرور الحالية لتأكيد التغييرات">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Remove the individual modals for name/phone/address as they're now combined in the Personal Info modal -->
@endsection 