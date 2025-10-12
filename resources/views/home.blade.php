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
                                <span></span><span></span><span></span></div>
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
                                <span></span><span></span><span></span></div>
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
    <div class="section-find ">
        <div class="uk-container">
            <div class="find-box">
                <div class="find-box__title"> <span>اعثر على المعدات المناسبة</span></div>
                <div class="find-box__form">
                    <form action="{{ route('equipments') }}" method="GET">
                        <div class="uk-grid uk-grid-medium uk-flex-middle uk-child-width-1-3@m uk-child-width-1-2@s"
                            data-uk-grid>
                            <div>
                                <div class="uk-inline uk-width-1-1">
                                    <select class="uk-select uk-form-large" name="category">
                                        <option value="">إختر الصنف</option>
                                        @foreach ($equipmentCategories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="uk-form-icon"><img src="{{ asset('assets/home/img/icons/truck.svg') }}"
                                            alt="truck" data-uk-svg></span>
                                </div>
                            </div>
                            <div>
                                <div class="uk-inline uk-width-1-1"><input class="uk-input uk-form-large uk-width-1-1"
                                        type="text" name="query" placeholder="اسم المعدة"> <span
                                        class="uk-form-icon"><img src="{{ asset('assets/home/img/icons/derrick.svg') }}"
                                            alt="derrick" data-uk-svg></span></div>
                            </div>
                            <div>
                                <div class="uk-inline uk-width-1-1">
                                    <select class="uk-select uk-form-large" name="location">
                                        <option value="">إختر موقعك الحالي</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location }}">{{ $location }}</option>
                                        @endforeach
                                    </select>
                                    <span class="uk-form-icon"> <img src="{{ asset('assets/home/img/icons/location.svg') }}"
                                            alt="location" data-uk-svg></span>
                                </div>
                            </div>
                        </div>
                        <div class="uk-margin-large-top uk-text-center"><button type="submit"
                                class="uk-button uk-button-default uk-button-large"><span>البحث عن المعدات</span></button>
                        </div> {{-- تم التعديل ليكون زر submit --}}
                    </form>
                </div>
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
                                    <div class="category-item__title"> <span>{{ $category->category_name }}</span></div>
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
                        href="{{ route('categories') }}"><span>المزيد من الفئات</span></a></div> {{-- تم التعديل --}}
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
                            <li> <strong>معدات<br> أقل تكلفة</strong><span>وفر على نفسك ، تأجر بدل ما تشتري وبأقل الأسعار
                                </span></li>
                            <li> <strong>خدمة عملاء<br>متاحة</strong><span>لدينا العديد من الموظفين لمتابعة خدماتكم</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
