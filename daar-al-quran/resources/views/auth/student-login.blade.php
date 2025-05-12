<!-- @extends('layouts.app')

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
                        <img src="{{ asset('img/logo.png') }}" alt="دار القرآن" class="img-fluid" style="max-height: 100px;">
                        <h4 class="mb-2">دار القرآن</h4>
                        <p class="mt-3 text-muted">أدخل بيانات الدخول الخاصة بك كطالب</p>
                    </div>

                    <form method="POST" action="{{ route('student.login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="login" class="form-label">البريد الإلكتروني أو اسم المستخدم</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" placeholder="أدخل اسم المستخدم أو البريد الإلكتروني" name="login" value="{{ old('login') }}" required autofocus>
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
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="أدخل كلمة المرور" name="password" required>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                تذكرني
                            </label>
                        </div>

                        <div class="mb-0">
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i> تسجيل الدخول
                            </button>

                            @if (Route::has('student.password.request'))
                                <div class="text-center">
                                    <a class="btn btn-link" href="{{ route('student.password.request') }}">
                                        نسيت كلمة المرور؟
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
                
                <div class="card-footer text-center py-3 bg-light">
                    <p class="mb-0">لست طالبًا؟ <a href="{{ route('login') }}">تسجيل دخول كمعلم أو مدير</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection  -->