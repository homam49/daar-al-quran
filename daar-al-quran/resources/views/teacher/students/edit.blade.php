@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-user-edit"></i></div>
        تعديل بيانات الطالب
    </h1>
    <div>
        <a href="{{ route('teacher.classroom.students', $classroom->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة لقائمة الطلاب
        </a>
    </div>
</div>





<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>تعديل بيانات الطالب - {{ $student->first_name }} {{ $student->last_name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.classroom.students.update', [$classroom->id, $student->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                        id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="middle_name" class="form-label">الاسم الأوسط</label>
                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                        id="middle_name" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}">
                    @error('middle_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="last_name" class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                        id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="birth_year" class="form-label">سنة الميلاد <span class="text-danger">*</span></label>
                    <select class="form-select @error('birth_year') is-invalid @enderror" id="birth_year" name="birth_year" required>
                        @for($year = date('Y') - 3; $year >= date('Y') - 20; $year--)
                            <option value="{{ $year }}" {{ old('birth_year', $student->birth_year) == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    @error('birth_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="phone" class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                        id="phone" name="phone" value="{{ old('phone', $student->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label for="address" class="form-label">العنوان</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                        id="address" name="address" value="{{ old('address', $student->address) }}">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>ملاحظة:</strong> لا يمكن تغيير البريد الإلكتروني أو اسم المستخدم أو كلمة المرور من هنا.
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> حفظ التغييرات
                </button>
                <a href="{{ route('teacher.classroom.students', $classroom->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection 