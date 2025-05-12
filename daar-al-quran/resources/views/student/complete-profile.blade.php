@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card profile-card">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0">استكمال الملف الشخصي</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-4x text-success mb-3"></i>
                        <h4>مرحباً {{ $student->first_name }} {{ $student->last_name }}</h4>
                        
                        @if($student->email && !$student->email_verified_at)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                البريد الإلكتروني غير مؤكد. يرجى التحقق من بريدك الإلكتروني للتأكيد أو تغيير البريد إذا أدخلت بريدًا خاطئًا.
                                <form class="d-inline mt-2" method="POST" action="{{ route('student.verification.resend') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-link p-0 m-0 align-baseline">إعادة إرسال رابط التأكيد</button>
                                </form>
                            </div>
                        @elseif($student->email && $student->email_verified_at)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                تم تأكيد البريد الإلكتروني بنجاح!
                            </div>
                        @endif
                        
                        <p class="text-muted">
                            يجب استكمال ملفك الشخصي للتمكن من الوصول إلى لوحة التحكم
                        </p>
                    </div>
                    
                    <form method="POST" action="{{ route('student.update-profile') }}">
                        @csrf
                        
                        <h5 class="border-bottom pb-2 mb-4">البيانات الشخصية</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                        id="email" name="email" value="{{ old('email', $student->email) }}" required>
                                    @if($student->email && $student->email_verified_at)
                                        <span class="input-group-text bg-success text-white">
                                            <i class="fas fa-check-circle"></i>
                                        </span>
                                    @elseif($student->email && !$student->email_verified_at)
                                        <span class="input-group-text bg-warning text-dark">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </span>
                                    @endif
                                </div>
                                @error('email')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">إذا قمت بتغيير البريد الإلكتروني، ستحتاج إلى تأكيده مرة أخرى.</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                        id="username" name="username" value="{{ old('username', $student->username) }}" required>
                                </div>
                                @error('username')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">يجب أن يكون 4 أحرف على الأقل</small>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                        id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                                </div>
                                @error('phone')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">العنوان</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                        id="address" name="address" value="{{ old('address', $student->address) }}">
                                </div>
                                @error('address')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <h5 class="border-bottom pb-2 mb-4">تغيير كلمة المرور {{ $student->first_login ? '(مطلوب)' : '(اختياري)' }}</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">كلمة المرور الجديدة{!! $student->first_login ? ' <span class="text-danger">*</span>' : '' !!}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                        id="password" name="password" {{ $student->first_login ? 'required' : '' }}>
                                </div>
                                @error('password')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور{!! $student->first_login ? ' <span class="text-danger">*</span>' : '' !!}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" 
                                        id="password_confirmation" name="password_confirmation" {{ $student->first_login ? 'required' : '' }}>
                                </div>
                            </div>
                        </div>
 
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> حفظ البيانات واستكمال التسجيل
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 