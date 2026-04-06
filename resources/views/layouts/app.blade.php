<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>لوحة التحكم </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        :root {
            --bs-primary: #4e73df;
            --bs-primary-rgb: 78, 115, 223;
            --bs-success: #1cc88a;
            --bs-info: #36b9cc;
            --bs-warning: #f6c23e;
            --sidebar-bg: #2C3E50;
            --sidebar-link-color: rgba(255, 255, 255, 0.7);
            --sidebar-link-hover: #ffffff;
            --sidebar-link-active: #ffffff;
            --sidebar-bg-active: var(--bs-primary);
            --topbar-bg: #ffffff;
            --body-bg: #f8f9fc;
            --card-bg: #ffffff;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            --text-heading: #3a3b45;
            --text-body: #5a5c69;
            --border-color: #e3e6f0;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--body-bg);
            color: var(--text-body);
        }

        #wrapper {
            display: flex;
        }

        #content-wrapper {
            width: 100%;
            overflow-x: hidden;
        }

        /* === Sidebar Final Fix === */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            transition: all 0.3s ease-in-out;
        }

        .sidebar .sidebar-brand {
            height: 5rem;
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .sidebar .nav-item {
            margin: 0 0.5rem;
        }

        .sidebar .nav-item .nav-link {
            text-align: right;
            padding: 0.85rem 1rem;
            color: var(--sidebar-link-color) !important;
            /* Force color */
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            border-radius: 0.5rem;
        }

        .sidebar .nav-item .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-link-hover) !important;
            /* Force color on hover */
        }

        .sidebar .nav-item.active .nav-link {
            background-color: var(--sidebar-bg-active);
            color: var(--sidebar-link-active) !important;
            /* Force color on active */
            font-weight: 700;
        }

        .sidebar .nav-item .nav-link i {
            margin-left: 0.75rem;
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar .sidebar-heading {
            text-align: right;
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: .05em;
            font-weight: 700;
        }

        .sidebar .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 1030;
                right: -250px;
            }

            .sidebar.toggled {
                right: 0;
            }
        }

        /* Topbar & Cards Unified Style */
        .topbar {
            background-color: var(--topbar-bg);
            box-shadow: var(--card-shadow);
            height: 4.5rem;
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            box-shadow: var(--card-shadow);
        }

        .card-header {
            background-color: #fcfdff;
            border-bottom: 1px solid var(--border-color);
            font-weight: 700;
            color: var(--text-heading);
            padding: 1rem 1.25rem;
        }

        /* Custom KPI cards */
        .kpi-card {
            border: none;
            border-radius: 0.75rem;
            color: #fff;
            position: relative;
            overflow: hidden;
            padding: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .kpi-card .kpi-icon {
            position: absolute;
            left: -20px;
            bottom: -30px;
            font-size: 6rem;
            opacity: 0.15;
            transform: rotate(-15deg);
            transition: all .3s ease;
        }

        .kpi-card:hover .kpi-icon {
            transform: rotate(-10deg) scale(1.1);
            opacity: 0.2;
        }

        .kpi-card .kpi-title {
            font-size: 1rem;
            font-weight: 500;
        }

        .kpi-card .kpi-value {
            font-size: 2.5rem;
            font-weight: 700;
        }

        .badge-counter {
            position: absolute;
            top: 6px;
            right: -5px;
            font-size: 0.6rem;
            border-radius: 50%;
            padding: 3px 6px;
        }

        /* RTL fixes for dropdown + modal */
        .dropdown-menu,
        .modal-content,
        .modal-header,
        .modal-body,
        .modal-footer {
            direction: rtl;
            text-align: right;
        }

        /* dropdown items spacing */
        .dropdown-item {
            white-space: normal;
            line-height: 1.3;
        }

        /* better dropdown width + scroll */
        .notif-dropdown {
            min-width: 360px;
            max-width: 420px;
            max-height: 360px;
            overflow-y: auto;
        }

        /* Modal close button position for RTL */
        .modal-header .btn-close {
            margin: 0;
        }

        /* nicer buttons order */
        .modal-footer {
            justify-content: flex-start;
            gap: .5rem;
        }
    </style>
    @yield('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        @include('layouts.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
                        <i class="fa fa-bars text-primary"></i>
                    </button>
                    <ul class="navbar-nav ms-auto">

                        <li class="nav-item dropdown me-3 d-flex align-items-center">
                            <a class="nav-link dropdown-toggle position-relative d-flex align-items-center"
                                href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-bell fa-lg text-gray-600"></i>

                                @if (($unreadCount ?? 0) > 0)
                                    <span class="badge bg-danger badge-counter">{{ $unreadCount }}</span>
                                @endif
                            </a>

                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in notif-dropdown"
                                aria-labelledby="notifDropdown" style="min-width: 340px;">

                                <div class="px-3 py-2 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>الإشعارات</strong>

                                        {{--  قراءة الكل أيقونة --}}
                                        <a href="{{ route('admin.notifications.readall') }}"
                                            class="btn btn-sm btn-outline-primary" title="تعليم الكل كمقروء">
                                            <i class="fas fa-check-double"></i>
                                        </a>
                                    </div>
                                </div>

                                @forelse(($latestNotifications ?? collect()) as $n)
                                    @php
                                        $kind = $notification->data['kind'] ?? 'system_alert';
                                        $ui = $notifUI[$kind] ?? ['icon' => 'fas fa-bell', 'class' => 'text-dark'];

                                        $meta = [
                                            'booking_id' => $notification->data['booking_id'] ?? null,
                                            'equipment_id' => $notification->data['equipment_id'] ?? null,
                                            'conversation_id' => $notification->data['conversation_id'] ?? null,
                                            'message_id' => $notification->data['message_id'] ?? null,
                                            'owner_id' => $notification->data['owner_id'] ?? null,
                                            'renter_id' => $notification->data['renter_id'] ?? null,
                                            'lat' => $notification->data['lat'] ?? null,
                                            'lng' => $notification->data['lng'] ?? null,
                                            'distance_km' => $notification->data['distance_km'] ?? null,
                                            'amount' => $notification->data['amount'] ?? null,
                                            'reason' => $notification->data['reason'] ?? null,
                                            'start_date' => $notification->data['start_date'] ?? null,
                                            'login_at' => $notification->data['login_at'] ?? null,
                                            'registered_at' => $notification->data['registered_at'] ?? null,
                                        ];
                                    @endphp

                                    <a href="#"
                                        class="dropdown-item py-2 {{ $n->read_at ? 'text-muted' : 'fw-bold' }} notif-item"
                                        data-bs-toggle="modal" data-bs-target="#notifModal"
                                        data-id="{{ $n->id }}" data-kind="{{ $kind }}"
                                        data-icon="{{ $ui['icon'] }}" data-color="{{ $ui['class'] }}"
                                        data-label="{{ $ui['label'] }}"
                                        data-title="{{ $n->data['title'] ?? ($titles[$kind] ?? $ui['label']) }}"
                                        data-message="{{ $n->data['data'] ?? '' }}"
                                        data-time="{{ optional($n->created_at)->diffForHumans() }}"
                                        data-meta='@json($meta)'>
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="{{ $ui['icon'] }} {{ $ui['class'] }} mt-1"></i>
                                            <div class="text-end">
                                                <div class="small">
                                                    {{ $titles[$kind] ?? ($n->data['title'] ?? 'إشعار') }}
                                                </div>
                                                <small
                                                    class="text-muted d-block">{{ optional($n->created_at)->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="dropdown-divider my-0"></div>

                                @empty
                                    <div class="dropdown-item text-muted py-3">لا يوجد إشعارات</div>
                                @endforelse

                                <a class="dropdown-item text-center small text-primary py-2"
                                    href="{{ route('admin.notifications.index') }}">
                                    عرض كل الإشعارات
                                </a>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                <span
                                    class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</span>
                                <img class="img-profile rounded-circle"
                                    src="https://placehold.co/60x60/4e73df/ffffff?text={{ substr(Auth::user()->first_name . ' ' . Auth::user()->last_name, 0, 1) }}">
                                {{-- <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name={{Auth::user()->first_name . ' ' . Auth::user()->last_name}}"> --}}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in text-end"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw ms-2 text-gray-400"></i>
                                    تسجيل الخروج
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf</form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <main class="container-fluid">
                    @yield('content')
                </main>
            </div>
            <footer class="sticky-footer bg-white mt-auto py-3">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>حقوق النشر محفوظة لدى &copy; BIT OF HOPE TEAM {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        (function() {
            "use strict";
            var sidebarToggle = document.querySelector("#sidebarToggleTop");
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    document.querySelector(".sidebar").classList.toggle("toggled");
                });
            }
        })();
    </script>
    <script>
        window.routes = {
            bookingConfirm: @json(route('admin.bookings.confirm', ['booking' => '__ID__'])),
            bookingCancel: @json(route('admin.bookings.cancel', ['booking' => '__ID__'])),
        };
    </script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('notifModal');

            const iconEl = document.getElementById('notifModalIcon');
            const titleEl = document.getElementById('notifModalTitle');
            const labelEl = document.getElementById('notifModalLabel');
            const msgEl = document.getElementById('notifModalMessage');
            const timeEl = document.getElementById('notifModalTime');
            const kindEl = document.getElementById('notifModalKind');

            const extraWrap = document.getElementById('notifExtraWrap');
            const metaEl = document.getElementById('notifModalMeta');

            const markForm = document.getElementById('markAsReadForm');
            const delForm = document.getElementById('deleteNotifForm');
            const confirmForm = document.getElementById('bookingConfirmForm');
            const cancelForm = document.getElementById('bookingCancelForm');

            modal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;

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

                msgEl.textContent = message;
                timeEl.textContent = time;
                kindEl.textContent = kind;

                // routes: read/delete
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
                    metaEl.innerHTML = '<ul class="mb-0">' + lines.map(x => `<li>${x}</li>`).join('') +
                        '</ul>';
                    extraWrap.style.display = '';
                } else {
                    metaEl.innerHTML = '';
                    extraWrap.style.display = 'none';
                }

                // booking actions فقط لطلب الحجز
                if (kind === 'booking_request' && meta.booking_id) {
                    confirmForm.classList.remove('d-none');
                    cancelForm.classList.remove('d-none');

                    // confirmForm.action = `{{ url('admin/bookings') }}/${meta.booking_id}/confirm`;
                    // cancelForm.action = `{{ url('admin/bookings') }}/${meta.booking_id}/cancel`;
                    confirmForm.action = window.routes.bookingConfirm.replace('__ID__', meta.booking_id);
                    cancelForm.action = window.routes.bookingCancel.replace('__ID__', meta.booking_id);
                } else {
                    confirmForm.classList.add('d-none');
                    cancelForm.classList.add('d-none');
                    confirmForm.action = '';
                    cancelForm.action = '';
                }
            });

            // سكّر dropdown قبل فتح المودال
            document.addEventListener('click', function(e) {
                const a = e.target.closest('.notif-item');
                if (!a) return;
                const dd = bootstrap.Dropdown.getInstance(document.getElementById('notifDropdown'));
                if (dd) dd.hide();
            });
        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('notifModal');
            if (!modal) return;

            const iconEl = document.getElementById('notifModalIcon');
            const titleEl = document.getElementById('notifModalTitle');
            const labelEl = document.getElementById('notifModalLabel');
            const msgEl = document.getElementById('notifModalMessage');
            const timeEl = document.getElementById('notifModalTime');
            const kindEl = document.getElementById('notifModalKind');

            const extraWrap = document.getElementById('notifExtraWrap');
            const metaEl = document.getElementById('notifModalMeta');

            const markForm = document.getElementById('markAsReadForm');
            const delForm = document.getElementById('deleteNotifForm');
            const confirmForm = document.getElementById('bookingConfirmForm');
            const cancelForm = document.getElementById('bookingCancelForm');

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

                markForm.action = `{{ url('admin/notifications') }}/${id}/read`;
                delForm.action = `{{ url('admin/notifications') }}/${id}`;

                const lines = [];
                if (meta.booking_id) lines.push(`رقم الحجز: ${meta.booking_id}`);
                if (meta.equipment_id) lines.push(`رقم المعدة: ${meta.equipment_id}`);
                if (meta.conversation_id) lines.push(`رقم المحادثة: ${meta.conversation_id}`);
                if (meta.message_id) lines.push(`رقم الرسالة: ${meta.message_id}`);
                if (meta.owner_id) lines.push(`رقم المؤجر: ${meta.owner_id}`);
                if (meta.renter_id) lines.push(`رقم المستأجر: ${meta.renter_id}`);
                if (meta.amount) lines.push(`المبلغ: ${meta.amount}`);
                if (meta.reason) lines.push(`السبب: ${meta.reason}`);
                if (meta.distance_km) lines.push(`المسافة: ${Number(meta.distance_km).toFixed(3)} كم`);
                if (meta.lat && meta.lng) lines.push(`الموقع: (${meta.lat}, ${meta.lng})`);
                if (meta.start_date) lines.push(`موعد البداية: ${meta.start_date}`);
                if (meta.login_at) lines.push(`وقت تسجيل الدخول: ${meta.login_at}`);
                if (meta.registered_at) lines.push(`وقت إنشاء الحساب: ${meta.registered_at}`);

                if (lines.length) {
                    metaEl.innerHTML = '<ul class="mb-0">' + lines.map(x => `<li>${x}</li>`).join('') +
                        '</ul>';
                    extraWrap.style.display = '';
                } else {
                    metaEl.innerHTML = '';
                    extraWrap.style.display = 'none';
                }

                if (kind === 'booking_request' && meta.booking_id) {
                    confirmForm.classList.remove('d-none');
                    cancelForm.classList.remove('d-none');

                    confirmForm.action = window.routes.bookingConfirm.replace('__ID__', meta.booking_id);
                    cancelForm.action = window.routes.bookingCancel.replace('__ID__', meta.booking_id);
                } else {
                    confirmForm.classList.add('d-none');
                    cancelForm.classList.add('d-none');
                    confirmForm.action = '';
                    cancelForm.action = '';
                }
            });

            document.addEventListener('click', function(e) {
                const a = e.target.closest('.notif-item');
                if (!a) return;

                const dd = bootstrap.Dropdown.getInstance(document.getElementById('notifDropdown'));
                if (dd) dd.hide();
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
