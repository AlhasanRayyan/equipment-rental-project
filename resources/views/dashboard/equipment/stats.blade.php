@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إحصاءات المعدات</h1>
        <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> رجوع لقائمة المعدات
        </a>
    </div>

    {{-- كروت ملخص الأرقام --}}
    <div class="row">

        <div class="col-md-4 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            إجمالي المعدات
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $total }}</div>
                    </div>
                    <div>
                        <i class="fas fa-cubes fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            معدات موافَق عليها
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $approved }}</div>
                        <small class="text-muted">
                            متاحة وموافَق عليها: {{ $approvedAndAvailable }}
                        </small>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            غير موافَق عليها / قيد المراجعة
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $notApproved }}</div>
                    </div>
                    <div>
                        <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            معدات متاحة
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $available }}</div>
                    </div>
                    <div>
                        <i class="fas fa-toggle-on fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            معدات غير متاحة
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $unavailable }}</div>
                    </div>
                    <div>
                        <i class="fas fa-ban fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- جدول إحصاءات حسب الفئة --}}
    <div class="card shadow mt-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-layer-group me-2"></i>
                إحصاءات حسب فئات المعدات
            </h6>
        </div>
        <div class="card-body">
            @if($categoriesStats->isEmpty())
                <p class="text-muted mb-0">لا توجد فئات معدات مسجّلة.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>الفئة</th>
                                <th class="text-center">عدد المعدات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoriesStats as $category)
                                <tr>
                                    <td>{{ $category->category_name ?? ('فئة #' . $category->id) }}</td>
                                    <td class="text-center">{{ $category->equipments_count }}</td>
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
