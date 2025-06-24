@extends('layouts.' . $layout)

@section($contentSection)
<div class="container">
    <div class="page-header">
        <h1 class="page-title">
            <div class="page-header-icon"><i class="fas fa-user-cog"></i></div>
            الملف الشخصي
        </h1>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h4 class="mb-0">الملف الشخصي للطالب</h4>
        </div>
        
        <div class="card-body">
            <form method="POST" action="{{ route('student.profile.info.update') }}" class="mb-4">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="username" class="form-label">اسم المستخدم:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ $student->username }}">
                        </div>
                        @error('username')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="email" class="form-label">البريد الإلكتروني:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $student->email }}">
                            @if(!$student->email_verified_at && $student->email)
                                <span class="input-group-text text-bg-warning">غير مؤكد</span>
                            @elseif($student->email_verified_at)
                                <span class="input-group-text text-bg-success">مؤكد</span>
                            @endif
                        </div>
                        @error('email')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                        @if(!$student->email_verified_at && $student->email)
                            <div class="mt-2">
                                <form method="POST" action="{{ route('student.verification.resend') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">إعادة إرسال رابط التأكيد</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="full_name" class="form-label">الاسم الكامل:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="text" class="form-control" id="full_name" value="{{ $student->first_name }} {{ $student->middle_name }} {{ $student->last_name }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="birth_year" class="form-label">سنة الميلاد:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="text" class="form-control" id="birth_year" value="{{ $student->birth_year }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="phone" class="form-label">رقم الهاتف:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ $student->phone }}">
                        </div>
                        @error('phone')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="address" class="form-label">العنوان:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ $student->address }}">
                        </div>
                        @error('address')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="current_password" class="form-label">كلمة المرور الحالية:</label>
                    </div>
                    <div class="col-md-9">
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="أدخل كلمة المرور الحالية لتأكيد التغييرات">
                        </div>
                        @error('current_password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-9 offset-md-3">
                        <button type="submit" class="btn btn-primary">تحديث البيانات</button>
                    </div>
                </div>
            </form>
            
            <hr>
            
            <h5 class="mb-3">تغيير كلمة المرور</h5>
            <form method="POST" action="{{ route('student.profile.password.update') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="current_password_2" class="form-label">كلمة المرور الحالية:</label>
                    </div>
                    <div class="col-md-9">
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password_2" name="current_password">
                        @error('current_password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="password" class="form-label">كلمة المرور الجديدة:</label>
                    </div>
                    <div class="col-md-9">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                        @error('password')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-3 text-md-end">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة:</label>
                    </div>
                    <div class="col-md-9">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-9 offset-md-3">
                        <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                    </div>
                </div>
            </form>
            
            <hr>
            
            <h5 class="mb-3">معلومات الحساب</h5>
            
            <!-- <div class="row mb-3">
                <div class="col-md-3 text-md-end">
                    <strong>حالة البريد الإلكتروني:</strong>
                </div>
                <div class="col-md-9">
                    @if($student->email_verified_at)
                        <span class="badge bg-success">مؤكد</span>
                        <small class="text-muted">تم التأكيد في {{ $student->email_verified_at->format('Y-m-d H:i') }}</small>
                    @else
                        <span class="badge bg-warning text-dark">غير مؤكد</span>
                    @endif
                </div>
            </div> -->
            
            <div class="row mb-3">
                <div class="col-md-3 text-md-end">
                    <strong>تاريخ التسجيل:</strong>
                </div>
                <div class="col-md-9">
                    {{ $student->created_at->format('Y-m-d H:i') }}
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3 text-md-end">
                    <strong>آخر تحديث:</strong>
                </div>
                <div class="col-md-9">
                    {{ $student->updated_at->format('Y-m-d H:i') }}
                </div>
            </div>
            
            <hr>
            
            <h5 class="mb-3">معلومات الفصول</h5>
            <div class="row mb-3">
                <div class="col-md-3 text-md-end">
                    <strong>عدد الفصول:</strong>
                </div>
                <div class="col-md-9">
                    {{ $student->classRooms->count() }}
                </div>
            </div>
            
            @if($student->classRooms->count() > 0)
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>اسم الفصل</th>
                            <th>المدرسة</th>
                            <th>المعلم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->classRooms as $classroom)
                        <tr>
                            <td>{{ $classroom->name }}</td>
                            <td>{{ $classroom->school ? $classroom->school->name : 'غير محدد' }}</td>
                            <td>{{ $classroom->user ? $classroom->user->name : 'غير محدد' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 