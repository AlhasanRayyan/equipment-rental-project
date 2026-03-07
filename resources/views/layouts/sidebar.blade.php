<style>
    /* ... التنسيقات الخاصة بك كما هي ... */
    .sidebar-dark .nav-item .nav-link { color: rgba(255, 255, 255, 0.8) !important; }
    .sidebar-dark .nav-item.active .nav-link { color: #ffffff !important; font-weight: 700; }
</style>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- شعار لوحة التحكم - الرابط يتغير حسب الدور -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" 
       href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : '#' }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-tools"></i>
        </div>
        <div class="sidebar-brand-text mx-3">
            {{ auth()->user()->isAdmin() ? 'لوحة المشرف' : 'لوحة المالك' }}
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    {{-- 1. عناصر تظهر للأدمن فقط --}}
    @if(auth()->user()->isAdmin())
        <li class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>لوحة التحكم</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">الإدارات الأساسية</div>

        <li class="nav-item {{ Request::is('admin/users*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.users.index') }}">
                <i class="fas fa-fw fa-users"></i>
                <span>إدارة المستخدمين</span>
            </a>
        </li>

        <li class="nav-item {{ Request::is('admin/categories*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.categories.index') }}">
                <i class="fas fa-fw fa-tags"></i>
                <span>إدارة الفئات</span>
            </a>
        </li>

        <li class="nav-item {{ Request::is('admin/equipment*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.equipment.index') }}">
                <i class="fas fa-fw fa-hard-hat"></i>
                <span>إدارة المعدات</span>
            </a>
        </li>

        <li class="nav-item {{ Request::is('admin/complaints*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.complaints.index') }}">
                <i class="fas fa-fw fa-headset"></i>
                <span>الشكاوى والاستفسارات</span>
            </a>
        </li>
    @endif

    {{-- 2. عنصر الحجوزات (يظهر للجميع ولكن بروابط مختلفة) --}}
    <li class="nav-item {{ (Request::is('admin/bookings*') || Request::is('owner/bookings*')) ? 'active' : '' }}">
        @if(auth()->user()->isAdmin())
            <a class="nav-link" href="{{ route('admin.bookings.index') }}">
                <i class="fas fa-calendar-check"></i>
                <span>كل الحجوزات (أدمن)</span>
            </a>
        @else
            {{-- رابط المالك إلى لوحة تحكم المالك التي أنشأناها --}}
            <a class="nav-link" href="{{ route('owner.bookings.index') }}">
                <i class="fas fa-calendar-check"></i>
                <span>حجوزاتي المستلمة</span>
            </a>
        @endif
    </li>

    {{-- 3. إعدادات النظام تظهر للأدمن فقط --}}
    @if(auth()->user()->isAdmin())
        <hr class="sidebar-divider">
        <div class="sidebar-heading">الإعدادات العامة</div>

        <li class="nav-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('admin.settings.index') }}">
                <i class="fas fa-fw fa-cogs"></i>
                <span>إعدادات النظام</span>
            </a>
        </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>