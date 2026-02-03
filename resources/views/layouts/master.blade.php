<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <title> @yield('title', 'SPCER') </title>
    <meta content="Templines" name="author">
    <meta content="SPCER" name="description">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="HandheldFriendly" content="true">
    <meta name="format-detection" content="telephone=no">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/home/img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('assets/home/img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('assets/home/img/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/home/img/favicon/site.html') }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#222222">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/home/css/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/home/css/main.css') }}">
    <!-- إضافة FontAwesome إذا لم تكن موجودة -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* تنسيقات القوائم المنسدلة الجديدة */
        .user-actions-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 15px;
        }

        .dropdownNot, .dropdown {
            position: relative;
            display: inline-block;
        }

        .NotificationsIcon {
            width: 24px;
            height: 24px;
            cursor: pointer;
            color: #333;
        }

        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            display: none; /* تظهر عند وجود إشعارات */
        }

        .user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            object-fit: cover;
            border: 2px solid #eee;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 250px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
        }

        .dropdown-content.show {
            display: block;
        }

        .dropdown-content a, .dropdown-link {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            transition: 0.3s;
            border: none;
            background: none;
            width: 100%;
            text-align: right;
            cursor: pointer;
        }

        .dropdown-content a:hover, .dropdown-link:hover {
            background-color: #f1f1f1;
        }

        /* تنسيقات خاصة بالإشعارات */
        .notif-header { padding: 10px; font-weight: bold; border-bottom: 1px solid #eee; text-align: center; }
        .notif-list { list-style: none; margin: 0; padding: 0; max-height: 300px; overflow-y: auto; }
        .notif-item { padding: 10px; border-bottom: 1px solid #eee; display: flex; gap: 10px; font-size: 13px; }
        .notif-item i { color: #007bff; margin-top: 3px; }
        .notif-footer { padding: 10px; text-align: center; background: #f9f9f9; }
        .notif-item-empty { padding: 20px; text-align: center; color: #888; display: none; }
        
        .logout-form { margin: 0; }
    </style>

    @stack('styles')
</head>

<body class="page-home">

    <div id="page-preloader"><span class="spinner border-t_second_b border-t_prim_a"></span></div>

    <div class="page-wrapper">
        <header class="page-header">
            <div class="page-header-bottom">
                <div class="page-header-bottom__left">
                    <div class="logo">
                        <a class="logo__link" href="{{ route('home') }}">
                            <img class="logo__img" src="{{ asset('assets/img/logo.png') }}" alt="شعار الموقع" width="90" height="90" >
                        </a>
                    </div>
                </div>
                <div class="page-header-bottom__right">
                    <nav class="uk-navbar-container uk-navbar-transparent" data-uk-navbar>
                        <div class="nav-overlay uk-visible@l List">
                            <ul class="uk-navbar-nav">
                                <li class="{{ request()->routeIs('home') ? 'uk-active' : '' }}"><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                                <li class="{{ request()->routeIs('categories') ? 'uk-active' : '' }}"><a href="{{ route('categories') }}">الفئات</a></li>
                                <li class="{{ request()->routeIs('equipments*') ? 'uk-active' : '' }}"><a href="{{ route('equipments') }}">المعدات</a></li>
                                <li class="{{ request()->routeIs('about') ? 'uk-active' : '' }}"><a href="{{ route('about') }}">عن المنصة</a></li>
                                <li class="{{ request()->routeIs('contact') ? 'uk-active' : '' }}"><a href="{{ route('contact') }}">تواصل معنا</a></li>
                            </ul>
                        </div>

                        <!-- بداية التعديل: استبدال login-link بالكود الجديد -->
                        <div class="user-actions-wrapper">
                            @auth
                                <!-- أيقونة الإشعارات -->
                                <div class="dropdownNot">
                                    <svg class="NotificationsIcon" id="userBtnNot" viewBox="0 0 24 24" fill="none"
                                        aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15 17H9c0 1.1.9 2 2 2s2-.9 2-2z" fill="currentColor" />
                                        <path d="M18 8c0-3.07-1.63-5.64-4.5-6.32V1a1.5 1.5 0 0 0-3 0v.68C7.63 2.36 6 4.92 6 8v5l-1.7 1.7A1 1 0 0 0 5 16h14a1 1 0 0 0 .7-1.7L18 13V8z"
                                            fill="currentColor" />
                                    </svg>
                                    <span class="notif-badge" id="notifBadge">3</span>

                                    <div class="dropdown-content" id="dropdownMenuNot">
                                        <div class="notif-header">الإشعارات</div>
                                        <ul class="notif-list">
                                            <li class="notif-item">
                                                <i class="fas fa-check-circle"></i>
                                                <span class="notif-item-text">تم تأكيد حجزك بنجاح</span>
                                            </li>
                                            <li class="notif-item"><i class="fas fa-clock"></i><span class="notif-item-text">لديك موعد جديد غدًا</span></li>
                                        </ul>
                                        <div class="notif-item-empty">لا توجد إشعارات جديدة</div>
                                        <div class="notif-footer">
                                            <a href="#" class="show-all">عرض الكل</a>
                                        </div>
                                    </div>
                                </div>
                            @endauth

                            <!-- قائمة المستخدم -->
                            <div class="dropdown">
                                @php
                                    $user = Auth::user();
                                @endphp

                                <img src="{{ $user ? ($user->profile_picture_url ? asset('storage/' . $user->profile_picture_url) : asset('assets/home/img/admin.jpeg')) : asset('assets/home/img/guest.png') }}"
                                    alt="User" class="user-img" id="userBtn">

                                <div class="dropdown-content" id="dropdownMenu">
                                    @if ($user)
                                        <a href="{{ route('profile.show', $user->id) }}"><i class="fas fa-user"></i> الملف الشخصي</a>
                                        <a href="{{ route('profile.edit', $user->id) }}"><i class="fas fa-edit"></i> تحديث البيانات</a>
                                        <form action="{{ route('logout') }}" method="POST" class="logout-form">
                                            @csrf
                                            <button type="submit" class="dropdown-link">
                                                <i class="fas fa-sign-out-alt"></i> تسجيل خروج
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> تسجيل دخول</a>
                                        <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> إنشاء حساب</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- نهاية التعديل -->

                        <div class="nav-overlay search-btn">
                            <a class="uk-navbar-toggle" data-uk-search-icon
                                data-uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>
                        </div>

                        <div class="nav-overlay uk-navbar-left uk-flex-1" hidden>
                            <div class="uk-navbar-item uk-width-expand">
                                <form class="uk-search uk-search-navbar uk-width-1-1" action="{{ route('equipments') }}" method="GET">
                                    <input class="uk-search-input" type="search" name="query" placeholder="ابحث عن المعدات..." autofocus>
                                </form>
                            </div>
                            <a class="uk-navbar-toggle" data-uk-close data-uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>
                        </div>
                    </nav>

                    <a class="uk-navbar-toggle uk-hidden@l" href="#offcanvas" data-uk-toggle><span data-uk-icon="menu"></span></a>
                </div>
            </div>
        </header>

        <main class="page-main">
            @yield('content')
        </main>

        <footer class="page-footer">
            <div class="uk-container uk-container-large">
                <div class="page-footer-middle">
                    <div class="uk-grid uk-child-width-1-4@l uk-child-width-1-2@s" data-uk-grid>
                        <div class="uk-flex-first@l">
                            <div class="title">عن المنصة</div>
                            <p>{{ $siteDescription ?? 'وصف المنصة هنا' }}</p>
                            <ul class="social-list">
                                <li class="social-list__item"><a class="social-list__link" href="#!"><i class="fab fa-facebook-f"></i></a></li>
                                <li class="social-list__item"><a class="social-list__link" href="#!"><i class="fab fa-twitter"></i></a></li>
                            </ul>
                        </div>
                        <div class="uk-flex-last@l">
                            <div class="title">معلومات التواصل</div>
                            <ul class="contacts-list">
                                <li class="contacts-list-item">
                                    <div class="contacts-list-item__icon"><img src="{{ asset('assets/home/img/icons/ico-phone24.svg') }}" data-uk-svg alt=""></div>
                                    <div class="contacts-list-item__desc">
                                        <div class="contacts-list-item__label">الدعم الفني</div>
                                        <div class="contacts-list-item__content"> <a href="tel:{{ $contactPhone ?? '' }}">{{ $contactPhone ?? '' }}</a></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <div class="title">روابط مفيدة</div>
                            <ul class="uk-nav uk-list-disc">
                                <li> <a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                                <li> <a href="{{ route('categories') }}">الفئات</a></li>
                            </ul>
                        </div>
                        <div>
                            <div class="title">اكتشف المنصة</div>
                            <ul class="uk-nav uk-list-disc">
                                @guest
                                    <li><a href="{{ route('register') }}">إنشاء حساب</a></li>
                                    <li><a href="{{ route('login') }}">تسجيل دخول</a></li>
                                @else
                                    <li><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="page-footer-bottom"><span>(c) 2025 SPCER- تأجير معدات البناء .حقوق النشر والطبع محفوظة</span></div>
                <a class="totop-link" href="#top" data-uk-scroll><img src="{{ asset('assets/home/img/icons/ico-totop.svg') }}" alt="totop"><span>Go to top</span></a>
            </div>

            <!-- Offcanvas لنسخة الموبايل -->
            <div id="offcanvas" data-uk-offcanvas="overlay: true">
                <div class="uk-offcanvas-bar">
                    <button class="uk-offcanvas-close" type="button" data-uk-close=""></button>
                    <div class="uk-margin">
                        <ul class="uk-nav-default uk-nav-parent-icon" data-uk-nav>
                            <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                            @guest
                                <li><a href="{{ route('login') }}">تسجيل دخول</a></li>
                            @else
                                <li><a href="{{ route('profile.show', Auth::id()) }}">الملف الشخصي</a></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" style="background:none; border:none; color:rgba(255,255,255,0.7); padding: 5px 20px; cursor:pointer;">تسجيل خروج</button>
                                    </form>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/home/js/libs.js') }}"></script>
    <script src="{{ asset('assets/home/js/main.js') }}"></script>

    <script>
        // كود تشغيل القوائم المنسدلة عند النقر
        document.addEventListener('click', function (event) {
            const userBtn = document.getElementById('userBtn');
            const userMenu = document.getElementById('dropdownMenu');
            const notifBtn = document.getElementById('userBtnNot');
            const notifMenu = document.getElementById('dropdownMenuNot');

            // فتح/إغلاق قائمة المستخدم
            if (userBtn && userBtn.contains(event.target)) {
                userMenu.classList.toggle('show');
                if(notifMenu) notifMenu.classList.remove('show');
            } 
            // فتح/إغلاق قائمة الإشعارات
            else if (notifBtn && notifBtn.contains(event.target)) {
                notifMenu.classList.toggle('show');
                userMenu.classList.remove('show');
            }
            // إغلاق القوائم عند النقر في أي مكان آخر
            else {
                if(userMenu) userMenu.classList.remove('show');
                if(notifMenu) notifMenu.classList.remove('show');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>