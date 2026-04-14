@extends('layouts.master')

@section('title', 'الملف الشخصي')
@push('styles')
 <link rel="stylesheet" href="{{ asset('assets/home/css/style.css') }}">
@endpush

@section('content')
    <!-- start user img section  -->
    <div class="wrap">
        <section class="hero" role="banner" aria-label="Hero">
            <div class="hero-inner">
                <div class="photo-wrap" aria-hidden="false">
                    <div class="photo" role="img" aria-label="صورة شخصية">
                        @php
                            // المستخدم الحالي أو مستخدم وهمي إذا لم يكن مسجل دخول
                            $user =
                                Auth::user() ??
                                (object) [
                                    'id' => 0,
                                    'first_name' => 'زائر',
                                    'last_name' => '',
                                    'profile_picture_url' => null,
                                    'description' => 'لم تقم بتسجيل الدخول بعد. قم بتسجيل الدخول لتخصيص ملفك الشخصي.',
                                ];

                            // تحديد الصورة
                            $userImage =
                                $user->id !== 0
                                    ? ($user->profile_picture_url
                                        ? asset('storage/' . $user->profile_picture_url)
                                        : asset('assets/home/img/guest.png'))
                                    : asset('assets/home/img/guest.png'); //  صورة افتراضية عند عدم تسجيل الدخول
                        @endphp

                        <img src="{{ $userImage }}" alt="الصورة الشخصية">
                    </div>

                    <div class="ring" aria-hidden="true"></div>
                    <div class="pulse" aria-hidden="true"></div>
                </div>

                <!-- عمود النص -->
                <div class="text">
                    <h1 class="title name">
                        مرحبًا
                        <span class="highlight">{{ $user->first_name . ' ' . $user->last_name }}</span>
                    </h1>

                    <p class="desc">
                        {{ $user->description ?? 'لم تقم بإضافة وصف بعد.' }}
                    </p>

                    <div class="actions">
                        <a class="btn btn--primary" href="{{ $user->id ? route('profile.edit', $user->id) : '#' }}"
                            title="تعديل الملف الشخصي">
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
        <a href="#" class="stat-box" onclick="loadPage('{{ route('profile.section', 'equipments') }}')">
            <div class="stat-icon"><i class="fas fa-truck"></i></div>
            <div class="stat-title">معداتي</div>
            <div class="stat-number">{{ $equipmentsCount ?? 0 }}</div>
        </a>

        <a href="#" class="stat-box" onclick="loadPage('{{ route('profile.section', 'bookings') }}')">
            <div class="stat-icon"><i class="fas fa-handshake"></i></div>
            <div class="stat-title">الحجوزات</div>
            <div class="stat-number">{{ $bookingsCount ?? 0 }}</div>
        </a>

        <a href="#" class="stat-box" onclick="loadPage('{{ route('profile.section', 'favorites') }}')">
            <div class="stat-icon"><i class="fas fa-heart"></i></div>
            <div class="stat-title">المفضلة</div>
            <div class="stat-number">{{ $favoritesCount ?? 0 }}</div>
        </a>

        <a href="#" class="stat-box" onclick="loadPage('{{ route('profile.section', 'invoices') }}')">
            <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
            <div class="stat-title">فواتيري</div>
            <div class="stat-number">{{ $invoicesCount ?? 0 }}</div>
        </a>

        <a href="#" class="stat-box" onclick="loadPage('{{ route('profile.section', 'rented') }}')">
            <div class="stat-icon"><i class="fa-solid fa-screwdriver-wrench"></i></div>
            <div class="stat-title">معدات استأجرتها</div>
            <div class="stat-number">{{ $rentedCount ?? 0 }}</div>
        </a>
    </section>

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

    <div id="contentArea" style="margin: 70px  30px;">
        <!-- في هذا القسم يتم اضافة الجداول حسب الزر الذي تم الضغط عليه  -->
    </div>
    <!-- end user info section  -->
@endsection
