@extends('layouts.master')

@section('title', $equipment->name)

@section('content')
    <main class="page-main">
        <div class="page-head">
            <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_categories.jpg') }})">
                <div class="page-head__content" data-uk-parallax="y: 0, 100">
                    <div class="uk-container">
                        <div class="page-head__title">{{ $equipment->name }}</div>
                        <div class="page-head__breadcrumb">
                            <ul class="uk-breadcrumb">
                                <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                                <li><a href="{{ route('equipments') }}">المعدات</a></li>
                                <li><span>{{ $equipment->name }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-content">
            <div class="uk-section-large uk-container">
                <div class="uk-grid" data-uk-grid>
                    <div class="uk-width-2-3@m">
                        <div class="equipment-detail">

                            {{-- معرض الصور --}}
                            <div class="equipment-detail__gallery">
                                @if ($equipment->images->count())
                                    <div data-uk-slideshow="min-height: 300; max-height: 430">
                                        <div class="uk-position-relative">
                                            <ul class="uk-slideshow-items uk-child-width-1-1"
                                                data-uk-lightbox="animation: scale">
                                                @foreach ($equipment->images as $image)
                                                    <li>
                                                        <a href="{{ asset('storage/' . $image->image_url) }}">
                                                            <img class="uk-width-1-1"
                                                                src="{{ asset('storage/' . $image->image_url) }}"
                                                                alt="{{ $equipment->name }}" data-uk-cover>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <a class="uk-position-center-left uk-position-small uk-hidden-hover"
                                                href="#" data-uk-slidenav-previous
                                                data-uk-slideshow-item="previous"></a>
                                            <a class="uk-position-center-right uk-position-small uk-hidden-hover"
                                                href="#" data-uk-slidenav-next data-uk-slideshow-item="next"></a>
                                        </div>

                                        <div class="uk-margin-top" data-uk-slider>
                                            <ul
                                                class="uk-thumbnav uk-slider-items uk-grid uk-grid-small uk-child-width-1-4@m">
                                                @foreach ($equipment->images as $i => $image)
                                                    <li data-uk-slideshow-item="{{ $i }}">
                                                        <a href="#"><img
                                                                src="{{ asset('storage/' . $image->image_url) }}"
                                                                alt="{{ $equipment->name }}"></a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @else
                                    <p>لا توجد صور متاحة لهذه المعدة.</p>
                                @endif
                            </div>

                            <div class="equipment-detail__title">{{ $equipment->name }}</div>
                            <div class="equipment-detail__location">
                                <span data-uk-icon="location"></span> {{ $equipment->location_address }}
                            </div>

                            <div class="equipment-detail__desc">
                                <div class="section-title">
                                    <div class="uk-h2">الوصف</div>
                                </div>
                                <p>{{ $equipment->description ?? 'لا يوجد وصف متاح.' }}</p>
                            </div>

                            <div class="equipment-detail__specification">
                                <div class="section-title">
                                    <div class="uk-h2">معلومات</div>
                                </div>
                                <table class="uk-table uk-table-striped">
                                    <tr>
                                        <td>سعر الإيجار اليومي:</td>
                                        <td>{{ $equipment->daily_rate ?? '-' }} $</td>
                                    </tr>
                                    <tr>
                                        <td>سعر الإيجار الأسبوعي:</td>
                                        <td>{{ $equipment->weekly_rate ?? '-' }} $</td>
                                    </tr>
                                    <tr>
                                        <td>سعر الإيجار الشهري:</td>
                                        <td>{{ $equipment->monthly_rate ?? '-' }} $</td>
                                    </tr>
                                    <tr>
                                        <td>قيمة الإيداع:</td>
                                        <td>{{ $equipment->deposit_amount ?? '-' }} $</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="uk-width-1-3@m">
                        <div class="equipment-sidebar">

                            {{-- ✅ بيانات المالك --}}
                            <div
                                class="equipment-user uk-card uk-card-default uk-card-body uk-text-center uk-margin-medium-bottom">
                                <img class="uk-border-circle"
                                    src="{{ $equipment->owner->avatar_url ?? asset('assets/home/img/default-user.png') }}"
                                    width="120" height="120" alt="">
                                <h4 class="uk-margin-small-top">{{ $equipment->owner->name }}</h4>
                                <p>{{ $equipment->owner->description ?? 'مالك المعدات' }}</p>
                            </div>

                            {{-- ✅ نموذج استئجار المعدة --}}
                            <form action="" method="POST"
                                class="uk-card uk-card-default uk-card-body equipment-order">
                                @csrf
                                <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">

                                <div class="equipment-order__price">
                                    <span>مدة الإيجار<small>اختر المدة</small></span>
                                </div>

                                <div class="equipment-order__form">
                                    <div class="uk-margin">
                                        <div class="uk-inline uk-width-1-1">
                                            <select class="uk-select uk-form-large" name="rental_type" required>
                                                <option value="">مدة الإيجار</option>
                                                <option value="daily">يومي</option>
                                                <option value="weekly">أسبوعي</option>
                                                <option value="monthly">شهري</option>
                                            </select>
                                            <span class="uk-form-icon">
                                                <img class="timer"
                                                    src="{{ asset('assets/home/img/icons/ico-timer.svg') }}" alt="timer"
                                                    data-uk-svg>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="uk-margin">
                                        <input class="uk-input" type="date" name="start_date" required
                                            placeholder="تاريخ البدء">
                                    </div>

                                    <div class="uk-margin">
                                        <input class="uk-input" type="date" name="end_date" required
                                            placeholder="تاريخ الإنتهاء">
                                    </div>

                                    <div class="uk-margin">
                                        <input class="uk-input" type="text" name="delivery_address"
                                            placeholder="أدخل عنوانك">
                                    </div>

                                    <div class="uk-margin">
                                        <div class="equipment-order__value">
                                            <span data-uk-icon="check"></span>
                                            <span id="rentalDays">المدة بالأيام : -</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="equipment-order-total">
                                    <ul>
                                        <li><span>إجمالي الإيجار</span><span id="rentalTotal">-</span></li>
                                        <li><span>قيمة الإيداع</span><span>{{ $equipment->deposit_amount ?? 0 }} $</span>
                                        </li>
                                        <li><span>الإجمالي</span><span id="rentalGrand">-</span></li>
                                    </ul>

                                    <button class="uk-button uk-button-large uk-width-1-1" type="submit">
                                        <span>استئجار الآن</span>
                                        <img class="makos" src="{{ asset('assets/home/img/icons/arrow.svg') }}"
                                            alt="arrow" data-uk-svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const startInput = document.querySelector('[name="start_date"]');
                const endInput = document.querySelector('[name="end_date"]');
                const typeSelect = document.querySelector('[name="rental_type"]');
                const rentalDays = document.getElementById('rentalDays');
                const rentalTotal = document.getElementById('rentalTotal');
                const rentalGrand = document.getElementById('rentalGrand');

                function updatePrices() {
                    if (!startInput.value || !endInput.value || !typeSelect.value) return;

                    const start = new Date(startInput.value);
                    const end = new Date(endInput.value);
                    const diff = (end - start) / (1000 * 60 * 60 * 24);
                    if (diff < 1) return;

                    rentalDays.textContent = `المدة بالأيام: ${diff} يوم`;

                    let rate = 0;
                    switch (typeSelect.value) {
                        case 'daily':
                            rate = {{ $equipment->daily_rate ?? 0 }};
                            break;
                        case 'weekly':
                            rate = {{ $equipment->weekly_rate ?? 0 }};
                            break;
                        case 'monthly':
                            rate = {{ $equipment->monthly_rate ?? 0 }};
                            break;
                    }

                    const total = rate * (typeSelect.value === 'daily' ? diff : typeSelect.value === 'weekly' ? diff /
                        7 : diff / 30);
                    const deposit = {{ $equipment->deposit_amount ?? 0 }};
                    rentalTotal.textContent = `$${total.toFixed(2)}`;
                    rentalGrand.textContent = `$${(total + deposit).toFixed(2)}`;
                }

                startInput.addEventListener('change', updatePrices);
                endInput.addEventListener('change', updatePrices);
                typeSelect.addEventListener('change', updatePrices);
            });
        </script>
    @endpush

@endsection
