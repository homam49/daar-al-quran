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
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>تأكيد حذف المدرسة</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                

                <div class="alert alert-warning">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>تحذير مهم!</h5>
                    <p>أنت على وشك حذف المدرسة: <strong>{{ $school->name }}</strong></p>
                    <hr>
                    <p class="mb-0">سيؤدي هذا الإجراء إلى حذف جميع البيانات المرتبطة بهذه المدرسة بما في ذلك:</p>
                    <ul>
                        <li>جميع الفصول الدراسية ({{ $school->classRooms->count() }} فصل)</li>
                        <li>جميع الطلاب المسجلين ({{ $school->students->count() }} طالب)</li>
                        <li>جميع الجلسات وسجلات الحضور</li>
                        <li>جميع الجداول الدراسية</li>
                    </ul>
                    <p class="text-danger font-weight-bold">هذا الإجراء غير قابل للتراجع!</p>
                </div>

                <form action="{{ route('admin.schools.delete', $school->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-3">
                        <label for="deletion_code" class="form-label">أدخل رمز الحذف للتأكيد <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="deletion_code" name="deletion_code" required>
                        <div class="form-text text-muted">
                            هذا هو الرمز الذي قمت بتحديده عند إنشاء المدرسة.
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-1"></i> تأكيد حذف المدرسة
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 