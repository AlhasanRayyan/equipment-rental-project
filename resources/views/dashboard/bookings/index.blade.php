@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة الحجوزات</h1>
            <a href="{{ route('admin.bookings.trash') }}" class="btn btn-outline-danger">
                سلة المحذوفات
            </a>
        </div>

        <div class="card shadow">
            {{-- <div class="card-header py-3">
                <form action="{{ route('admin.bookings.index') }}" method="GET" class="d-flex">
                    <input type="text" name="query" class="form-control" placeholder="ابحث برقم الحجز أو المستخدم..."
                        value="{{ $query ?? '' }}">

                    <select name="status" class="form-select ms-2" style="max-width: 200px">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>معلق</option>
                        <option value="confirmed" {{ ($status ?? '') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                        <option value="active" {{ ($status ?? '') == 'active' ? 'selected' : '' }}>فعّال</option>
                        <option value="completed" {{ ($status ?? '') == 'completed' ? 'selected' : '' }}>منتهي</option>
                        <option value="cancelled" {{ ($status ?? '') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>

                    <button class="btn btn-primary ms-2">بحث</button>
                </form>

            </div> --}}
            <div class="card-header py-3">
                <form action="{{ route('admin.bookings.index') }}" method="GET" class="d-flex">
                    <input type="text" name="query" class="form-control" placeholder="ابحث برقم الحجز أو المستخدم..."
                        value="{{ $query ?? '' }}">

                    <select name="status" class="form-select ms-2" style="max-width: 200px" onchange="this.form.submit()">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>معلق</option>
                        <option value="confirmed" {{ ($status ?? '') === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                        <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>فعّال</option>
                        <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>منتهي</option>
                        <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>

                    <button class="btn btn-primary ms-2">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>المعدة</th>
                            <th>المستأجر</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->equipment->name ?? '-' }}</td>
                                <td>{{ $booking->renter->first_name ?? '-' }}</td>

                                {{-- الحالة --}}
                                <td>
                                    @switch($booking->booking_status)
                                        @case('pending')
                                            <span class="badge bg-warning text-dark">معلق</span>
                                        @break

                                        @case('confirmed')
                                            <span class="badge bg-info text-dark">مؤكد</span>
                                        @break

                                        @case('active')
                                            <span class="badge bg-primary">قيد التنفيذ</span>
                                        @break

                                        @case('completed')
                                            <span class="badge bg-success">منتهي</span>
                                        @break

                                        @case('cancelled')
                                            <span class="badge bg-danger">ملغي</span>
                                        @break

                                        @default
                                            -
                                    @endswitch
                                </td>


                                <td>{{ $booking->created_at?->format('Y-m-d H:i') }}</td>

                                <td class="text-center">
                                    <div class="dropdown action-dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">
                                            إجراءات
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">

                                            {{-- تفاصيل --}}
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('admin.bookings.show', $booking) }}">
                                                    <i class="fas fa-eye text-info"></i> تفاصيل الحجز
                                                </a>
                                            </li>

                                            {{-- تأكيد --}}
                                            @if (in_array($booking->booking_status, ['pending']))
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="event.preventDefault(); document.getElementById('confirm-booking-{{ $booking->id }}').submit();">
                                                        <i class="fas fa-check-circle text-success"></i> تأكيد الحجز
                                                    </a>
                                                </li>
                                                <form id="confirm-booking-{{ $booking->id }}"
                                                    action="{{ route('admin.bookings.confirm', $booking) }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                </form>
                                            @endif

                                            {{-- تفعيل --}}
                                            @if (in_array($booking->booking_status, ['confirmed']))
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="event.preventDefault(); document.getElementById('activate-booking-{{ $booking->id }}').submit();">
                                                        <i class="fas fa-play text-primary"></i> تفعيل (بدء الإيجار)
                                                    </a>
                                                </li>
                                                <form id="activate-booking-{{ $booking->id }}"
                                                    action="{{ route('admin.bookings.activate', $booking) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                            @endif

                                            {{-- إنهاء --}}
                                            @if (in_array($booking->booking_status, ['active']))
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="event.preventDefault(); document.getElementById('complete-booking-{{ $booking->id }}').submit();">
                                                        <i class="fas fa-flag-checkered text-success"></i> إنهاء الحجز
                                                    </a>
                                                </li>
                                                <form id="complete-booking-{{ $booking->id }}"
                                                    action="{{ route('admin.bookings.complete', $booking) }}"
                                                    method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                            @endif

                                            {{-- إعادة لمعلّق --}}
                                            @if (!in_array($booking->booking_status, ['pending', 'cancelled']))
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="event.preventDefault(); document.getElementById('hold-booking-{{ $booking->id }}').submit();">
                                                        <i class="fas fa-pause-circle text-warning"></i> إعادة إلى معلّق
                                                    </a>
                                                </li>
                                                <form id="hold-booking-{{ $booking->id }}"
                                                    action="{{ route('admin.bookings.hold', $booking) }}" method="POST"
                                                    class="d-none">
                                                    @csrf
                                                </form>
                                            @endif

                                            {{-- إلغاء --}}
                                            @if ($booking->booking_status !== 'cancelled')
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cancelBookingModal{{ $booking->id }}">
                                                        <i class="fas fa-times-circle"></i> إلغاء الحجز
                                                    </a>
                                                </li>
                                            @endif

                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>

                                            {{-- حذف لسلة المحذوفات --}}
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#deleteBookingModal{{ $booking->id }}">
                                                    <i class="fas fa-trash"></i> حذف الحجز
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- مودال إلغاء الحجز --}}
                            <div class="modal fade" id="cancelBookingModal{{ $booking->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">إلغاء الحجز #{{ $booking->id }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>هل أنت متأكد من إلغاء هذا الحجز؟</p>
                                                <div class="mb-3">
                                                    <label class="form-label">سبب الإلغاء (اختياري)</label>
                                                    <textarea name="reason" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">إغلاق</button>
                                                <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- مودال حذف الحجز --}}
                            <div class="modal fade" id="deleteBookingModal{{ $booking->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-header">
                                                <h5 class="modal-title">حذف الحجز #{{ $booking->id }}</h5>
                                                <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>هل أنت متأكد من نقل هذا الحجز إلى سلة المحذوفات؟</p>
                                                <div class="alert alert-warning mb-0">
                                                    يمكن استرجاعه لاحقاً من سلة المحذوفات
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-danger">نعم، حذف</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4">لا توجد حجوزات.</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>


                <div class="card-footer">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    @endsection
