<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

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
        .notif-item-btn {
            cursor: pointer;

        }

        .is-unread {
            font-weight: 700;
        }

        .is-read {
            opacity: .75;
        }

        #userNotifModalMessage {
            line-height: 1.8;
        }

        /* للمودال */
        .dropdown-content {
            display: none;
        }

        .dropdown-content.show {
            display: block;
        }

        .notif-item-btn {
            cursor: pointer;
        }

        .is-unread {
            font-weight: 700;
        }

        .is-read {
            opacity: .7;
        }

        /* تنسيقات القوائم المنسدلة الجديدة */
        .user-actions-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 15px;
        }

        .dropdownNot,
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .NotificationsIcon {
            width: 24px;
            height: 24px;
            cursor: pointer;
            color: #333;
        }

        /*
        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            display: none;
            /* تظهر عند وجود إشعارات *
        }

        */
        /* .user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            object-fit: cover;
            border: 2px solid #eee;
        } */

        .dropdown-content {
            display: none;
            position: absolute;
            right: -225px;
            background-color: #fff;
            min-width: 250px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 10px;
        }

        .dropdown-content.show {
            display: block;
        }

        .dropdown-content a,
        .dropdown-link {
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

        .dropdown-content a:hover,
        .dropdown-link:hover {
            background-color: #f1f1f1;
        }

        /* تنسيقات خاصة بالإشعارات */
        .notif-header {
            padding: 10px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        .notif-list {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 300px;
            overflow-y: auto;
        }

        .notif-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            gap: 10px;
            font-size: 13px;
        }

        .notif-item i {
            color: #007bff;
            margin-top: 3px;
        }

        .notif-footer {
            padding: 10px;
            text-align: center;
            background: #f9f9f9;
        }

        /* .notif-item-empty {
            padding: 20px;
            text-align: center;
            color: #888;
            display: none;
        } */

        .notif-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #dc3545;
            color: #fff;
            border-radius: 999px;
            min-width: 18px;
            height: 18px;
            padding: 0 6px;
            font-size: 10px;
            line-height: 18px;
            text-align: center;
            font-weight: 700;
        }

        .notif-item-empty {
            padding: 20px;
            text-align: center;
            color: #888;
        }

        .logout-form {
            margin: 0;
        }

        .page-header-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-header-bottom__right {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-shrink: 0;
        }

        .user-img {
            width: 50px;
            height: 50px;
            min-width: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eee;

        }

        .uk-navbar-nav {
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .dropdownNot {
            margin-top: 0;
            margin-bottom: 0;
        }

        .NotificationsIcon {
            width: 25px;
            height: 25px;
            min-width: 25px;
            display: block;
        }



        .page-header-bottom__right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            right: -150px;
            left: auto;
            background-color: #fff;
            min-width: 300px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            border-radius: 8px;
            margin-top: 12px;
        }
     */
        .dropdownNot {
            position: relative;
            /* هذا هو "المرساة" التي ستثبت القائمة */
            display: inline-flex;
            align-items: center;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            /* لجعلها تبدأ فوراً بعد نهاية الهيدر */
            right: -150px;
            /* لمحاذاتها مع طرف الجرس من اليمين */
            left: auto;
            background-color: #fff;
            min-width: 300px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            /* لضمان ظهورها فوق السلايدر والمحتوى */
            border-radius: 8px;
            margin-top: 12px;
            /* مسافة بسيطة تحت الجرس لتبدو أنيقة */
        }

        #userNotifModal .modal-content {
            animation: notifModalPop .18s ease;
        }

        #userNotifModal .btn {
            transition: .2s ease;
        }

        #userNotifModal .btn:hover {
            transform: translateY(-1px);
        }

        #userNotifModal #userNotifModalMeta ul {
            margin: 0;
            padding-right: 1rem;
        }

        #userNotifModal #userNotifModalMeta li {
            margin-bottom: 6px;
        }

        #userNotifModal .btn-close {
            box-shadow: none !important;
            opacity: .7;
        }

        #userNotifModal .btn-close:hover {
            opacity: 1;
        }

        @keyframes notifModalPop {
            from {
                opacity: 0;
                transform: translateY(8px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }


        .lang-btn {
            color: inherit;
            /* نفس لون العناصر */
            font-weight: normal;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* نفس تأثير الهوفر */
        .lang-btn:hover {
            color: #f0b90b;
            /* نفس اللون الأصفر تبع الـ active عندك */
        }
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
                            <img class="logo__img" src="{{ asset('assets/img/logo.png') }}" alt="شعار الموقع"
                                width="90" height="90">
                        </a>
                    </div>
                </div>
                <div class="page-header-bottom__right">
                    <nav class="uk-navbar-container uk-navbar-transparent" data-uk-navbar>
                        <div class="nav-overlay uk-visible@l List">

                            <ul class="uk-navbar-nav">
                                <li class="{{ request()->routeIs('home') ? 'uk-active' : '' }}">
                                    <a href="{{ route('home') }}">{{ __('app.home') }}</a>
                                </li>
                                <li class="{{ request()->routeIs('categories') ? 'uk-active' : '' }}">
                                    <a href="{{ route('categories') }}">{{ __('app.categories') }}</a>
                                </li>
                                <li class="{{ request()->routeIs('equipments*') ? 'uk-active' : '' }}">
                                    <a href="{{ route('equipments') }}">{{ __('app.equipments') }}</a>
                                </li>
                                <li class="{{ request()->routeIs('tracking.*') ? 'uk-active' : '' }}">
                                    <a href="{{ route('tracking.index') }}">{{ __('app.track_equipment') }}</a>
                                </li>
                                <li class="{{ request()->routeIs('about') ? 'uk-active' : '' }}">
                                    <a href="{{ route('about') }}">{{ __('app.about') }}</a>
                                </li>
                                <li class="{{ request()->routeIs('contact') ? 'uk-active' : '' }}">
                                    <a href="{{ route('contact') }}">{{ __('app.contact') }}</a>
                                </li>
                            </ul>

                        </div>

                        <!-- بداية التعديل: استبدال login-link بالكود الجديد -->
                        <div class="user-actions-wrapper">
                            @auth
                                @php
                                    $unreadCount = auth()->user()->unreadNotifications()->count();
                                    $latest = auth()->user()->notifications()->latest()->take(5)->get();

                                    $notifUI = [
                                        'booking_request' => [
                                            'icon' => 'fas fa-calendar-plus',
                                            'class' => 'text-primary',
                                            'label' => 'حجز',
                                        ],
                                        'booking_confirmed' => [
                                            'icon' => 'fas fa-check-circle',
                                            'class' => 'text-success',
                                            'label' => 'حجز',
                                        ],
                                        'booking_cancelled' => [
                                            'icon' => 'fas fa-times-circle',
                                            'class' => 'text-danger',
                                            'label' => 'حجز',
                                        ],
                                        'new_message' => [
                                            'icon' => 'fas fa-envelope',
                                            'class' => 'text-info',
                                            'label' => 'رسائل',
                                        ],
                                        'payment_received' => [
                                            'icon' => 'fas fa-money-bill-wave',
                                            'class' => 'text-success',
                                            'label' => 'دفع',
                                        ],
                                        'payment_failed' => [
                                            'icon' => 'fas fa-exclamation-triangle',
                                            'class' => 'text-warning',
                                            'label' => 'دفع',
                                        ],
                                        'refund_issued' => [
                                            'icon' => 'fas fa-undo',
                                            'class' => 'text-secondary',
                                            'label' => 'دفع',
                                        ],
                                        'equipment_moved' => [
                                            'icon' => 'fas fa-location-arrow',
                                            'class' => 'text-danger',
                                            'label' => 'GPS',
                                        ],
                                        'system_alert' => [
                                            'icon' => 'fas fa-bell',
                                            'class' => 'text-dark',
                                            'label' => 'تنبيه',
                                        ],
                                    ];

                                    $titles = [
                                        'booking_request' => 'طلب حجز جديد',
                                        'booking_confirmed' => 'تم تأكيد الحجز',
                                        'booking_cancelled' => 'تم إلغاء الحجز',
                                        'new_message' => 'رسالة جديدة',
                                        'payment_received' => 'تم استلام الدفع',
                                        'payment_failed' => 'فشل الدفع',
                                        'refund_issued' => 'تم إصدار استرداد',
                                        'equipment_moved' => 'تحذير حركة',
                                        'system_alert' => 'إشعار',
                                    ];
                                @endphp

                                <div class="dropdownNot">
                                    <svg class="NotificationsIcon" id="userBtnNot" viewBox="0 0 24 24" fill="none"
                                        aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15 17H9c0 1.1.9 2 2 2s2-.9 2-2z" fill="currentColor" />
                                        <path
                                            d="M18 8c0-3.07-1.63-5.64-4.5-6.32V1a1.5 1.5 0 0 0-3 0v.68C7.63 2.36 6 4.92 6 8v5l-1.7 1.7A1 1 0 0 0 5 16h14a1 1 0 0 0 .7-1.7L18 13V8z"
                                            fill="currentColor" />
                                    </svg>

                                    @if ($unreadCount > 0)
                                        <span class="notif-badge" id="notifBadge">{{ $unreadCount }}</span>
                                    @endif

                                    <div class="dropdown-content" id="dropdownMenuNot">
                                        <div class="notif-header d-flex justify-content-between align-items-center">
                                            <span>الإشعارات</span>

                                            <form class="text-decoration-none"
                                                action="{{ route('front.notifications.readall') }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"><i class="fas fa-check-double"></i></button>
                                            </form>
                                        </div>

                                        <ul class="notif-list">
                                            @forelse($latest as $n)
                                                @php
                                                    $kind = $n->data['kind'] ?? 'system_alert';
                                                    $ui = $notifUI[$kind] ?? [
                                                        'icon' => 'fas fa-bell',
                                                        'class' => 'text-dark',
                                                        'label' => 'تنبيه',
                                                    ];

                                                    $title = $n->data['title'] ?? 'إشعار';
                                                    $msg = $n->data['data'] ?? '';
                                                @endphp

                                                <li class="notif-item notif-item-btn {{ $n->read_at ? 'is-read' : 'is-unread' }}"
                                                    data-bs-toggle="modal" data-bs-target="#userNotifModal"
                                                    data-id="{{ $n->id }}" data-kind="{{ $kind }}"
                                                    data-icon="{{ $ui['icon'] }}" data-color="{{ $ui['class'] }}"
                                                    data-label="{{ $ui['label'] }}"
                                                    data-title="{{ $n->data['title'] ?? ($titles[$kind] ?? 'إشعار') }}"
                                                    data-message="{{ $n->data['data'] ?? '' }}"
                                                    data-time="{{ optional($n->created_at)->diffForHumans() }}"
                                                    data-meta='@json($n->data)'>
                                                    <i class="{{ $ui['icon'] }} {{ $ui['class'] }}"></i>
                                                    <span
                                                        class="notif-item-text">{{ $n->data['title'] ?? ($titles[$kind] ?? 'إشعار') }}</span>
                                                </li>
                                            @empty
                                                <li class="notif-item-empty">لا توجد إشعارات</li>
                                            @endforelse
                                        </ul>

                                        <div class="notif-footer">
                                            <a href="{{ route('front.notifications.index') }}" class="show-all">عرض
                                                الكل</a>
                                        </div>
                                    </div>
                                </div>
                            @endauth

                            <div class="lang-switcher">
                                <a href="{{ route('language.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                                    class="lang-btn">
                                    <span>🌐</span>
                                    <span>
                                        {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                                    </span>
                                </a>
                            </div>


                            <!-- قائمة المستخدم -->
                            <div class="dropdown">
                                @php
                                    $user = Auth::user();
                                @endphp

                                <img src="{{ $user ? ($user->profile_picture_url ? asset('storage/' . $user->profile_picture_url) : asset('assets/home/img/guest.png')) : asset('assets/home/img/guest.png') }}"
                                    alt="User" class="user-img" id="userBtn">

                                <div class="dropdown-content" id="dropdownMenu">

                                    @if ($user)
                                        <a href="{{ route('profile.show', $user->id) }}">
                                            <i class="fas fa-user"></i> {{ __('app.profile') }}
                                        </a>
                                        <a href="{{ route('profile.edit', $user->id) }}">
                                            <i class="fas fa-edit"></i> {{ __('app.edit_profile') }}
                                        </a>
                                        <form action="{{ route('logout') }}" method="POST" class="logout-form">
                                            @csrf
                                            <button type="submit" class="dropdown-link">
                                                <i class="fas fa-sign-out-alt"></i> {{ __('app.logout') }}
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}">
                                            <i class="fas fa-sign-in-alt"></i> {{ __('app.login') }}
                                        </a>
                                        <a href="{{ route('register') }}">
                                            <i class="fas fa-user-plus"></i> {{ __('app.register') }}
                                        </a>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <!-- نهاية التعديل -->

                        <div class="nav-overlay search-btn">
                            <a class="uk-navbar-toggle" data-uk-search-icon
                                data-uk-toggle="target: .nav-overlay; animation: uk-animation-fade"
                                href="#"></a>
                        </div>


                        <div class="nav-overlay uk-navbar-left uk-flex-1" hidden>
                            <div class="uk-navbar-item uk-width-expand">
                                <form class="uk-search uk-search-navbar uk-width-1-1"
                                    action="{{ route('equipments') }}" method="GET">
                                    <input class="uk-search-input" type="search" name="query"
                                        placeholder="ابحث عن المعدات..." autofocus>
                                </form>
                            </div>
                            <a class="uk-navbar-toggle" data-uk-close
                                data-uk-toggle="target: .nav-overlay; animation: uk-animation-fade"
                                href="#"></a>
                        </div>
                    </nav>

                    <a class="uk-navbar-toggle uk-hidden@l" href="#offcanvas" data-uk-toggle><span
                            data-uk-icon="menu"></span></a>
                </div>
            </div>
        </header>

        <main class="page-main">
            @yield('content')
        </main>

        <!-- start footer html  -->
        <footer class="page-footer">
            <div class="uk-container uk-container-large">

                <div class="page-footer-middle">
                    <div class="uk-grid uk-child-width-1-4@l uk-child-width-1-2@s" data-uk-grid>

                        {{-- القسم الأول --}}
                        <div class="uk-flex-first@l">
                            <div class="title">{{ __('footer.about_platform') }}</div>
                            <p>
                                {{ __('footer.platform_description') }}
                            </p>
                            <ul class="social-list">
                                <li class="social-list__item"><a class="social-list__link" href="#"><i
                                            class="fab fa-facebook-f"></i></a></li>
                                <li class="social-list__item"><a class="social-list__link" href="#"><i
                                            class="fab fa-twitter"></i></a></li>
                                <li class="social-list__item"><a class="social-list__link" href="#"><i
                                            class="fab fa-google-plus-g"></i></a></li>
                                <li class="social-list__item"><a class="social-list__link" href="#"><i
                                            class="fab fa-linkedin-in"></i></a></li>
                                <li class="social-list__item"><a class="social-list__link" href="#"><i
                                            class="fab fa-vimeo-v"></i></a></li>
                            </ul>
                        </div>

                        {{-- القسم الثاني --}}
                        <div class="uk-flex-last@l">
                            <div class="title">{{ __('footer.contact_info') }}</div>
                            <ul class="contacts-list">
                                <li class="contacts-list-item">
                                    <div class="contacts-list-item__icon">
                                        <img src="{{ asset('assets/home/img/icons/ico-phone24.svg') }}" data-uk-svg
                                            alt="{{ __('footer.support') }}">
                                    </div>
                                    <div class="contacts-list-item__desc">
                                        <div class="contacts-list-item__label">{{ __('footer.support') }}</div>
                                        <div class="contacts-list-item__content">
                                            <a href="tel:+970597234892">+970 59 723 4892</a>
                                        </div>
                                    </div>
                                </li>

                                <li class="contacts-list-item">
                                    <div class="contacts-list-item__icon">
                                        <img src="{{ asset('assets/home/img/icons/ico-timer.svg') }}" data-uk-svg
                                            alt="{{ __('footer.office_hours') }}">
                                    </div>
                                    <div class="contacts-list-item__desc">
                                        <div class="contacts-list-item__label">{{ __('footer.office_hours') }}</div>
                                        <div class="contacts-list-item__content">{{ __('footer.office_hours_value') }}
                                        </div>
                                    </div>
                                </li>

                                <li class="contacts-list-item">
                                    <div class="contacts-list-item__icon">
                                        <img src="{{ asset('assets/home/img/icons/ico-mail.svg') }}" data-uk-svg
                                            alt="{{ __('footer.email_us') }}">
                                    </div>
                                    <div class="contacts-list-item__desc">
                                        <div class="contacts-list-item__label">{{ __('footer.email_us') }}</div>
                                        <div class="contacts-list-item__content">
                                            <a href="mailto:rentals@my-domain.net">rentals@my-domain.net</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        {{-- القسم الثالث --}}
                        <div>
                            <div class="title">{{ __('footer.useful_links') }}</div>
                            <ul class="uk-nav uk-list-disc">
                                <li><a href="{{ route('home') }}">{{ __('footer.home') }}</a></li>
                                <li><a href="{{ route('categories') }}">{{ __('footer.categories') }}</a></li>
                                <li><a href="{{ route('equipments') }}">{{ __('footer.equipments') }}</a></li>
                                <li><a href="{{ route('contact') }}">{{ __('footer.contact') }}</a></li>
                                <li><a href="{{ route('about') }}">{{ __('footer.about') }}</a></li>
                            </ul>
                        </div>

                        {{-- القسم الرابع --}}
                        <div>
                            <div class="title">{{ __('footer.discover_platform') }}</div>
                            <ul class="uk-nav uk-list-disc">
                                <li><a href="{{ route('register') }}">{{ __('footer.register') }}</a></li>
                                <li><a href="{{ route('login') }}">{{ __('footer.login') }}</a></li>
                                <li><a href="{{ route('faq') }}">{{ __('footer.faq') }}</a></li>
                            </ul>
                        </div>

                    </div>
                </div>

                {{-- الفوتر السفلي --}}
                <div class="page-footer-bottom">
                    <span>{{ __('footer.copyright', ['year' => '2025']) }}</span>
                </div>

                <a class="totop-link" href="#top" data-uk-scroll>
                    <img src="{{ asset('assets/home/img/icons/ico-totop.svg') }}" alt="totop">
                    <span>{{ __('footer.back_to_top') }}</span>
                </a>
            </div>

            {{-- القائمة الجانبية للموبايل --}}
            <div id="offcanvas" data-uk-offcanvas="overlay: true">
                <div class="uk-offcanvas-bar">
                    <button class="uk-offcanvas-close" type="button" data-uk-close></button>

                    <div class="uk-margin">
                        <div class="logo">
                            <a class="logo__link" href="{{ route('home') }}">
                                <img class="logo__img" src="{{ asset('assets/img/logo.png') }}" width="90"
                                    height="90" alt="logo">
                            </a>
                        </div>
                    </div>

                    <div class="uk-margin">
                        <ul class="uk-nav-default uk-nav-parent-icon" data-uk-nav>
                            <li><a href="{{ route('home') }}">{{ __('footer.home') }}</a></li>
                            <li><a href="{{ route('categories') }}">{{ __('footer.categories') }}</a></li>
                            <li><a href="{{ route('equipments') }}">{{ __('footer.equipments') }}</a></li>
                            <li><a href="{{ route('about') }}">{{ __('footer.about') }}</a></li>
                            <li><a href="{{ route('contact') }}">{{ __('footer.contact') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end footer html  -->
    </div>

    {{-- User Notifications Modal (Front) --}}
    <div class="modal fade" id="userNotifModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg overflow-hidden" dir="rtl"
                style="border-radius: 22px; background: #fff;">

                {{-- Header --}}
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div class="d-flex align-items-start gap-3 w-100">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width: 58px; height: 58px; background: linear-gradient(135deg, #f8f9fa, #eef2f7); box-shadow: inset 0 1px 0 rgba(255,255,255,.8);">
                            <i id="userNotifModalIcon" class="fas fa-bell text-dark" style="font-size: 22px;"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h5 class="modal-title mb-0 fw-bold" id="userNotifModalTitle"
                                    style="font-size: 1.2rem; color: #1f2937;">
                                    إشعار
                                </h5>

                                <span id="userNotifModalLabel" class="badge rounded-pill px-3 py-2 border"
                                    style="background: #f8f9fa; color: #374151; font-size: .78rem;">
                                </span>
                            </div>

                            <small id="userNotifModalTime" class="d-block" style="color:#6b7280;"></small>
                        </div>

                        <button type="button" class="btn-close ms-0 me-auto shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="px-4 mt-3">
                    <div style="height:1px; background: linear-gradient(to left, transparent, #e5e7eb, transparent);">
                    </div>
                </div>

                {{-- Body --}}
                <div class="modal-body px-4 py-4">

                    <div id="userNotifModalMessage" class="mb-4"
                        style="font-size: 1.05rem; line-height: 2; color: #374151; font-weight: 500;">
                    </div>

                    <div id="userNotifExtraWrap" class="border-0 rounded-4 p-3 mb-3"
                        style="display:none; background: #f8fafc; box-shadow: inset 0 0 0 1px #e5e7eb;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 32px; height: 32px; background: #eef2ff;">
                                <i class="fas fa-info-circle" style="color:#4f46e5;"></i>
                            </div>
                            <strong class="small" style="color:#111827;">تفاصيل الإشعار</strong>
                        </div>

                        <div id="userNotifModalMeta" class="small" style="color:#6b7280; line-height:1.9;"></div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="small fw-semibold" style="color:#6b7280;">نوع الإشعار:</span>
                        <code id="userNotifModalKind" class="px-3 py-2 rounded-pill"
                            style="background:#f3f4f6; color:#be123c; font-size:.82rem;">
                            system_alert
                        </code>
                    </div>
                </div>

                {{-- Footer --}}
                <div
                    class="modal-footer border-0 px-4 pb-4 pt-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <button type="button" class="btn px-4 py-2 rounded-pill" data-bs-dismiss="modal"
                        style="background:#6b7280; color:#fff; border:none; min-width:110px;">
                        <i class="fas fa-times ms-1"></i>
                        إغلاق
                    </button>

                    <div class="d-flex align-items-center gap-2">
                        {{-- حذف --}}
                        <form id="userDeleteNotifForm" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn px-3 py-2 rounded-pill" title="حذف"
                                style="border:1px solid #fecaca; background:#fff1f2; color:#dc2626; min-width:50px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>

                        {{-- تعليم كمقروء --}}
                        <form id="userMarkAsReadForm" method="POST" class="m-0">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn px-3 py-2 rounded-pill" title="تعليم كمقروء"
                                style="border:1px solid #bfdbfe; background:#eff6ff; color:#2563eb; min-width:50px;">
                                <i class="fas fa-check-double"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/home/js/libs.js') }}"></script>
    <script src="{{ asset('assets/home/js/main.js') }}"></script>

    {{-- <script>
        // كود تشغيل القوائم المنسدلة عند النقر
        document.addEventListener('click', function(event) {
            const userBtn = document.getElementById('userBtn');
            const userMenu = document.getElementById('dropdownMenu');
            const notifBtn = document.getElementById('userBtnNot');
            const notifMenu = document.getElementById('dropdownMenuNot');

            // فتح/إغلاق قائمة المستخدم
            if (userBtn && userBtn.contains(event.target)) {
                userMenu.classList.toggle('show');
                if (notifMenu) notifMenu.classList.remove('show');
            }
            // فتح/إغلاق قائمة الإشعارات
            else if (notifBtn && notifBtn.contains(event.target)) {
                notifMenu.classList.toggle('show');
                userMenu.classList.remove('show');
            }
            // إغلاق القوائم عند النقر في أي مكان آخر
            else {
                if (userMenu) userMenu.classList.remove('show');
                if (notifMenu) notifMenu.classList.remove('show');
            }
        });
    </script>

    {{-- لفتح واغلاق المودال --}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('userBtnNot');
            const menu = document.getElementById('dropdownMenuNot');

            if (!btn || !menu) return;

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                menu.classList.toggle('show');
            });

            document.addEventListener('click', () => {
                menu.classList.remove('show');
            });
        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userBtn = document.getElementById('userBtn');
            const userMenu = document.getElementById('dropdownMenu');
            const notifBtn = document.getElementById('userBtnNot');
            const notifMenu = document.getElementById('dropdownMenuNot');

            document.addEventListener('click', function(event) {
                const clickedUserBtn = userBtn && userBtn.contains(event.target);
                const clickedUserMenu = userMenu && userMenu.contains(event.target);
                const clickedNotifBtn = notifBtn && notifBtn.contains(event.target);
                const clickedNotifMenu = notifMenu && notifMenu.contains(event.target);

                if (clickedUserBtn) {
                    userMenu?.classList.toggle('show');
                    notifMenu?.classList.remove('show');
                    return;
                }

                if (clickedNotifBtn) {
                    notifMenu?.classList.toggle('show');
                    userMenu?.classList.remove('show');
                    return;
                }

                if (!clickedUserMenu) {
                    userMenu?.classList.remove('show');
                }

                if (!clickedNotifMenu) {
                    notifMenu?.classList.remove('show');
                }
            });
        });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('userNotifModal');
            if (!modal) return;

            const iconEl = document.getElementById('userNotifModalIcon');
            const titleEl = document.getElementById('userNotifModalTitle');
            const labelEl = document.getElementById('userNotifModalLabel');
            const msgEl = document.getElementById('userNotifModalMessage');
            const timeEl = document.getElementById('userNotifModalTime');
            const kindEl = document.getElementById('userNotifModalKind');

            const extraWrap = document.getElementById('userNotifExtraWrap');
            const metaEl = document.getElementById('userNotifModalMeta');

            const markForm = document.getElementById('userMarkAsReadForm');
            const delForm = document.getElementById('userDeleteNotifForm');

            modal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const id = btn.getAttribute('data-id');
                const kind = btn.getAttribute('data-kind') || 'system_alert';
                const icon = btn.getAttribute('data-icon') || 'fas fa-bell';
                const color = btn.getAttribute('data-color') || 'text-dark';
                const label = btn.getAttribute('data-label') || '';
                const title = btn.getAttribute('data-title') || 'إشعار';
                const message = btn.getAttribute('data-message') || '';
                const time = btn.getAttribute('data-time') || '';
                const meta = JSON.parse(btn.getAttribute('data-meta') || '{}');

                iconEl.className = `${icon} ${color}`;
                titleEl.textContent = title;
                labelEl.textContent = label;
                labelEl.style.display = label ? '' : 'none';

                msgEl.textContent = message;
                timeEl.textContent = time;
                kindEl.textContent = kind;

                //  روات القراءة والحذف (نفس اللي عندك بالأدمن)
                markForm.action = `{{ url('notifications') }}/${id}/read`;
                delForm.action = `{{ url('notifications') }}/${id}`;

                // تفاصيل إضافية
                const lines = [];
                if (meta.booking_id) lines.push(`رقم الحجز: ${meta.booking_id}`);
                if (meta.equipment_id) lines.push(`رقم المعدة: ${meta.equipment_id}`);
                if (meta.conversation_id) lines.push(`رقم المحادثة: ${meta.conversation_id}`);
                if (meta.distance_km) lines.push(`المسافة: ${Number(meta.distance_km).toFixed(3)} كم`);
                // if (meta.speed) lines.push(`السرعة: ${meta.speed}`);
                // if (meta.battery_level) lines.push(`البطارية: ${meta.battery_level}%`);
                if (meta.lat && meta.lng) lines.push(`الموقع: (${meta.lat}, ${meta.lng})`);

                if (lines.length) {
                    metaEl.innerHTML = '<ul class="mb-0 ps-3">' + lines.map(x => `<li>${x}</li>`).join('') +
                        '</ul>';
                    extraWrap.style.display = '';
                } else {
                    metaEl.innerHTML = '';
                    extraWrap.style.display = 'none';
                }
            });

            //  إغلاق dropdown custom تبعك قبل فتح المودال
            document.addEventListener('click', function(e) {
                const item = e.target.closest('.notif-item-btn');
                if (!item) return;

                const menu = document.getElementById('dropdownMenuNot');
                if (menu) menu.classList.remove('show');
            });
        });
    </script> --}}


    {{-- مودال اليوزر العادي
 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('userNotifModal');
            if (!modal) return;

            const iconEl = document.getElementById('userNotifModalIcon');
            const titleEl = document.getElementById('userNotifModalTitle');
            const labelEl = document.getElementById('userNotifModalLabel');
            const msgEl = document.getElementById('userNotifModalMessage');
            const timeEl = document.getElementById('userNotifModalTime');
            const kindEl = document.getElementById('userNotifModalKind');

            const extraWrap = document.getElementById('userNotifExtraWrap');
            const metaEl = document.getElementById('userNotifModalMeta');

            const markForm = document.getElementById('userMarkAsReadForm');
            const delForm = document.getElementById('userDeleteNotifForm');

            modal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const id = btn.getAttribute('data-id');
                const kind = btn.getAttribute('data-kind') || 'system_alert';
                const icon = btn.getAttribute('data-icon') || 'fas fa-bell';
                const color = btn.getAttribute('data-color') || 'text-dark';
                const label = btn.getAttribute('data-label') || '';
                const title = btn.getAttribute('data-title') || 'إشعار';
                const message = btn.getAttribute('data-message') || '';
                const time = btn.getAttribute('data-time') || '';
                const meta = JSON.parse(btn.getAttribute('data-meta') || '{}');

                iconEl.className = `${icon} ${color}`;
                titleEl.textContent = title;
                labelEl.textContent = label;
                labelEl.style.display = label ? '' : 'none';

                msgEl.textContent = message;
                timeEl.textContent = time;
                kindEl.textContent = kind;

                markForm.action = `{{ url('my-notifications') }}/${id}/read`;
                delForm.action = `{{ url('my-notifications') }}/${id}`;

                const lines = [];

                if (meta.booking_id) lines.push(`رقم الحجز: ${meta.booking_id}`);
                if (meta.equipment_id) lines.push(`رقم المعدة: ${meta.equipment_id}`);
                if (meta.conversation_id) lines.push(`رقم المحادثة: ${meta.conversation_id}`);
                if (meta.amount) lines.push(`المبلغ: ${meta.amount}`);
                if (meta.reason) lines.push(`السبب: ${meta.reason}`);
                if (meta.distance_km) lines.push(`المسافة: ${Number(meta.distance_km).toFixed(3)} كم`);
                if (meta.lat && meta.lng) lines.push(`الموقع: (${meta.lat}, ${meta.lng})`);
                if (meta.login_at) lines.push(`وقت تسجيل الدخول: ${meta.login_at}`);
                if (meta.registered_at) lines.push(`وقت إنشاء الحساب: ${meta.registered_at}`);
                if (meta.start_date) lines.push(`موعد البداية: ${meta.start_date}`);

                if (lines.length) {
                    metaEl.innerHTML = '<ul class="mb-0 ps-3">' + lines.map(x => `<li>${x}</li>`).join('') +
                        '</ul>';
                    extraWrap.style.display = '';
                } else {
                    metaEl.innerHTML = '';
                    extraWrap.style.display = 'none';
                }

                const notifMenu = document.getElementById('dropdownMenuNot');
                if (notifMenu) notifMenu.classList.remove('show');
            });
        });
    </script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('userNotifModal');
            if (!modal) return;

            const iconEl = document.getElementById('userNotifModalIcon');
            const titleEl = document.getElementById('userNotifModalTitle');
            const labelEl = document.getElementById('userNotifModalLabel');
            const msgEl = document.getElementById('userNotifModalMessage');
            const timeEl = document.getElementById('userNotifModalTime');
            const kindEl = document.getElementById('userNotifModalKind');

            const extraWrap = document.getElementById('userNotifExtraWrap');
            const metaEl = document.getElementById('userNotifModalMeta');

            const markForm = document.getElementById('userMarkAsReadForm');
            const delForm = document.getElementById('userDeleteNotifForm');

            modal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;

                const id = btn.getAttribute('data-id');
                const kind = btn.getAttribute('data-kind') || 'system_alert';
                const icon = btn.getAttribute('data-icon') || 'fas fa-bell';
                const color = btn.getAttribute('data-color') || 'text-dark';
                const label = btn.getAttribute('data-label') || '';
                const title = btn.getAttribute('data-title') || 'إشعار';
                const message = btn.getAttribute('data-message') || '';
                const time = btn.getAttribute('data-time') || '';
                const meta = JSON.parse(btn.getAttribute('data-meta') || '{}');

                iconEl.className = `${icon} ${color}`;
                titleEl.textContent = title;
                labelEl.textContent = label;
                labelEl.style.display = label ? '' : 'none';

                msgEl.textContent = message;
                timeEl.textContent = time;
                kindEl.textContent = kind;

                //  روات Front
                markForm.action = `{{ url('my-notifications') }}/${id}/read`;
                delForm.action = `{{ url('my-notifications') }}/${id}`;

                const lines = [];
                if (meta.booking_id) lines.push(`رقم الحجز: ${meta.booking_id}`);
                if (meta.equipment_id) lines.push(`رقم المعدة: ${meta.equipment_id}`);
                if (meta.conversation_id) lines.push(`رقم المحادثة: ${meta.conversation_id}`);
                if (meta.distance_km) lines.push(`المسافة: ${Number(meta.distance_km).toFixed(3)} كم`);
                // if (meta.speed) lines.push(`السرعة: ${meta.speed}`);
                // if (meta.battery_level) lines.push(`البطارية: ${meta.battery_level}%`);
                if (meta.lat && meta.lng) lines.push(`الموقع: (${meta.lat}, ${meta.lng})`);

                if (lines.length) {
                    metaEl.innerHTML = '<ul class="mb-0 ps-3">' + lines.map(x => `<li>${x}</li>`).join('') +
                        '</ul>';
                    extraWrap.style.display = '';
                } else {
                    metaEl.innerHTML = '';
                    extraWrap.style.display = 'none';
                }
            });
        });
    </script> --}}

    @stack('scripts')
</body>

</html>
