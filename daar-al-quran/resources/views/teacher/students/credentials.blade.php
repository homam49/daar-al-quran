@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-key"></i></div>
        بيانات تسجيل دخول الطالب
    </h1>
    <div>
        <a href="{{ route('teacher.classroom.students', $classroom->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة لقائمة الطلاب
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>بيانات تسجيل دخول الطالب: {{ $student->first_name }} {{ $student->middle_name ?? '' }} {{ $student->last_name }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    فيما يلي بيانات تسجيل دخول الطالب. يرجى مشاركتها مع الطالب وتذكيره بتغيير كلمة المرور بعد تسجيل الدخول الأول.
                </div>
                
                <div class="card mb-3 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>اسم المستخدم</h5>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" class="form-control" id="username" value="{{ $student->username }}" readonly>
                            <button class="btn btn-outline-secondary copy-btn" type="button" onclick="copyToClipboard('username')">
                                <i class="fas fa-copy"></i> نسخ
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-3 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-key me-2"></i>كلمة المرور</h5>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" class="form-control" id="password" value="{{ $student->username }}" readonly>
                            <button class="btn btn-outline-secondary copy-btn" type="button" onclick="copyToClipboard('password')">
                                <i class="fas fa-copy"></i> نسخ
                            </button>
                        </div>
                        <div class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i> كلمة المرور هي نفسها اسم المستخدم.
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('teacher.classroom.students', $classroom->id) }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right me-1"></i> العودة لقائمة الطلاب
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function copyToClipboard(elementId) {
    var element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Change button appearance
    var button = element.nextElementSibling;
    var originalHTML = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> تم النسخ';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-secondary');
    
    setTimeout(function() {
        button.innerHTML = originalHTML;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}
</script>
@endsection 