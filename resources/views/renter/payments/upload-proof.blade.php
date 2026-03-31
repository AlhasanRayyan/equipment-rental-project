@extends('layouts.master')

@section('title', 'رفع إشعار التحويل')

@section('content')
<main class="page-main">
    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_categories.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="page-head__title">رفع إشعار التحويل</div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="uk-section uk-container uk-container-small">

            {{-- معلومات حساب المالك للتحويل --}}
            @if($booking->equipment->owner->bank_info ?? false)
            <div class="uk-card uk-card-primary uk-card-body uk-margin-bottom">
                <h4 class="uk-card-title">معلومات الحساب للتحويل</h4>
                <p>{{ $booking->equipment->owner->bank_info }}</p>
            </div>
            @else
            <div class="uk-alert-primary uk-margin-bottom" data-uk-alert>
                <p>
                    <strong>تواصل مع المالك للحصول على تفاصيل حسابه البنكي أو محفظته الإلكترونية.</strong>
                </p>
            </div>
            @endif

            {{-- نموذج رفع الإشعار --}}
            <div class="uk-card uk-card-default uk-card-body">
                <h3 class="uk-card-title">تفاصيل التحويل</h3>

                <form action="{{ route('renter.payments.submit-proof', $booking) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="uk-margin">
                        <label class="uk-form-label">المبلغ المحوّل <span class="uk-text-danger">*</span></label>
                        <div class="uk-form-controls">
                            <div class="uk-inline uk-width-1-1">
                                <span class="uk-form-icon" data-uk-icon="icon: tag"></span>
                                <input class="uk-input @error('transferred_amount') uk-form-danger @enderror"
                                       type="number" name="transferred_amount" step="0.01" min="0"
                                       value="{{ old('transferred_amount', $booking->total_cost) }}"
                                       placeholder="المبلغ بالدولار" required>
                            </div>
                        </div>
                        @error('transferred_amount')
                            <p class="uk-text-danger uk-text-small">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label">اسم البنك / المحفظة الإلكترونية <span class="uk-text-danger">*</span></label>
                        <div class="uk-form-controls">
                            <input class="uk-input @error('bank_or_wallet_name') uk-form-danger @enderror"
                                   type="text" name="bank_or_wallet_name"
                                   value="{{ old('bank_or_wallet_name') }}"
                                   placeholder="مثال: البنك الإسلامي، محفظة XYZ..." required>
                        </div>
                        @error('bank_or_wallet_name')
                            <p class="uk-text-danger uk-text-small">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label">صورة الإشعار <span class="uk-text-danger">*</span></label>
                        <div class="uk-form-controls">
                            <div data-uk-form-custom="target: true">
                                <input type="file" name="proof_image" accept="image/*"
                                       id="proofImageInput" required
                                       class="@error('proof_image') uk-form-danger @enderror">
                                <input class="uk-input" type="text" placeholder="اختر صورة الإشعار..." readonly>
                            </div>
                            <p class="uk-text-small uk-text-muted">صيغ مدعومة: JPG, PNG, WEBP - الحد الأقصى: 5 ميغابايت</p>
                        </div>
                        @error('proof_image')
                            <p class="uk-text-danger uk-text-small">{{ $message }}</p>
                        @enderror

                        {{-- معاينة الصورة --}}
                        <div id="imagePreview" class="uk-margin-small-top uk-hidden">
                            <img id="previewImg" src="" alt="معاينة" style="max-width: 200px; border-radius: 8px; border: 2px solid #e5e5e5;">
                        </div>
                    </div>

                    <div class="uk-margin">
                        <label class="uk-form-label">ملاحظات إضافية</label>
                        <div class="uk-form-controls">
                            <textarea class="uk-textarea @error('notes') uk-form-danger @enderror"
                                      name="notes" rows="3"
                                      placeholder="أي معلومات إضافية تريد إضافتها...">{{ old('notes') }}</textarea>
                        </div>
                        @error('notes')
                            <p class="uk-text-danger uk-text-small">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="uk-margin-top">
                        <button type="submit" class="uk-button uk-button-primary uk-button-large">
                            <span data-uk-icon="icon: upload"></span>
                            رفع الإشعار
                        </button>
                        <a href="{{ route('renter.payments.show', $booking) }}"
                           class="uk-button uk-button-default uk-margin-small-right">
                            رجوع
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
document.getElementById('proofImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('previewImg').src = ev.target.result;
            document.getElementById('imagePreview').classList.remove('uk-hidden');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection