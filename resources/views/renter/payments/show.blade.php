@extends('layouts.master')

@section('title', 'الدفع - حجز #' . $booking->id)

@section('content')
<main class="page-main">
    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_categories.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="page-head__title">الدفع للحجز</div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ route('home') }}">الرئيسية</a></li>
                            {{-- <li><a href="{{ route('renter.bookings.index') }}">حجوزاتي</a></li> --}}
                            <li><span>الدفع #{{ $booking->id }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="uk-section uk-container uk-container-small">

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

            {{-- ملخص الحجز --}}
            <div class="uk-card uk-card-default uk-card-body uk-margin-medium-bottom">
                <h3 class="uk-card-title">ملخص الحجز</h3>
                <div class="uk-grid uk-grid-small" data-uk-grid>
                    <div class="uk-width-1-2@s">
                        <p><strong>المعدة:</strong> {{ $booking->equipment->name }}</p>
                        <p><strong>المالك:</strong> {{ $booking->equipment->owner->name }}</p>
                        <p><strong>من:</strong> {{ \Carbon\Carbon::parse($booking->start_date)->format('Y/m/d') }}</p>
                        <p><strong>إلى:</strong> {{ \Carbon\Carbon::parse($booking->end_date)->format('Y/m/d') }}</p>
                    </div>
                    <div class="uk-width-1-2@s uk-text-left@s">
                        <p><strong>إجمالي الإيجار:</strong> {{ number_format($booking->total_cost, 2) }} $</p>
                        <p><strong>التأمين:</strong> {{ number_format($booking->equipment->deposit_amount, 2) }} $</p>
                        <p class="uk-text-large"><strong>المجموع:</strong>
                            <span class="uk-text-primary">
                                {{ number_format($booking->total_cost + $booking->equipment->deposit_amount, 2) }} $
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- حالة الدفع الحالية --}}
            @if($booking->paymentProof)
                <div class="uk-card uk-card-default uk-card-body uk-margin-medium-bottom">
                    <h3 class="uk-card-title">حالة إشعار التحويل</h3>
                    @php $proof = $booking->paymentProof; @endphp

                    <span class="uk-label uk-label-{{ $proof->statusColor() }} uk-margin-small-bottom">
                        {{ $proof->statusLabel() }}
                    </span>

                    <div class="uk-grid uk-grid-small uk-margin-small-top" data-uk-grid>
                        <div class="uk-width-1-2@s">
                            <p><strong>المبلغ المحوّل:</strong> {{ number_format($proof->transferred_amount, 2) }} $</p>
                            <p><strong>البنك/المحفظة:</strong> {{ $proof->bank_or_wallet_name }}</p>
                            @if($proof->transaction_number)
                                <p><strong>رقم العملية:</strong> {{ $proof->transaction_number }}</p>
                            @endif
                        </div>
                        <div class="uk-width-1-2@s">
                            <p><strong>الإشعار:</strong></p>
                            <a href="{{ asset('storage/' . $proof->proof_image) }}" data-uk-lightbox>
                                <img src="{{ asset('storage/' . $proof->proof_image) }}"
                                     alt="إشعار التحويل" style="max-width: 150px; border-radius: 8px;">
                            </a>
                        </div>
                    </div>

                    @if($proof->isRejected() && $proof->rejection_reason)
                        <div class="uk-alert-danger uk-margin-top" data-uk-alert>
                            <p><strong>سبب الرفض:</strong> {{ $proof->rejection_reason }}</p>
                        </div>
                        <a href="{{ route('renter.payments.upload-proof', $booking) }}"
                           class="uk-button uk-button-primary uk-margin-small-top">
                            رفع إشعار جديد
                        </a>
                    @endif
                </div>

            @elseif(!$booking->payment_method)
                {{-- اختيار طريقة الدفع --}}
                <div class="uk-card uk-card-default uk-card-body">
                    <h3 class="uk-card-title">اختر طريقة الدفع</h3>
                    <form action="{{ route('renter.payments.select-method', $booking) }}" method="POST">
                        @csrf
                        <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-margin" data-uk-grid>

                            {{-- كاش --}}
                            <div>
                                <label class="payment-method-card">
                                    <input type="radio" name="payment_method_type" value="cash" class="uk-hidden" required>
                                    <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-text-center payment-method-option">
                                        <span data-uk-icon="icon: bag; ratio: 2.5" class="uk-text-success"></span>
                                        <h4>نقدي</h4>
                                        <p class="uk-text-small uk-text-muted">ادفع عند الاستلام نقداً للمالك مباشرة</p>
                                    </div>
                                </label>
                            </div>

                            {{-- تحويل بنكي --}}
                            <div>
                                <label class="payment-method-card">
                                    <input type="radio" name="payment_method_type" value="bank_transfer" class="uk-hidden" required>
                                    <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-text-center payment-method-option">
                                        <span data-uk-icon="icon: credit-card; ratio: 2.5" class="uk-text-primary"></span>
                                        <h4>تحويل بنكي</h4>
                                        <p class="uk-text-small uk-text-muted">حوّل المبلغ إلى حساب المالك البنكي</p>
                                    </div>
                                </label>
                            </div>

                            {{-- محفظة إلكترونية --}}
                            <div>
                                <label class="payment-method-card">
                                    <input type="radio" name="payment_method_type" value="wallet" class="uk-hidden" required>
                                    <div class="uk-card uk-card-default uk-card-hover uk-card-body uk-text-center payment-method-option">
                                        <span data-uk-icon="icon: phone; ratio: 2.5" class="uk-text-warning"></span>
                                        <h4>محفظة إلكترونية</h4>
                                        <p class="uk-text-small uk-text-muted">ادفع عبر محفظة إلكترونية أو تطبيق دفع</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @error('payment_method_type')
                            <p class="uk-text-danger">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="uk-button uk-button-primary uk-button-large">
                            متابعة
                        </button>
                    </form>
                </div>

            @elseif($booking->payment_method === 'cash')
                <div class="uk-alert-warning" data-uk-alert>
                    <p>
                        <span data-uk-icon="icon: clock"></span>
                        اخترت الدفع النقدي. سيتم التواصل معك من قبل المالك لترتيب عملية الاستلام.
                    </p>
                </div>
            @endif

        </div>
    </div>
</main>

@push('styles')
<style>
.payment-method-option {
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}
input[type="radio"]:checked + .payment-method-option {
    border-color: #1e87f0;
    background: #f0f7ff;
}
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.payment-method-card').forEach(label => {
    label.addEventListener('click', () => {
        document.querySelectorAll('.payment-method-option').forEach(opt => {
            opt.style.borderColor = 'transparent';
            opt.style.background = '';
        });
        label.querySelector('.payment-method-option').style.borderColor = '#1e87f0';
        label.querySelector('.payment-method-option').style.background = '#f0f7ff';
    });
});
</script>
@endpush
@endsection