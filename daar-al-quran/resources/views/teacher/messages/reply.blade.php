@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-reply"></i></div>
        الرد على رسالة
    </h1>
    <a href="{{ route('teacher.messages') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-right me-1"></i> العودة للرسائل
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-envelope-open me-2"></i>الرسالة الأصلية</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <strong>من:</strong> {{ $message->sender->full_name ?? 'طالب' }}
        </div>
        <div class="mb-3">
            <strong>الموضوع:</strong> {{ $message->subject }}
        </div>
        <div class="mb-3">
            <strong>التاريخ:</strong> {{ $message->created_at->format('Y-m-d H:i') }}
        </div>
        <div class="p-3 bg-light rounded">
            {!! nl2br(e($message->content)) !!}
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>إرسال الرد</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('teacher.messages.send-reply', $message->id) }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="subject" class="form-label">الموضوع</label>
                <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', 'رد: ' . $message->subject) }}" required>
                @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="content" class="form-label">محتوى الرد</label>
                <textarea name="content" id="content" rows="5" class="form-control @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> إرسال الرد
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 