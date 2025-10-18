@extends('layouts.master')

@section('title', 'تحديث المعدة')

@section('content')
    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_contacts.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="header-icons"><span></span><span></span><span></span></div>
                    <div class="page-head__title">تحديث بيانات المعدة</div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                            <li><span>تحديث المعدة</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="uk-section-large uk-container">
            <div class="contacts-block">
                <div class="uk-grid uk-grid-collapse" data-uk-grid>
                    <div class="uk-width-3-3@m">
                        <div class="block-form">
                            <div class="section-title">
                                <div class="uk-h2">تعديل البيانات</div>
                            </div>
                            <div class="section-content">
                                <p>يرجى تعديل البيانات بطريقة صحيحة ودقيقة</p>

                                {{-- ✅ نموذج التعديل --}}
                                <form action="{{ route('equipments.update', $equipment->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="uk-grid uk-grid-small uk-child-width-1-2@s" data-uk-grid>

                                        <div>
                                            <input name="name" class="uk-input uk-form-large" type="text"
                                                placeholder="اسم المعدة *" value="{{ old('name', $equipment->name) }}"
                                                required>
                                        </div>

                                        <div>
                                            <select name="category_id" class="uk-select uk-form-large" required>
                                                <option value="">إختر الفئة</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $equipment->category_id == $category->id ? 'selected' : '' }}>
                                                        {{ $category->category_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <input name="location_address" class="uk-input uk-form-large" type="text"
                                                placeholder="الموقع *"
                                                value="{{ old('location_address', $equipment->location_address) }}"
                                                required>
                                        </div>

                                        <div>
                                            <input name="daily_rate" class="uk-input uk-form-large" type="number"
                                                step="0.01" placeholder="سعر الإيجار اليومي *"
                                                value="{{ old('daily_rate', $equipment->daily_rate) }}">
                                        </div>

                                        <div>
                                            <input name="weekly_rate" class="uk-input uk-form-large" type="number"
                                                step="0.01" placeholder="سعر الإيجار الأسبوعي *"
                                                value="{{ old('weekly_rate', $equipment->weekly_rate) }}">
                                        </div>

                                        <div>
                                            <input name="monthly_rate" class="uk-input uk-form-large" type="number"
                                                step="0.01" placeholder="سعر الإيجار الشهري *"
                                                value="{{ old('monthly_rate', $equipment->monthly_rate) }}">
                                        </div>

                                        <div>
                                            <input name="deposit_amount" class="uk-input uk-form-large" type="number"
                                                step="0.01" placeholder="قيمة الإيداع *"
                                                value="{{ old('deposit_amount', $equipment->deposit_amount) }}">
                                        </div>

                                        <div>
                                            <select name="has_gps_tracker" class="uk-select uk-form-large">
                                                <option value="0"
                                                    {{ !$equipment->has_gps_tracker ? 'selected' : '' }}>لا يوجد جهاز تتبع
                                                </option>
                                                <option value="1" {{ $equipment->has_gps_tracker ? 'selected' : '' }}>
                                                    يوجد جهاز تتبع</option>
                                            </select>
                                        </div>

                                        <div class="uk-width-1-1">
                                            <label class="custum-file-upload">
                                                <div class="text"><span>اضغط لتحديث الصور (اختياري)</span></div>
                                                <div class="icon"><i class="fas fa-upload"></i></div>
                                                <input type="file" name="images[]" multiple>
                                            </label>

                                            {{-- عرض الصور الحالية --}}
                                            @if ($equipment->images && count($equipment->images) > 0)
                                                <div class="uk-margin-top">
                                                    <p class="uk-text-bold">📸 الصور الحالية:</p>

                                                    <div class="uk-grid-small uk-child-width-1-5@m uk-child-width-1-2@s"
                                                        data-uk-grid data-uk-lightbox="animation: fade">
                                                        @foreach ($equipment->images as $image)
                                                            <div class="uk-text-center">
                                                                <a href="{{ asset('storage/' . $image->image_url) }}"
                                                                    data-caption="صورة المعدة">
                                                                    <img src="{{ asset('storage/' . $image->image_url) }}"
                                                                        alt="صورة" class="uk-border-rounded"
                                                                        style="width: 140px; height: 140px; object-fit: cover; border: 2px solid #eee; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                        </div>

                                        <div class="uk-width-1-1">
                                            <textarea name="description" class="uk-textarea uk-form-large" placeholder="الوصف">{{ old('description', $equipment->description) }}</textarea>
                                        </div>

                                        <div>
                                            <button class="uk-button uk-button-large" type="submit">
                                                <span>تحديث</span>
                                                <img src="{{ asset('assets/home/img/icons/arrow.svg') }}" alt="arrow"
                                                    data-uk-svg>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                {{-- ✅ نهاية النموذج --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
