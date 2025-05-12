@extends('layouts.teacher')

@section('teacher-content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">تعديل الفصل</h1>
        <a href="{{ route('classrooms.show', $classroom->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right ml-1"></i>
            عودة إلى الفصل
        </a>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title mb-0">{{ $classroom->name }}</h3>
            <div class="text-muted">{{ $classroom->school->name }}</div>
        </div>
        <div class="card-body">
            <form action="{{ route('classrooms.update', $classroom->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">اسم الفصل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $classroom->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">وصف الفصل</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description', $classroom->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">مواعيد الدروس</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">أيام الدروس <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach($days as $day)
                                        <div class="form-check">
                                            <input class="form-check-input @error('days') is-invalid @enderror" 
                                                type="checkbox" name="days[]" value="{{ $day }}" 
                                                id="day-{{ $day }}" 
                                                {{ in_array($day, $selectedDays) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="day-{{ $day }}">
                                                {{ $day }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('days')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">وقت البدء <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                    id="start_time" name="start_time" 
                                    value="{{ old('start_time', $startTime ? $startTime->format('H:i') : '') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">وقت الإنتهاء <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                    id="end_time" name="end_time" 
                                    value="{{ old('end_time', $endTime ? $endTime->format('H:i') : '') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save ml-1"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 