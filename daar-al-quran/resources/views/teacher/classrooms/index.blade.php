@extends('layouts.teacher')

@section('teacher-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-chalkboard me-2"></i>الفصول الدراسية</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('classrooms.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> إنشاء فصل جديد
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">قائمة الفصول</h5>
            </div>
            <div class="card-body">
                @if(count($classRooms) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الفصل</th>
                                <th>المدرسة</th>
                                <th>أيام الدوام</th>
                                <th>الوقت</th>
                                <th>عدد الطلاب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classRooms as $index => $classroom)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $classroom->name }}</td>
                                <td>{{ $classroom->school->name }}</td>
                                <td>
                                    @if($classroom->schedules->count() > 0)
                                        {{ $classroom->schedules->pluck('day')->implode(' - ') }}
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    @if($classroom->schedules->count() > 0)
                                        @php
                                            $schedule = $classroom->schedules->first();
                                            try {
                                                // Try different time formats
                                                if (strlen($schedule->start_time) > 8) {
                                                    // Full datetime format
                                                    $startTime = \Carbon\Carbon::parse($schedule->start_time)->format('h:i A');
                                                    $endTime = \Carbon\Carbon::parse($schedule->end_time)->format('h:i A');
                                                } else {
                                                    // Time only format (H:i:s or H:i)
                                                    $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->format('h:i A');
                                                    $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time)->format('h:i A');
                                                }
                                            } catch (\Exception $e) {
                                                // Fallback to simple formatting
                                                $startTime = date('h:i A', strtotime($schedule->start_time));
                                                $endTime = date('h:i A', strtotime($schedule->end_time));
                                            }
                                        @endphp
                                        <span class="time-display">{{ $startTime }} - {{ $endTime }}</span>
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($classroom->students))
                                        {{ $classroom->students->count() }}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center">
                                        <a href="{{ route('classrooms.show', $classroom->id) }}" class="btn btn-sm btn-primary mx-1" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('classrooms.edit', $classroom->id) }}" class="btn btn-sm btn-warning mx-1" title="تعديل الفصل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-info mx-1" onclick="openBroadcastModal('{{ $classroom->id }}', '{{ $classroom->name }}')" title="إرسال إشعار جماعي">
                                            <i class="fas fa-bullhorn"></i>
                                        </button>
                                        <form action="{{ route('classrooms.destroy', $classroom->id) }}" method="POST" style="display: inline; margin: 0; padding: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger mx-1" onclick="return confirm('هل أنت متأكد من حذف هذا الفصل؟')" title="حذف الفصل">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-chalkboard text-muted fa-3x mb-3"></i>
                    <p class="mb-0">لا يوجد فصول دراسية حتى الآن</p>
                    <div class="mt-3">
                        <a href="{{ route('classrooms.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle me-1"></i> إنشاء فصل جديد
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Broadcast Message Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="broadcastModalLabel"><i class="fas fa-bullhorn me-2"></i>إرسال إشعار جماعي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('classrooms.broadcast-message') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="classroom_id" id="broadcast_classroom_id">
                    <div class="mb-3">
                        <label for="broadcast_classroom_name" class="form-label">الفصل المستهدف</label>
                        <input type="text" class="form-control" id="broadcast_classroom_name" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="broadcast_subject" class="form-label">عنوان الإشعار</label>
                        <input type="text" class="form-control" id="broadcast_subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="broadcast_content" class="form-label">محتوى الإشعار</label>
                        <textarea class="form-control" id="broadcast_content" name="content" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-info">إرسال للجميع</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function openBroadcastModal(classroomId, classroomName) {
        document.getElementById('broadcast_classroom_id').value = classroomId;
        document.getElementById('broadcast_classroom_name').value = classroomName;
        
        // Clear previous input
        document.getElementById('broadcast_subject').value = '';
        document.getElementById('broadcast_content').value = '';
        
        // Open the modal
        const broadcastModal = new bootstrap.Modal(document.getElementById('broadcastModal'));
        broadcastModal.show();
    }
</script>
@endsection 