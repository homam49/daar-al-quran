@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-user-plus"></i></div>
        إضافة طالب جديد
    </h1>
    <div>
        <a href="{{ route('teacher.classroom.students', $classroom->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة لقائمة الطلاب
        </a>
    </div>
</div>





<div class="row">
    <!-- Add Existing Student -->
    <div class="col-md-5 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-user-check me-2"></i>إضافة طالب موجود</h5>
            </div>
            <div class="card-body">
                @if(count($existingStudents) > 0)
                    <form action="{{ route('teacher.classroom.students.store', $classroom->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="student_id" class="form-label">اختر طالب <span class="text-danger">*</span></label>
                            <select class="form-select @error('student_id') is-invalid @enderror" name="student_id" id="student_id">
                                <option value="">-- اختر الطالب --</option>
                                @foreach($existingStudents as $existingStudent)
                                    <option value="{{ $existingStudent->id }}" {{ old('student_id') == $existingStudent->id ? 'selected' : '' }}>
                                        {{ $existingStudent->first_name }} {{ $existingStudent->middle_name ?? '' }} {{ $existingStudent->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            يتم عرض الطلاب من نفس المدرسة فقط الذين لم يتم إضافتهم بالفعل إلى هذا الفصل.
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-plus me-1"></i> إضافة الطالب للفصل
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-user-slash text-muted fa-3x"></i>
                        </div>
                        <p>لا يوجد طلاب متاحين للإضافة من هذه المدرسة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Create New Student -->
    <div class="col-md-7 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>إنشاء طالب جديد</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('teacher.classroom.students.store', $classroom->id) }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="middle_name" class="form-label">الاسم الأوسط</label>
                            <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                            @error('middle_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="birth_year" class="form-label">سنة الميلاد <span class="text-danger">*</span></label>
                            <select class="form-select @error('birth_year') is-invalid @enderror" id="birth_year" name="birth_year" required>
                                <option value="">-- اختر السنة --</option>
                                @for($year = date('Y') - 3; $year >= date('Y') - 20; $year--)
                                    <option value="{{ $year }}" {{ old('birth_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                            @error('birth_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                id="address" name="address" value="{{ old('address') }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                            id="email" name="email" value="{{ old('email') }}">
                        <div class="form-text text-muted">
                            البريد الإلكتروني اختياري. في حالة عدم تعيينه، سيستخدم الطالب اسم المستخدم وكلمة المرور للدخول.
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ملاحظة:</strong> سيتم إنشاء اسم مستخدم وكلمة مرور تلقائياً للطالب وعرضها بعد الإنشاء.
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> إنشاء الطالب وإضافته للفصل
                        </button>
                        <a href="{{ route('teacher.classroom.students', $classroom->id) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 