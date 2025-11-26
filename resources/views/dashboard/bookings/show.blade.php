@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">تفاصيل الحجز #{{ $booking->id }}</h1>

            <div>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right ms-1"></i> رجوع لقائمة الحجوزات
                </a>
            </div>
        </div>

        {{-- بطاقة حالة الحجز الأساسية --}}
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <strong>الحالة العامة</strong>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>حالة الحجز:</strong>
                            @php
                                $status = $booking->booking_status;
                            @endphp

                            @switch($status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark">قيد الانتظار</span>
                                @break

                                @case('confirmed')
                                    <span class="badge bg-primary">مؤكد</span>
                                @break

                                @case('active')
                                    <span class="badge bg-info text-dark">نشط</span>
                                @break

                                @case('completed')
                                    <span class="badge bg-success">مكتمل</span>
                                @break

                                @case('cancelled')
                                    <span class="badge bg-danger">ملغي</span>
                                @break

                                @default
                                    <span class="badge bg-secondary">{{ $status }}</span>
                            @endswitch
                        </p>

                        <p>
                            <strong>حالة الدفع:</strong>
                            @php
                                $payStatus = $booking->payment_status;
                            @endphp

                            @switch($payStatus)
                                @case('pending')
                                    <span class="badge bg-warning text-dark">بانتظار الدفع</span>
                                @break

                                @case('paid')
                                    <span class="badge bg-success">مدفوع</span>
                                @break

                                @case('refunded')
                                    <span class="badge bg-info text-dark">مسترد</span>
                                @break

                                @case('failed')
                                    <span class="badge bg-danger">فشل الدفع</span>
                                @break

                                @default
                                    <span class="badge bg-secondary">{{ $payStatus }}</span>
                            @endswitch
                        </p>

                        <p>
                            <strong>تاريخ الإنشاء:</strong>
                            {{ $booking->created_at?->format('Y-m-d H:i') ?? '-' }}
                        </p>

                        @if ($booking->confirmed_at)
                            <p><strong>تاريخ التأكيد:</strong> {{ $booking->confirmed_at->format('Y-m-d H:i') }}</p>
                        @endif

                        @if ($booking->cancelled_at)
                            <p><strong>تاريخ الإلغاء:</strong> {{ $booking->cancelled_at->format('Y-m-d H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- تفاصيل الفترة والتكلفة --}}
            <div class="col-lg-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <strong>تفاصيل الفترة والتكلفة</strong>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>تاريخ البدء:</strong>
                            {{ $booking->start_date ?? '-' }}
                        </p>
                        <p>
                            <strong>تاريخ الانتهاء:</strong>
                            {{ $booking->end_date ?? '-' }}
                        </p>
                        <p>
                            <strong>مدة الإيجار (أيام):</strong>
                            {{ $booking->rental_duration_days }}
                        </p>
                        <p>
                            <strong>نوع التسعير:</strong>
                            @switch($booking->rental_rate_type)
                                @case('daily')
                                    يومي
                                @break

                                @case('weekly')
                                    أسبوعي
                                @break

                                @case('monthly')
                                    شهري
                                @break

                                @default
                                    {{ $booking->rental_rate_type }}
                            @endswitch
                        </p>
                        <p>
                            <strong>إجمالي التكلفة:</strong>
                            ${{ number_format($booking->total_cost, 2) }}
                        </p>
                        <p>
                            <strong>العربون المدفوع:</strong>
                            ${{ number_format($booking->deposit_amount_paid, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- مواقع التسليم + متطلبات خاصة --}}
            <div class="col-lg-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <strong>تفاصيل إضافية</strong>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>موقع الاستلام:</strong>
                            {{ $booking->pickup_location ?? '-' }}
                        </p>
                        <p>
                            <strong>موقع الإرجاع:</strong>
                            {{ $booking->return_location ?? '-' }}
                        </p>

                        @if ($booking->contract_url)
                            <p>
                                <strong>عقد الإيجار:</strong>
                                <a href="{{ $booking->contract_url }}" target="_blank">
                                    عرض العقد <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </p>
                        @endif

                        @if ($booking->special_requirements)
                            <p class="mb-0">
                                <strong>متطلبات خاصة:</strong><br>
                                <span style="white-space: pre-line;">
                                    {{ $booking->special_requirements }}
                                </span>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- بيانات الأطراف + المعدّة --}}
        <div class="row mb-4">
            {{-- المستأجر --}}
            <div class="col-lg-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <strong>المستأجر (Renter)</strong>
                    </div>
                    <div class="card-body">
                        @php $renter = $booking->renter; @endphp
                        @if ($renter)
                            <p><strong>الاسم:</strong> {{ $renter->first_name }} {{ $renter->last_name }}</p>
                            <p><strong>البريد:</strong> {{ $renter->email }}</p>
                            <p><strong>الهاتف:</strong> {{ $renter->phone ?? '-' }}</p>
                            <a href="{{ route('admin.users.show', $renter) }}" class="btn btn-sm btn-outline-primary">
                                عرض ملف المستخدم
                            </a>
                        @else
                            <p class="text-muted mb-0">تم حذف المستأجر أو غير متوفر.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- المالك --}}
            <div class="col-lg-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <strong>مالك المعدّة (Owner)</strong>
                    </div>
                    <div class="card-body">
                        @php $owner = $booking->owner; @endphp
                        @if ($owner)
                            <p><strong>الاسم:</strong> {{ $owner->first_name }} {{ $owner->last_name }}</p>
                            <p><strong>البريد:</strong> {{ $owner->email }}</p>
                            <p><strong>الهاتف:</strong> {{ $owner->phone ?? '-' }}</p>
                            <a href="{{ route('admin.users.show', $owner) }}" class="btn btn-sm btn-outline-primary">
                                عرض ملف المالك
                            </a>
                        @else
                            <p class="text-muted mb-0">تم حذف المالك أو غير متوفر.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- المعدّة --}}
            <div class="col-lg-4 mb-3">
                <div class="card shadow h-100">
                    <div class="card-header">
                        <strong>المعدة المحجوزة</strong>
                    </div>
                    <div class="card-body">
                        @php $equipment = $booking->equipment; @endphp
                        @if ($equipment)
                            <p><strong>الاسم:</strong> {{ $equipment->name ?? '-' }}</p>
                            <p><strong>الفئة:</strong> {{ optional($equipment->category)->category_name ?? '-' }}</p>
                            @if ($equipment->images && $equipment->images->first())
                                <img src="{{ $equipment->images->first()->image_url }}" alt="صورة المعدة"
                                    class="img-thumbnail mb-2" style="max-width: 120px;">
                            @endif
                            <a href="{{ route('admin.equipment.index', ['query' => $equipment->name]) }}"
                                class="btn btn-sm btn-outline-secondary">
                                فتح في صفحة المعدات
                            </a>
                        @else
                            <p class="text-muted mb-0">تم حذف المعدة أو غير متوفرة.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
