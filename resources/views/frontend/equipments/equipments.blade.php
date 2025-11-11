{{-- @extends('layouts.master')

@section('title', 'نتائج البحث عن المعدات')

@section('content')

    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_blog.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="header-icons"><span></span><span></span><span></span></div>
                    <div class="page-head__title">المعدات</div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li> {{-- تم تصحيح الـ route --}
                            <li><span>المعدات</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- start search part - تم نقل كود البحث هنا من home.blade.php ليظهر في صفحة النتائج أيضاً --
    <div class="section-find section-find1 uk-section-large uk-background-muted"> {{-- أضفت فئات UIkit لتحسين المظهر --}
        <div class="uk-container">
            <div class="uk-card uk-card-default uk-card-body uk-box-shadow-large uk-border-rounded"> {{-- بطاقة جميلة للبحث --}
                <div class="find-box">
                    <div class="find-box__title uk-text-center uk-h2 uk-margin-medium-bottom"> <span>اعثر على المعدات
                            المناسبة</span></div>
                    <div class="find-box__form">
                        <form action="{{ route('equipments') }}" method="GET">
                            <div class="uk-grid uk-grid-medium uk-child-width-1-1 uk-child-width-1-3@m" data-uk-grid>
                                {{-- فلتر الفئات الرئيسية والفرعية المحسن --}
                                <div>
                                    <label class="uk-form-label" for="filter-category">الصنف</label>
                                    <div class="uk-form-controls">
                                        <div class="uk-inline uk-width-1-1"> {{-- بنية UIkit الصحيحة للـ select --}
                                            <span class="uk-form-icon" data-uk-icon="icon: chevron-down"></span>
                                            <select class="uk-select uk-form-large" id="filter-category" name="category">
                                                <option value="">إختر الصنف</option>
                                                @foreach ($categories->whereNull('parent_id') as $parentCategory)
                                                    <option value="{{ $parentCategory->id }}"
                                                        {{ isset($categoryId) && $categoryId == $parentCategory->id ? 'selected' : '' }}>
                                                        {{ $parentCategory->category_name }}
                                                    </option>
                                                    @foreach ($parentCategory->children->where('is_active', true) as $subCategory)
                                                        <option value="{{ $subCategory->id }}"
                                                            {{ isset($categoryId) && $categoryId == $subCategory->id ? 'selected' : '' }}>
                                                            &nbsp;&nbsp;&nbsp;&nbsp;— {{ $subCategory->category_name }}
                                                        </option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- فلتر الموقع --}
                                <div>
                                    <label class="uk-form-label" for="filter-location">الموقع</label>
                                    <div class="uk-form-controls">
                                        <div class="uk-inline uk-width-1-1"> {{-- بنية UIkit الصحيحة للـ select --}
                                            <span class="uk-form-icon" data-uk-icon="icon: chevron-down"></span>
                                            <select class="uk-select uk-form-large" id="filter-location" name="location">
                                                <option value="">إختر موقعك الحالي</option>
                                                @foreach ($locations as $loc)
                                                    <option value="{{ $loc }}"
                                                        {{ isset($location) && $location == $loc ? 'selected' : '' }}>
                                                        {{ $loc }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- فلتر اسم المعدة --}
                                <div>
                                    <label class="uk-form-label" for="filter-query">اسم المعدة</label>
                                    <div class="uk-form-controls uk-inline uk-width-1-1">
                                        <span class="uk-form-icon uk-form-icon-flip" data-uk-icon="icon: search"></span>
                                        <input class="uk-input uk-form-large" id="filter-query" type="text"
                                            name="query" placeholder="اسم المعدة" value="{{ $query ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            {{-- فلاتر إضافية للأسعار --}
                            <div class="uk-grid uk-grid-medium uk-child-width-1-1 uk-child-width-1-2@m uk-margin-top"
                                data-uk-grid>
                                <div>
                                    <label class="uk-form-label" for="filter-min-rate">الحد الأدنى للسعر اليومي</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-large" id="filter-min-rate" type="number"
                                            step="0.01" name="min_daily_rate" placeholder="أقل سعر"
                                            value="{{ $minDailyRate ?? '' }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label" for="filter-max-rate">الحد الأقصى للسعر اليومي</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-large" id="filter-max-rate" type="number"
                                            step="0.01" name="max_daily_rate" placeholder="أقصى سعر"
                                            value="{{ $maxDailyRate ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin-large-top uk-text-center">
                                <button type="submit"
                                    class="uk-button uk-button-primary uk-button-large uk-margin-small-right"><span>البحث عن
                                        المعدات</span></button>
                                <a href="{{ route('equipments') }}"
                                    class="uk-button uk-button-default uk-button-large"><span>إعادة تعيين الفلاتر</span></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end search part -->

    <div class="page-content">
        <div class="uk-section-large uk-container">
            <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                @forelse($equipments as $equipment)
                    <div>
                        <div class="blog-item"> {{-- يمكن تغيير هذا إلى uk-card أو أي تصميم مناسب لعرض المعدة --}
                            <div class="blog-item__media">
                                <a href="{{ route('equipments.show', $equipment->id) }}"> {{-- رابط لعرض تفاصيل المعدة --}
                                    {{-- تأكد أن $equipment->images موجودة وأنها ترجع مجموعة صور --}
                                    @if ($equipment->images->count() > 0)
                                        <img src="{{ asset('storage/' . $equipment->images->first()->image_path) }}"
                                            alt="{{ $equipment->name }}" data-uk-img> {{-- data-uk-img لتحسين تحميل الصور --}
                                    @else
                                        <img src="{{ asset('assets/home/img/equipment-default.jpg') }}"
                                            alt="صورة افتراضية للمعدة" data-uk-img>
                                    @endif
                                </a>
                                <div class="blog-item__category">{{ $equipment->category->category_name ?? 'غير مصنف' }}
                                </div>
                            </div>
                            <div class="blog-item__body">
                                <div class="blog-item__info">
                                    <div class="blog-item__date">{{ $equipment->created_at->format('d M, Y') }}</div>
                                    <div class="blog-item__author"> بواسطة <a
                                            href="#">{{ $equipment->owner->name ?? 'غير معروف' }}</a></div>
                                </div>
                                <div class="blog-item__title">{{ $equipment->name }}</div>
                                <div class="blog-item__intro">{{ Str::limit($equipment->description, 100) }}</div>
                                {{-- تقييم المعدة - تأكد أن لديك حقل average_rating في نموذج Equipment --}
                                <div class="rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="uk-icon"
                                            data-uk-icon="icon: star{{ $equipment->average_rating >= $i ? '' : '-o' }}; ratio: 0.8"></span>
                                    @endfor
                                    {{-- هذا التقييم هو عرض فقط، وليس مدخلات للمستخدم --}
                                </div>
                            </div>
                            <div class="blog-item__bottom">
                                <a class="link-more" href="{{ route('equipments.show', $equipment->id) }}">
                                    <span>إقرأ أكثر</span>
                                    <img class="ukos" src="{{ asset('assets/home/img/icons/arrow.svg') }}"
                                        alt="arrow" data-uk-svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="uk-width-1-1"> {{-- لجعل رسالة "لا توجد معدات" تأخذ كامل العرض --}
                        <div class="uk-alert-warning uk-text-center" data-uk-alert>
                            <a class="uk-alert-close" data-uk-close></a>
                            <p>لا توجد معدات مطابقة لمعايير البحث حالياً.</p>
                        </div>
                    </div>
                @endforelse
            </div>


        </div>
    </div>

@endsection --}}
@extends('layouts.master')

@section('title', 'نتائج البحث عن المعدات')

@section('content')

<div class="page-head">
    <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_blog.jpg') }})">
        <div class="page-head__content" data-uk-parallax="y: 0, 100">
            <div class="uk-container">
                <div class="header-icons"><span></span><span></span><span></span></div>
                <div class="page-head__title">المعدات</div>
                <div class="page-head__breadcrumb">
                    <ul class="uk-breadcrumb">
                        <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                        <li><span>المعدات</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- start search part -->
<div class="section-find section-find1 uk-section-large uk-background-muted">
    <div class="uk-container">
        <div class="uk-card uk-card-default uk-card-body uk-box-shadow-large uk-border-rounded">
            <div class="find-box">
                <div class="find-box__title uk-text-center uk-h2 uk-margin-medium-bottom">
                    <span>اعثر على المعدات المناسبة</span>
                </div>
                <div class="find-box__form">
                    <form action="{{ route('equipments') }}" method="GET">
                        <div class="uk-grid uk-grid-medium uk-child-width-1-1 uk-child-width-1-3@m" data-uk-grid>
                            {{-- فلتر الفئات --}}
                            <div>
                                <label class="uk-form-label" for="filter-category">الصنف</label>
                                <div class="uk-form-controls">
                                    <div class="uk-inline uk-width-1-1">
                                        <span class="uk-form-icon" data-uk-icon="icon: chevron-down"></span>
                                        <select class="uk-select uk-form-large" id="filter-category" name="category">
                                            <option value="">إختر الصنف</option>
                                            @foreach ($equipmentCategories->whereNull('parent_id') as $parentCategory)
                                                <option value="{{ $parentCategory->id }}"
                                                    {{ isset($categoryId) && $categoryId == $parentCategory->id ? 'selected' : '' }}>
                                                    {{ $parentCategory->category_name }}
                                                </option>
                                                @foreach ($parentCategory->children->where('is_active', true) as $subCategory)
                                                    <option value="{{ $subCategory->id }}"
                                                        {{ isset($categoryId) && $categoryId == $subCategory->id ? 'selected' : '' }}>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;— {{ $subCategory->category_name }}
                                                    </option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- فلتر الموقع --}}
                            <div>
                                <label class="uk-form-label" for="filter-location">الموقع</label>
                                <div class="uk-form-controls">
                                    <div class="uk-inline uk-width-1-1">
                                        <span class="uk-form-icon" data-uk-icon="icon: chevron-down"></span>
                                        <select class="uk-select uk-form-large" id="filter-location" name="location">
                                            <option value="">إختر موقعك الحالي</option>
                                            @foreach ($locations as $loc)
                                                <option value="{{ $loc }}"
                                                    {{ isset($location) && $location == $loc ? 'selected' : '' }}>
                                                    {{ $loc }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- فلتر اسم المعدة --}}
                            <div>
                                <label class="uk-form-label" for="filter-query">اسم المعدة</label>
                                <div class="uk-form-controls uk-inline uk-width-1-1">
                                    <span class="uk-form-icon uk-form-icon-flip" data-uk-icon="icon: search"></span>
                                    <input class="uk-input uk-form-large" id="filter-query" type="text"
                                        name="query" placeholder="اسم المعدة" value="{{ $query ?? '' }}">
                                </div>
                            </div>
                        </div>

                        {{-- فلاتر الأسعار --}}
                        <div class="uk-grid uk-grid-medium uk-child-width-1-1 uk-child-width-1-2@m uk-margin-top" data-uk-grid>
                            <div>
                                <label class="uk-form-label" for="filter-min-rate">الحد الأدنى للسعر اليومي</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-large" id="filter-min-rate" type="number"
                                        step="0.01" name="min_daily_rate" placeholder="أقل سعر"
                                        value="{{ $minDailyRate ?? '' }}">
                                </div>
                            </div>
                            <div>
                                <label class="uk-form-label" for="filter-max-rate">الحد الأقصى للسعر اليومي</label>
                                <div class="uk-form-controls">
                                    <input class="uk-input uk-form-large" id="filter-max-rate" type="number"
                                        step="0.01" name="max_daily_rate" placeholder="أقصى سعر"
                                        value="{{ $maxDailyRate ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="uk-margin-large-top uk-text-center">
                            <button type="submit"
                                class="uk-button uk-button-primary uk-button-large uk-margin-small-right">
                                <span>البحث عن المعدات</span>
                            </button>
                            <a href="{{ route('equipments') }}"
                                class="uk-button uk-button-default uk-button-large">
                                <span>إعادة تعيين الفلاتر</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end search part -->

<div class="page-content">
    <div class="uk-section-large uk-container">
        <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
            @forelse($equipments as $equipment)
                <div>
                    <div class="blog-item">
                        <div class="blog-item__media">
                            <a href="{{ route('equipments.show', $equipment->id) }}">
                                @if ($equipment->images->count() > 0)
                                    <img src="{{ asset('storage/' . $equipment->images->first()->image_path) }}"
                                        alt="{{ $equipment->name }}" data-uk-img>
                                @else
                                    <img src="{{ asset('assets/home/img/equipment-default.jpg') }}"
                                        alt="صورة افتراضية للمعدة" data-uk-img>
                                @endif
                            </a>
                            <div class="blog-item__category">
                                {{ $equipment->category->category_name ?? 'غير مصنف' }}
                            </div>
                        </div>
                        <div class="blog-item__body">
                            <div class="blog-item__info">
                                <div class="blog-item__date">{{ $equipment->created_at->format('d M, Y') }}</div>
                                <div class="blog-item__author"> بواسطة
                                    <a href="#">{{ $equipment->owner->name ?? 'غير معروف' }}</a>
                                </div>
                            </div>
                            <div class="blog-item__title">{{ $equipment->name }}</div>
                            <div class="blog-item__intro">{{ Str::limit($equipment->description, 100) }}</div>
                            <div class="rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="uk-icon"
                                        data-uk-icon="icon: star{{ $equipment->average_rating >= $i ? '' : '-o' }}; ratio: 0.8"></span>
                                @endfor
                            </div>
                        </div>
                        <div class="blog-item__bottom">
                            <a class="link-more" href="{{ route('equipments.show', $equipment->id) }}">
                                <span>إقرأ أكثر</span>
                                <img class="ukos" src="{{ asset('assets/home/img/icons/arrow.svg') }}"
                                    alt="arrow" data-uk-svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="uk-width-1-1">
                    <div class="uk-alert-warning uk-text-center" data-uk-alert>
                        <a class="uk-alert-close" data-uk-close></a>
                        <p>لا توجد معدات مطابقة لمعايير البحث حالياً.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
