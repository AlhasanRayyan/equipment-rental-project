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
            /* تظهر عند وجود إشعارات */
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

        .notif-item-empty {
            padding: 20px;
            text-align: center;
            color: #888;
            display: none;
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
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 50%;
            object-fit: cover;
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
.dropdown-content {
    display: none;
    position: absolute;
    top: 100%;    /* لجعلها تبدأ فوراً بعد نهاية الهيدر */
    right: 0;      /* لمحاذاتها مع طرف الجرس من اليمين */
    left: auto;
    background-color: #fff;
    min-width: 300px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 9999; /* لضمان ظهورها فوق السلايدر والمحتوى */
    border-radius: 8px;
    margin-top: 12px; /* مسافة بسيطة تحت الجرس لتبدو أنيقة */
}
        .dropdownNot {
            position: relative;
            /* هذا هو "المرساة" التي ستثبت القائمة */
            display: inline-flex;
            align-items: center;
        }
        .dropdown-content {
    display: none;
    position: absolute;
    top: 100%;    /* لجعلها تبدأ فوراً بعد نهاية الهيدر */
    right: 0;      /* لمحاذاتها مع طرف الجرس من اليمين */
    left: auto;
    background-color: #fff;
    min-width: 300px;
    box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
    z-index: 9999; /* لضمان ظهورها فوق السلايدر والمحتوى */
    border-radius: 8px;
    margin-top: 12px; /* مسافة بسيطة تحت الجرس لتبدو أنيقة */
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
                                <li class="{{ request()->routeIs('home') ? 'uk-active' : '' }}"><a
                                        href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                                <li class="{{ request()->routeIs('categories') ? 'uk-active' : '' }}"><a
                                        href="{{ route('categories') }}">الفئات</a></li>
                                <li class="{{ request()->routeIs('equipments*') ? 'uk-active' : '' }}"><a
                                        href="{{ route('equipments') }}">المعدات</a></li>
                                <li class="{{ request()->routeIs('tracking.*') ? 'uk-active' : '' }}"><a
                                        href="{{ route('tracking.index') }}">تتبع معداتي</a></li>
                                <li class="{{ request()->routeIs('about') ? 'uk-active' : '' }}"><a
                                        href="{{ route('about') }}">عن المنصة</a></li>
                                <li class="{{ request()->routeIs('contact') ? 'uk-active' : '' }}"><a
                                        href="{{ route('contact') }}">تواصل معنا</a></li>
                            </ul>
                        </div>

                        <!-- بداية التعديل: استبدال login-link بالكود الجديد -->
                        <div class="user-actions-wrapper">
                            @auth
                                @php
                                    $unreadCount = auth()->user()->unreadNotifications()->count();
                                    $latest = auth()->user()->notifications()->latest()->take(5)->get();
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

                            <!-- قائمة المستخدم -->
                            <div class="dropdown">
                                @php
                                    $user = Auth::user();
                                @endphp

                                <img src="{{ $user ? ($user->profile_picture_url ? asset('storage/' . $user->profile_picture_url) : asset('assets/home/img/admin.jpeg')) : asset('assets/home/img/guest.png') }}"
                                    alt="User" class="user-img" id="userBtn">

                                <div class="dropdown-content" id="dropdownMenu">
                                    @if ($user)
                                        <a href="{{ route('profile.show', $user->id) }}"><i class="fas fa-user"></i>
                                            الملف الشخصي</a>
                                        <a href="{{ route('profile.edit', $user->id) }}"><i class="fas fa-edit"></i>
                                            تحديث البيانات</a>
                                        <form action="{{ route('logout') }}" method="POST" class="logout-form">
                                            @csrf
                                            <button type="submit" class="dropdown-link">
                                                <i class="fas fa-sign-out-alt"></i> تسجيل خروج
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> تسجيل
                                            دخول</a>
                                        <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> إنشاء
                                            حساب</a>
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

        <footer class="page-footer">
            <div class="uk-container uk-container-large">
                <div class="page-footer-middle">
                    <div class="uk-grid uk-child-width-1-4@l uk-child-width-1-2@s" data-uk-grid>
                        <div class="uk-flex-first@l">
                            <div class="title">عن المنصة</div>
                            <p>{{ $siteDescription ?? 'وصف المنصة هنا' }}</p>
                            <ul class="social-list">
                                <li class="social-list__item"><a class="social-list__link" href="#!"><i
                                            class="fab fa-facebook-f"></i></a></li>
                                <li class="social-list__item"><a class="social-list__link" href="#!"><i
                                            class="fab fa-twitter"></i></a></li>
                            </ul>
                        </div>
                        <div class="uk-flex-last@l">
                            <div class="title">معلومات التواصل</div>
                            <ul class="contacts-list">
                                <li class="contacts-list-item">
                                    <div class="contacts-list-item__icon"><img
                                            src="{{ asset('assets/home/img/icons/ico-phone24.svg') }}" data-uk-svg
                                            alt=""></div>
                                    <div class="contacts-list-item__desc">
                                        <div class="contacts-list-item__label">الدعم الفني</div>
                                        <div class="contacts-list-item__content"> <a
                                                href="tel:{{ $contactPhone ?? '' }}">{{ $contactPhone ?? '' }}</a>
                                        </div>
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
                <div class="page-footer-bottom"><span>(c) 2025 SPCER- تأجير معدات البناء .حقوق النشر والطبع
                        محفوظة</span></div>
                <a class="totop-link" href="#top" data-uk-scroll><img
                        src="{{ asset('assets/home/img/icons/ico-totop.svg') }}" alt="totop"><span>Go to
                        top</span></a>
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
                                        <button type="submit"
                                            style="background:none; border:none; color:rgba(255,255,255,0.7); padding: 5px 20px; cursor:pointer;">تسجيل
                                            خروج</button>
                                    </form>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    {{-- User Notifications Modal (Front) --}}
    <div class="modal fade" id="userNotifModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-end" dir="rtl">

                <div class="modal-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width:44px;height:44px;background:#f1f3f5;">
                            <i id="userNotifModalIcon" class="fas fa-bell text-dark" style="font-size:18px;"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title mb-0" id="userNotifModalTitle">إشعار</h5>
                                <span id="userNotifModalLabel" class="badge bg-light text-dark border"></span>
                            </div>
                            <small class="text-muted d-block" id="userNotifModalTime"></small>
                        </div>
                    </div>

                    <button type="button" class="btn-close ms-0" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div id="userNotifModalMessage" class="mb-3"></div>

                    <div id="userNotifExtraWrap" class="border rounded p-3 bg-light" style="display:none;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-info-circle text-secondary"></i>
                            <strong class="small">تفاصيل</strong>
                        </div>
                        <div id="userNotifModalMeta" class="small text-muted"></div>
                    </div>

                    <div class="mt-3 small text-muted">
                        <span class="me-2">نوع:</span>
                        <code id="userNotifModalKind">system_alert</code>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        {{-- تعليم كمقروء --}}
                        <form id="userMarkAsReadForm" method="POST" class="m-0">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-outline-primary" title="تعليم كمقروء">
                                <i class="fas fa-check-double"></i>
                            </button>
                        </form>

                        {{-- حذف --}}
                        <form id="userDeleteNotifForm" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="إغلاق">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/home/js/libs.js') }}"></script>
    <script src="{{ asset('assets/home/js/main.js') }}"></script>

    <script>
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

    {{-- لفتح واغلاق المودال --}}
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
    </script>
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

                //  روات القراءة والحذف (نفس اللي عندك بالأدمن)
                markForm.action = `{{ url('notifications') }}/${id}/read`;
                delForm.action = `{{ url('notifications') }}/${id}`;

                // تفاصيل إضافية
                const lines = [];
                if (meta.booking_id) lines.push(`رقم الحجز: ${meta.booking_id}`);
                if (meta.equipment_id) lines.push(`رقم المعدة: ${meta.equipment_id}`);
                if (meta.conversation_id) lines.push(`رقم المحادثة: ${meta.conversation_id}`);
                if (meta.distance_km) lines.push(`المسافة: ${Number(meta.distance_km).toFixed(3)} كم`);
                if (meta.speed) lines.push(`السرعة: ${meta.speed}`);
                if (meta.battery_level) lines.push(`البطارية: ${meta.battery_level}%`);
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
    </script>


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

                //  روات Front
                markForm.action = `{{ url('my-notifications') }}/${id}/read`;
                delForm.action = `{{ url('my-notifications') }}/${id}`;

                const lines = [];
                if (meta.booking_id) lines.push(`رقم الحجز: ${meta.booking_id}`);
                if (meta.equipment_id) lines.push(`رقم المعدة: ${meta.equipment_id}`);
                if (meta.conversation_id) lines.push(`رقم المحادثة: ${meta.conversation_id}`);
                if (meta.distance_km) lines.push(`المسافة: ${Number(meta.distance_km).toFixed(3)} كم`);
                if (meta.speed) lines.push(`السرعة: ${meta.speed}`);
                if (meta.battery_level) lines.push(`البطارية: ${meta.battery_level}%`);
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
    </script>

    @stack('scripts')
</body>

</html>
