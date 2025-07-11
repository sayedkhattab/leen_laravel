<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة تحكم لين')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'El Messiri', sans-serif;
            background-color: #f8f9fc;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 250px;
            background-color: #2F3E3B;
            color: white;
            z-index: 1;
            transition: all 0.3s;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            overflow-y: auto;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 8px 10px;
            margin: 3px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(-5px);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 173, 201, 0.3);
            border-right: 4px solid #FFADC9;
        }
        
        .sidebar .nav-link i {
            margin-left: 8px;
            font-size: 1.1rem;
        }
        
        .sidebar-brand {
            text-align: center;
            padding: 15px 0;
            margin-bottom: 5px;
        }
        
        .sidebar-brand h4 {
            color: white;
            margin: 10px 0 0 0;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .content {
            margin-right: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .navbar-brand {
            display: none;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            font-weight: bold;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0 !important;
        }
        
        .sidebar-toggler {
            display: none;
            background: none;
            border: none;
            color: #2F3E3B;
            font-size: 1.5rem;
        }
        
        .logo {
            max-width: 60px;
        }
        
        .border-left-primary {
            border-left: 4px solid #2F3E3B !important;
        }
        
        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }
        
        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }
        
        .border-left-warning {
            border-left: 4px solid #f6c23e !important;
        }
        
        .text-primary {
            color: #2F3E3B !important;
        }
        
        .btn-primary {
            background-color: #2F3E3B;
            border-color: #2F3E3B;
        }
        
        .btn-primary:hover {
            background-color: #253230;
            border-color: #253230;
        }
        
        .btn-secondary {
            background-color: #FFADC9;
            border-color: #FFADC9;
        }
        
        .btn-secondary:hover {
            background-color: #ff9ab9;
            border-color: #ff9ab9;
        }
        
        .stat-card {
            padding: 20px;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            min-height: 120px;
        }
        
        .stat-card .stat-icon {
            position: absolute;
            left: 20px;
            bottom: 20px;
            font-size: 3rem;
            opacity: 0.3;
        }
        
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-card .stat-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0;
            opacity: 0.8;
        }
        
        .stat-customers {
            background: linear-gradient(45deg, #2F3E3B, #3a4a47);
            color: white;
        }
        
        .stat-sellers {
            background: linear-gradient(45deg, #1cc88a, #13855c);
            color: white;
        }
        
        .stat-services {
            background: linear-gradient(45deg, #36b9cc, #258391);
            color: white;
        }
        
        .stat-bookings {
            background: linear-gradient(45deg, #FFADC9, #ff84a9);
            color: white;
        }
        
        .table th {
            font-weight: 600;
            background-color: #f8f9fc;
        }
        
        .badge {
            font-weight: 500;
            padding: 5px 10px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .user-welcome {
            font-weight: 600;
            color: #2F3E3B;
        }
        
        .divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.2);
            margin: 8px 10px;
        }
        
        .sidebar-section-title {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.8rem;
            text-transform: uppercase;
            padding: 8px 12px;
            margin-top: 5px;
            letter-spacing: 1px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .content {
                margin-right: 0;
            }
            
            .navbar-brand {
                display: block;
            }
            
            .sidebar-toggler {
                display: block;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo/pink_logo.png') }}" alt="لين" class="logo">
        </div>
        
        <div class="divider"></div>
        
        <div class="sidebar-section-title">القائمة الرئيسية</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> الرئيسية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    <i class="bi bi-grid"></i> التصنيفات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.subcategories.*') ? 'active' : '' }}" href="{{ route('admin.subcategories.index') }}">
                    <i class="bi bi-diagram-3"></i> التصنيفات الفرعية
                </a>
            </li>
        </ul>
        
        <div class="divider"></div>
        
        <div class="sidebar-section-title">إدارة المستخدمين</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                    <i class="bi bi-people"></i> العملاء
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}" href="{{ route('admin.sellers.index') }}">
                    <i class="bi bi-shop"></i> مقدمي الخدمات
                </a>
            </li>
        </ul>
        
        <div class="divider"></div>
        
        <div class="sidebar-section-title">إدارة المحتوى</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.promotional.banners.*') ? 'active' : '' }}" href="{{ route('admin.promotional.banners.index') }}">
                    <i class="bi bi-image"></i> البانرات الإعلانية
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.promotional.featured-services.*') ? 'active' : '' }}" href="{{ route('admin.promotional.featured-services.index') }}">
                    <i class="bi bi-star"></i> الخدمات المميزة
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.promotional.featured-professionals.*') ? 'active' : '' }}" href="{{ route('admin.promotional.featured-professionals.index') }}">
                    <i class="bi bi-person-badge"></i> الأخصائيين المميزين
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.promotional.special-offers.*') ? 'active' : '' }}" href="{{ route('admin.promotional.special-offers.index') }}">
                    <i class="bi bi-tags"></i> العروض الخاصة
                </a>
            </li>
        </ul>
        
        <div class="divider"></div>
        
        <div class="sidebar-section-title">إدارة الخدمات</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}">
                    <i class="bi bi-briefcase"></i> الخدمات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
                    <i class="bi bi-calendar-check"></i> الحجوزات
                </a>
            </li>
        </ul>
        
        <div class="divider"></div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-left"></i> تسجيل الخروج
                </a>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
    
    <div class="content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <button class="sidebar-toggler" type="button" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('images/logo/pink_logo.png') }}" alt="لين" height="30">
                </a>
                <div class="ms-auto">
                    <span class="user-welcome">مرحبا، {{ auth()->user()->name }}</span>
                </div>
            </div>
        </nav>
        
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggler = document.querySelector('.sidebar-toggler');
            if (sidebarToggler) {
                sidebarToggler.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('show');
                });
            }
        });
        
        // Handle AJAX errors for expired sessions
        $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
            if (jqXHR.status === 401) {
                // Session expired
                const response = jqXHR.responseJSON || {};
                
                if (response.redirect) {
                    // Show alert and redirect
                    alert('انتهت جلستك. سيتم إعادة توجيهك إلى صفحة تسجيل الدخول.');
                    window.location.href = response.redirect;
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html> 