@if ($invoices->isEmpty())
    <div class="uk-text-center uk-padding">
        <span data-uk-icon="icon: file-text; ratio: 3" class="uk-text-muted"></span>
        <p class="uk-text-muted uk-margin-small-top">لا توجد فواتير حالياً.</p>
    </div>
@else
    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-divider uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="uk-text-center" style="width: 50px">#</th>
                    <th>رقم الفاتورة</th>
                    <th>المعدة</th>
                    <th class="uk-text-center">تاريخ الإصدار</th>
                    <th class="uk-text-center">تاريخ الاستحقاق</th>
                    <th class="uk-text-center">المبلغ</th>
                    <th class="uk-text-center">الحالة</th>
                    <th class="uk-text-center">تحميل</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td class="uk-text-center uk-text-muted">{{ $invoice->id }}</td>

                        <td>
                            <span class="uk-text-bold">{{ $invoice->invoice_number }}</span>
                        </td>

                        <td>
                            {{ $invoice->booking->equipment->name ?? 'تم حذف المعدة' }}
                        </td>

                        <td class="uk-text-center uk-text-small">
                            {{ $invoice->issue_date?->format('Y/m/d') ?? '—' }}
                        </td>

                        <td class="uk-text-center uk-text-small">
                            @if($invoice->due_date)
                                <span class="{{ $invoice->isOverdue() ? 'uk-text-danger' : '' }}">
                                    {{ $invoice->due_date->format('Y/m/d') }}
                                </span>
                            @else
                                —
                            @endif
                        </td>

                        <td class="uk-text-center">
                            <div class="uk-text-small uk-text-muted">
                                المجموع: {{ number_format($invoice->subtotal, 2) }} $
                            </div>
                            <div class="uk-text-small uk-text-muted">
                                ضريبة: {{ number_format($invoice->tax_amount, 2) }} $
                            </div>
                            <div class="uk-text-bold uk-text-primary">
                                {{ number_format($invoice->total_amount, 2) }} $
                            </div>
                        </td>

                        {{-- الحالة --}}
                        <td class="uk-text-center">
                            @php
                                $statusMap = [
                                    'issued'    => ['label' => 'صادرة',   'color' => 'uk-label-warning'],
                                    'paid'      => ['label' => 'مدفوعة',  'color' => 'uk-label-success'],
                                    'overdue'   => ['label' => 'متأخرة',  'color' => 'uk-label-danger'],
                                    'cancelled' => ['label' => 'ملغاة',   'color' => 'uk-label-danger'],
                                ];
                                $status = $invoice->isOverdue() ? 'overdue' : $invoice->status;
                                $s = $statusMap[$status] ?? ['label' => $status, 'color' => ''];
                            @endphp
                            <span class="uk-label {{ $s['color'] }}">{{ $s['label'] }}</span>
                        </td>

                        {{-- تحميل --}}
                        <td class="uk-text-center">
                            @if($invoice->pdf_url)
                                <a href="{{ $invoice->pdf_url }}"
                                   class="uk-button uk-button-default uk-button-small"
                                   download target="_blank">
                                    <span data-uk-icon="icon: download; ratio: 0.8"></span>
                                    PDF
                                </a>
                            @else
                                <a href="{{ route('admin.invoices.download', $invoice->id) }}"
                                   class="uk-button uk-button-primary uk-button-small">
                                    <span data-uk-icon="icon: download; ratio: 0.8"></span>
                                    تحميل
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif