@extends('layouts.master')

@section('title', 'إضافة معدة جديدة')

@section('content')
<div class="page-head">
    <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_contacts.jpg') }})">
        <div class="page-head__content" data-uk-parallax="y: 0, 100">
            <div class="uk-container">
                <div class="header-icons"><span></span><span></span><span></span></div>
                <div class="page-head__title">إضافة معدة جديدة</div>
                <div class="page-head__breadcrumb">
                    <ul class="uk-breadcrumb">
                        <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                        <li><span>إضافة معدة</span></li>
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
                            <div class="uk-h2">إضافة البيانات</div>
                        </div>
                        <div class="section-content">
                            <p>يجب عليك إضافة بيانات صحيحة ودقيقة</p>

                            {{-- ✅ نموذج الإضافة --}}
                            <form action="{{ route('equipments.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="uk-grid uk-grid-small uk-child-width-1-2@s" data-uk-grid>
                                    
                                    <div>
                                        <input name="name" class="uk-input uk-form-large" type="text" placeholder="اسم المعدة *" value="{{ old('name') }}" required>
                                    </div>

                                    <div>
                                        <select name="category_id" class="uk-select uk-form-large" required>
                                            <option value="">إختر الفئة</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <input name="location_address" class="uk-input uk-form-large" type="text" placeholder="الموقع *" value="{{ old('location_address') }}" required>
                                    </div>

                                    <div><input name="daily_rate" class="uk-input uk-form-large" type="number" step="0.01" placeholder="سعر الإيجار اليومي *" value="{{ old('daily_rate') }}"></div>
                                    <div><input name="weekly_rate" class="uk-input uk-form-large" type="number" step="0.01" placeholder="سعر الإيجار الأسبوعي *" value="{{ old('weekly_rate') }}"></div>
                                    <div><input name="monthly_rate" class="uk-input uk-form-large" type="number" step="0.01" placeholder="سعر الإيجار الشهري *" value="{{ old('monthly_rate') }}"></div>

                                    <div><input name="deposit_amount" class="uk-input uk-form-large" type="number" step="0.01" placeholder="قيمة الإيداع *" value="{{ old('deposit_amount') }}"></div>

                                    <div>
                                        <select name="has_gps_tracker" class="uk-select uk-form-large">
                                            <option value="0">لا يوجد جهاز تتبع</option>
                                            <option value="1">يوجد جهاز تتبع</option>
                                        </select>
                                    </div>

                                    <div class="uk-width-1-1">
                                        <label class="custum-file-upload">
                                            <div class="text"><span>اضغط لإضافة الصور</span></div>
                                            <div class="icon"><i class="fas fa-upload"></i></div>
                                            <input type="file" name="images[]" multiple>
                                        </label>
                                    </div>

                                    <div class="uk-width-1-1">
                                        <textarea name="description" class="uk-textarea uk-form-large" placeholder="الوصف">{{ old('description') }}</textarea>
                                    </div>

                                    <div>
                                        <button class="uk-button uk-button-large" type="submit">
                                            <span>إضافة</span>
                                            <img src="{{ asset('assets/home/img/icons/arrow.svg') }}" alt="arrow" data-uk-svg>
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
