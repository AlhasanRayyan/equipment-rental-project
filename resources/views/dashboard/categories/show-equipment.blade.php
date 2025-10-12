@extends('layouts.app')

@section('styles')
    <style>
        .equipment-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">المعدات في فئة: {{ $equipmentCategory->category_name }}</h1>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-right fa-sm me-2"></i>العودة للفئات
            </a>
        </div>

        @include('partials.alerts')

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-hard-hat me-2"></i>المعدات المرتبطة ({{ $equipment->total() }})
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>المعدة</th>
                                <th>المالك</th>
                                <th>الحالة</th>
                                <th>تاريخ الإضافة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($equipment as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->images->first()->image_url ?? asset('assets/img/default-equipment.png') }}"
                                                alt="صورة المعدة" class="equipment-image me-3">
                                            <div>
                                                <div class="fw-bold">{{ Str::limit($item->name, 30) }}</div>
                                                <div class="text-muted small">{{ Str::limit($item->category->category_name ?? 'غير محدد', 20) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $item->owner->first_name ?? 'غير معروف' }} {{ $item->owner->last_name ?? '' }}</td>
                                    <td>
                                        @if ($item->is_approved_by_admin)
                                            <span class="badge bg-success">معتمد</span>
                                        @else
                                            <span class="badge bg-warning text-dark">بانتظار الموافقة</span>
                                        @endif
                                        <br>
                                        <span class="badge bg-secondary mt-1">{{ ucfirst($item->status) }}</span>
                                    </td>
                                    <td>{{ $item->created_at->diffForHumans() }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.equipment.show', $item) }}" class="btn btn-sm btn-primary">عرض</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">لا توجد معدات مرتبطة بهذه الفئة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">{{ $equipment->links() }}</div>
            </div>
        </div>
    </div>
@endsection