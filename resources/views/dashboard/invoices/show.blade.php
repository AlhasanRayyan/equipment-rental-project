@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">


            <div class="col-md-11 col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">تفاصيل الفاتورة</h2>
                    <a href="{{ route('admin.invoices.download', $invoice->id) }}" class="btn btn-dark">
                        <i class="fas fa-download"></i> تنزيل PDF
                    </a>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3">معلومات الفاتورة</h5>
                                <p><strong>رقم الفاتورة:</strong> {{ $invoice->invoice_number }}</p>
                                <p><strong>تاريخ الإصدار:</strong> {{ $invoice->issue_date?->format('Y-m-d') }}</p>
                                <p><strong>تاريخ الاستحقاق:</strong> {{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</p>
                               
                                <p>
                                    <strong>الحالة:</strong>
                                    @switch($invoice->status)
                                        @case('issued')
                                            <span class="badge bg-primary">صادرة</span>
                                        @break

                                        @case('paid')
                                            <span class="badge bg-success">مدفوعة</span>
                                        @break

                                        @case('overdue')
                                            <span class="badge bg-warning text-dark">متأخرة</span>
                                        @break

                                        @case('cancelled')
                                            <span class="badge bg-danger">ملغية</span>
                                        @break

                                        @default
                                            -
                                    @endswitch
                                </p>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3">معلومات الحجز</h5>
                                <p><strong>رقم الحجز:</strong> {{ $invoice->booking->id ?? '-' }}</p>
                                <p><strong>المعدة:</strong> {{ $invoice->booking->equipment->name ?? '-' }}</p>
                                <p><strong>تاريخ البداية:</strong>
                                    {{ $invoice->booking->start_date?->format('Y-m-d') ?? '-' }}</p>
                                <p><strong>تاريخ النهاية:</strong>
                                    {{ $invoice->booking->end_date?->format('Y-m-d') ?? '-' }}</p>
                                <p><strong>عدد الأيام:</strong> {{ $invoice->booking->rental_duration_days ?? '-' }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="mb-3">بيانات المستأجر</h5>
                                <p>
                                    <strong>الاسم:</strong>
                                    {{ $invoice->booking->renter->first_name ?? '' }}
                                    {{ $invoice->booking->renter->last_name ?? '' }}
                                </p>
                                <p><strong>الإيميل:</strong> {{ $invoice->booking->renter->email ?? '-' }}</p>
                            </div>

                            <div class="col-md-6">
                                <h5 class="mb-3">بيانات المالك</h5>
                                <p>
                                    <strong>الاسم:</strong>
                                    {{ $invoice->booking->owner->first_name ?? '' }}
                                    {{ $invoice->booking->owner->last_name ?? '' }}
                                </p>
                                <p><strong>الإيميل:</strong> {{ $invoice->booking->owner->email ?? '-' }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <strong>Subtotal:</strong>
                                    <div class="mt-2">${{ number_format((float) $invoice->subtotal, 2) }}</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <strong>Tax:</strong>
                                    <div class="mt-2">${{ number_format((float) $invoice->tax_amount, 2) }}</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded">
                                    <strong>Total:</strong>
                                    <div class="mt-2 fw-bold">${{ number_format((float) $invoice->total_amount, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">
                                رجوع
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
