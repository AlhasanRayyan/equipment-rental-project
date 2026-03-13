<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
        }

        .container {
            width: 100%;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h2 {
            margin: 0 0 10px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        table th {
            background: #f2f2f2;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Invoice</h2>
            <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Issue Date:</strong> {{ $invoice->issue_date?->format('Y-m-d') }}</p>
            <p><strong>Due Date:</strong> {{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</p>
            <p>
                <strong>الحالة:</strong>
                @switch($invoice->status)
                    @case('issued')
                        صادرة
                    @break

                    @case('paid')
                        مدفوعة
                    @break

                    @case('overdue')
                        متأخرة
                    @break

                    @case('cancelled')
                        ملغية
                    @break

                    @default
                        -
                @endswitch
            </p>
        </div>

        <div class="section">
            <h3>Booking Details</h3>
            <p><strong>Booking ID:</strong> {{ $invoice->booking->id ?? '-' }}</p>
            <p><strong>Equipment:</strong> {{ $invoice->booking->equipment->name ?? '-' }}</p>
            <p><strong>Start Date:</strong> {{ $invoice->booking->start_date?->format('Y-m-d') ?? '-' }}</p>
            <p><strong>End Date:</strong> {{ $invoice->booking->end_date?->format('Y-m-d') ?? '-' }}</p>
            <p><strong>Rental Duration Days:</strong> {{ $invoice->booking->rental_duration_days ?? '-' }}</p>
        </div>

        <div class="section">
            <h3>Renter Information</h3>
            <p>
                {{ $invoice->booking->renter->first_name ?? '' }}
                {{ $invoice->booking->renter->last_name ?? '' }}
            </p>
            <p>{{ $invoice->booking->renter->email ?? '-' }}</p>
        </div>

        <div class="section">
            <h3>Owner Information</h3>
            <p>
                {{ $invoice->booking->owner->first_name ?? '' }}
                {{ $invoice->booking->owner->last_name ?? '' }}
            </p>
            <p>{{ $invoice->booking->owner->email ?? '-' }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">${{ number_format((float) $invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Tax</td>
                    <td class="text-right">${{ number_format((float) $invoice->tax_amount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Total</td>
                    <td class="text-right">${{ number_format((float) $invoice->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
