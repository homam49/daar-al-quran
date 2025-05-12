@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header text-center py-3">
                    <h4 class="mb-0">تسجيل دخول الطالب</h4>
                </div>

                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo.png') }}" alt="دار القرآن" class="img-fluid mb-3" style="max-height: 100px;">
                        <h4 class="mb-2">دار القرآن</h4>
                        <p class="text-muted">أدخل بيانات الدخول الخاصة بك للوصول إلى حسابك</p>
                    </div>

                    
                    
                    <form method="POST" action="{{ route('student.login.submit') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="login" class="form-label">البريد الإلكتروني أو اسم المستخدم</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control @error('login') is-invalid @enderror" 
                                    id="login" name="login" value="{{ old('login') }}" placeholder="أدخل اسم المستخدم أو البريد الإلكتروني" required autofocus>
                            </div>
                            @error('login')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" placeholder="أدخل كلمة المرور" required>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        
                        <div class="mb-0">
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i> تسجيل الدخول
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <a href="{{ route('student.password.request') }}" class="btn btn-link">
                                نسيت كلمة المرور؟
                            </a>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer text-center py-3 bg-light">
                    <p class="text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        للحصول على بيانات تسجيل الدخول، يرجى التواصل مع المعلم
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 