@extends('layouts.app')

@section('content')
    @php
        // ألوان ثابتة للفئات الرئيسية (بنفس ترتيب $allParentCategories)
        $baseColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
            '#e74a3b', '#20c997', '#6610f2', '#fd7e14',
            '#17a2b8', '#6f42c1', '#d63384', '#858796',
        ];

        // خريطة: اسم الفئة => لون
        $categoryColorMap = [];
        foreach ($allParentCategories as $index => $cat) {
            $categoryColorMap[$cat->category_name] = $baseColors[$index % count($baseColors)];
        }
    @endphp

    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">لوحة تحكم المشرف</h1>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> توليد تقرير
            </a>
        </div>

        <!-- صف الإحصائيات -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <x-stats-card icon="fas fa-users" title="إجمالي المستخدمين" :value="$stats['total_users']" color="primary" />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-stats-card icon="fas fa-hard-hat" title="إجمالي المعدات" :value="$stats['total_equipment']" color="success" />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-stats-card icon="fas fa-calendar-check" title="إجمالي الحجوزات" :value="$stats['total_bookings']" color="info" />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <x-stats-card icon="fas fa-dollar-sign" title="إجمالي الأرباح" :value="$stats['total_revenue']" color="warning" prefix="$" />
            </div>
        </div>

        <!-- صف الرسوم البيانية -->
        <div class="row">
            <!-- توزيع المعدات حسب الفئة -->
            <div class="col-xl-5 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">توزيع المعدات حسب الفئة</h6>
                    </div>
                    <div class="card-body">
                        {{-- الفئات الرئيسية فوق الدائرة --}}
                        <div class="mb-3 text-center">
                            <strong>الفئات الرئيسية:</strong>
                            <div class="mt-2">
                                @foreach ($allParentCategories as $index => $category)
                                    @php
                                        $color = $categoryColorMap[$category->category_name] ?? '#858796';
                                        $count =
                                            optional(
                                                $equipmentCategoriesCount->firstWhere(
                                                    'category_name',
                                                    $category->category_name
                                                )
                                            )['equipment_count'] ?? 0;
                                    @endphp
                                    <span
                                        class="badge text-white mb-2 me-1"
                                        style="background-color: {{ $color }};">
                                        {{ $category->category_name }}
                                        ({{ $count }})
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- الدائرة --}}
                        <div style="height:260px">
                            <canvas id="equipmentCategoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الحجوزات الشهرية -->
            <div class="col-xl-7 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">الحجوزات الشهرية</h6>
                    </div>
                    <div class="card-body">
                        <div style="height:260px">
                            <canvas id="monthlyBookingsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- صف الجداول -->
        <div class="row">
            <!-- آخر المعدات بانتظار الموافقة -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">آخر 5 معدات بانتظار الموافقة</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody>
                                    @forelse($pendingEquipment as $equipment)
                                        <tr>
                                            <td class="text-center">
                                                <img src="{{ $equipment->images->first()->image_url ?? asset('assets/img/default-equipment.png') }}"
                                                    alt="صورة المعدة" width="40" height="40" class="rounded">
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ Str::limit($equipment->name, 25) }}</div>
                                                <small class="text-muted">{{ $equipment->owner->first_name ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-end">
                                                @if ($equipment->is_approved_by_admin)
                                                    <span class="badge bg-success text-white">معتمد</span>
                                                @else
                                                    <a href="{{ route('admin.equipment.index', ['status' => 'pending', 'query' => $equipment->name]) }}"
                                                        class="btn btn-sm btn-warning">مراجعة</a>
                                                @endif
                                            </td>
                                            <td class="text-end text-muted small">
                                                {{ $equipment->created_at ? $equipment->created_at->diffForHumans() : '--' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center p-4" colspan="4">لا توجد معدات بانتظار الموافقة.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- آخر الشكاوى / الاستفسارات -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">آخر 5 شكاوى / استفسارات</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody>
                                    @forelse($latestComplaints as $complaint)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-placeholder rounded-circle me-3">
                                                        <span>{{ mb_substr($complaint->sender->first_name ?? 'U', 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $complaint->sender->first_name ?? 'مستخدم' }}</div>
                                                        <small class="text-muted">{{ Str::limit($complaint->content, 30) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.complaints.show', $complaint) }}"
                                                    class="btn btn-sm btn-info">عرض</a>
                                            </td>
                                            <td class="text-end text-muted small">
                                                {{ $complaint->created_at ? $complaint->created_at->diffForHumans() : '--' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center p-4" colspan="3">لا توجد شكاوى أو استفسارات حديثة.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // خريطة الألوان من السيرفر: { 'اسم الفئة': '#color' }
    const categoryColorMap = @json($categoryColorMap);

    // ================================
    //  توزيع المعدات حسب الفئة (دائرة)
    // ================================
    const ctxCategory = document.getElementById('equipmentCategoryChart').getContext('2d');
    const categoryData = @json($equipmentCategoriesCount);

    const categoryLabels = categoryData.map(item => item.category_name);
    const categoryCounts = categoryData.map(item => item.equipment_count);

    // نأخذ اللون حسب اسم الفئة (نفس ألوان الشيبس فوق)
    const categoryColors = categoryLabels.map(name => categoryColorMap[name] ?? '#cccccc');

    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryCounts,
                backgroundColor: categoryColors,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false, // لأننا نعرض الفئات كشيبس فوق
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' معدة';
                        }
                    }
                }
            },
            animation: {
                animateScale: false,
                animateRotate: true
            }
        }
    });

    // ================================
    //  الحجوزات الشهرية (أعمدة ملوّنة)
    // ================================
    const ctxBookings = document.getElementById('monthlyBookingsChart').getContext('2d');
    const monthlyBookings = @json($monthlyBookings);

    const bookingLabels = monthlyBookings.map(item => item.month_name);
    const bookingCounts = monthlyBookings.map(item => item.count);

    const baseColors = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
        '#e74a3b', '#20c997', '#6610f2', '#fd7e14',
        '#17a2b8', '#6f42c1', '#d63384', '#858796',
    ];
    const bookingColors = bookingLabels.map((_, idx) => baseColors[idx % baseColors.length]);

    new Chart(ctxBookings, {
        type: 'bar',
        data: {
            labels: bookingLabels,
            datasets: [{
                label: 'عدد الحجوزات',
                data: bookingCounts,
                backgroundColor: bookingColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' حجز';
                        }
                    }
                }
            },
            animation: {
                duration: 800,
                easing: 'easeOutQuart'
            }
        }
    });
</script>

<style>
    .avatar-placeholder {
        width: 40px;
        height: 40px;
        background-color: #4e73df;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
    }
</style>
@endpush
