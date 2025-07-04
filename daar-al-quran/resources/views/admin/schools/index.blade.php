@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-school me-2"></i>إدارة المدارس</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.schools.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> إضافة مدرسة جديدة
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">قائمة المدارس</h5>
                <span class="badge bg-success">{{ count($schools) }} مدرسة</span>
            </div>
            <div class="card-body">
                
                
                
                
                @if(count($schools) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>اسم المدرسة</th>
                                    <th>العنوان</th>
                                    <th>رمز المدرسة</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schools as $index => $school)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $school->name }}</td>
                                    <td>{{ $school->address ?? 'غير محدد' }}</td>
                                    <td><code>{{ $school->code }}</code></td>
                                    <td>{{ $school->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.schools.show', $school->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.schools.confirm-delete', $school->id) }}" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-school text-muted fa-3x mb-3"></i>
                        <p class="mb-0">لا توجد مدارس مسجلة حاليًا</p>
                        <a href="{{ route('admin.schools.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus me-1"></i> إضافة مدرسة جديدة
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 