@extends('layouts.app') {{-- أو admin.layout إذا كان لديك ملف layout خاص بالأدمن --}}

@section('content')
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">لوحة تحكم المشرف</h1> {{-- تم التعديل --}}
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> توليد تقرير
            </a>
        </div>

        <!--  صف الإحصائيات  -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                {{-- إحصائية: إجمالي المستخدمين --}}
                <x-stats-card icon="fas fa-users" title="إجمالي المستخدمين" :value="$stats['total_users']" color="primary" />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                {{-- إحصائية: إجمالي المعدات  --}}
                <x-stats-card icon="fas fa-hard-hat" title="إجمالي المعدات" :value="$stats['total_equipment']" color="success" />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                {{-- إحصائية: إجمالي الحجوزات --}}
                <x-stats-card icon="fas fa-calendar-check" title="إجمالي الحجوزات" :value="$stats['total_bookings']" color="info" />
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                {{-- إحصائية: إجمالي الأرباح  --}}
                <x-stats-card icon="fas fa-dollar-sign" title="إجمالي الأرباح" :value="$stats['total_revenue']" color="warning"
                    prefix="$" />
            </div>
        </div>

        <!-- صف الرسوم البيانية  -->
        <div class="row">
            <!-- الرسم البياني الدائري (توزيع المعدات حسب الفئة) -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">توزيع المعدات حسب الفئة</h6>
                    </div>
                    <div class="card-body"><canvas id="equipmentCategoryChart"></canvas></div>
                </div>
            </div>
            <!-- الرسم البياني الخطي  -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">الحجوزات الشهرية</h6>
                    </div>
                    <div class="card-body"><canvas id="monthlyBookingsChart"></canvas></div>
                </div>
            </div>
        </div>

        <!-- صف الجداول  -->
        <div class="row">
            <!-- جدول آخر المعدات المضافة (لمراجعة الإعلانات) -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">آخر 5 معدات بانتظار الموافقة</h6> {{-- (مراجعة الإعلانات) --}}
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody>
                                    @forelse($pendingEquipment as $equipment)
                                        {{-- متغير جديد --}}
                                        <tr>
                                            <td class="text-center">
                                                {{-- عرض صورة المعدة إذا وجدت --}}
                                                <img src="{{ $equipment->images->first()->image_url ?? asset('assets/img/default-equipment.png') }}"
                                                    alt="صورة المعدة" width="40" height="40" class="rounded">
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ Str::limit($equipment->name, 25) }}</div>
                                                <small
                                                    class="text-muted">{{ $equipment->owner->first_name ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-end">
                                                @if ($equipment->is_approved_by_admin)
                                                    <span class="badge bg-success text-white">معتمد</span>
                                                @else
                                                    <a href="{{ route('admin.equipment.index', ['status' => 'pending', 'query' => $equipment->name]) }}"
                                                        class="btn btn-sm btn-warning">مراجعة</a> {{-- توجيه لصفحة إدارة المعدات مع فلتر --}}
                                                    {{-- أو إذا أردت صفحة تفاصيل مباشرة: <a href="{{ route('admin.equipment.show', $equipment) }}" class="btn btn-sm btn-warning">مراجعة</a> --}}
                                                @endif
                                            </td>
                                            <td class="text-end text-muted small">
                                                @if ($equipment->created_at)
                                                    {{ $equipment->created_at->diffForHumans() }}
                                                @else
                                                    --
                                                @endif
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

            <!-- جدول آخر الشكاوى والاستفسارات -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">آخر 5 شكاوى/استفسارات</h6> {{-- (إدارة الشكاوى والاستفسارات) --}}
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <tbody>
                                    @forelse($latestComplaints as $complaint)
                                        {{-- متغير جديد (سأفترض وجود نموذج Complaint/Message للشكاوى) --}}
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-placeholder rounded-circle me-3">
                                                        <span>{{ mb_substr($complaint->sender->first_name ?? 'U', 0, 1) }}</span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            {{ $complaint->sender->first_name ?? 'مستخدم' }}</div>
                                                        <small
                                                            class="text-muted">{{ Str::limit($complaint->content, 30) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.complaints.show', $complaint) }}"
                                                    class="btn btn-sm btn-info">عرض</a> {{-- زر عرض --}}
                                            </td>
                                            <td class="text-end text-muted small">
                                                @if ($complaint->created_at)
                                                    {{ $complaint->created_at->diffForHumans() }}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center p-4" colspan="3">لا توجد شكاوى أو استفسارات حديثة.
                                            </td>
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
        document.addEventListener('DOMContentLoaded', function() {
            // الرسم البياني لتوزيع المعدات حسب الفئة
            const equipmentCategoryCtx = document.getElementById('equipmentCategoryChart').getContext('2d');
            new Chart(equipmentCategoryCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($equipmentCategoriesCount->pluck('category_name')) !!},
                    datasets: [{
                        data: {!! json_encode($equipmentCategoriesCount->pluck('equipment_count')) !!},
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                            '#6f42c1', '#fd7e14'
                        ],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b200',
                            '#d43f30', '#5a2d9b', '#e26b0a'
                        ],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                    cutoutPercentage: 80,
                }
            });

            // الرسم البياني للحجوزات الشهرية
            const monthlyBookingsCtx = document.getElementById('monthlyBookingsChart').getContext('2d');
            new Chart(monthlyBookingsCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlyBookings->pluck('month_name')) !!}, // أسماء الأشهر
                    datasets: [{
                        label: 'عدد الحجوزات',
                        data: {!! json_encode($monthlyBookings->pluck('count')) !!},
                        backgroundColor: '#4e73df',
                        hoverBackgroundColor: '#2e59d9',
                        borderColor: '#4e73df',
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'month'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 6
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                maxTicksLimit: 5,
                                padding: 10,
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                }
            });
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
