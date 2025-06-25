@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-paper-plane"></i></div>
        إرسال رسالة جديدة
    </h1>
    <a href="{{ route('teacher.messages') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-right me-1"></i> العودة للرسائل
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>كتابة رسالة جديدة</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.messages.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="type" class="form-label">نوع الرسالة</label>
                <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required onchange="toggleRecipientType()">
                    <option value="">اختر نوع الرسالة</option>
                    <option value="personal" {{ old('type') == 'personal' ? 'selected' : '' }}>رسالة شخصية (لطالب)</option>
                    <option value="class" {{ old('type') == 'class' ? 'selected' : '' }}>إعلان فصل (لجميع الطلاب)</option>
                </select>
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div id="personal-recipient" class="mb-3" style="{{ old('type') == 'personal' ? '' : 'display: none;' }}">
                <label for="student_id" class="form-label">الطالب</label>
                <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror">
                    <option value="">اختر الطالب</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                                        {{ $student->name }} ({{ $student->classRooms->first()->name ?? 'بدون فصل' }})
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div id="class-recipient" class="mb-3" style="{{ old('type') == 'class' ? '' : 'display: none;' }}">
                <label for="class_room_id" class="form-label">الفصل</label>
                <select name="class_room_id" id="class_room_id" class="form-select @error('class_room_id') is-invalid @enderror">
                    <option value="">اختر الفصل</option>
                    @foreach($classRooms as $classroom)
                        <option value="{{ $classroom->id }}" {{ old('class_room_id') == $classroom->id ? 'selected' : '' }}>
                            {{ $classroom->name }} ({{ $classroom->students->count() }} طالب)
                        </option>
                    @endforeach
                </select>
                @error('class_room_id')
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
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleRecipientType() {
        const messageType = document.getElementById('type').value;
        const personalRecipient = document.getElementById('personal-recipient');
        const classRecipient = document.getElementById('class-recipient');
        
        if (messageType === 'personal') {
            personalRecipient.style.display = 'block';
            classRecipient.style.display = 'none';
        } else if (messageType === 'class') {
            personalRecipient.style.display = 'none';
            classRecipient.style.display = 'block';
        } else {
            personalRecipient.style.display = 'none';
            classRecipient.style.display = 'none';
        }
    }
</script>
@endsection 