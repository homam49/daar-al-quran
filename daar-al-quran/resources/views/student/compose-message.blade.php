@extends('layouts.student')

@section('student-content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <div class="page-header-icon"><i class="fas fa-paper-plane"></i></div>
            إرسال رسالة جديدة
        </h1>
        <a href="{{ route('student.messages') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right me-1"></i> العودة للرسائل
        </a>
    </div>
</div>

@if(session('debug_classroom'))
<div class="alert alert-info">
    <h5>Debug Information:</h5>
    <pre>{{ json_encode(session('debug_classroom'), JSON_PRETTY_PRINT) }}</pre>
</div>
@endif

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>كتابة رسالة جديدة</h5>
    </div>
    <div class="card-body">
        @if($teachers->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                أنت غير مسجل في أي فصل بعد، لذلك لا يمكنك إرسال رسائل في الوقت الحالي.
            </div>
        @else
            <form action="{{ route('student.messages.send') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="recipient_id" class="form-label">إرسال إلى</label>
                    <select name="recipient_id" id="recipient_id" class="form-select @error('recipient_id') is-invalid @enderror" required>
                        <option value="">اختر المعلم</option>
                        @foreach($teachers as $teacher)
                            @php
                                $classroom = $classrooms->where('user_id', $teacher->id)->first();
                                $classroomName = $classroom ? $classroom->name : 'معلم';
                            @endphp
                            <option value="{{ $teacher->id }}" {{ old('recipient_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }} ({{ $classroomName }})
                            </option>
                        @endforeach
                    </select>
                    @error('recipient_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="subject" class="form-label">الموضوع</label>
                    <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject') }}" required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">محتوى الرسالة</label>
                    <textarea name="content" id="content" rows="5" class="form-control @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> إرسال الرسالة
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection 