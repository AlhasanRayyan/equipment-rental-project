<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>الصفحة الرئيسية - تأجير المعدات</title> 
  <meta content="Templines" name="author">
  <meta content="SPCER" name="description">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="HandheldFriendly" content="true">
  <meta name="format-detection" content="telephone=no">
  <meta content="IE=edge" http-equiv="X-UA-Compatible">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/home/img/favicon/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/home/img/favicon/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/home/img/favicon/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('assets/home/img/favicon/site.html') }}">
  <meta name="msapplication-TileColor" content="#da532c"> 
  <meta name="theme-color" content="#222222">
  <link rel="stylesheet" href="{{ asset('assets/home/css/libs.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/home/css/main.css') }}">
</head>

<body class="page-home">

      <!-- Loader-->
      <div id="page-preloader"><span class="spinner border-t_second_b border-t_prim_a"></span></div>
      <!-- Loader end-->

  <div class="page-wrapper">
    <!-- start header html  -->
    <header class="page-header">
      <div class="page-header-bottom" >
        <div class="page-header-bottom__left">
          <div class="logo"><a class="logo__link" href="{{ route('home') }}"><img class="logo__img" src="{{ asset('assets/home/img/logo.png') }}" alt="شعار الموقع"></a></div>

        </div>
        <div class="page-header-bottom__right">
          <nav class="uk-navbar-container  uk-navbar-transparent" data-uk-navbar>
            <div class="nav-overlay uk-visible@l  List">
              <ul class="uk-navbar-nav">
                <li class="uk-active"><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                <li><a href="{{ route('categories') }}">الفئات</a></li> 
                <li><a href="{{ route('equipments') }}">المعدات</a></li> 
                <li><a href="{{ route('about') }}">عن الموقع</a></li> 
                <li><a href="{{ route('contact') }}">تواصل معنا</a></li> 
              </ul>
            </div>
            <div class="login-link">
                <i class="fas fa-sign-in-alt"></i>
                @auth
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-home').submit();">تسجيل الخروج</a>
                    <form id="logout-form-home" action="{{ route('logout') }}" method="POST" class="uk-hidden">@csrf</form>
                @else
                    <a href="{{ route('login') }}">تسجيل الدخول</a>
                @endauth
            </div>

            <div class="nav-overlay search-btn"><a class="uk-navbar-toggle" data-uk-search-icon data-uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a></div>
            <div class="nav-overlay uk-navbar-left uk-flex-1" hidden>
              <div class="uk-navbar-item uk-width-expand">
                <form class="uk-search uk-search-navbar uk-width-1-1" action="{{ route('equipments') }}" method="GET"><input class="uk-search-input" type="search" name="query" placeholder="ابحث عن المعدات..." autofocus></form>
              </div><a class="uk-navbar-toggle" data-uk-close data-uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>
            </div>
          </nav>
          <a class="uk-navbar-toggle uk-hidden@l" href="#offcanvas" data-uk-toggle><span data-uk-icon="menu"></span></a>

        </div>
      </div>
    </header>
    <!-- end header html  -->
    <main class="page-main">
      <div class="section-slideshow">
        <div class="uk-position-relative uk-light" tabindex="-1" data-uk-slideshow="animation: fade; min-height: 400; max-height: 700;">
          <ul class="uk-slideshow-items">
            {{-- يمكن جعل الشرائح ديناميكية من إعدادات النظام أو جدول مخصص للشرائح --}}
            <li class="slideshow-item"><img src="{{ asset('assets/home/img/slideshow-1.jpg') }}" alt="معدة بناء" data-uk-cover>
              <div class="slideshow-item__content">
                <div class="uk-position-center uk-position-small uk-text-center">
                  <div class="header-icons" data-uk-slideshow-parallax="x: -200,200"><span></span><span></span><span></span></div>
                  <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">يقدم لك الموقع معدات البناء المتنوعة </p>
                  <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">الموقع الأفضل في تأجير المعدات<br>في قطاع غزة</h2>
                </div>
              </div>
            </li>
            <li class="slideshow-item"><img src="{{ asset('assets/home/img/slideshow-2.jpg') }}" alt="معدات متطورة" data-uk-cover>
              <div class="slideshow-item__content">
                <div class="uk-position-center uk-position-small uk-text-center">
                  <div class="header-icons" data-uk-slideshow-parallax="x: -200,200"><span></span><span></span><span></span></div>
                  <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">معدات متطورة جاهزة بأسعار مرنة</p>
                  <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">كل ما تحتاجه للبناء<br> متوفر هنا </h2>
                </div>
              </div>
            </li>
          </ul>
          <div class="uk-visible@m"><a class="uk-position-center-left uk-position-small" href="#" data-uk-slidenav-previous data-uk-slideshow-item="previous"></a><a class="uk-position-center-right uk-position-small" href="#" data-uk-slidenav-next data-uk-slideshow-item="next"></a></div>
        </div>
      </div>
      <div class="section-find ">
        <div class="uk-container">
          <div class="find-box">
            <div class="find-box__title"> <span>اعثر على المعدات المناسبة</span></div>
            <div class="find-box__form">
              <form action="{{ route('equipments') }}" method="GET">
                <div class="uk-grid uk-grid-medium uk-flex-middle uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                  <div>
                    <div class="uk-inline uk-width-1-1">
                      <select class="uk-select uk-form-large" name="category">
                        <option value="">إختر الصنف</option>
                        @foreach ($equipmentCategories as $category)
                          <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                      </select>
                    <span class="uk-form-icon"><img src="{{ asset('assets/home/img/icons/truck.svg') }}" alt="truck" data-uk-svg></span>
                    </div>
                  </div>
                  <div>
                    <div class="uk-inline uk-width-1-1"><input class="uk-input uk-form-large uk-width-1-1" type="text" name="query" placeholder="اسم المعدة"> <span class="uk-form-icon"><img src="{{ asset('assets/home/img/icons/derrick.svg') }}" alt="derrick" data-uk-svg></span></div>
                  </div>
                  <div>
                    <div class="uk-inline uk-width-1-1">
                        <select  class="uk-select uk-form-large" name="location">
                        <option value="">إختر موقعك الحالي</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                      </select>
                      <span class="uk-form-icon"> <img src="{{ asset('assets/home/img/icons/location.svg') }}" alt="location" data-uk-svg></span>
                    </div>
                  </div>
                  </div>
            <div class="uk-margin-large-top uk-text-center"><button type="submit" class="uk-button uk-button-default uk-button-large"><span>البحث عن المعدات</span></button></div> {{-- تم التعديل ليكون زر submit --}}
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="section-category">
        <div class="uk-container">
          <div class="section-title uk-text-center">
            <div class="uk-text-meta">ستجد لدينا افضل المعدات</div>
            <div class="uk-h2">تصفح فئات الآلات</div>
          </div>
          <div class="section-content">
            <div class="uk-grid uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
              @forelse ($featuredCategories as $category)
                <div>
                  <div class="category-item">
                    <a class="category-item__link uk-inline-clip uk-transition-toggle" href="{{ route('equipments', ['category' => $category->id]) }}" tabindex="0"> 
                      <div class="category-item__media">
                        <img src="{{ $category->image_url ? asset('storage/' . $category->image_url) : asset('assets/home/img/category-default.jpg') }}" alt="{{ $category->category_name }}" /> 
                        <div class="uk-transition-fade">
                          <div class="uk-overlay-primary uk-position-cover"></div>
                          <div class="uk-position-center"><span data-uk-icon="icon: plus; ratio: 2"></span></div>
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
            <div class="uk-margin-large-top uk-text-center"><a class="uk-button uk-button-default uk-button-large" href="{{ route('categories') }}"><span>المزيد من الفئات</span></a></div> {{-- تم التعديل --}}
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
                  <li> <strong>معدات<br> أقل تكلفة</strong><span>وفر على نفسك ، تأجر بدل ما تشتري وبأقل الأسعار </span></li>
                  <li> <strong>خدمة عملاء<br>متاحة</strong><span>لدينا العديد من الموظفين لمتابعة خدماتكم</span></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

    </main>
    <!-- start footer html  -->
    <footer class="page-footer">
      <div class="uk-container uk-container-large">

        <div class="page-footer-middle">
          <div class="uk-grid uk-child-width-1-4@l uk-child-width-1-2@s" data-uk-grid>
            <div class="uk-flex-first@l">
              <div class="title">عن المنصة</div>
              <p>{{ $siteDescription }}</p> 
              <ul class="social-list">
                <li class="social-list__item"><a class="social-list__link" href="#!"><i class="fab fa-facebook-f"></i></a></li>
                <li class="social-list__item"><a class="social-list__link" href="#!"><i class="fab fa-twitter"></i></a></li>
                <li class="social-list__item"><a class="social-list__link" href="#!"><i class="fab fa-google-plus-g"></i></a></li>
                <li class="social-list__item"><a class="social-list__link" href="#!"><i class="fab fa-linkedin-in"></i></a></li>
                <li class="social-list__item"><a class="social-list__link" href="#!"><i class="fab fa-vimeo-v"></i></a></li>
              </ul>
            </div>
            <div class="uk-flex-last@l">
              <div class="title">معلومات التواصل</div>
              <ul class="contacts-list">
                <li class="contacts-list-item">
                  <div class="contacts-list-item__icon"><img src="{{ asset('assets/home/img/icons/ico-phone24.svg') }}" data-uk-svg alt="For Rental Support"></div>
                  <div class="contacts-list-item__desc">
                    <div class="contacts-list-item__label">الدعم الفني</div>
                    <div class="contacts-list-item__content"> <a href="tel:{{ $contactPhone }}">{{ $contactPhone }}</a></div> {{-- تم التعديل --}}
                  </div>
                </li>
                <li class="contacts-list-item">
                  <div class="contacts-list-item__icon"><img src="{{ asset('assets/home/img/icons/ico-timer.svg') }}" data-uk-svg alt="The Office Hours"></div>
                  <div class="contacts-list-item__desc">
                    <div class="contacts-list-item__label">ساعات العمل</div>
                    <div class="contacts-list-item__content">{{ $officeHours }}</div> 
                  </div>
                </li>
                <li class="contacts-list-item">
                  <div class="contacts-list-item__icon"><img src="{{ asset('assets/home/img/icons/ico-mail.svg') }}" data-uk-svg alt="Send Us Email"></div>
                  <div class="contacts-list-item__desc">
                    <div class="contacts-list-item__label">راسلنا على الإيميل</div>
                    <div class="contacts-list-item__content"> <a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></div> {{-- تم التعديل --}}
                  </div>
                </li>
              </ul>
            </div>
            <div>
              <div class="title">روابط مفيدة</div>
              <ul class="uk-nav uk-list-disc">
                <li> <a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                <li> <a href="{{ route('categories') }}">الفئات</a></li>
                <li> <a href="{{ route('equipments') }}">المعدات</a></li>
                <li> <a href="{{ route('contact') }}">تواصل معنا</a></li>
                <li> <a href="{{ route('about') }}">عن المنصة</a></li>
              </ul>
            </div>
            <div>
              <div class="title">اكتشف المنصة</div>
              <ul class="uk-nav uk-list-disc">
                @guest
                    <li><a href="{{ route('register') }}">إنشاء حساب</a></li>
                    <li><a href="{{ route('login') }}">تسجيل دخول</a></li>
                @else
                    <li><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li> {{-- توجيه للداشبورد إذا كان مسجلاً --}}
                @endguest
                <li><a href="#">اقرأ الأسئلة الشائعة</a></li> {{-- يمكن ربطها بصفحة FAQs --}}
              </ul>
            </div>
          </div>
        </div>

        <div class="page-footer-bottom"><span>(c) 2025 SPCER- تأجير معدات البناء .حقوق النشر والطبع محفوظة</span></div><a class="totop-link" href="#top" data-uk-scroll><img src="{{ asset('assets/home/img/icons/ico-totop.svg') }}" alt="totop"><span>Go to top</span></a>
      </div>
      <div id="offcanvas" data-uk-offcanvas="overlay: true">
        <div class="uk-offcanvas-bar"><button class="uk-offcanvas-close" type="button" data-uk-close=""></button>
          <div class="uk-margin">
            <div class="logo"><a class="logo__link" href="{{ route('home') }}"><img class="logo__img" src="{{ asset('assets/home/img/logo.png') }}" alt="شعار"></a></div>
          </div>
          <div class="uk-margin">
            <ul class="uk-nav-default uk-nav-parent-icon" data-uk-nav>
              <li class="uk-active"><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
              <li><a href="{{ route('categories') }}">الفئات</a></li>
              <li><a href="{{ route('equipments') }}">المعدات</a></li>
              <li><a href="{{ route('about') }}">عن الموقع</a></li>
              <li ><a href="{{ route('contact') }}">تواصل معنا</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="uk-flex-top" id="callback" data-uk-modal="">
        <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical"><button class="uk-modal-close-default" type="button" data-uk-close=""></button>
          <p>هذا نموذج لخدمة الاتصال السريع، يمكن ربط محتواه بخدمة تواصل معينة.</p> 
        </div>
      </div>
    </footer>
    <!-- end footer html  -->
  </div>
  <script src="{{ asset('assets/home/js/libs.js') }}"></script>
  <script src="{{ asset('assets/home/js/main.js') }}"></script>
</body>
</html>