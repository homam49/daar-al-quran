@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1 class="page-title">
        <div class="page-header-icon"><i class="fas fa-calendar-plus"></i></div>
        إنشاء جلسة جديدة
    </h1>
    <a href="{{ route('classrooms.show', $classroom->id) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-right"></i> العودة للفصل
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>معلومات الجلسة</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <strong>الفصل:</strong> {{ $classroom->name }}
        </div>
        <div class="mb-3">
            <strong>المدرسة:</strong> {{ $classroom->school->name }}
        </div>
    </div>
</div>

<form method="POST" action="{{ route('classroom.sessions.store', $classroom->id) }}">
    @csrf
    
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">تفاصيل الجلسة</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="session_date" class="form-label">تاريخ الجلسة<span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="session_date" name="session_date" 
                           value="{{ old('session_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="start_time" class="form-label">وقت البداية<span class="text-danger">*</span></label>
                    <input type="time" class="form-control" id="start_time" name="start_time" 
                           value="{{ old('start_time', $startTime ?? '') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="end_time" class="form-label">وقت النهاية<span class="text-danger">*</span></label>
                    <input type="time" class="form-control" id="end_time" name="end_time" 
                           value="{{ old('end_time', $endTime ?? '') }}" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">وصف الجلسة</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                <small class="text-muted">اكتب ملخصاً عن محتوى الجلسة، المواضيع التي تم تناولها، إلخ.</small>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">تسجيل الحضور</h5>
        </div>
        <div class="card-body">
            @if(count($classroom->students) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الطالب</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classroom->students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                                                            <td>{{ $student->name }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" 
                                               id="present-{{ $student->id }}" value="present" checked>
                                        <label class="btn btn-outline-success" for="present-{{ $student->id }}">
                                            <i class="fas fa-check"></i> حاضر
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" 
                                               id="late-{{ $student->id }}" value="late">
                                        <label class="btn btn-outline-warning" for="late-{{ $student->id }}">
                                            <i class="fas fa-clock"></i> متأخر
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" 
                                               id="absent-{{ $student->id }}" value="absent">
                                        <label class="btn btn-outline-danger" for="absent-{{ $student->id }}">
                                            <i class="fas fa-times"></i> غائب
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <textarea class="form-control" name="notes[{{ $student->id }}]" rows="1" 
                                              placeholder="ملاحظات خاصة بالطالب..."></textarea>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    لا يوجد طلاب مسجلين في هذا الفصل. يرجى إضافة طلاب قبل إنشاء جلسة.
                </div>
            @endif
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="send_message" name="send_message">
                <label class="form-check-label" for="send_message">
                    إرسال رسالة للطلاب عن هذه الجلسة
                </label>
            </div>
        </div>
        <div class="card-body" id="message_form" style="display: none;">
            <div class="mb-3">
                <label for="message_title" class="form-label">عنوان الرسالة</label>
                <input type="text" class="form-control" id="message_title" name="message_title">
            </div>
            <div class="mb-3">
                <label for="message_content" class="form-label">محتوى الرسالة</label>
                <textarea class="form-control" id="message_content" name="message_content" rows="3"></textarea>
                <small class="text-muted">ستصل هذه الرسالة لجميع طلاب الفصل.</small>
            </div>
        </div>
    </div>
    
    <div class="text-center mb-4">
        <button type="submit" class="btn btn-primary btn-lg" 
                {{ count($classroom->students) === 0 ? 'disabled' : '' }}>
            <i class="fas fa-save me-2"></i>
            حفظ الجلسة وتسجيل الحضور
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle message form
        $('#send_message').change(function() {
            if(this.checked) {
                $('#message_form').slideDown();
                $('#message_title').attr('required', true);
                $('#message_content').attr('required', true);
            } else {
                $('#message_form').slideUp();
                $('#message_title').removeAttr('required');
                $('#message_content').removeAttr('required');
            }
        });
    });
</script>
@endpush 