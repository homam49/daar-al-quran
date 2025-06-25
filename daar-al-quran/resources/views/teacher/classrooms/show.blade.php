@extends('layouts.teacher')

@section('teacher-content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                    <h6 class="mb-2">تفاصيل الفصل: {{ $classroom->name }}</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('classrooms.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                        <a href="{{ route('classrooms.edit', $classroom->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل الفصل
                        </a>
                        <button type="button" class="btn btn-info" onclick="openBroadcastModal()">
                            <i class="fas fa-bullhorn"></i> إرسال إشعار للجميع
                        </button>
                        <form action="{{ route('classrooms.destroy', $classroom->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الفصل؟')">
                                <i class="fas fa-trash"></i> حذف الفصل
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Classroom Information -->
                        <div class="col-lg-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>معلومات الفصل</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>المدرسة:</strong> {{ $classroom->school->name }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>الوصف:</strong> 
                                        <p>{{ $classroom->description ?: 'لا يوجد وصف' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <strong>مواعيد الدروس:</strong>
                                        <ul class="list-group mt-2">
                                            @foreach($classroom->schedules as $schedule)
                                            <li class="list-group-item">
                                                @php
                                                    try {
                                                        if (strlen($schedule->start_time) > 8) {
                                                            $startTime = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                                                            $endTime = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');
                                                        } else {
                                                            $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->start_time)->format('H:i');
                                                            $endTime = \Carbon\Carbon::createFromFormat('H:i:s', $schedule->end_time)->format('H:i');
                                                        }
                                                    } catch (\Exception $e) {
                                                        $startTime = date('H:i', strtotime($schedule->start_time));
                                                        $endTime = date('H:i', strtotime($schedule->end_time));
                                                    }
                                                @endphp
                                                {{ $schedule->day }} (<span class="time-display">{{ $startTime }} - {{ $endTime }}</span>)
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Students Panel -->
                        <div class="col-lg-8 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-user-graduate me-2"></i>الطلاب المسجلين</h5>
                                    <div>
                                        <button type="button" class="btn btn-light btn-sm" onclick="document.getElementById('newStudentModalOverlay').style.display='block';">
                                            <i class="fas fa-user-plus"></i> إضافة طالب جديد
                                        </button>
                                        <button type="button" class="btn btn-light btn-sm" onclick="document.getElementById('existingStudentModalOverlay').style.display='block';">
                                            <i class="fas fa-user-check"></i> إضافة طالب موجود
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(count($classroom->students) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>اسم الطالب</th>
                                                    <th>العمر</th>
                                                    <th>رقم الهاتف</th>
                                                    <th>حالة الدخول</th>
                                                    <th>الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($classroom->students as $index => $student)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $student->name }}</td>
                                                    <td>{{ $student->age }} سنة</td>
                                                    <td>{{ $student->phone ?: 'غير متوفر' }}</td>
                                                    <td>
                                                        @if($student->first_login) 
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="fas fa-clock me-1"></i>لم يسجل دخول
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check-circle me-1"></i>سجل دخول
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <div class="d-flex gap-1">
                                                                
                                                                <button type="button" class="btn btn-sm btn-warning send-note-btn" 
                                                                        data-student-id="{{ $student->id }}"
                                                                        data-student-name="{{ $student->name }}"
                                                                        onclick="openSendNoteModal('{{ $student->id }}', '{{ $student->name }}')">
                                                                    <i class="fas fa-envelope"></i>
                                                                </button>
                                                                <a href="{{ route('teacher.memorization.show', $student->id) }}" 
                                                                   class="btn btn-sm btn-success" 
                                                                   title="تتبع الحفظ">
                                                                    <i class="fas fa-book-quran"></i>
                                                                </a>
                                                                <form action="{{ route('classroom.students.remove', [$classroom->id, $student->id]) }}" 
                                                                      method="POST" class="d-inline" 
                                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب من الفصل؟');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                                @if(!$student->email)
                                                                <button type="button" class="btn btn-sm btn-info" 
                                                                       onclick="(function(){ 
                                                                         var popup=document.getElementById('credentialsPopup'); 
                                                                         document.getElementById('popup-student-name').value='{{ $student->name }}'; 
                                                                         document.getElementById('popup-username').value='{{ $student->username }}'; 
                                                                         popup.style.display='block'; 
                                                                         return false;
                                                                       })();">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Floating Credentials Popup -->
                                    <div id="credentialsPopup" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; background-color: white; border-radius: 5px; box-shadow: 0 0 15px rgba(0,0,0,0.3); z-index: 9999;">
                                        <div class="p-3 bg-info text-white rounded-top">
                                            <h5 class="mb-0"><i class="fas fa-key me-2"></i>بيانات تسجيل الدخول</h5>
                                            <button type="button" onclick="document.getElementById('credentialsPopup').style.display='none';" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: white; font-size: 18px;">&times;</button>
                                        </div>
                                        <div class="p-3">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <span>اسم المستخدم وكلمة المرور متطابقان</span>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">اسم الطالب</label>
                                                <input type="text" class="form-control" id="popup-student-name" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">اسم المستخدم وكلمة المرور</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="popup-username" readonly>
                                                    <button class="btn btn-outline-secondary" type="button" id="copyCredentialBtn" onclick="(function(){
                                                        var input = document.getElementById('popup-username');
                                                        input.select();
                                                        document.execCommand('copy');
                                                        this.innerHTML = '<i class=\'fas fa-check\'></i> تم النسخ';
                                                        setTimeout(function(btn) {
                                                            btn.innerHTML = '<i class=\'fas fa-copy\'></i>';
                                                        }, 2000, this);
                                                    })();">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">*يمكن للطالب تغيير كلمة المرور بعد تسجيل الدخول لأول مرة</small>
                                            </div>
                                        </div>
                                        <div class="p-2 border-top text-end">
                                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('credentialsPopup').style.display='none';">إغلاق</button>
                                        </div>
                                    </div>

                                    @else
                                    <div class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="fas fa-user-graduate text-muted fa-4x"></i>
                                        </div>
                                        <p class="lead">لا يوجد طلاب مسجلين في هذا الفصل</p>
                                        <p>قم بإضافة طلاب لبدء تسجيل الحضور وإدارة المتابعة</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Sessions Panel -->
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>جلسات الفصل</h5>
                                    <a href="{{ route('classroom.sessions.create', $classroom->id) }}" class="btn btn-light btn-sm">
                                        <i class="fas fa-plus"></i> إنشاء جلسة جديدة
                                    </a>
                                </div>
                                <div class="card-body">
                                    @if(count($classroom->sessions) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>التاريخ</th>
                                                    <th>التوقيت</th>
                                                    <th>عدد الحضور</th>
                                                    <th>عدد الغياب</th>
                                                    <th>ملاحظات</th>
                                                    <th>الإجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($classroom->sessions()->orderBy('session_date', 'desc')->get() as $index => $session)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $session->session_date->format('Y-m-d') }}</td>
                                                    <td><span class="time-display">{{ $session->start_time }} - {{ $session->end_time }}</span></td>
                                                    <td>{{ $session->attendances->where('status', 'present')->count() }}</td>
                                                    <td>{{ $session->attendances->where('status', 'absent')->count() }}</td>
                                                    <td>{{ Str::limit($session->notes, 30) }}</td>
                                                    <td>
                                                        <a href="{{ route('classroom.sessions.show', [$classroom->id, $session->id]) }}" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4">
                                        <div class="mb-3">
                                            <i class="fas fa-calendar-day text-muted fa-4x"></i>
                                        </div>
                                        <p class="lead">لا توجد جلسات مسجلة لهذا الفصل</p>
                                        <p>قم بإنشاء جلسة جديدة لتسجيل الحضور والملاحظات</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Student Modal - Using custom styling -->
<div id="newStudentModalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: relative; width: 80%; max-width: 800px; margin: 50px auto; background-color: white; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.3);">
        <div style="background-color: #28a745; color: white; padding: 15px; border-radius: 5px 5px 0 0;">
            <h5 style="margin: 0;"><i class="fas fa-user-plus"></i> إضافة طالب جديد</h5>
            <button onclick="document.getElementById('newStudentModalOverlay').style.display='none';" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: white; font-size: 20px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 20px;">
            @if($errors->any() && request()->input('form_type') == 'new_student')
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('classroom.students.store', $classroom->id) }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="new_student">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="first_name" class="form-label">الاسم الأول<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="middle_name" class="form-label">الاسم الأوسط</label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="last_name" class="form-label">الاسم الأخير<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="birth_year" class="form-label">سنة الميلاد<span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="birth_year" name="birth_year" min="{{ date('Y') - 100 }}" max="{{ date('Y') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <!-- <div class="col-md-4 mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div> -->
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">العنوان</label>
                    <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('newStudentModalOverlay').style.display='none';">إلغاء</button>
                    <button type="submit" class="btn btn-success">إضافة طالب جديد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Existing Student Modal - Using custom styling -->
<div id="existingStudentModalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: relative; width: 80%; max-width: 800px; margin: 50px auto; background-color: white; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.3);">
        <div style="background-color: #17a2b8; color: white; padding: 15px; border-radius: 5px 5px 0 0;">
            <h5 style="margin: 0;"><i class="fas fa-user-check"></i> إضافة طالب موجود</h5>
            <button onclick="document.getElementById('existingStudentModalOverlay').style.display='none';" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: white; font-size: 20px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 20px;">
            @if($errors->any() && request()->input('form_type') == 'existing_student')
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('classroom.students.attach', $classroom->id) }}" method="POST">
                @csrf
                <input type="hidden" name="form_type" value="existing_student">
                <div class="mb-3">
                    <label for="student_search" class="form-label">البحث عن طالب</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="student_search" placeholder="اكتب اسم الطالب للبحث...">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="existing_student_id" class="form-label">اختر الطالب<span class="text-danger">*</span></label>
                    <select class="form-select form-select-lg" id="existing_student_id" name="existing_student_id" required>
                        <option value="">-- اختر طالب --</option>
                        @foreach($existingStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1"><i class="fas fa-info-circle"></i> سيتم عرض الطلاب الموجودين في نفس المدرسة فقط</small>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('existingStudentModalOverlay').style.display='none';">إلغاء</button>
                    <button type="submit" class="btn btn-info">إضافة إلى الفصل</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Note Modal -->
<div class="modal fade" id="sendNoteModal" tabindex="-1" aria-labelledby="sendNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="sendNoteModalLabel"><i class="fas fa-paper-plane me-2"></i>إرسال ملاحظة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('classroom.students.note', $classroom->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="note_student_id">
                    <div class="mb-3">
                        <label for="recipient" class="form-label">المستلم</label>
                        <input type="text" class="form-control" id="recipient" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان الملاحظة</label>
                        <input type="text" class="form-control" id="title" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">محتوى الملاحظة</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">إرسال</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Student Credentials Modal -->
<div class="modal fade" id="credentialsModal" tabindex="-1" aria-labelledby="credentialsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="credentialsModalLabel"><i class="fas fa-key me-2"></i>بيانات تسجيل الدخول</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="credentials-loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-2">جاري جلب البيانات...</p>
                </div>
                <div id="credentials-content" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="credential-note">اسم المستخدم وكلمة المرور متطابقان</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">اسم الطالب</label>
                        <input type="text" class="form-control" id="credential-student-name" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">اسم المستخدم وكلمة المرور</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="credential-username" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="copyCredential">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        <small class="text-muted">*يمكن للطالب تغيير كلمة المرور بعد تسجيل الدخول لأول مرة</small>
                    </div>
                    <div class="mb-3" id="credential-email-container">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="text" class="form-control" id="credential-email" readonly>
                    </div>
                </div>
                <div id="credentials-error" class="alert alert-danger" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

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
                    <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
                    <div class="mb-3">
                        <label class="form-label">الفصل المستهدف</label>
                        <input type="text" class="form-control" value="{{ $classroom->name }}" readonly>
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

@endsection 

@section('scripts')
<script>
    // Function to open broadcast modal
    function openBroadcastModal() {
        // Clear previous input
        document.getElementById('broadcast_subject').value = '';
        document.getElementById('broadcast_content').value = '';
        
        // Open the modal
        const broadcastModal = new bootstrap.Modal(document.getElementById('broadcastModal'));
        broadcastModal.show();
    }

    // Function to open send note modal
    function openSendNoteModal(studentId, studentName) {
        try {
            console.log("Opening send note modal for student:", studentId, studentName);
            
            // Set the student ID and name in the form
            document.getElementById('note_student_id').value = studentId;
            document.getElementById('recipient').value = studentName;
            
            // Clear previous input
            document.getElementById('title').value = '';
            document.getElementById('content').value = '';
            
            // Get the modal element
            var sendNoteModal = new bootstrap.Modal(document.getElementById('sendNoteModal'));
            
            // Show the modal
            sendNoteModal.show();
            
            console.log("Modal opened successfully");
        } catch (error) {
            console.error("Error opening send note modal:", error);
            alert("حدث خطأ أثناء محاولة فتح نافذة إرسال الرسالة. يرجى المحاولة مرة أخرى.");
        }
    }
    
    // Add copy functionality to credential popup
    document.addEventListener('DOMContentLoaded', function() {
        var copyCredBtn = document.getElementById('copyCredentialBtn');
        if (copyCredBtn) {
            copyCredBtn.addEventListener('click', function() {
                try {
                    var input = document.getElementById('popup-username');
                    input.select();
                    document.execCommand('copy');
                    
                    this.innerHTML = '<i class="fas fa-check"></i> تم النسخ';
                    setTimeout(function() {
                        copyCredBtn.innerHTML = '<i class="fas fa-copy"></i>';
                    }, 2000);
                    
                    console.log("Credentials copied successfully");
                } catch (error) {
                    console.error("Error copying credentials:", error);
                }
            });
        }
        
        // Show modal if there are validation errors
        @if($errors->any())
            @if(request()->input('form_type') == 'new_student')
                document.getElementById('newStudentModalOverlay').style.display = 'block';
            @elseif(request()->input('form_type') == 'existing_student')
                document.getElementById('existingStudentModalOverlay').style.display = 'block';
            @endif
        @endif
    });
</script>
@endsection 