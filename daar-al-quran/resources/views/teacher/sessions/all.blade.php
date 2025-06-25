@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>جميع الجلسات</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('classrooms.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-chalkboard me-1"></i> عرض الفصول
            </a>
        </div>
    </div>
</div>





<div class="card">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">قائمة الجلسات ({{ $sessions->total() }})</h5>
            </div>
            <div class="col-auto">
                <div class="d-flex">
                    <select class="form-select form-select-sm me-2" id="classroomFilter">
                        <option value="all">جميع الفصول</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" id="schoolFilter">
                        <option value="all">جميع المدارس</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($sessions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="sessionsTable">
                    <thead>
                        <tr>
                            <th>اسم الفصل</th>
                            <th>المدرسة</th>
                            <th>تاريخ الجلسة</th>
                            <th>وقت الجلسة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                        <tr 
                            data-classroom-id="{{ $session->class_room_id }}"
                            data-school-id="{{ $session->classroom->school->id ?? '' }}"
                        >
                            <td>{{ $session->classroom->name }}</td>
                            <td>{{ $session->classroom->school->name ?? 'غير محدد' }}</td>
                            <td>{{ $session->formatted_date ?? $session->session_date }}</td>
                                                            <td><span class="time-display">{{ $session->formatted_start_time ?? $session->start_time }} - {{ $session->formatted_end_time ?? $session->end_time }}</span></td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('classroom.sessions.show', ['classroom' => $session->class_room_id, 'session' => $session->id]) }}" 
                                        class="btn btn-sm btn-primary me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('classroom.sessions.edit', ['classroom' => $session->class_room_id, 'session' => $session->id]) }}" 
                                        class="btn btn-sm btn-success me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="noMatchMessage" class="alert alert-info mt-3" style="display: none;">لا توجد جلسات مطابقة للبحث</div>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $sessions->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times text-muted fa-3x mb-3"></i>
                <p class="mb-0">لم يتم إنشاء أي جلسات حتى الآن</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get filter elements
    const classroomFilter = document.getElementById('classroomFilter');
    const schoolFilter = document.getElementById('schoolFilter');
    
    if (classroomFilter && schoolFilter) {
        // Define the filter function - simplified version
        function applyFilter() {
            const classroomValue = classroomFilter.value;
            const schoolValue = schoolFilter.value;
            
            // Get all rows
            const tableRows = document.querySelectorAll('#sessionsTable tbody tr');
            let visibleRows = 0;
            
            // Filter each row manually
            tableRows.forEach(function(row) {
                let showRow = true;
                
                // Apply only the active filter
                if (classroomValue !== 'all') {
                    // Classroom filter is active
                    const rowClassroom = row.getAttribute('data-classroom-id');
                    if (rowClassroom !== classroomValue) {
                        showRow = false;
                    }
                } else if (schoolValue !== 'all') {
                    // School filter is active
                    const rowSchool = row.getAttribute('data-school-id');
                    if (rowSchool !== schoolValue) {
                        showRow = false;
                    }
                }
                
                // Show or hide row
                row.style.display = showRow ? '' : 'none';
                if (showRow) visibleRows++;
            });
            
            // Show/hide no results message
            const noMatch = document.getElementById('noMatchMessage');
            if (noMatch) {
                noMatch.style.display = visibleRows === 0 ? 'block' : 'none';
            }
        }
        
        // Add event listeners
        classroomFilter.addEventListener('change', function() {
            if (this.value !== 'all') {
                // Reset school filter
                schoolFilter.value = 'all';
            }
            applyFilter();
        });
        
        schoolFilter.addEventListener('change', function() {
            if (this.value !== 'all') {
                // Reset classroom filter
                classroomFilter.value = 'all';
            }
            applyFilter();
        });
        
        // Run initial filter
        applyFilter();
    }
});
</script>
@endsection 