@extends('layouts.teacher')

@section('teacher-content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">تعديل الحضور</h1>
        <a href="{{ route('classroom.sessions.show', [$classroom->id, $session->id]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right ml-1"></i>
            عودة إلى تفاصيل الجلسة
        </a>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title mb-0">{{ $classroom->name }}</h3>
            <div class="text-muted">{{ $session->session_date->format('Y-m-d') }}</div>
        </div>
        <div class="card-body">
            @if($classroom->students->isEmpty())
                <div class="alert alert-warning">
                    لا يوجد طلاب في هذا الفصل
                </div>
            @else
                <form action="{{ route('classroom.sessions.update', [$classroom->id, $session->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="session_date" class="form-label">تاريخ الجلسة</label>
                                <input type="date" class="form-control" id="session_date" name="session_date" 
                                    value="{{ $session->session_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_time" class="form-label">وقت البداية</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" 
                                    value="{{ date('H:i', strtotime($session->start_time)) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="end_time" class="form-label">وقت النهاية</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" 
                                    value="{{ date('H:i', strtotime($session->end_time)) }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label for="description" class="form-label">الوصف (اختياري)</label>
                        <textarea class="form-control" id="description" name="description" rows="2">{{ $session->description }}</textarea>
                    </div>
                    
                    <h4 class="mb-3">سجل الحضور</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>اسم الطالب</th>
                                    <th>الحضور</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classroom->students as $student)
                                    @php
                                        $attendance = $session->attendances->where('student_id', $student->id)->first();
                                    @endphp
                                    <tr>
                                                                                    <td>{{ $student->name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <input type="radio" 
                                                    class="btn-check" 
                                                    name="attendance[{{ $student->id }}][status]" 
                                                    value="present" 
                                                    id="present_{{ $student->id }}"
                                                    {{ $attendance && $attendance->status === 'present' ? 'checked' : '' }}
                                                    required>
                                                <label class="btn btn-outline-success" for="present_{{ $student->id }}">
                                                    حاضر
                                                </label>

                                                <input type="radio" 
                                                    class="btn-check" 
                                                    name="attendance[{{ $student->id }}][status]" 
                                                    value="late" 
                                                    id="late_{{ $student->id }}"
                                                    {{ $attendance && $attendance->status === 'late' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-warning" for="late_{{ $student->id }}">
                                                    متأخر
                                                </label>

                                                <input type="radio" 
                                                    class="btn-check" 
                                                    name="attendance[{{ $student->id }}][status]" 
                                                    value="absent" 
                                                    id="absent_{{ $student->id }}"
                                                    {{ $attendance && $attendance->status === 'absent' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger" for="absent_{{ $student->id }}">
                                                    غائب
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                class="form-control" 
                                                name="attendance[{{ $student->id }}][note]"
                                                value="{{ $attendance ? $attendance->note : '' }}"
                                                placeholder="ملاحظات (اختياري)">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save ml-1"></i>
                            حفظ الحضور
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection 