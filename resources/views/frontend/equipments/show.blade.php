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
                                    {{-- ✅ مشاركة المعدة و QR Code --}}
                                    <div class="uk-card uk-card-default uk-card-body uk-margin-medium-top uk-text-center">
                                        <h4 class="uk-heading-line"><span>مشاركة المعدة</span></h4>

                                        {{-- 🔗 رابط المشاركة --}}
                                        <div class="uk-margin">
                                            <p class="uk-text-small uk-text-muted">يمكنك مشاركة هذه المعدة عبر الرابط
                                                التالي:</p>
                                            <div class="uk-inline uk-width-expand">
                                                <input class="uk-input uk-text-center" type="text"
                                                    value="{{ route('equipments.show', $equipment->id) }}" readonly
                                                    id="equipmentLink">
                                                <button class="uk-button uk-button-primary uk-margin-small-top"
                                                    onclick="copyEquipmentLink()">نسخ الرابط</button>
                                            </div>
                                        </div>

                                        {{-- 📱 QR Code --}}
                                        <div class="uk-margin">
                                            <p class="uk-text-small uk-text-muted">أو امسح رمز QR لفتح الصفحة مباشرة:</p>
                                            <div class="uk-flex uk-flex-center uk-margin-small-bottom">
                                                {!! QrCode::size(200)->generate(route('equipments.show', $equipment->id)) !!}
                                            </div>

                                            {{-- زر تحميل QR --}}
                                            <a class="uk-button uk-button-default uk-margin-small-top"
                                                href="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(300)->generate(route('equipments.show', $equipment->id))) }}"
                                                download="equipment-{{ $equipment->id }}.png">
                                                تحميل رمز QR
                                            </a>
                                        </div>
                                        {{-- 🔗 أزرار المشاركة --}}
                                        <div class="uk-margin">
                                            <p class="uk-text-small uk-text-muted">شارك عبر:</p>

                                            <div class="uk-flex uk-flex-center uk-grid-small" data-uk-grid>
                                                @php
                                                    $shareUrl = urlencode(route('equipments.show', $equipment->id));
                                                    $shareText = urlencode(
                                                        'شاهد هذه المعدة على منصة تأجير المعدات: ' . $equipment->name,
                                                    );
                                                @endphp

                                                <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
                                                    class="share-btn whatsapp" target="_blank" title="واتساب">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>

                                                <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}"
                                                    class="share-btn telegram" target="_blank" title="تليجرام">
                                                    <i class="fab fa-telegram-plane"></i>
                                                </a>

                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                                                    class="share-btn facebook" target="_blank" title="فيسبوك">
                                                    <i class="fab fa-facebook-f"></i>
                                                </a>

                                                <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}"
                                                    class="share-btn twitter" target="_blank" title="تويتر">
                                                    <i class="fab fa-x-twitter"></i>
                                                </a>
                                            </div>
                                        </div>

                                    </div>

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
                                        <td>قيمة التأمين:</td>
                                        <td>{{ $equipment->deposit_amount ?? '-' }} $</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="uk-width-1-3@m">
                        <div class="equipment-sidebar">
                            {{-- ✅ بيانات المالك --}}
                            @if ($equipment->owner)
                                <div
                                    class="equipment-user uk-card uk-card-default uk-card-body uk-text-center uk-margin-medium-bottom">
                                    <img class="uk-border-circle" src="{{ $equipment->owner->avatar_url }}" width="120"
                                        height="120" alt="{{ $equipment->owner->name }}">

                                    <h4 class="uk-margin-small-top">{{ $equipment->owner->name }}</h4>

                                    <p>{{ $equipment->owner->description ?? 'مالك المعدات' }}</p>
                                </div>
                            @endif

                            <form action="{{ route('bookings.store') }}" method="POST"
                                class="uk-card uk-card-default uk-card-body equipment-order">
                                @csrf
                                <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">

                                <div class="equipment-order__form">
                                    <div class="uk-margin">
                                        <select class="uk-select uk-form-large" name="rental_rate_type"
                                            id="rental_rate_type" required>
                                            <option value="">مدة الإيجار</option>
                                            <option value="daily" data-price="{{ $equipment->daily_rate }}">يومي
                                                ({{ $equipment->daily_rate }}$)</option>
                                            <option value="weekly" data-price="{{ $equipment->weekly_rate }}">أسبوعي
                                                ({{ $equipment->weekly_rate }}$)</option>
                                            <option value="monthly" data-price="{{ $equipment->monthly_rate }}">شهري
                                                ({{ $equipment->monthly_rate }}$)</option>
                                        </select>
                                    </div>

                                    <div class="uk-margin">
                                        <label>تاريخ البدء</label>
                                        <input class="uk-input" type="date" name="start_date" id="start_date"
                                            required min="{{ date('Y-m-d') }}">
                                    </div>

                                    <div class="uk-margin">
                                        <label>تاريخ الإنتهاء</label>
                                        <input class="uk-input" type="date" name="end_date" id="end_date" required
                                            min="{{ date('Y-m-d') }}">
                                    </div>

                                    <div class="uk-margin">
                                        <input class="uk-input" type="text" name="pickup_location"
                                            placeholder="عنوان الاستلام">
                                    </div>

                                    <div class="uk-margin">
                                        <div class="equipment-order__value">
                                            <span id="rentalDays">المدة بالأيام : -</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="equipment-order-total">
                                    <ul>
                                        <li><span>إجمالي الإيجار</span><span id="rentalTotal">-</span></li>
                                        <li><span>قيمة الإيداع (تأمين)</span><span>{{ $equipment->deposit_amount }}
                                                $</span></li>
                                        <hr>
                                        <li><span>الإجمالي النهائي</span><span id="rentalGrand">-</span></li>
                                    </ul>

                                    <button class="uk-button uk-button-large uk-width-1-1" type="submit">
                                        <span>استئجار الآن</span>
                                    </button>

                                    {{-- ✅ زر المفضلة --}}
                                    @auth
                                        <style>
                                            .fav-btn {
                                                background: none;
                                                border: none;
                                                cursor: pointer;
                                                width: 100%;
                                                padding: 10px;
                                                margin-top: 10px;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                gap: 8px;
                                                border-radius: 6px;
                                                border: 2px solid #ddd;
                                                transition: all 0.3s;
                                                font-size: 15px;
                                                color: #666;
                                            }

                                            .fav-btn:hover {
                                                border-color: #e63946;
                                                color: #e63946;
                                            }

                                            .fav-btn.active {
                                                border-color: #e63946;
                                                color: #e63946;
                                                background: #fff5f5;
                                            }

                                            .fav-btn svg {
                                                transition: all 0.3s;
                                            }

                                            .fav-btn.active svg {
                                                fill: #e63946;
                                                stroke: #e63946;
                                            }
                                        </style>

                                        <button id="favBtn" onclick="toggleFavorite({{ $equipment->id }})"
                                            class="fav-btn {{ $isFavorite ? 'active' : '' }}" type="button">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="{{ $isFavorite ? '#e63946' : 'none' }}"
                                                stroke="{{ $isFavorite ? '#e63946' : 'currentColor' }}" stroke-width="2"
                                                id="favSvg">
                                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                 a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                            </svg>
                                            <span
                                                id="favText">{{ $isFavorite ? 'إزالة من المفضلة' : 'إضافة للمفضلة' }}</span>
                                        </button>
                                    @endauth
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
            function toggleFavorite(equipmentId) {
                fetch('{{ route('favorites.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            equipment_id: equipmentId
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        const btn = document.getElementById('favBtn');
                        const text = document.getElementById('favText');
                        if (data.status === 'added') {
                            text.innerText = 'إزالة من المفضلة';
                            btn.classList.add('uk-button-danger');
                            btn.classList.remove('uk-button-default');
                        } else {
                            text.innerText = 'إضافة للمفضلة';
                            btn.classList.remove('uk-button-danger');
                            btn.classList.add('uk-button-default');
                        }
                    });
            }
        </script>

        <script>
            function copyEquipmentLink() {
                const input = document.getElementById('equipmentLink');
                input.select();
                input.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(input.value);
                UIkit.notification({
                    message: '✅ تم نسخ الرابط بنجاح!',
                    status: 'success'
                });
            }
        </script>

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
        <script>
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const rateSelect = document.getElementById('rental_rate_type');
            const deposit = {{ $equipment->deposit_amount ?? 0 }};

            function calculatePrice() {
                const start = new Date(startDateInput.value);
                const end = new Date(endDateInput.value);
                const rate = parseFloat(rateSelect.options[rateSelect.selectedIndex]?.getAttribute('data-price') || 0);

                if (start && end && end > start && rate > 0) {
                    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                    const totalRent = days * rate;

                    document.getElementById('rentalDays').innerText = "المدة بالأيام : " + days;
                    document.getElementById('rentalTotal').innerText = totalRent.toFixed(2) + " $";
                    document.getElementById('rentalGrand').innerText = (totalRent + deposit).toFixed(2) + " $";
                }
            }

            startDateInput.addEventListener('change', calculatePrice);
            endDateInput.addEventListener('change', calculatePrice);
            rateSelect.addEventListener('change', calculatePrice);
        </script>
    @endpush

@endsection
