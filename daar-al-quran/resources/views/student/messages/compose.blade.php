@extends('layouts.student')

@section('student-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-paper-plane"></i></div>
        إرسال رسالة جديدة
    </h1>
    <a href="{{ route('student.messages') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-right me-1"></i> العودة للرسائل
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">إرسال رسالة إلى المعلم</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('student.messages.send') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="teacher_id" class="form-label">المعلم المستلم <span class="text-danger">*</span></label>
                <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                    <option value="">اختر المعلم</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="subject" class="form-label">موضوع الرسالة <span class="text-danger">*</span></label>
                <input type="text" 
                       name="subject" 
                       id="subject" 
                       class="form-control @error('subject') is-invalid @enderror" 
                       value="{{ old('subject') }}" 
                       maxlength="255" 
                       required>
                @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="content" class="form-label">محتوى الرسالة <span class="text-danger">*</span></label>
                <textarea name="content" 
                          id="content" 
                          rows="6" 
                          class="form-control @error('content') is-invalid @enderror" 
                          maxlength="2000" 
                          required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">الحد الأقصى 2000 حرف</div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('student.messages') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> إلغاء
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> إرسال الرسالة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for content textarea
    const contentTextarea = document.getElementById('content');
    const maxLength = 2000;
    
    if (contentTextarea) {
        // Create character counter
        const counterDiv = document.createElement('div');
        counterDiv.className = 'form-text text-end';
        counterDiv.id = 'character-counter';
        contentTextarea.parentNode.appendChild(counterDiv);
        
        function updateCounter() {
            const currentLength = contentTextarea.value.length;
            const remaining = maxLength - currentLength;
            counterDiv.textContent = `${currentLength}/${maxLength} حرف`;
            
            if (remaining < 100) {
                counterDiv.className = 'form-text text-end text-warning';
            } else if (remaining < 50) {
                counterDiv.className = 'form-text text-end text-danger';
            } else {
                counterDiv.className = 'form-text text-end text-muted';
            }
        }
        
        // Initial update
        updateCounter();
        
        // Update on input
        contentTextarea.addEventListener('input', updateCounter);
    }
});
</script>
@endsection 