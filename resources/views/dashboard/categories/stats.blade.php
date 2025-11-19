@extends('layouts.app')

@section('styles')
    <style>
        .category-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إحصائيات فئات المعدات</h1>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-1"></i> العودة لقائمة الفئات
            </a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-chart-bar me-2"></i>
                    ملخص عام
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    إجمالي عدد المعدات المرتبطة بالفئات:
                    <strong>{{ $totalEquipments }}</strong>
                </p>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-list me-2"></i>
                    تفصيل الفئات وعدد المعدات
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>الفئة</th>
                                <th>الفئة الرئيسية</th>
                                <th>عدد المعدات</th>
                                <th>نسبة من الإجمالي</th>
                                <th>آخر تحديث</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                @php
                                    $percent = $totalEquipments > 0
                                        ? round(($category->equipment_count / $totalEquipments) * 100)
                                        : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($category->image_url)
                                                <img src="{{ asset('storage/' . $category->image_url) }}"
                                                     class="category-image me-2" alt="">
                                            @endif
                                            <span class="fw-bold">{{ $category->category_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($category->parent)
                                            <span class="badge bg-secondary">
                                                {{ $category->parent->category_name }}
                                            </span>
                                        @else
                                            <span class="text-muted small">فئة رئيسية</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $category->equipment_count }} معدة
                                        </span>
                                    </td>
                                    <td>{{ $percent }}%</td>
                                    <td>{{ $category->updated_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">
                                        لا توجد فئات مسجّلة حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
