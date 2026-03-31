@extends('layouts.master')

@section('title', 'مراجعة الدفع - حجز #' . $booking->id)

@section('content')
<main class="page-main">
    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_categories.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="page-head__title">مراجعة الدفع</div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ route('owner.payments.index') }}">المدفوعات</a></li>
                            <li><span>حجز #{{ $booking->id }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="uk-section uk-container">

            @if(session('success'))
                <div class="uk-alert-success" data-uk-alert>
                    <a class="uk-alert-close" data-uk-close></a>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="uk-alert-danger" data-uk-alert>
                    <a class="uk-alert-close" data-uk-close></a>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="uk-grid uk-grid-medium" data-uk-grid>

                {{-- العمود الأيسر: بيانات الحجز والعميل --}}
                <div class="uk-width-2-3@m">

                    {{-- بيانات الحجز --}}
                    <div class="uk-card uk-card-default uk-card-body uk-margin-bottom">
                        <h3 class="uk-card-title">بيانات الحجز</h3>
                        <table class="uk-table uk-table-striped">
                            <tr><td>رقم الحجز</td><td>#{{ $booking->id }}</td></tr>
                            <tr><td>المعدة</td><td>{{ $booking->equipment->name }}</td></tr>
                            <tr><td>العميل</td><td>{{ $booking->renter->name }}</td></tr>
                            <tr><td>من</td><td>{{ \Carbon\Carbon::parse($booking->start_date)->format('Y/m/d') }}</td></tr>
                            <tr><td>إلى</td><td>{{ \Carbon\Carbon::parse($booking->end_date)->format('Y/m/d') }}</td></tr>
                            <tr><td>إجمالي التكلفة</td><td>{{ number_format($booking->total_cost, 2) }} $</td></tr>
                            <tr>
                                <td>طريقة الدفع</td>
                                <td>
                                    @php
                                        $methodLabels = ['cash' => 'نقدي', 'bank_transfer' => 'تحويل بنكي', 'wallet' => 'محفظة إلكترونية'];
                                    @endphp
                                    {{ $methodLabels[$booking->payment_method] ?? $booking->payment_method }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    {{-- إشعار التحويل (إن وجد) --}}
                    @if($booking->paymentProof)
                    @php $proof = $booking->paymentProof; @endphp
                    <div class="uk-card uk-card-default uk-card-body uk-margin-bottom">
                        <h3 class="uk-card-title">
                            إشعار التحويل
                            <span class="uk-label uk-label-{{ $proof->statusColor() }} uk-margin-small-right">
                                {{ $proof->statusLabel() }}
                            </span>
                        </h3>

                        <div class="uk-grid uk-grid-medium" data-uk-grid>
                            <div class="uk-width-1-2@s">
                                <table class="uk-table uk-table-small">
                                    <tr>
                                        <td><strong>المبلغ المحوّل:</strong></td>
                                        <td>{{ number_format($proof->transferred_amount, 2) }} $</td>
                                    </tr>
                                    <tr>
                                        <td><strong>البنك / المحفظة:</strong></td>
                                        <td>{{ $proof->bank_or_wallet_name }}</td>
                                    </tr>
                                    @if($proof->notes)
                                    <tr>
                                        <td><strong>الملاحظات:</strong></td>
                                        <td>{{ $proof->notes }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>تاريخ الرفع:</strong></td>
                                        <td>{{ $proof->created_at->format('Y/m/d H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="uk-width-1-2@s uk-text-center">
                                <p><strong>صورة الإشعار:</strong></p>
                                <a href="{{ asset('storage/' . $proof->proof_image) }}" data-uk-lightbox="caption: إشعار التحويل">
                                    <img src="{{ asset('storage/' . $proof->proof_image) }}"
                                         alt="إشعار التحويل"
                                         style="max-width: 200px; border-radius: 8px; border: 2px solid #e5e5e5; cursor: zoom-in;">
                                </a>
                                <p class="uk-text-small uk-text-muted uk-margin-small-top">انقر للتكبير</p>
                            </div>
                        </div>

                        {{-- إجراءات المراجعة --}}
                        @if($proof->isPending())
                        <hr>
                        <div class="uk-margin-top">
                            <p class="uk-text-muted">تحقق من حسابك البنكي/محفظتك، ثم اتخذ الإجراء المناسب:</p>
                            <div class="uk-grid uk-grid-small" data-uk-grid>

                                {{-- قبول --}}
                                <div class="uk-width-auto">
                                    <form action="{{ route('owner.payments.approve-proof', $booking) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="uk-button uk-button-primary"
                                                onclick="return confirm('هل تأكدت من وصول المبلغ وتريد قبول الإشعار؟')">
                                            <span data-uk-icon="icon: check"></span>
                                            قبول وتأكيد الحجز
                                        </button>
                                    </form>
                                </div>

                                {{-- رفض --}}
                                <div class="uk-width-auto">
                                    <button class="uk-button uk-button-danger"
                                            data-uk-toggle="target: #reject-modal">
                                        <span data-uk-icon="icon: close"></span>
                                        رفض الإشعار
                                    </button>
                                </div>
                            </div>
                        </div>
                        @elseif($proof->isRejected())
                        <div class="uk-alert-danger uk-margin-top" data-uk-alert>
                            <p><strong>سبب الرفض:</strong> {{ $proof->rejection_reason }}</p>
                            <p class="uk-text-small">تمت المراجعة بواسطة: {{ $proof->reviewer->name ?? '-' }}
                                في {{ $proof->reviewed_at?->format('Y/m/d H:i') }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- دفع كاش بدون إشعار --}}
                    @elseif($booking->payment_method === 'cash')
                    <div class="uk-card uk-card-default uk-card-body uk-margin-bottom">
                        <h3 class="uk-card-title">الدفع النقدي</h3>
                        <div class="uk-alert-warning" data-uk-alert>
                            <p>اختار العميل الدفع نقداً. عند استلام المبلغ، اضغط على زر التأكيد.</p>
                        </div>

                        <div class="uk-grid uk-grid-small" data-uk-grid>
                            {{-- تأكيد استلام الكاش --}}
                            <div class="uk-width-auto">
                                <form action="{{ route('owner.payments.confirm-cash', $booking) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="uk-button uk-button-primary"
                                            onclick="return confirm('هل استلمت المبلغ النقدي؟')">
                                        <span data-uk-icon="icon: check"></span>
                                        تأكيد استلام المبلغ
                                    </button>
                                </form>
                            </div>

                            {{-- تغيير حالة الحجز --}}
                            <div class="uk-width-auto">
                                <button class="uk-button uk-button-default"
                                        data-uk-toggle="target: #status-modal">
                                    تغيير حالة الحجز
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- العمود الأيمن: بيانات العميل --}}
                <div class="uk-width-1-3@m">
                    <div class="uk-card uk-card-default uk-card-body uk-text-center">
                        <img class="uk-border-circle" src="{{ $booking->renter->avatar_url ?? asset('assets/default-avatar.png') }}"
                             width="80" height="80" alt="{{ $booking->renter->name }}">
                        <h4 class="uk-margin-small-top">{{ $booking->renter->name }}</h4>
                        <p class="uk-text-small uk-text-muted">{{ $booking->renter->email }}</p>
                        @if($booking->renter->phone)
                            <a href="tel:{{ $booking->renter->phone }}" class="uk-button uk-button-default uk-button-small uk-width-1-1">
                                <span data-uk-icon="icon: receiver"></span>
                                {{ $booking->renter->phone }}
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

{{-- Modal رفض الإشعار --}}
<div id="reject-modal" class="uk-flex-top" data-uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <button class="uk-modal-close-default" type="button" data-uk-close></button>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">رفض إشعار التحويل</h3>
        </div>
        <form action="{{ route('owner.payments.reject-proof', $booking) }}" method="POST">
            @csrf
            <div class="uk-modal-body">
                <div class="uk-alert-warning" data-uk-alert>
                    <p>سيتم إشعار العميل بسبب الرفض ليتمكن من رفع إشعار صحيح.</p>
                </div>
                <label class="uk-form-label">سبب الرفض <span class="uk-text-danger">*</span></label>
                <textarea class="uk-textarea" name="rejection_reason" rows="4"
                          placeholder="اذكر سبب الرفض بوضوح، مثلاً: المبلغ غير مكتمل، الصورة غير واضحة..." required minlength="10"></textarea>
                @error('rejection_reason')
                    <p class="uk-text-danger uk-text-small">{{ $message }}</p>
                @enderror
            </div>
            <div class="uk-modal-footer uk-text-left">
                <button type="button" class="uk-button uk-button-default uk-modal-close">إلغاء</button>
                <button type="submit" class="uk-button uk-button-danger uk-margin-small-right">
                    تأكيد الرفض وإشعار العميل
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal تغيير حالة الحجز النقدي --}}
<div id="status-modal" class="uk-flex-top" data-uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical">
        <button class="uk-modal-close-default" type="button" data-uk-close></button>
        <div class="uk-modal-header">
            <h3 class="uk-modal-title">تغيير حالة الحجز</h3>
        </div>
        <form action="{{ route('owner.payments.update-cash-status', $booking) }}" method="POST">
            @csrf
            <div class="uk-modal-body">
                <label class="uk-form-label">الحالة الجديدة</label>
                <select class="uk-select" name="booking_status" required>
                    <option value="confirmed">مؤكد</option>
                    <option value="active">نشط (المعدة مُسلَّمة)</option>
                    <option value="completed">مكتمل</option>
                    <option value="cancelled">ملغي</option>
                </select>
                <div class="uk-margin-small-top">
                    <label class="uk-form-label">ملاحظة (اختياري)</label>
                    <input class="uk-input" type="text" name="reason" placeholder="سبب التغيير...">
                </div>
            </div>
            <div class="uk-modal-footer uk-text-left">
                <button type="button" class="uk-button uk-button-default uk-modal-close">إلغاء</button>
                <button type="submit" class="uk-button uk-button-primary uk-margin-small-right">تحديث الحالة</button>
            </div>
        </form>
    </div>
</div>
@endsection