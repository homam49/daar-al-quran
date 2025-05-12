@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header">
    <div class="d-flex justify-content-between">
        <h4 class="mb-0"><i class="fas fa-school me-2"></i>الانضمام إلى مدرسة</h4>
        <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>الانضمام إلى مدرسة باستخدام الرمز</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('teacher.join-school.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="school_code" class="form-label">رمز المدرسة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('school_code') is-invalid @enderror" 
                            id="school_code" name="school_code" value="{{ old('school_code') }}" required>
                        <div class="form-text">أدخل رمز المدرسة الذي حصلت عليه من مدير المدرسة</div>
                        @error('school_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-1"></i> انضمام
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection 