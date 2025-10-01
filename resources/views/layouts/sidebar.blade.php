<!-- 
    هذا الـ Style Block هو حل مخصص ومباشر لمشكلة عدم وضوح نصوص القائمة الجانبية.
    يقوم بتجاوز أي أنماط أخرى قد تسبب المشكلة ويضمن ظهور النصوص بشكل صحيح.
-->
<style>
    /* إعطاء لون أبيض واضح للافتراضي وغير النشط */
    .sidebar-dark .nav-item .nav-link {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .sidebar-dark .nav-item .nav-link i {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    .sidebar-dark .sidebar-heading {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    /* إعطاء لون أبيض كامل للخيار النشط حالياً */
    .sidebar-dark .nav-item.active .nav-link {
        color: #ffffff !important;
        font-weight: 700;
    }
    .sidebar-dark .nav-item.active .nav-link i {
         color: #ffffff !important;
    }

    /* إعطاء لون أبيض كامل عند مرور الماوس */
    .sidebar-dark .nav-item:hover .nav-link {
        color: #ffffff !important;
    }
</style>

<!-- بداية القائمة الجانبية للتحكم -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- شعار لوحة التحكم -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}"> {{-- تم التعديل --}}
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-tools"></i> 
        </div>
        <div class="sidebar-brand-text mx-3">إدارة المعدات</div> 
    </a>

    <!-- فاصل -->
    <hr class="sidebar-divider my-0">

    <!-- عنصر لوحة التحكم الرئيسية (Admin Dashboard) -->
    <li class="nav-item {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}"> 
        <a class="nav-link" href="{{ route('admin.dashboard') }}"> 
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>لوحة التحكم</span></a>
    </li>

    <!-- فاصل وعنوان قسم الإدارات -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        الإدارات الأساسية
    </div>

    <!-- إدارة المستخدمين (تفعيل / حظر) -->
    <li class="nav-item {{ Request::is('admin/users*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>إدارة المستخدمين</span></a>
    </li>

    <!-- إدارة المعدات (مراجعة الإعلانات) -->
    <li class="nav-item {{ Request::is('admin/equipment*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.equipment.index') }}"> 
            <i class="fas fa-fw fa-hard-hat"></i> 
            <span>إدارة المعدات</span></a>
    </li>

    <!-- إدارة الشكاوى والاستفسارات -->
    <li class="nav-item {{ Request::is('admin/complaints*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.complaints.index') }}"> 
            <i class="fas fa-fw fa-headset"></i> {{-- أيقونة مقترحة للشكاوى --}}
            <span>الشكاوى والاستفسارات</span></a>
    </li>

    <!-- فاصل وإعدادات النظام  -->
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        الإعدادات العامة
    </div>

    <li class="nav-item {{ Request::is('admin/settings*') ? 'active' : '' }}">
        <a class="nav-link" href=""> 
            <i class="fas fa-fw fa-cogs"></i>
            <span>إعدادات النظام</span></a>
    </li>

    <!-- زر إخفاء/إظهار القائمة الجانبية -->
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
