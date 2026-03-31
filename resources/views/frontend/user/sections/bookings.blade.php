@if ($bookings->isEmpty())
    <div class="uk-text-center uk-padding">
        <span data-uk-icon="icon: calendar; ratio: 3" class="uk-text-muted"></span>
        <p class="uk-text-muted uk-margin-small-top">لا توجد حجوزات حالياً.</p>
    </div>
@else
    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-divider uk-table-hover uk-table-middle">
            <thead>
                <tr>
                    <th class="uk-text-center" style="width: 50px">#</th>
                    <th>المعدة</th>
                    <th class="uk-text-center">من</th>
                    <th class="uk-text-center">إلى</th>
                    <th class="uk-text-center">حالة الحجز</th>
                    <th class="uk-text-center">الدفع</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bookings as $b)
                    <tr>
                        <td class="uk-text-center uk-text-muted">{{ $b->id }}</td>

                        <td>
                            <div class="uk-flex uk-flex-middle">
                                @if($b->equipment?->images->first())
                                    <img src="{{ asset('storage/' . $b->equipment->images->first()->image_url) }}"
                                         width="40" height="40"
                                         style="border-radius: 6px; object-fit: cover; margin-left: 10px;">
                                @endif
                                <span>{{ $b->equipment->name ?? 'تم حذف المعدة' }}</span>
                            </div>
                        </td>

                        <td class="uk-text-center uk-text-small">
                            {{ \Carbon\Carbon::parse($b->start_date)->format('Y/m/d') }}
                        </td>

                        <td class="uk-text-center uk-text-small">
                            {{ \Carbon\Carbon::parse($b->end_date)->format('Y/m/d') }}
                        </td>

                        {{-- حالة الحجز --}}
                        <td class="uk-text-center">
                            @php
                                $statusMap = [
                                    'pending'    => ['label' => 'معلق',   'color' => 'uk-label-warning'],
                                    'confirmed'  => ['label' => 'مؤكد',   'color' => 'uk-label-success'],
                                    'active'     => ['label' => 'نشط',    'color' => 'uk-label-success'],
                                    'completed'  => ['label' => 'مكتمل',  'color' => ''],
                                    'cancelled'  => ['label' => 'ملغي',   'color' => 'uk-label-danger'],
                                ];
                                $s = $statusMap[$b->booking_status] ?? ['label' => $b->booking_status, 'color' => ''];
                            @endphp
                            <span class="uk-label {{ $s['color'] }}">{{ $s['label'] }}</span>
                        </td>

                        {{-- حالة الدفع --}}
                        <td class="uk-text-center">
                            @if($b->payment?->status === 'completed')
                                <span class="uk-label uk-label-success">
                                    <span data-uk-icon="icon: check; ratio: 0.8"></span>
                                    مدفوع
                                </span>

                            @elseif($b->payment_method === 'cash' && $b->payment?->status !== 'completed')
                                <span class="uk-label uk-label-warning">نقدي - بانتظار التأكيد</span>

                            @elseif($b->paymentProof)
                                @php $proof = $b->paymentProof; @endphp
                                <span class="uk-label uk-label-{{ $proof->statusColor() }}">
                                    {{ $proof->statusLabel() }}
                                </span>
                                @if($proof->isRejected())
                                    <br>
                                    <a href="{{ route('renter.payments.upload-proof', $b->id) }}"
                                       class="uk-button uk-button-danger uk-button-small uk-margin-small-top">
                                        رفع إشعار جديد
                                    </a>
                                @endif

                            @elseif(in_array($b->booking_status, ['pending', 'confirmed']))
                                <a href="{{ route('renter.payments.show', $b->id) }}"
                                   class="uk-button uk-button-primary uk-button-small">
                                    <span data-uk-icon="icon: credit-card; ratio: 0.8"></span>
                                    ادفع الآن
                                </a>

                            @else
                                <span class="uk-text-muted uk-text-small">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif