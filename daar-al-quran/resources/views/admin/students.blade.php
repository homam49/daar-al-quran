@extends('layouts.admin')

@section('admin-content')
<style>
    /* Class count styling */
    .class-count {
        color: #0d6efd;
        cursor: pointer;
        text-decoration: underline;
        font-weight: bold;
    }
    
    .class-count:hover {
        color: #0a58ca;
    }
    
    /* The dropdown menu */
    .classes-list {
        display: none;
        position: absolute;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        padding: 8px;
        z-index: 1000;
        min-width: 150px;
        max-width: 250px;
        list-style: none;
        margin: 0;
        padding: 0;
        top: 0;
        right: 100%;
        margin-right: 10px;
        text-align: right;
    }
    
    /* Add a small arrow pointing to the right */
    .classes-list::after {
        content: '';
        position: absolute;
        top: 10px;
        right: -6px;
        border-width: 6px 0 6px 6px;
        border-style: solid;
        border-color: transparent transparent transparent white;
    }
    
    /* Add a border arrow */
    .classes-list::before {
        content: '';
        position: absolute;
        top: 10px;
        right: -7px;
        border-width: 6px 0 6px 6px;
        border-style: solid;
        border-color: transparent transparent transparent #dee2e6;
    }
    
    .classes-list li {
        padding: 5px 10px;
        border-bottom: 1px solid #f0f0f0;
        white-space: nowrap;
    }
    
    .classes-list li:last-child {
        border-bottom: none;
    }
    
    /* Class count container */
    .class-count-container {
        position: relative;
        display: inline-block;
    }
    
    /* Show dropdown class */
    .show-dropdown {
        display: block;
    }

    /* Keep filter select on top */
    .filter-container {
        z-index: 1001;
        position: relative;
    }
</style>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i>إدارة الطلاب</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-right"></i> العودة إلى لوحة التحكم
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة الطلاب</h5>
            <div class="d-flex align-items-center filter-container">
                @if(count($classrooms) > 0)
                <select class="form-select form-select-sm me-2" id="classroomFilter" onchange="filterByClassroom()">
                    <option value="all">جميع الفصول</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        @if(count($students) > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="studentsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>اسم الطالب</th>
                            <th>العمر</th>
                            <th>الفصول</th>
                            <th>البريد الإلكتروني</th>
                            <th>رقم الهاتف</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                            <tr class="student-row" data-classroom-ids="{{ $student->classRooms->pluck('id')->implode(',') }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->name ?? ($student->first_name . ' ' . $student->last_name) }}</td>
                                <td>{{ isset($student->birth_year) ? (date('Y') - $student->birth_year) : 'غير محدد' }} سنة</td>
                                <td class="position-relative">
                                    @if($student->classRooms->count() > 0)
                                        <div class="class-count-container">
                                            <a href="javascript:void(0)" class="class-count" onclick="toggleClassesList({{ $student->id }})">
                                                {{ $student->classRooms->count() }}
                                            </a>
                                            <ul id="classes-list-{{ $student->id }}" class="classes-list" style="display:none;">
                                                @foreach($student->classRooms as $classroom)
                                                    <li>{{ $classroom->name }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>{{ $student->email ?? 'غير متوفر' }}</td>
                                <td>{{ $student->phone ?? 'غير متوفر' }}</td>
                                <td>{{ \Carbon\Carbon::parse($student->created_at)->format('Y-m-d') }}</td>
                                <td>
                                    <form action="{{ route('admin.students.delete', $student->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا الطالب؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="noStudentsFound" class="alert alert-info mt-3" style="display: none;">
                <i class="fas fa-info-circle me-2"></i>
                لا يوجد طلاب مطابقون لمعايير البحث
            </div>
        @else
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-user-graduate text-muted fa-4x"></i>
                </div>
                <p class="lead">لا يوجد طلاب مسجلين في المدرسة الخاصة بك</p>
                <p>قم بتعيين معلمين لبدء تسجيل الطلاب في الفصول</p>
            </div>
        @endif
    </div>
</div>

<!-- Add this to the bottom of your page, just before the closing body tag -->
<script>
    // Global variable to track currently open dropdown
    var currentOpenDropdown = null;
    
    // Function to toggle classes list visibility
    function toggleClassesList(studentId) {
        // Get the dropdown element
        var dropdown = document.getElementById('classes-list-' + studentId);
        
        // If we're clicking the same dropdown that's already open, just close it
        if (currentOpenDropdown === dropdown) {
            dropdown.style.display = 'none';
            currentOpenDropdown = null;
            return;
        }
        
        // Close currently open dropdown if any
        if (currentOpenDropdown) {
            currentOpenDropdown.style.display = 'none';
        }
        
        // Show the clicked dropdown
        dropdown.style.display = 'block';
        currentOpenDropdown = dropdown;
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        // If click is not on a class-count and we have an open dropdown
        if (!event.target.closest('.class-count') && currentOpenDropdown) {
            currentOpenDropdown.style.display = 'none';
            currentOpenDropdown = null;
        }
    });
    
    // Function to filter students by classroom
    function filterByClassroom() {
        var classroomFilter = document.getElementById('classroomFilter');
        var selectedClassroom = classroomFilter.value;
        var rows = document.querySelectorAll('.student-row');
        var visibleRows = 0;
        
        // Close any open dropdown
        if (currentOpenDropdown) {
            currentOpenDropdown.style.display = 'none';
            currentOpenDropdown = null;
        }
        
        // Loop through all rows and show/hide based on classroom
        rows.forEach(function(row) {
            var classroomIds = row.getAttribute('data-classroom-ids').split(',');
            
            if (selectedClassroom === 'all' || classroomIds.includes(selectedClassroom)) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show or hide no results message
        var noStudentsFound = document.getElementById('noStudentsFound');
        if (visibleRows === 0 && rows.length > 0) {
            noStudentsFound.style.display = 'block';
        } else {
            noStudentsFound.style.display = 'none';
        }
    }
</script>
@endsection 