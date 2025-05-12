@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0 text-danger"><i class="fas fa-trash-alt me-2"></i>حذف المدرسة</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-right me-1"></i> العودة للوحة التحكم
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>تأكيد حذف المدرسة</h5>
            </div>
            <div class="card-body">
                <div id="response-messages">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if ($errors->any() && !session('error'))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="alert alert-warning">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>تحذير مهم!</h5>
                    <p>أنت على وشك حذف المدرسة: <strong>{{ $school->name }}</strong></p>
                    <hr>
                    <p class="mb-0">سيؤدي هذا الإجراء إلى حذف جميع البيانات المرتبطة بهذه المدرسة بما في ذلك:</p>
                    <ul>
                        <li>جميع الفصول الدراسية ({{ $school->classRooms->count() }} فصل)</li>
                        <li>جميع الطلاب المسجلين ({{ $school->students->count() }} طالب)</li>
                        <li>جميع الجلسات وسجلات الحضور</li>
                        <li>جميع المعلمين المرتبطين بالمدرسة</li>
                    </ul>
                    <p class="text-danger font-weight-bold">هذا الإجراء غير قابل للتراجع!</p>
                </div>

                <form action="{{ route('admin.schools.deletion-action') }}" method="POST" id="deletionForm">
                    @csrf
                    <div class="mb-3">
                        <label for="school_id" class="form-label">رمز المدرسة</label>
                        <input type="text" class="form-control @error('school_id') is-invalid @enderror" 
                            id="school_id" name="school_id" required value="{{ $school->code }}" readonly
                            placeholder="أدخل رمز المدرسة">
                        <div class="form-text text-muted">
                            هذا هو رمز المدرسة الذي تم إنشاؤه تلقائياً عند إنشاء المدرسة.
                        </div>
                        @error('school_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="deletion_code" class="form-label">رمز الحذف</label>
                        <input type="text" class="form-control @error('deletion_code') is-invalid @enderror" 
                            id="deletion_code" name="deletion_code" required 
                            placeholder="أدخل رمز الحذف المرتبط بالمدرسة">
                        <div class="form-text text-muted">
                            هذا هو الرمز الذي قمت بتحديده عند إنشاء المدرسة.
                            @if(empty($school->deletion_code))
                                <span class="text-danger">لم يتم تحديد رمز حذف لهذه المدرسة. يمكنك إدخال أي قيمة.</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirm_deletion" name="confirm_deletion" required>
                            <label class="form-check-label text-danger" for="confirm_deletion">
                                أؤكد أنني أفهم أن هذا الإجراء نهائي ولا يمكن التراجع عنه، وسيؤدي إلى حذف جميع بيانات المدرسة والمستخدمين المرتبطين بها
                            </label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger" id="confirmDeletionBtn">
                            <i class="fas fa-trash-alt me-1"></i> تأكيد حذف المدرسة
                        </button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 