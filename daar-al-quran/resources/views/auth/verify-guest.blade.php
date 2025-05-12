@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">تأكيد عنوان البريد الإلكتروني</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            تم إرسال رابط تحقق جديد إلى عنوان بريدك الإلكتروني.
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info" role="alert">
                            {{ session('info') }}
                        </div>
                    @endif

                    <p>يرجى التحقق من بريدك الإلكتروني للحصول على رابط التحقق.</p>
                    <p>إذا لم تستلم رسالة التحقق، يمكنك إدخال بريدك الإلكتروني أدناه لإرسال رابط تحقق جديد:</p>
                    
                    <form method="POST" action="{{ route('verification.resend-guest') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ session('email') }}" required autofocus>
                            <small class="form-text text-muted">
                                يمكنك تغيير بريدك الإلكتروني هنا إذا كنت قد أدخلت عنوانًا خاطئًا سابقًا.
                            </small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                إرسال رابط تحقق جديد
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-3">
                        <p>هل لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 