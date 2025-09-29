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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

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

        #wrapper { display: flex; }
        #content-wrapper { width: 100%; overflow-x: hidden; }

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
        .sidebar .nav-item { margin: 0 0.5rem; }
        .sidebar .nav-item .nav-link {
            text-align: right;
            padding: 0.85rem 1rem;
            color: var(--sidebar-link-color) !important; /* Force color */
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            border-radius: 0.5rem;
        }
        .sidebar .nav-item .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--sidebar-link-hover) !important; /* Force color on hover */
        }
        .sidebar .nav-item.active .nav-link {
            background-color: var(--sidebar-bg-active);
            color: var(--sidebar-link-active) !important; /* Force color on active */
            font-weight: 700;
        }
        .sidebar .nav-item .nav-link i { margin-left: 0.75rem; font-size: 1rem; width: 20px; text-align: center; }
        .sidebar .sidebar-heading {
            text-align: right;
            padding: 0.5rem 1.5rem;
            margin-top: 1rem;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: .05em;
            font-weight: 700;
        }
        .sidebar .sidebar-divider { border-top: 1px solid rgba(255, 255, 255, 0.15); }
        
        @media (max-width: 768px) {
            .sidebar { position: fixed; z-index: 1030; right: -250px; }
            .sidebar.toggled { right: 0; }
        }

        /* Topbar & Cards Unified Style */
        .topbar { background-color: var(--topbar-bg); box-shadow: var(--card-shadow); height: 4.5rem; }
        .card { border: 1px solid var(--border-color); border-radius: 0.5rem; box-shadow: var(--card-shadow); }
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
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
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
        .kpi-card .kpi-title { font-size: 1rem; font-weight: 500; }
        .kpi-card .kpi-value { font-size: 2.5rem; font-weight: 700; }
    </style>
    @yield('styles')
</head>
<body id="page-top">
    <div id="wrapper">
        @include('layout.sidebar')
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
                        <i class="fa fa-bars text-primary"></i>
                    </button>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name ?? 'زائر' }}</span>
                                <img class="img-profile rounded-circle" src="https://placehold.co/60x60/4e73df/ffffff?text={{ substr(Auth::user()->name ?? 'Z', 0, 1) }}">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in text-end" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw ms-2 text-gray-400"></i>
                                    تسجيل الخروج
                                </a>
                                <form id="logout-form" action="" method="POST" class="d-none">@csrf</form>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

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
    @stack('scripts')
</body>
</html>