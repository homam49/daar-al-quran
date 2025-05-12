@extends('layouts.moderator')

@section('moderator-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-edit me-2"></i>تعديل مدرسة</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('moderator.schools') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-right me-1"></i> العودة لقائمة المدارس
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">تعديل بيانات المدرسة</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('moderator.schools.update', $school->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">اسم المدرسة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $school->name) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">عنوان المدرسة</label>
                        <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $school->address) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">رمز المدرسة</label>
                        <input type="text" class="form-control" id="code" value="{{ $school->code }}" readonly disabled>
                        <small class="text-muted">لا يمكن تغيير رمز المدرسة</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_id" class="form-label">مدير المدرسة</label>
                        <select class="form-select" id="admin_id" name="admin_id">
                            <option value="">اختر مدير المدرسة</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}" {{ (old('admin_id', $school->admin_id) == $admin->id) ? 'selected' : '' }}>
                                    {{ $admin->name }} ({{ $admin->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> حفظ التغييرات
                        </button>
                        <a href="{{ route('moderator.schools.show', $school->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 