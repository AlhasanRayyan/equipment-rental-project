<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>الملف الشخصي</title>
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
    <link rel="stylesheet" href="{{ asset('assets/home/css/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/home/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/home/css/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-rOA1p1+Xg2z0L2sX4Bjk4YxYpR5hx2vPSm9cH1xLZmI+Z7F9yyEJZxZ2LhHj6NkXhY1x3Pq1kQ4xg36Xl6+cw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* nav plus  */
        .user-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .NotificationsIcon {
            height: 25px;
            width: 25px;
            color: #000;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.2s;
            margin-top: 13px;
        }

        .user-img:hover {
            transform: scale(1.1);
        }

        .dropdown,
        .dropdownNot {
            position: relative;
            display: inline-block;
            margin-left: 10px;
            margin-top: 20px;
            margin-bottom: 20px;
            position: relative;
        }

        .notif-badge {
            position: absolute;
            top: 7px;
            right: 1px;
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            z-index: 10;
        }

        .dropdown-content {
            position: absolute;
            top: 55px;
            left: -10px;
            background-color: #fff;
            min-width: 180px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            opacity: 0;
            transform: translateY(-10px);
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
            z-index: 999;
        }

        .dropdown.show .dropdown-content,
        .dropdownNot.show .dropdown-content {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .dropdown-content a {
            display: block;
            padding: 12px 16px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: background-color 0.2s, color 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dropdown-content a:hover {
            background-color: #ffd633;
            color: #000;
        }

        .notif-header {
            background-color: #ffd633;
            color: #000;
            text-align: center;
            font-weight: bold;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .notif-list {
            list-style: none;
            min-width: 280px !important;
            margin: 0;
            padding: 0;
            max-height: 260px;
        }

        .notif-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 14px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s ease;
            overflow: hidden;

        }


        .notif-item i {
            flex-shrink: 0;
            margin-top: 2px;
        }


        .notif-item:hover {
            background-color: #fff9d6;
        }

        .notif-item-empty {
            text-align: center;
            padding: 12px;
            color: #888;
            font-size: 13px;
            display: none;
        }

        .notif-item-text {
            display: -webkit-box;
            /* -webkit-line-clamp: 2; */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
            max-height: calc(1.4em * 2);
            white-space: normal;
        }

        .notif-list:empty+.notif-item-empty {
            display: block;
        }

        .notif-footer {
            border-top: 1px solid #eee;
            text-align: center;
            background-color: #fafafa;
        }

        .title,
        li,
        p {
            text-align: right;
        }

        .show-all {
            display: block;
            padding: 10px;
            color: #000;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .show-all:hover {
            background-color: #ffd633;
        }

        .notif-list::-webkit-scrollbar {
            width: 6px;
        }

        .notif-list::-webkit-scrollbar-thumb {
            background-color: #ffd633;
            border-radius: 3px;
        }

        @media (max-width: 450px) {
            .dropdownNot .dropdown-content {
                right: -150px;
                left: -70px;
            }

            .text .name {
                text-align: center;
            }

        }

        /* end nav plus  */
    </style>
</head>

<body class="page-home">

    <!-- Loader-->
    <div id="page-preloader"><span class="spinner border-t_second_b border-t_prim_a"></span></div>
    <!-- Loader end-->

    <div class="page-wrapper">
        <!-- start header html  -->
        <header class="page-header">
            <div class="page-header-bottom">
                <div class="page-header-bottom__left">
                    <div class="logo"><a class="logo__link" href="home.html"><img class="logo__img"
                                src="{{ asset('assets/home/img/logo.png') }}" alt=""></a></div>

                </div>
                <div class="page-header-bottom__right">
                    <nav class="uk-navbar-container  uk-navbar-transparent" data-uk-navbar>
                        <div class="nav-overlay uk-visible@l  List">

                            <ul class="uk-navbar-nav">
                                <li><a href="home.html">الصفحة الرئيسية</a></li>
                                <li><a href="categories.html">الفئات</a></li>
                                <li><a href="equipments.html">المعدات</a></li>
                                <li><a href="about.html">عن الموقع</a></li>
                                <li><a href="contact.html">تواصل معنا</a></li>
                            </ul>
                        </div>
                        <!-- أيقونة الإشعارات -->
                        <div class="dropdownNot">
                            <svg class="NotificationsIcon" id="userBtnNot" viewBox="0 0 24 24" fill="none"
                                aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15 17H9c0 1.1.9 2 2 2s2-.9 2-2z" fill="currentColor" />
                                <path
                                    d="M18 8c0-3.07-1.63-5.64-4.5-6.32V1a1.5 1.5 0 0 0-3 0v.68C7.63 2.36 6 4.92 6 8v5l-1.7 1.7A1 1 0 0 0 5 16h14a1 1 0 0 0 .7-1.7L18 13V8z"
                                    fill="currentColor" />
                            </svg>
                            <span class="notif-badge" id="notifBadge"></span>

                            <div class="dropdown-content" id="dropdownMenuNot">
                                <div class="notif-header">الإشعارات</div>
                                <ul class="notif-list">
                                    <li class="notif-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span class="notif-item-text">
                                            تم تأكيد حجزك بنجاح تم تأكيد حجزك بنجاح تم تأكيد حجزك بنجاح تم تأكيد حجزك
                                            بنجاح تم تأكيد حجزك
                                            بنجاح
                                        </span>
                                    </li>
                                    <li class="notif-item"><i class="fas fa-clock"></i><span
                                            class="notif-item-text">لديك موعد جديد
                                            غدًا</span></li>
                                    <li class="notif-item"><i class="fas fa-cog"></i><span class="notif-item-text">تم
                                            تحديث بياناتك
                                            الشخصية</span></li>
                                    <li class="notif-item"><i class="fas fa-box"></i><span class="notif-item-text">تم
                                            إرسال الطلب إلى
                                            موقعك</span></li>
                                </ul>
                                <div class="notif-item-empty">لا توجد إشعارات جديدة</div>
                                <div class="notif-footer">
                                    <a href="#" class="show-all">عرض الكل</a>
                                </div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <img src="{{ asset('assets/home/img/admin.jpeg') }}" alt="User" class="user-img"
                                id="userBtn">
                            <div class="dropdown-content" id="dropdownMenu">
                                <a href="userProfile.html"><i class="fas fa-user"></i> الملف الشخصي</a>
                                <a href="updateProfile.html"><i class="fas fa-edit"></i> تحديث البيانات</a>
                                <a href="#"><i class="fas fa-sign-out-alt"></i> تسجيل خروج</a>
                            </div>
                        </div>

                        <div class="nav-overlay search-btn"><a class="uk-navbar-toggle" data-uk-search-icon
                                data-uk-toggle="target: .nav-overlay; animation: uk-animation-fade"
                                href="#"></a></div>
                        <div class="nav-overlay uk-navbar-left uk-flex-1" hidden>
                            <div class="uk-navbar-item uk-width-expand">
                                <form class="uk-search uk-search-navbar uk-width-1-1" action="#!"><input
                                        class="uk-search-input" type="search" placeholder="Search..." autofocus>
                                </form>
                            </div><a class="uk-navbar-toggle" data-uk-close
                                data-uk-toggle="target: .nav-overlay; animation: uk-animation-fade"
                                href="#"></a>
                        </div>
                    </nav>
                    <a class="uk-navbar-toggle uk-hidden@l" href="#offcanvas" data-uk-toggle><span
                            data-uk-icon="menu"></span></a>

                </div>
            </div>
        </header>
        <!-- end header html  -->
        <main class="page-main">

            <!-- start user img section  -->
            <div class="wrap">
                <section class="hero" role="banner" aria-label="Hero">
                    <div class="hero-inner">
                        <div class="photo-wrap" aria-hidden="false">
                            <div class="photo" role="img" aria-label="صورة شخصية">
                                <img src="{{ asset('assets/home/img/admin.jpeg') }}" alt="الصورة الشخصية">
                            </div>

                            <div class="ring" aria-hidden="true"></div>
                            <div class="pulse" aria-hidden="true"></div>
                        </div>

                        <!-- عمود النص -->
                        <div class="text">
                            <h1 class="title name">مرحبًا <span class="highlight">مصطفى الترتوري</span></h1>

                            <p class="desc">
                                أنا مصمم ومطور واجهات تجربة المستخدم (UI/UX) مستقل أقوم ببناء تطبيقات ويب جميلة وغامرة.
                                أعمل على كتابة
                                شفرات مصقولة تُعطي تجربة مستخدم مركزة وعملية.
                            </p>

                            <div class="actions">
                                <a class="btn btn--primary" href="updateProfile.html" title="تعديل الملف الشخصي ">
                                    <!-- أيقونة شخص -->
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path
                                            d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.4c-3.4 0-10.2 1.7-10.2 5.1v1.7H22.2v-1.7c0-3.4-6.8-5.1-10.2-5.1z" />
                                    </svg>
                                    تعديل الملف الشخصي
                                </a>
                            </div>
                        </div>

                    </div>

                    <div class="decor" aria-hidden="true"></div>
                </section>

            </div>
            <!-- end user img section  -->

            <!-- start user info section  -->
            <section class="stats-section">
                <a href="user-equipments.html" class="stat-box">
                    <div class="stat-icon"><i class="fas fa-truck"></i></div>
                    <div class="stat-title">معداتي</div>
                    <div class="stat-number">200</div>
                </a>

                <a class="stat-box" onclick="loadPage('bookings.html')">
                    <div class="stat-icon"><i class="fas fa-handshake"></i></div>
                    <div class="stat-title"> الحجوزات</div>
                    <div class="stat-number">50</div>
                </a>
                <!-- مثال للتوضيح  -->
                <script>
                    function loadPage(url) {
                        fetch(url)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('فشل في تحميل الصفحة');
                                }
                                return response.text();
                            })
                            .then(data => {
                                const content = document.getElementById('contentArea');
                                content.innerHTML = data;
                                setTimeout(() => {
                                    content.scrollIntoView({
                                        behavior: 'smooth'
                                    });
                                }, 100);
                            })
                            .catch(error => {
                                document.getElementById('contentArea').innerHTML = '<p>حدث خطأ أثناء تحميل المحتوى.</p>';
                                console.error(error);
                            });
                    }
                </script>


                <a href="Favorites.html" class="stat-box">
                    <div class="stat-icon"><i class="fas fa-heart"></i></div>
                    <div class="stat-title"> المفضلة</div>
                    <div class="stat-number">320</div>
                </a>

                <a class="stat-box" onclick="loadPage('user-invoices.html')">
                    <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
                    <div class="stat-title"> فواتيري</div>
                    <div class="stat-number">70</div>
                </a>

                <a class="stat-box">
                    <div class="stat-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
                    <div class="stat-title">معدات إستاجرتها</div>
                    <div class="stat-number">30</div>
                </a>
            </section>

            <div id="contentArea" style="margin: 70px  0px;">
                <!-- في هذا القسم يتم اضافة الجداول حسب الزر الذي تم الضغط عليه  -->
            </div>

            <!-- end user info section  -->
        </main>
        <!-- start footer html  -->
        <footer class="page-footer">
            <div class="uk-container uk-container-large">

                <div class="page-footer-middle">
                    <div class="uk-grid uk-child-width-1-4@l uk-child-width-1-2@s" data-uk-grid>
                        <div class="uk-flex-first@l">
                            <div class="title">عن المنصة</div>
                            <p>منصة تتيح للمستخدمين خدمات من تأجير واستئجار معدات البناء بجميع أنواعها وبأسعار مناسبة
                            </p>
                            <ul class="social-list">
                                <li class="social-list__item"><a class="social-list__link" href="#!"><i
                                            class="fab fa-facebook-f"></i></a></li>
                                <li class="social-list__item"><a class="social-list__link" href="#!"><i
                                            class="fab fa-twitter"></i></a>
                                </li>
                                <li class="social-list__item"><a class="social-list__link" href="#!"><i
                                            class="fab fa-google-plus-g"></i></a></li>
                                <li class="social-list__item"><a class="social-list__link" href="#!"><i
                                            class="fab fa-linkedin-in"></i></a></li>
                                <li class="social-list__item"><a class="social-list__link" href="#!"><i
                                            class="fab fa-vimeo-v"></i></a>
                                </li>
                            </ul>
                        </div>
                        <div class="uk-flex-last@l">
                            <div class="title">معلومات التواصل</div>
                            <ul class="contacts-list">
                                <li class="contacts-list-item">
                                    <div class="contacts-list-item__icon"><img
                                            src="{{ asset('assets/home/img/icons/ico-phone24.svg') }}" data-uk-svg
                                            alt="For Rental Support"></div>
                                    <div class="contacts-list-item__desc">
                                        <div class="contacts-list-item__label">الدعم الفني</div>
                                        <div class="contacts-list-item__content"> <a href="tel:12367995500/6600">+970
                                                59 723 4892</a></div>
                                    </div>
                                </li>
                                <li class="contacts-list-item">
                                    <div class="contacts-list-item__icon"><img
                                            src="{{ asset('assets/home/img/icons/ico-timer.svg') }}" data-uk-svg
                                            alt="The Office Hours"></div>
                                    <div class="contacts-list-item__desc">
                                        <div class="contacts-list-item__label">ساعات العمل</div>
                                        <div class="contacts-list-item__content">السبت - الخميس ( 8ص - 6م)</div>
                                    </div>
                                </li>
                                <li class="contacts-list-item">
                                    <div class="contacts-list-item__icon"><img
                                            src="{{ asset('assets/home/img/icons/ico-mail.svg') }}" data-uk-svg
                                            alt="Send Us Email"></div>
                                    <div class="contacts-list-item__desc">
                                        <div class="contacts-list-item__label">راسلنا على الإيميل</div>
                                        <div class="contacts-list-item__content"> <a
                                                href="rentals@my-domain.net">rentals@my-domain.net</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <div class="title">روابط مفيدة</div>
                            <ul class="uk-nav uk-list-disc">
                                <li> <a href="home.html">الصفحة الرئيسية</a></li>
                                <li> <a href="categories.html">الفئات</a></li>
                                <li> <a href="equipments.html">المعدات</a></li>
                                <li> <a href="#">تواصل معنا</a></li>
                                <li> <a href="#">عن المنصة</a></li>

                            </ul>
                        </div>
                        <div>
                            <div class="title">اكتشف المنصة</div>
                            <ul class="uk-nav uk-list-disc">
                                <li><a href="typography.html">إنشاء حساب</a></li>
                                <li><a href="typography.html">تسجيل دخول</a></li>
                                <li><a href="typography.html">اقرأ الأسئلة الشائعة</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="page-footer-bottom"><span>(c) 2025 SPCER- تأجير معدات البناء .حقوق النشر والطبع
                        محفوظة</span></div>
                <a class="totop-link" href="#top" data-uk-scroll><img
                        src="{{ asset('assets/home/img/icons/ico-totop.svg') }}" alt="totop"><span>Go
                        to top</span></a>
            </div>
            <div id="offcanvas" data-uk-offcanvas="overlay: true">
                <div class="uk-offcanvas-bar"><button class="uk-offcanvas-close" type="button"
                        data-uk-close=""></button>
                    <div class="uk-margin">
                        <div class="logo"><a class="logo__link" href="home.html"><img class="logo__img"
                                    src="{{ asset('assets/home/img/logo.png') }}" alt="logo"></a></div>
                    </div>
                    <div class="uk-margin">
                        <ul class="uk-nav-default uk-nav-parent-icon" data-uk-nav>
                            <li class="uk-active"><a href="home.html">الصفحة الرئيسية</a></li>
                            <li><a href="categories.html">الفئات</a>
                            </li>
                            <li><a href="equipments.html">المعدات</a>
                            </li>
                            <li><a href="#">عن الموقع</a></li>
                            <li><a href="#">تواصل معنا</a>
                            </li>
                        </ul>
                    </div>
                    <div class="uk-margin">
                    </div>
                    <div class="uk-margin">
                    </div>
                </div>
            </div>

        </footer>
        <!-- end footer html  -->
    </div>
    <script src="{{ asset('assets/home/js/libs.js') }}"></script>
    <script src="{{ asset('assets/home/js/main.js') }}"></script>
    <script>
        const userBtn = document.getElementById('userBtn');
        const dropdown = document.querySelector('.dropdown');
        const userBtnNot = document.getElementById('userBtnNot');
        const dropdownNot = document.querySelector('.dropdownNot');

        // فتح/إغلاق قائمة المستخدم
        userBtn.addEventListener('click', (e) => {
            dropdown.classList.toggle('show');
            e.stopPropagation();
        });

        // فتح/إغلاق قائمة الإشعارات
        userBtnNot.addEventListener('click', (e) => {
            dropdownNot.classList.toggle('show');
            e.stopPropagation();
        });

        // إغلاق أي قائمة عند الضغط خارجها
        document.addEventListener('click', () => {
            dropdown.classList.remove('show');
            dropdownNot.classList.remove('show');
        });
    </script>
</body>

</html>
