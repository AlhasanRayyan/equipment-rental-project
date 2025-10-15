@extends('layouts.master')

@section('title', 'الصفحة الرئيسية - تأجير المعدات')

@section('content')
    <div class="section-slideshow">
        <div class="uk-position-relative uk-light" tabindex="-1"
            data-uk-slideshow="animation: fade; min-height: 400; max-height: 700;">
            <ul class="uk-slideshow-items">
                {{-- يمكن جعل الشرائح ديناميكية من إعدادات النظام أو جدول مخصص للشرائح --}}
                <li class="slideshow-item"><img src="{{ asset('assets/home/img/slideshow-1.jpg') }}" alt="معدة بناء"
                        data-uk-cover>
                    <div class="slideshow-item__content">
                        <div class="uk-position-center uk-position-small uk-text-center">
                            <div class="header-icons" data-uk-slideshow-parallax="x: -200,200">
                                <span></span><span></span><span></span>
                            </div>
                            <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">يقدم لك الموقع معدات
                                البناء المتنوعة </p>
                            <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">الموقع الأفضل في
                                تأجير المعدات<br>في قطاع غزة</h2>
                        </div>
                    </div>
                </li>
                <li class="slideshow-item"><img src="{{ asset('assets/home/img/slideshow-2.jpg') }}" alt="معدات متطورة"
                        data-uk-cover>
                    <div class="slideshow-item__content">
                        <div class="uk-position-center uk-position-small uk-text-center">
                            <div class="header-icons" data-uk-slideshow-parallax="x: -200,200">
                                <span></span><span></span><span></span>
                            </div>
                            <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">معدات متطورة جاهزة
                                بأسعار مرنة</p>
                            <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">كل ما تحتاجه
                                للبناء<br> متوفر هنا </h2>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="uk-visible@m"><a class="uk-position-center-left uk-position-small" href="#"
                    data-uk-slidenav-previous data-uk-slideshow-item="previous"></a><a
                    class="uk-position-center-right uk-position-small" href="#" data-uk-slidenav-next
                    data-uk-slideshow-item="next"></a></div>
        </div>
    </div>
    <div class="py-5 bg-light"> {{-- section-find uk-section-large uk-background-muted --}}
        <div class="container"> {{-- uk-container --}}
            <div class="card shadow-lg border-0 rounded-3"> {{-- uk-card uk-card-default uk-card-body uk-box-shadow-large uk-border-rounded --}}
                <div class="card-body p-4"> {{-- find-box (body part), uk-card-body --}}
                    <div class="find-box"> {{-- يمكنك حذف هذا الـ div إذا لم يكن له تنسيقات CSS خاصة بك --}}
                        <div class="text-center mb-4"> {{-- find-box__title uk-text-center uk-h2 uk-margin-medium-bottom --}}
                            <h2 class="h2"> <span>اعثر على المعدات المناسبة</span></h2>
                        </div>
                        <div class="find-box__form"> {{-- يمكنك حذف هذا الـ div أيضاً إذا لم يكن له تنسيقات CSS خاصة --}}
                            <form action="{{ route('equipments') }}" method="GET">
                                {{-- الصف الأول للفلاتر الأساسية (الصنف، الموقع، اسم المعدة) --}}
                                <div class="row g-3"> {{-- uk-grid uk-grid-medium uk-child-width-1-1 uk-child-width-1-3@m data-uk-grid --}}
                                    {{-- فلتر الفئات الرئيسية والفرعية المحسن --}}
                                    <div class="col-md-4 col-12"> {{-- uk-child-width-1-1 uk-child-width-1-3@m --}}
                                        <label class="form-label" for="filter-category">الصنف</label>
                                        <div class="input-group"> {{-- uk-form-controls - input-group لدمج الأيقونة --}}
                                            <select class="form-select form-select-lg" id="filter-category" name="category">
                                                {{-- uk-select uk-form-large --}}
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
                                            {{-- أيقونة Bootstrap (تتطلب تضمين Bootstrap Icons أو Font Awesome) --}}
                                            <label class="input-group-text" for="filter-category"><i
                                                    class="bi bi-chevron-down"></i></label>
                                            {{-- إذا كنت تستخدم Font Awesome: <label class="input-group-text" for="filter-category"><i class="fas fa-chevron-down"></i></label> --}}
                                        </div>
                                    </div>

                                    {{-- فلتر الموقع --}}
                                    <div class="col-md-4 col-12"> {{-- uk-child-width-1-1 uk-child-width-1-3@m --}}
                                        <label class="form-label" for="filter-location">الموقع</label>
                                        <div class="input-group"> {{-- uk-form-controls --}}
                                            <select class="form-select form-select-lg" id="filter-location" name="location">
                                                {{-- uk-select uk-form-large --}}
                                                <option value="">إختر موقعك الحالي</option>
                                                @foreach ($locations as $loc)
                                                    <option value="{{ $loc }}"
                                                        {{ isset($location) && $location == $loc ? 'selected' : '' }}>
                                                        {{ $loc }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label class="input-group-text" for="filter-location"><i
                                                    class="bi bi-chevron-down"></i></label>
                                        </div>
                                    </div>

                                    {{-- فلتر اسم المعدة مع أيقونة البحث المدمجة --}}
                                    <div class="col-md-4 col-12"> {{-- uk-child-width-1-1 uk-child-width-1-3@m --}}
                                        <label class="form-label" for="filter-query">اسم المعدة</label>
                                        <div class="input-group"> {{-- uk-form-controls uk-inline uk-width-1-1 --}}
                                            <input class="form-control form-control-lg" id="filter-query" type="text"
                                                name="query" placeholder="بحث باسم المعدة..."
                                                value="{{ $query ?? '' }}"> {{-- uk-input uk-form-large --}}
                                            {{-- أيقونة Bootstrap (تتطلب تضمين Bootstrap Icons أو Font Awesome) --}}
                                            <label class="input-group-text" for="filter-query"><i
                                                    class="bi bi-search"></i></label>
                                            {{-- إذا كنت تستخدم Font Awesome: <label class="input-group-text" for="filter-query"><i class="fas fa-search"></i></label> --}}
                                        </div>
                                    </div>
                                </div>

                                {{-- الصف الثاني للفلاتر الإضافية للأسعار --}}
                                <div class="row g-3 mt-4"> {{-- uk-grid uk-grid-medium uk-child-width-1-1 uk-child-width-1-2@m uk-margin-top data-uk-grid --}}
                                    <div class="col-md-6 col-12"> {{-- uk-child-width-1-2@m --}}
                                        <label class="form-label" for="filter-min-rate">الحد الأدنى للسعر اليومي</label>
                                        <div class="input-group"> {{-- uk-form-controls --}}
                                            <input class="form-control form-control-lg" id="filter-min-rate" type="number"
                                                step="0.01" name="min_daily_rate" placeholder="أقل سعر"
                                                value="{{ $minDailyRate ?? '' }}"> {{-- uk-input uk-form-large --}}
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12"> {{-- uk-child-width-1-2@m --}}
                                        <label class="form-label" for="filter-max-rate">الحد الأقصى للسعر اليومي</label>
                                        <div class="input-group"> {{-- uk-form-controls --}}
                                            <input class="form-control form-control-lg" id="filter-max-rate"
                                                type="number" step="0.01" name="max_daily_rate"
                                                placeholder="أقصى سعر" value="{{ $maxDailyRate ?? '' }}">
                                            {{-- uk-input uk-form-large --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-5"> {{-- uk-margin-large-top uk-text-center --}}
                                    <button type="submit" class="btn btn-primary btn-lg me-2"><span>البحث عن
                                            المعدات</span></button> {{-- uk-button uk-button-primary uk-button-large uk-margin-small-right --}}
                                    {{-- زر لإعادة تعيين الفلاتر --}}
                                    <a href="{{ route('equipments') }}" class="btn btn-secondary btn-lg"><span>إعادة
                                            تعيين الفلاتر</span></a> {{-- uk-button uk-button-default uk-button-large --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-category uk-section-large">
            <div class="uk-container">
                <div class="section-title uk-text-center">
                    <div class="uk-text-meta">ستجد لدينا افضل المعدات</div>
                    <div class="uk-h2">تصفح فئات المعدات الرئيسية</div> {{-- تم التعديل --}}
                </div>
                <div class="section-content">
                    <div class="uk-grid uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                        @forelse ($featuredCategories as $category)
                            {{-- ستعرض الآن الفئات الرئيسية فقط --}}
                            <div>
                                <div class="category-item">
                                    <a class="category-item__link uk-inline-clip uk-transition-toggle"
                                        href="{{ route('categories', ['parent_id' => $category->id]) }}" tabindex="0">
                                        <div class="category-item__media">
                                            <img src="{{ $category->image_url ? asset('storage/' . $category->image_url) : asset('home/assets/img/category-default.jpg') }}"
                                                alt="{{ $category->category_name }}" />
                                            <div class="uk-transition-fade">
                                                <div class="uk-overlay-primary uk-position-cover"></div>
                                                <div class="uk-position-center"><span
                                                        data-uk-icon="icon: plus; ratio: 2"></span></div>
                                            </div>
                                        </div>
                                        <div class="category-item__title"> <span>{{ $category->category_name }}</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="uk-width-1-1 uk-text-center">
                                <p>لا توجد فئات رئيسية متاحة حالياً.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="uk-margin-large-top uk-text-center"><a class="uk-button uk-button-default uk-button-large"
                            href="{{ route('categories') }}"><span>المزيد من الفئات</span></a></div>
                </div>
            </div>
        </div>

        <div class="section-category">
            <div class="uk-container">
                <div class="section-title uk-text-center">
                    <div class="uk-text-meta">ستجد لدينا افضل المعدات</div>
                    <div class="uk-h2">تصفح فئات المعدات</div>
                </div>
                <div class="section-content">
                    <div class="uk-grid uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                        @forelse ($featuredCategories as $category)
                            <div>
                                <div class="category-item">
                                    <a class="category-item__link uk-inline-clip uk-transition-toggle"
                                        href="{{ route('equipments', ['category' => $category->id]) }}" tabindex="0">
                                        <div class="category-item__media">
                                            <img src="{{ $category->image_url ? asset('storage/' . $category->image_url) : asset('assets/home/img/category-default.jpg') }}"
                                                alt="{{ $category->category_name }}" />
                                            <div class="uk-transition-fade">
                                                <div class="uk-overlay-primary uk-position-cover"></div>
                                                <div class="uk-position-center"><span
                                                        data-uk-icon="icon: plus; ratio: 2"></span></div>
                                            </div>
                                        </div>
                                        <div class="category-item__title"> <span>{{ $category->category_name }}</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="uk-width-1-1 uk-text-center">
                                <p>لا توجد فئات متاحة حالياً.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="uk-margin-large-top uk-text-center"><a class="uk-button uk-button-default uk-button-large"
                            href="{{ route('categories') }}"><span>المزيد من الفئات</span></a></div>
                    {{-- تم التعديل --}}
                </div>
            </div>
        </div>
        <div class="section-about">
            <div class="uk-container">
                <div class="section-content">
                    <div class="uk-grid uk-grid-large uk-child-width-1-2@m" data-uk-grid>
                        <div><img src="{{ asset('assets/home/img/img-about.png') }}" alt="عن الموقع"></div>
                        <div>
                            <div class="section-title">
                                <div class="uk-text-meta">منصة تأجير معدات البناء</div>
                                <div class="uk-h2">نحن نقدم خدمات عديدة <br> جميع اشكال و احجام<br> معدات البناء</div>
                            </div>
                            <p>{{ $siteDescription }}</p>
                            <ul class="about-list">
                                <li> <strong>معدات<br> أقل تكلفة</strong><span>وفر على نفسك ، تأجر بدل ما تشتري وبأقل
                                        الأسعار
                                    </span></li>
                                <li> <strong>خدمة عملاء<br>متاحة</strong><span>لدينا العديد من الموظفين لمتابعة
                                        خدماتكم</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
