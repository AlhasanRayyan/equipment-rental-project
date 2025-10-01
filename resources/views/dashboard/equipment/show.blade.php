@extends('layouts.app')

@section('styles')
    <style>
        .equipment-main-image {
            max-width: 100%;
            height: auto;
            max-height: 400px; /* تحديد ارتفاع أقصى للصورة الرئيسية */
            object-fit: contain; /* عرض الصورة بالكامل دون قص */
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .equipment-thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.25rem;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.2s ease-in-out;
        }
        .equipment-thumbnail.active {
            border-color: var(--bs-primary);
        }
        .detail-label {
            font-weight: bold;
            color: #6c757d;
        }
        .detail-value {
            color: #343a40;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">تفاصيل المعدة: {{ $equipment->name }}</h1>
            <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-right fa-sm me-2"></i>العودة لإدارة المعدات
            </a>
        </div>

        @include('partials.alerts')

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>معلومات المعدة
                </h6>
                <div class="dropdown action-dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">إجراءات</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if (!$equipment->is_approved_by_admin)
                            <li>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('approve-form-{{ $equipment->id }}').submit();">
                                    <i class="fas fa-check-circle text-success"></i> الموافقة على المعدة
                                </a>
                            </li>
                            <form id="approve-form-{{ $equipment->id }}" action="{{ route('admin.equipment.approve', $equipment) }}" method="POST" class="d-none">@csrf</form>
                            <li>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#rejectEquipmentModal{{ $equipment->id }}">
                                    <i class="fas fa-times-circle text-danger"></i> رفض المعدة
                                </a>
                            </li>
                        @endif
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteEquipmentModal{{ $equipment->id }}"><i class="fas fa-trash text-danger"></i> حذف المعدة</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        {{-- عرض الصور --}}
                        <div class="mb-3 text-center">
                            @php
                                $mainImage = $equipment->images->where('is_main', true)->first();
                                $fallbackImage = asset('assets/img/default-equipment.png');
                                $displayImage = $mainImage ? $mainImage->image_url : $fallbackImage;
                            @endphp
                            <img src="{{ $displayImage }}" alt="صورة المعدة الرئيسية" class="equipment-main-image mb-3" id="mainEquipmentImage">
                            @if ($equipment->images->count() > 1)
                                <div class="d-flex justify-content-center flex-wrap gap-2">
                                    @foreach ($equipment->images as $image)
                                        <img src="{{ $image->image_url }}" alt="صورة مصغرة" class="equipment-thumbnail {{ $image->is_main ? 'active' : '' }}" data-full-image="{{ $image->image_url }}">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <h4 class="mb-3 text-primary">{{ $equipment->name }}</h4>
                        <p class="detail-label">الوصف:</p>
                        <p class="detail-value">{{ $equipment->description }}</p>

                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">المالك:</span>
                                <span class="detail-value">{{ $equipment->owner->first_name ?? 'غير معروف' }} {{ $equipment->owner->last_name ?? '' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">الفئة:</span>
                                <span class="detail-value">{{ $equipment->category->category_name ?? 'غير محدد' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">السعر اليومي:</span>
                                <span class="detail-value">{{ number_format($equipment->daily_rate, 2) }}</span>
                            </li>
                            @if ($equipment->weekly_rate)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">السعر الأسبوعي:</span>
                                <span class="detail-value">{{ number_format($equipment->weekly_rate, 2) }}</span>
                            </li>
                            @endif
                            @if ($equipment->monthly_rate)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">السعر الشهري:</span>
                                <span class="detail-value">{{ number_format($equipment->monthly_rate, 2) }}</span>
                            </li>
                            @endif
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">مبلغ التأمين:</span>
                                <span class="detail-value">{{ number_format($equipment->deposit_amount, 2) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">حالة الموافقة:</span>
                                <span class="detail-value">
                                    @if ($equipment->is_approved_by_admin)
                                        <span class="badge bg-success">معتمد</span>
                                    @else
                                        <span class="badge bg-warning text-dark">بانتظار الموافقة</span>
                                    @endif
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">الحالة التشغيلية:</span>
                                <span class="detail-value"><span class="badge bg-info">{{ ucfirst($equipment->status) }}</span></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">تتبع GPS:</span>
                                <span class="detail-value">@if($equipment->has_gps_tracker) <span class="badge bg-primary">نعم</span> @else <span class="badge bg-secondary">لا</span> @endif</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="detail-label">تاريخ الإضافة:</span>
                                <span class="detail-value">{{ $equipment->created_at->format('Y-m-d H:i') }}</span>
                            </li>
                            @if($equipment->last_maintenance_date)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="detail-label">تاريخ آخر صيانة:</span>
                                    <span class="detail-value">{{ $equipment->last_maintenance_date->format('Y-m-d') }}</span>
                                </li>
                                <li class="list-group-item">
                                    <span class="detail-label">ملاحظات الصيانة:</span>
                                    <p class="detail-value mb-0">{{ $equipment->maintenance_notes }}</p>
                                </li>
                            @endif
                        </ul>
                        
                        {{-- يمكن عرض تقييمات المعدة هنا --}}
                        @if($equipment->total_reviews > 0)
                            <div class="mt-4">
                                <h6 class="text-primary">التقييم العام:</h6>
                                <p class="h5">{{ number_format($equipment->average_rating, 2) }} / 5 (من {{ $equipment->total_reviews }} مراجعة)</p>
                                {{-- يمكن إضافة رابط لصفحة المراجعات الكاملة لهذه المعدة --}}
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- يمكن عرض سجلات التتبع هنا إذا كانت المعدة تحتوي على GPS --}}
                @if($equipment->has_gps_tracker && $equipment->trackingRecords->count() > 0)
                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <h5 class="text-primary mb-3">سجل التتبع الأخير</h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>خط العرض (Latitude)</th>
                                            <th>خط الطول (Longitude)</th>
                                            <th>السرعة</th>
                                            <th>مستوى البطارية</th>
                                            <th>الحالة</th>
                                            <th>الوقت</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($equipment->trackingRecords->take(5) as $record)
                                            <tr>
                                                <td>{{ $record->latitude }}</td>
                                                <td>{{ $record->longitude }}</td>
                                                <td>{{ number_format($record->speed, 2) }} كم/س</td>
                                                <td>{{ number_format($record->battery_level, 2) }}%</td>
                                                <td><span class="badge bg-secondary">{{ ucfirst($record->status) }}</span></td>
                                                <td>{{ $record->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- يمكن إضافة رابط لصفحة عرض كل سجلات التتبع إذا كان العدد كبيراً --}}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- نافذة رفض المعدة (مكررة من index.blade.php) --}}
    <div class="modal fade" id="rejectEquipmentModal{{ $equipment->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.equipment.reject', $equipment) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">رفض المعدة: {{ $equipment->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد من رفض المعدة <strong>{{ $equipment->name }}</strong>؟</p>
                        <div class="mb-3">
                            <label for="reject_reason_{{ $equipment->id }}" class="form-label">سبب الرفض (اختياري)</label>
                            <textarea name="reject_reason" id="reject_reason_{{ $equipment->id }}" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="alert alert-warning" role="alert">
                            سيتم إخطار المالك بأن معدته قد تم رفضها.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- نافذة حذف المعدة (مكررة من index.blade.php) --}}
    <div class="modal fade" id="deleteEquipmentModal{{ $equipment->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.equipment.destroy', $equipment) }}" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد من حذف المعدة <strong>{{ $equipment->name }}</strong>؟</p>
                        <div class="alert alert-danger" role="alert">
                            سيتم حذف جميع البيانات المتعلقة بهذه المعدة بشكل دائم.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // تهيئة Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });

            // وظيفة تبديل الصورة الرئيسية عند النقر على الصورة المصغرة
            const mainImage = document.getElementById('mainEquipmentImage');
            document.querySelectorAll('.equipment-thumbnail').forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    // إزالة الفئة 'active' من جميع الصور المصغرة
                    document.querySelectorAll('.equipment-thumbnail').forEach(t => t.classList.remove('active'));
                    // إضافة الفئة 'active' للصورة المصغرة التي تم النقر عليها
                    this.classList.add('active');
                    // تغيير الصورة الرئيسية
                    mainImage.src = this.dataset.fullImage;
                });
            });
        });
    </script>
@endpush