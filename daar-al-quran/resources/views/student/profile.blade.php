@extends('layouts.student')

@section('student-content')
<div class="page-header">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-user-cog"></i></div>
        الملف الشخصي
    </h1>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">بياناتك الشخصية</h5>
    </div>
    <div class="card-body">
        
        
        

        <form method="POST" action="{{ route('student.update-profile') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">الاسم الأول</label>
                    <input type="text" class="form-control" id="first_name" value="{{ $student->first_name }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">اسم العائلة</label>
                    <input type="text" class="form-control" id="last_name" value="{{ $student->last_name }}" readonly>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">اسم المستخدم<span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" 
                           value="{{ old('username', $student->username) }}" required>
                    @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">يمكنك استخدام اسم المستخدم لتسجيل الدخول.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني<span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                           value="{{ old('email', $student->email) }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">سيستخدم هذا البريد الإلكتروني للتواصل معك وإرسال الإشعارات.</small>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" 
                           value="{{ old('phone', $student->phone) }}">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="address" class="form-label">العنوان</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" 
                           value="{{ old('address', $student->address) }}">
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">تغيير كلمة المرور (اختياري)</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">اترك هذا الحقل فارغا إذا كنت لا ترغب في تغيير كلمة المرور.</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">معلومات حول حسابك</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>تاريخ التسجيل:</strong> {{ $student->created_at->format('Y-m-d') }}</p>
                <p><strong>آخر تحديث للملف:</strong> {{ $student->updated_at->format('Y-m-d') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>الحالة:</strong> <span class="badge bg-success">نشط</span></p>
                <p><strong>عدد الفصول المسجل بها:</strong> {{ $student->classrooms->count() }}</p>
            </div>
        </div>
    </div>
</div>
@endsection 