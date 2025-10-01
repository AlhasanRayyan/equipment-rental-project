@extends('layouts.app')

@section('styles')
    <style>
        .equipment-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        .action-dropdown .dropdown-menu { min-width: 200px; border-radius: 0.5rem; box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15); }
        .action-dropdown .dropdown-item { display: flex; align-items: center; gap: 10px; padding: 0.5rem 1rem; }
        .action-dropdown .dropdown-item i { width: 18px; text-align: center; opacity: 0.7; }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة المعدات</h1>
        </div>

        @include('partials.alerts')

        <div class="card shadow">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary mb-2 mb-md-0">
                        <i class="fas fa-hard-hat me-2"></i>قائمة المعدات ({{ $equipment->total() }})
                    </h6>
                    <form id="equipmentFilterForm" action="{{ route('admin.equipment.index') }}" method="GET" class="d-flex" style="max-width: 600px; width: 100%;">
                        <input type="text" name="query" class="form-control me-2" placeholder="ابحث بالاسم أو الوصف..." value="{{ $query ?? '' }}">
                        <select name="status" id="statusFilterSelect" class="form-select me-2" style="width: auto;"> {{-- تم إضافة id هنا --}}
                            <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>بانتظار الموافقة</option>
                            <option value="approved" {{ $statusFilter == 'approved' ? 'selected' : '' }}>معتمد</option>
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>جميع المعدات</option>
                        </select>
                        <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                        @if ($query || $statusFilter != 'pending')
                            <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary ms-2" title="إلغاء البحث والفلاتر"><i class="fas fa-times"></i></a>
                        @endif
                    </form>
                </div>
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
                                            <div class="dropdown action-dropdown">
                                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">إجراءات</button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewEquipmentModal{{ $item->id }}"><i class="fas fa-eye text-primary"></i> عرض التفاصيل</a></li>
                                                    @if (!$item->is_approved_by_admin)
                                                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('approve-form-{{ $item->id }}').submit();"><i class="fas fa-check-circle text-success"></i> الموافقة على المعدة</a></li>
                                                        <form id="approve-form-{{ $item->id }}" action="{{ route('admin.equipment.approve', $item) }}" method="POST" class="d-none">@csrf</form>
                                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#rejectEquipmentModal{{ $item->id }}"><i class="fas fa-times-circle text-danger"></i> رفض المعدة</a></li>
                                                    @endif
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteEquipmentModal{{ $item->id }}"><i class="fas fa-trash text-danger"></i> حذف المعدة</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center p-4">لا توجد معدات لعرضها.</td>
                                    </tr>
                                @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">{{ $equipment->links() }}</div>
            </div>
        </div>
    </div>

    @foreach ($equipment as $item)
        {{-- نافذة عرض تفاصيل المعدة --}}
        <div class="modal fade" id="viewEquipmentModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تفاصيل المعدة: {{ $item->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                @if($item->images->count() > 0)
                                    <img src="{{ $item->images->first()->image_url }}" alt="صورة المعدة" class="img-fluid rounded shadow-sm">
                                @else
                                    <img src="{{ asset('assets/img/default-equipment.png') }}" alt="صورة افتراضية" class="img-fluid rounded shadow-sm">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p><strong>الاسم:</strong> {{ $item->name }}</p>
                                <p><strong>الوصف:</strong> {{ $item->description }}</p>
                                <p><strong>المالك:</strong> {{ $item->owner->first_name ?? 'غير معروف' }} {{ $item->owner->last_name ?? '' }}</p>
                                <p><strong>الفئة:</strong> {{ $item->category->category_name ?? 'غير محدد' }}</p>
                                <p><strong>السعر اليومي:</strong> {{ number_format($item->daily_rate, 2) }}</p>
                                <p><strong>مبلغ التأمين:</strong> {{ number_format($item->deposit_amount, 2) }}</p>
                                <p><strong>الحالة:</strong>
                                    @if ($item->is_approved_by_admin)
                                        <span class="badge bg-success">معتمد</span>
                                    @else
                                        <span class="badge bg-warning text-dark">بانتظار الموافقة</span>
                                    @endif
                                    <span class="badge bg-info ms-2">{{ ucfirst($item->status) }}</span>
                                </p>
                                <p><strong>تاريخ الإضافة:</strong> {{ $item->created_at->format('Y-m-d H:i') }}</p>
                                @if($item->last_maintenance_date)
                                    <p><strong>تاريخ آخر صيانة:</strong> {{ $item->last_maintenance_date->format('Y-m-d') }}</p>
                                    <p><strong>ملاحظات الصيانة:</strong> {{ $item->maintenance_notes }}</p>
                                @endif
                                <p><strong>تتبع GPS:</strong> @if($item->has_gps_tracker) <span class="badge bg-primary">نعم</span> @else <span class="badge bg-secondary">لا</span> @endif</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- نافذة رفض المعدة --}}
        <div class="modal fade" id="rejectEquipmentModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.equipment.reject', $item) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">رفض المعدة: {{ $item->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من رفض المعدة <strong>{{ $item->name }}</strong>؟</p>
                            <div class="mb-3">
                                <label for="reject_reason_{{ $item->id }}" class="form-label">سبب الرفض (اختياري)</label>
                                <textarea name="reject_reason" id="reject_reason_{{ $item->id }}" class="form-control" rows="3"></textarea>
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

        {{-- نافذة حذف المعدة --}}
        <div class="modal fade" id="deleteEquipmentModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.equipment.destroy', $item) }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">تأكيد الحذف</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من حذف المعدة <strong>{{ $item->name }}</strong>؟</p>
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
    @endforeach
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // تهيئة Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl) });

            const statusFilterSelect = document.getElementById('statusFilterSelect');
            if (statusFilterSelect) {
                statusFilterSelect.addEventListener('change', function() {
                    document.getElementById('equipmentFilterForm').submit();
                });
            }
        });
    </script>
@endpush