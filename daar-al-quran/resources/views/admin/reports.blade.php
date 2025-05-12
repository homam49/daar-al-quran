@extends('layouts.admin')

@section('admin-content')
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i>التقارير</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">تقارير المدارس</h5>
            </div>
            <div class="card-body">
                @if(count($schools) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>اسم المدرسة</th>
                                <th>عدد الفصول</th>
                                <th>عدد المعلمين</th>
                                <th>عدد الطلاب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schools as $school)
                            <tr>
                                <td>{{ $school->name }}</td>
                                <td>{{ $school->classRooms()->count() }}</td>
                                <td>{{ $school->teachers()->count() }}</td>
                                <td>{{ $school->students()->count() }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-pdf"></i> تصدير التقرير
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-school text-muted fa-3x mb-3"></i>
                    <p class="mb-0">لا توجد مدارس حاليًا</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">إحصائيات النظام</h5>
            </div>
            <div class="card-body">
                <p class="text-center py-4">
                    سيتم تفعيل الإحصائيات قريبًا
                </p>
            </div>
        </div>
    </div>
</div>
@endsection 