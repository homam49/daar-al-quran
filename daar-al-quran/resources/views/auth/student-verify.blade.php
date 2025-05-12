@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">تأكيد عنوان البريد الإلكتروني للطالب</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            تم إرسال رابط تحقق جديد إلى عنوان بريدك الإلكتروني.
                        </div>
                    @endif

                    @if (session('email_updated'))
                        <div class="alert alert-success" role="alert">
                            تم تحديث بريدك الإلكتروني بنجاح. يرجى التحقق من بريدك الجديد للحصول على رابط التحقق.
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <p>قبل المتابعة، يرجى التحقق من بريدك الإلكتروني للحصول على رابط التحقق.</p>
                    <p>إذا لم تستلم البريد الإلكتروني،
                    <form class="d-inline" method="POST" action="{{ route('student.verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">انقر هنا لطلب آخر</button>.
                    </form>
                    </p>

                    <hr>
                    
                    <h5 class="mt-4">تحديث البريد الإلكتروني</h5>
                    <p>إذا كنت قد أدخلت عنوان بريد إلكتروني خاطئ، يمكنك تحديثه أدناه:</p>
                    
                    <form method="POST" action="{{ route('student.verification.update-email') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="current_email" class="form-label">البريد الإلكتروني الحالي</label>
                            <input id="current_email" type="email" class="form-control" value="{{ auth()->guard('student')->user()->email }}" disabled>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني الجديد</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" required>
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                تحديث البريد الإلكتروني
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 