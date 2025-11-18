@extends('layouts.master')

@section('title', 'عن المنصة')

@section('content')
    <div class="section-slideshow">
        <div class="uk-position-relative uk-light" tabindex="-1"
            data-uk-slideshow="animation: fade; min-height: 400; max-height: 700;">
            <ul class="uk-slideshow-items">
                <li class="slideshow-item"><img src="{{ asset('assets/home/img/slideshow-1.jpg') }}" alt data-uk-cover>
                    <div class="slideshow-item__content">
                        <div class="uk-position-center uk-position-small uk-text-center">
                            <div class="header-icons" data-uk-slideshow-parallax="x: -200,200">
                                <span></span><span></span><span></span>
                            </div>
                            <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">عن المنصة</p>
                            <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">منصة ذكية لتأجير <br>
                                معدات البناء</h2>
                        </div>
                    </div>
                </li>
                <li class="slideshow-item"><img src="{{ asset('assets/home/img/slideshow-2.jpg') }}" alt data-uk-cover>
                    <div class="slideshow-item__content">
                        <div class="uk-position-center uk-position-small uk-text-center">
                            <div class="header-icons" data-uk-slideshow-parallax="x: -200,200">
                                <span></span><span></span><span></span>
                            </div>
                            <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">معدات متطورة جاهزة
                                بأسعار مرنة حول</p>
                            <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">مصدرك الوحيد
                                للجميع<br> احتياجات تأجير المعدات</h2>
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

    {{-- <div class="section-steps">
        <div class="uk-container uk-container-large">
            <div class="section-title uk-text-center">
                <div class="uk-text-meta">نعدك بالعثور على المعدات المناسبة لك</div>
                <div class="uk-h2">احصل على إيجاراتك بخطوات سهلة</div>
            </div>
            <div class="section-content">
                <div class="steps-list uk-position-relative" tabindex="-1" data-uk-slider="finite: true">
                    <ul
                        class="uk-slider-items uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l">
                        <li>
                            <div class="steps-item">
                                <div class="steps-item__box"
                                    data-uk-tooltip="title: قم بالبحث عن المعدة التي تناسبك  ; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 500; pos: bottom">
                                    <div class="steps-item__icon"><img
                                            src="{{ asset('assets/home/img/icons/ico-step-1.png') }}" alt="icon-step"></div>
                                    <div class="steps-item__title"><b>1</b><span>ابحث عن معداتك</span></div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="steps-item">
                                <div class="steps-item__box"
                                    data-uk-tooltip="title: إختر المعدة المناسبة لعملك ; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 500; pos: bottom">
                                    <div class="steps-item__icon"><img
                                            src="{{ asset('assets/home/img/icons/ico-step-2.png') }}" alt="icon-step"></div>
                                    <div class="steps-item__title"><b>2</b><span>إختر معدتك</span></div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="steps-item">
                                <div class="steps-item__box"
                                    data-uk-tooltip="title: احجز المعدة و اكمل باقي الإجراءات بسهولة تامة ; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 500; pos: bottom">
                                    <div class="steps-item__icon"><img
                                            src="{{ asset('assets/home/img/icons/ico-step-3.png') }}" alt="icon-step"></div>
                                    <div class="steps-item__title"><b>3</b><span>حجز المعدات </span></div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="steps-item">
                                <div class="steps-item__box"
                                    data-uk-tooltip="title: استقبل الفاتورة و استلم المعدة المطلوبة.; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 500; pos: bottom">
                                    <div class="steps-item__icon"><img
                                            src="{{ asset('assets/home/img/icons/ico-step-4.png') }}" alt="icon-step"></div>
                                    <div class="steps-item__title"><b>4</b><span> إستلم المعدة</span></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="uk-hidden@l">
                        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin-medium-top"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="section-steps">
        <div class="uk-container uk-container-large">
            <div class="section-title uk-text-center">
                <div class="uk-text-meta">{{ $stepsSubtitle }}</div>
                <div class="uk-h2">{{ $stepsTitle }}</div>
            </div>
            <div class="section-content">
                <div class="steps-list uk-position-relative" tabindex="-1" data-uk-slider="finite: true">
                    <ul
                        class="uk-slider-items uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l">
                        <li>
                            <div class="steps-item">
                                <div class="steps-item__box"
                                    data-uk-tooltip="title: {{ $step1Tooltip }}; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 500; pos: bottom">
                                    <div class="steps-item__icon">
                                        <img src="{{ asset($step1Icon) }}" alt="icon-step">
                                    </div>
                                    <div class="steps-item__title"><b>1</b><span>{{ $step1Title }}</span></div>
                                </div>

                            </div>
                        </li>
                        <li>
                            <div class="steps-item">
                                <div class="steps-item__box"
                                    data-uk-tooltip="title: {{ $step2Tooltip }}; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 500; pos: bottom">
                                    <div class="steps-item__icon">
                                        <img src="{{ asset($step2Icon) }}" alt="icon-step">
                                    </div>
                                    <div class="steps-item__title"><b>2</b><span>{{ $step2Title }}</span></div>
                                </div>

                            </div>
                        </li>
                        <li>
                            <div class="steps-item">
                                <div class="steps-item__box"
                                    data-uk-tooltip="title: {{ $step3Tooltip }}; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 500; pos: bottom">
                                    <div class="steps-item__icon">
                                        <img src="{{ asset($step3Icon) }}" alt="icon-step">
                                    </div>
                                    <div class="steps-item__title"><b>3</b><span>{{ $step3Title }}</span></div>
                                </div>

                            </div>
                        </li>
                        <li>
                            <div class="steps-item">
                                <div class="steps-item__box"
                                    data-uk-tooltip="title: {{ $step4Tooltip }}; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 500; pos: bottom">
                                    <div class="steps-item__icon">
                                        <img src="{{ asset($step4Icon) }}" alt="icon-step">
                                    </div>
                                    <div class="steps-item__title"><b>4</b><span>{{ $step4Title }}</span></div>
                                </div>

                            </div>
                        </li>
                    </ul>
                    <div class="uk-hidden@l">
                        <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin-medium-top"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="section-stats">
        <div class="uk-container uk-container-xlarge">
            <div class="uk-grid uk-child-width-1-3@l uk-child-width-1-2@s" data-uk-grid>

                <div>
                    <div class="stats-item">
                        <div class="stats-item__header">
                            <div class="stats-item__numb">{{ $usersCount }}</div>
                            <div class="stats-item__icon">
                                <img src="{{ asset('assets/home/img/icons/ico-stats-2.svg') }}" data-uk-svg>
                            </div>
                        </div>
                        <div class="stats-item__title">عدد المستخدمين</div>
                        <div class="stats-item__desc">نحن نخدم مستخدمين من كل مناطق القطاع.</div>
                    </div>
                </div>

                <div>
                    <div class="stats-item">
                        <div class="stats-item__header">
                            <div class="stats-item__numb">{{ $equipmentsCount }}</div>
                            <div class="stats-item__icon">
                                <img src="{{ asset('assets/home/img/icons/ico-stats-1.svg') }}" data-uk-svg>
                            </div>
                        </div>
                        <div class="stats-item__title">عدد المعدات</div>
                        <div class="stats-item__desc">معدات متعددة جاهزة للتأجير من مختلف الفئات.</div>
                    </div>
                </div>

                <div>
                    <div class="stats-item">
                        <div class="stats-item__header">
                            <div class="stats-item__numb">{{ $bookingsCount }}</div>
                            <div class="stats-item__icon">
                                <img src="{{ asset('assets/home/img/icons/ico-stats-3.svg') }}" data-uk-svg>
                            </div>
                        </div>
                        <div class="stats-item__title">معاملات التأجير</div>
                        <div class="stats-item__desc">حجوزات ناجحة تم تنفيذها عبر المنصة.</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- <div class="section-why-choose-us">
        <div class="uk-container">
            <div class="uk-grid uk-grid-large uk-child-width-1-2@m uk-flex-middle" data-uk-grid>
                <div>
                    <div class="blocks-icon uk-grid uk-grid-medium uk-child-width-1-2@s" data-uk-grid>
                        <div>
                            <div class="block-icon"><a class="block-icon__link" href="">
                                    <div class="block-icon__box">
                                        <div class="block-icon__ico"><img
                                                src="{{ asset('assets/home/img/icons/ico-why-choose-1.svg') }}"
                                                alt="block-icon"></div>
                                        <div class="block-icon__title">جودة معدات<br> عالية</div>
                                    </div>
                                </a></div>
                        </div>
                        <div>
                            <div class="block-icon"><a class="block-icon__link" href="">
                                    <div class="block-icon__box">
                                        <div class="block-icon__ico"><img
                                                src="{{ asset('assets/home/img/icons/ico-why-choose-2.svg') }}"
                                                alt="block-icon"></div>
                                        <div class="block-icon__title">موثوقية &<br> خدمة سريعة</div>
                                    </div>
                                </a></div>
                        </div>
                        <div>
                            <div class="block-icon"><a class="block-icon__link" href="">
                                    <div class="block-icon__box">
                                        <div class="block-icon__ico"><img
                                                src="{{ asset('assets/home/img/icons/ico-why-choose-3.svg') }}"
                                                alt="block-icon"></div>
                                        <div class="block-icon__title">أفضل الأسعار</div>
                                    </div>
                                </a></div>
                        </div>
                        <div>
                            <div class="block-icon"><a class="block-icon__link" href="">
                                    <div class="block-icon__box">
                                        <div class="block-icon__ico"><img
                                                src="{{ asset('assets/home/img/icons/ico-why-choose-4.svg') }}"
                                                alt="block-icon"></div>
                                        <div class="block-icon__title">الإيجار مع <br> الأمن الكامل</div>
                                    </div>
                                </a></div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="section-title">
                        <div class="uk-text-meta">لماذا تختار منصتنا </div>
                        <div class="uk-h2">معدات متنوعة و طرق ايجار موثوقة </div>
                    </div>
                    <div class="section-content">
                        <p class="justify">تهدف المنصة إلى تسهيل عملية التأجير، تعزيز الشفافية، ودعم إعادة الإعمار من خلال
                            حلول رقمية حديثة تخدم قطاع البناء والمقاولات.</p>
                        <ul class="list-checked">
                            <li>سهولة تأجير واستئجار المعدات</li>
                            <li>واجهة استخدام بسيطة وسريعة</li>
                            <li>نظام دفع إلكتروني آمن مع إمكانية الدفع نقدًا عند التسليم.</li>
                            <li>نظام إشعارات ذكي للتنبيهات حول المواعيد والحجوزات والتجديدات.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}


    <div class="section-why-choose-us">
        <div class="uk-container">
            <div class="uk-grid uk-grid-large uk-child-width-1-2@m uk-flex-middle" data-uk-grid>
                <div>
                    <div class="blocks-icon uk-grid uk-grid-medium uk-child-width-1-2@s" data-uk-grid>
                        <div>
                            <div class="block-icon">
                                <a class="block-icon__link" href="">
                                    <div class="block-icon__box">
                                        <div class="block-icon__ico">
                                            <img src="{{ asset($whyBox1Icon) }}" alt="block-icon">
                                        </div>
                                        <div class="block-icon__title">{{ $whyBox1Title }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div>
                            <div class="block-icon">
                                <a class="block-icon__link" href="">
                                    <div class="block-icon__box">
                                        <div class="block-icon__ico">
                                            <img src="{{ asset($whyBox2Icon) }}" alt="block-icon">

                                        </div>
                                        <div class="block-icon__title">{{ $whyBox2Title }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div>
                            <div class="block-icon">
                                <a class="block-icon__link" href="">
                                    <div class="block-icon__box">
                                        <div class="block-icon__ico">
                                            <img src="{{ asset($whyBox3Icon) }}" alt="block-icon">
                                        </div>
                                        <div class="block-icon__title">{{ $whyBox3Title }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div>
                            <div class="block-icon">
                                <a class="block-icon__link" href="">
                                    <div class="block-icon__box">
                                        <div class="block-icon__ico">
                                            <img src="{{ asset($whyBox4Icon) }}" alt="block-icon">
                                        </div>
                                        <div class="block-icon__title">{{ $whyBox4Title }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="section-title">
                        <div class="uk-text-meta">{{ $whySubtitle }}</div>
                        <div class="uk-h2">{{ $whyTitle }}</div>
                    </div>
                    <div class="section-content">
                        <p class="justify">{{ $whyText }}</p>
                        <ul class="list-checked">
                            <li>سهولة تأجير واستئجار المعدات</li>
                            <li>واجهة استخدام بسيطة وسريعة</li>
                            <li>نظام دفع إلكتروني آمن مع إمكانية الدفع نقدًا عند التسليم.</li>
                            <li>نظام إشعارات ذكي للتنبيهات حول المواعيد والحجوزات والتجديدات.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- <div class="section-equipment">
        <div class="uk-container">
            <div class="equipment-box">
                <div class="equipment-box__media"><img src="{{ asset('assets/home/img/img-equipment.jpg') }}"
                        alt=""></div>
                <div class="equipment-box__desc">
                    <div class="equipment-box__title">هل أنت قلق بشأن وقوف المعدات في الخلاء ؟</div>
                    <div class="equipment-box__text">ابدأ بإدراج معداتك معنا اليوم!</div>
                    <div class="equipment-box__btn"><a class="uk-button uk-button-large"
                            href="{{ auth()->check() ? route('equipments.create') : route('login', ['redirect' => 'equipments.create']) }}">
                            <span>إبدأالآن</span><img class="makos" src="{{ asset('assets/home/img/icons/arrow.svg') }}"
                                alt="arrow" data-uk-svg></a>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="section-equipment">
        <div class="uk-container">
            <div class="equipment-box">
                <div class="equipment-box__media">
                    <img src="{{ asset('assets/home/img/img-equipment.jpg') }}" alt="">
                </div>
                <div class="equipment-box__desc">
                    <div class="equipment-box__title">{{ $ctaTitle }}</div>
                    <div class="equipment-box__text">{{ $ctaText }}</div>
                    <div class="equipment-box__btn">
                        <a class="uk-button uk-button-large"
                            href="{{ auth()->check() ? route('equipments.create') : route('login', ['redirect' => 'equipments.create']) }}">
                            <span>إبدأالآن</span>
                            <img class="makos" src="{{ asset('assets/home/img/icons/arrow.svg') }}" alt="arrow"
                                data-uk-svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <br>
@endsection
