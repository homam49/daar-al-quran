@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>إنشاء فصل جديد</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('classrooms.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة للفصول
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">معلومات الفصل الجديد</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('classrooms.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="school_id" class="form-label">المدرسة</label>
                        <select class="form-select @error('school_id') is-invalid @enderror" id="school_id" name="school_id" required>
                            <option value="">اختر المدرسة</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ (old('school_id', $schoolId ?? '') == $school->id) ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('school_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            @if(isset($schoolName))
                                تم اختيار مدرسة "{{ $schoolName }}" تلقائيًا بعد انضمامك إليها.
                            @else
                                اختر المدرسة التي سيُنشأ فيها هذا الفصل.
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الفصل</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="مثال: تحفيظ القرآن - المستوى الأول" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">وصف الفصل</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="اكتب وصفًا مختصرًا للفصل">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">أيام الدوام</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($days as $day)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="days[]" value="{{ $day }}" id="day_{{ $loop->index }}" {{ in_array($day, old('days', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="day_{{ $loop->index }}">
                                            {{ $day }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('days')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">وقت البدء</label>
                            <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label">وقت الانتهاء</label>
                            <input type="time" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="{{ route('classrooms.index') }}" class="btn btn-light me-md-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">إنشاء الفصل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 