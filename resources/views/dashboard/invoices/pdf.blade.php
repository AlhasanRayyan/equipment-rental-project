<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>فاتورة {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @font-face {
            font-family: 'Cairo';
            src: url('{{ str_replace('\\', '/', public_path('fonts/Cairo.ttf')) }}') format('truetype');
        }

        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
        }

        .page {
            background: #fff;
            padding: 30px;
            border-top: 10px solid #9f181c;
        }

        .clearfix:after {
            content: '';
            display: block;
            clear: both;
        }

        /* ===== HEADER ===== */
        .header-company {
            float: left;
            width: 48%;
        }

        .header-invoice {
            float: right;
            width: 48%;
            text-align: right;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #9f181c;
        }

        .company-info {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
            line-height: 1.8;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #333;
        }

        .invoice-number {
            font-size: 13px;
            color: #9f181c;
            font-weight: bold;
            margin-top: 4px;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 18px 0;
            clear: both;
        }

        /* ===== INFO BOXES ===== */
        .info-col {
            float: left;
            width: 31%;
            margin-right: 2%;
        }

        .info-col:last-child {
            margin-right: 0;
        }

        .info-col h4 {
            font-size: 11px;
            color: #9f181c;
            font-weight: bold;
            border-bottom: 2px solid #9f181c;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .info-col p {
            font-size: 12px;
            color: #444;
            line-height: 1.9;
        }

        .badge {
            display: inline;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-issued {
            background: #fff3cd;
            color: #856404;
        }

        .badge-paid {
            background: #d1e7dd;
            color: #0a3622;
        }

        .badge-overdue {
            background: #f8d7da;
            color: #842029;
        }

        .badge-cancelled {
            background: #e2e3e5;
            color: #41464b;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        thead tr {
            background: #333;
        }

        thead th {
            color: #fff;
            padding: 10px 12px;
            text-align: right;
            font-size: 12px;
        }

        tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        /* ===== TOTALS ===== */
        .totals {
            float: right;
            width: 260px;
            margin-top: 10px;
        }

        .totals table {
            margin: 0;
        }

        .totals td {
            padding: 6px 10px !important;
            font-size: 12px;
            border: none !important;
        }

        .total-final td {
            font-size: 15px;
            font-weight: bold;
            color: #9f181c;
            border-top: 2px solid #333 !important;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 16px;
        }

        .sig-right {
            float: left;
            width: 45%;
        }

        .sig-left {
            float: right;
            width: 45%;
            text-align: right;
        }

        .sig-line {
            border-top: 1px dashed #aaa;
            margin-top: 35px;
            padding-top: 5px;
            font-size: 11px;
            color: #999;
        }

        .footer-note {
            text-align: center;
            font-size: 10px;
            color: #aaa;
            margin-top: 20px;
            clear: both;
        }

        .bottom-border {
            border-bottom: 6px solid #333;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- HEADER --}}
        <div class="clearfix">
            <div class="header-invoice">
                <div class="invoice-title">فاتورة</div>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            </div>
            <div class="header-company">
                <div class="company-name">SPCER</div>
                <div class="company-info">
                    +970 59 723 4892<br>
                    rentals@my-domain.net<br>
                    فلسطين - غزة
                </div>
            </div>
        </div>

        <hr>

        {{-- INFO BOXES --}}
        <div class="clearfix">
            <div class="info-col">
                <h4>معلومات الفاتورة</h4>
                <p>
                    <strong>تاريخ الاصدار:</strong> {{ $invoice->issue_date?->format('Y/m/d') ?? '—' }}<br>
                    <strong>تاريخ الاستحقاق:</strong> {{ $invoice->due_date?->format('Y/m/d') ?? '—' }}<br>
                    <strong>الحالة:</strong>
                    @php
                        $statusMap = [
                            'issued' => ['label' => 'صادرة', 'class' => 'badge-issued'],
                            'paid' => ['label' => 'مدفوعة', 'class' => 'badge-paid'],
                            'overdue' => ['label' => 'متاخرة', 'class' => 'badge-overdue'],
                            'cancelled' => ['label' => 'ملغاة', 'class' => 'badge-cancelled'],
                        ];
                        $s = $statusMap[$invoice->status] ?? ['label' => $invoice->status, 'class' => ''];
                    @endphp
                    <span class="badge {{ $s['class'] }}">{{ $s['label'] }}</span>
                </p>
            </div>

            <div class="info-col">
                <h4>المستاجر</h4>
                <p>
                    <strong>{{ $invoice->booking->renter->first_name ?? '' }}
                        {{ $invoice->booking->renter->last_name ?? '' }}</strong><br>
                    {{ $invoice->booking->renter->email ?? '—' }}<br>
                    {{ $invoice->booking->renter->phone_number ?? '' }}
                </p>
            </div>

            <div class="info-col">
                <h4>المالك</h4>
                <p>
                    <strong>{{ $invoice->booking->owner->first_name ?? '' }}
                        {{ $invoice->booking->owner->last_name ?? '' }}</strong><br>
                    {{ $invoice->booking->owner->email ?? '—' }}<br>
                    {{ $invoice->booking->owner->phone_number ?? '' }}
                </p>
            </div>
        </div>

        {{-- TABLE --}}
        <table>
            <thead>
                <tr>
                    <th>المعدة</th>
                    <th>رقم الحجز</th>
                    <th>تاريخ البداية</th>
                    <th>تاريخ النهاية</th>
                    <th>عدد الايام</th>
                    <th>السعر اليومي</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $invoice->booking->equipment->name ?? '—' }}</strong></td>
                    <td>#{{ $invoice->booking->id }}</td>
                    <td>{{ $invoice->booking->start_date?->format('Y/m/d') ?? '—' }}</td>
                    <td>{{ $invoice->booking->end_date?->format('Y/m/d') ?? '—' }}</td>
                    <td>{{ $invoice->booking->rental_duration_days ?? '—' }} يوم</td>
                    <td>{{ number_format($invoice->booking->equipment->daily_rate ?? 0, 2) }} $</td>
                </tr>
            </tbody>
        </table>

        {{-- TOTALS --}}
        <div class="clearfix">
            <div class="totals">
                <table>
                    <tbody>
                        <tr>
                            <td>المجموع الفرعي</td>
                            <td><strong>{{ number_format($invoice->subtotal, 2) }} $</strong></td>
                        </tr>
                        @if (!empty($invoice->discount_amount) && $invoice->discount_amount > 0)
                            <tr>
                                <td>الخصم</td>
                                <td style="color:#2a7a2a"><strong>- {{ number_format($invoice->discount_amount, 2) }}
                                        $</strong></td>
                            </tr>
                        @endif
                        <tr>
                            <td>الضريبة</td>
                            <td><strong>{{ number_format($invoice->tax_amount, 2) }} $</strong></td>
                        </tr>
                        <tr class="total-final">
                            <td>الاجمالي</td>
                            <td>{{ number_format($invoice->total_amount, 2) }} $</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer clearfix">
            <div class="sig-right">
                <div class="sig-line">توقيع المستاجر</div>
            </div>
            <div class="sig-left">
                <div class="sig-line">توقيع المالك</div>
            </div>
        </div>

        <div class="bottom-border"></div>

        <div class="footer-note">
            شكرا لاستخدامكم منصة SPCER لتاجير المعدات — جميع المبالغ بالدولار الامريكي
        </div>

    </div>
</body>

</html>
