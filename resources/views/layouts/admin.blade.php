<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Promoção Modelo')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Aplica tema e estado do sidebar antes do render para evitar flash/pulos --}}
    <script>
        (function() {
            var html = document.documentElement;
            html.classList.add('no-transition');
            html.setAttribute('data-bs-theme', localStorage.getItem('admin-theme') || 'light');
            if (localStorage.getItem('admin-sidebar-collapsed') === 'true') {
                html.classList.add('sidebar-collapsed-early');
            }
        })();
    </script>

    <style>
        /* ===== Light Theme Variables ===== */
        :root, [data-bs-theme="light"] {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 72px;
            --topbar-height: 56px;
            --pm-primary: #6f42c1;
            --pm-primary-dark: #5a32a3;
            --pm-gradient: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);

            --sidebar-bg: #1e1e2d;
            --sidebar-hover: #2a2a3d;
            --sidebar-active: #6f42c1;
            --sidebar-text: #9899ac;
            --sidebar-text-active: #ffffff;
            --sidebar-border: rgba(255,255,255,0.05);
            --sidebar-section-color: #555770;

            --topbar-bg: #ffffff;
            --topbar-border: #e9ecef;
            --topbar-text: #333333;
            --topbar-icon: #555555;
            --topbar-hover-bg: #f0f0f0;

            --main-bg: #f4f6f9;
            --card-bg: #ffffff;
            --card-shadow: 0 1px 6px rgba(0,0,0,0.06);
            --text-primary: #212529;
            --text-muted: #6c757d;
        }

        /* ===== Dark Theme Variables ===== */
        [data-bs-theme="dark"] {
            --sidebar-bg: #141422;
            --sidebar-hover: #1f1f35;
            --sidebar-active: #6f42c1;
            --sidebar-text: #8585a0;
            --sidebar-text-active: #ffffff;
            --sidebar-border: rgba(255,255,255,0.04);
            --sidebar-section-color: #4a4a60;

            --topbar-bg: #1a1a2e;
            --topbar-border: #2a2a40;
            --topbar-text: #d0d0e0;
            --topbar-icon: #9090a8;
            --topbar-hover-bg: #252540;

            --main-bg: #12121e;
            --card-bg: #1a1a2e;
            --card-shadow: 0 1px 6px rgba(0,0,0,0.25);
            --text-primary: #e0e0f0;
            --text-muted: #8080a0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--main-bg);
            color: var(--text-primary);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ===== Sidebar ===== */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1040;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.3s cubic-bezier(.4,0,.2,1), transform 0.3s ease, background-color 0.3s ease;
            scrollbar-width: thin;
            scrollbar-color: #3a3a4d transparent;
        }

        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: #3a3a4d; border-radius: 4px; }

        .sidebar-brand {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            height: var(--topbar-height);
            border-bottom: 1px solid var(--sidebar-border);
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar-brand i { font-size: 1.3rem; min-width: 28px; text-align: center; margin-right: 10px; color: var(--pm-primary); flex-shrink: 0; }
        .sidebar-brand:hover { color: #fff; }
        .sidebar-brand-text { transition: opacity 0.2s ease; }

        .sidebar-section {
            padding: 12px 16px 4px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--sidebar-section-color);
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s ease;
        }

        .sidebar-nav { list-style: none; padding: 4px 8px; margin: 0; }

        .sidebar-nav .nav-item a {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 2px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .sidebar-nav .nav-item a i {
            font-size: 1.1rem;
            width: 28px;
            min-width: 28px;
            text-align: center;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .sidebar-nav .nav-item a:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-nav .nav-item a.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text-active);
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(111, 66, 193, 0.3);
        }

        .sidebar-nav .nav-item a .badge {
            margin-left: auto;
            font-size: 0.65rem;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .sidebar-nav .nav-item a.sidebar-logout {
            color: #f87171 !important;
        }
        .sidebar-nav .nav-item a.sidebar-logout:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5 !important;
        }

        .sidebar-nav .nav-item a .nav-text {
            transition: opacity 0.2s ease;
            overflow: hidden;
        }

        /* Collapse toggle at bottom */
        .sidebar-collapse-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            border-top: 1px solid var(--sidebar-border);
            cursor: pointer;
            color: var(--sidebar-text);
            transition: all 0.2s ease;
            background: none;
            border-left: none;
            border-right: none;
            border-bottom: none;
            width: 100%;
        }
        .sidebar-collapse-btn:hover { color: #fff; background: var(--sidebar-hover); }
        .sidebar-collapse-btn i { font-size: 1.1rem; transition: transform 0.3s ease; }

        /* ===== Sidebar Collapsed ===== */
        .sidebar-collapsed .admin-sidebar {
            width: var(--sidebar-collapsed-width);
        }
        .sidebar-collapsed .admin-topbar {
            left: var(--sidebar-collapsed-width);
        }
        .sidebar-collapsed .admin-main {
            margin-left: var(--sidebar-collapsed-width);
        }

        .sidebar-collapsed .sidebar-brand-text,
        .sidebar-collapsed .sidebar-section,
        .sidebar-collapsed .nav-text {
            opacity: 0;
            width: 0;
            pointer-events: none;
        }

        .sidebar-collapsed .sidebar-brand {
            padding: 16px 0;
            justify-content: center;
        }
        .sidebar-collapsed .sidebar-brand i {
            margin-right: 0;
        }

        .sidebar-collapsed .sidebar-nav .nav-item a {
            justify-content: center;
            padding: 10px 0;
        }
        .sidebar-collapsed .sidebar-nav .nav-item a i {
            margin-right: 0;
        }

        .sidebar-collapsed .sidebar-nav .nav-item a .badge {
            position: absolute;
            top: 4px;
            right: 8px;
            margin-left: 0;
            transform: scale(0.85);
        }

        .sidebar-collapsed .sidebar-collapse-btn i {
            transform: rotate(180deg);
        }

        /* Tooltip on collapsed sidebar */
        .sidebar-collapsed .sidebar-nav .nav-item a {
            position: relative;
        }
        .sidebar-collapsed .sidebar-nav .nav-item a::after {
            content: attr(data-title);
            position: absolute;
            left: calc(var(--sidebar-collapsed-width) - 8px);
            top: 50%;
            transform: translateY(-50%);
            background: #333;
            color: #fff;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s ease;
            z-index: 9999;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .sidebar-collapsed .sidebar-nav .nav-item a:hover::after {
            opacity: 1;
        }

        /* Early collapsed (before JS loads) — mirrors all .sidebar-collapsed rules
           so the layout is correct from first paint, preventing "jumps" on navigation */
        .sidebar-collapsed-early .admin-sidebar { width: var(--sidebar-collapsed-width); }
        .sidebar-collapsed-early .admin-topbar  { left: var(--sidebar-collapsed-width); }
        .sidebar-collapsed-early .admin-main    { margin-left: var(--sidebar-collapsed-width); }

        .sidebar-collapsed-early .sidebar-brand-text,
        .sidebar-collapsed-early .sidebar-section,
        .sidebar-collapsed-early .nav-text {
            opacity: 0; width: 0; pointer-events: none;
        }
        .sidebar-collapsed-early .sidebar-brand { padding: 16px 0; justify-content: center; }
        .sidebar-collapsed-early .sidebar-brand i { margin-right: 0; }
        .sidebar-collapsed-early .sidebar-nav .nav-item a { justify-content: center; padding: 10px 0; }
        .sidebar-collapsed-early .sidebar-nav .nav-item a i { margin-right: 0; }
        .sidebar-collapsed-early .sidebar-nav .nav-item a .badge {
            position: absolute; top: 4px; right: 8px; margin-left: 0; transform: scale(0.85);
        }
        .sidebar-collapsed-early .sidebar-collapse-btn i { transform: rotate(180deg); }

        /* Disable transitions during initial page load to avoid animation flash */
        .no-transition,
        .no-transition *,
        .no-transition *::before,
        .no-transition *::after {
            transition: none !important;
        }

        /* ===== Topbar ===== */
        .admin-topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--topbar-border);
            z-index: 1030;
            display: flex;
            align-items: center;
            padding: 0 24px;
            transition: left 0.3s cubic-bezier(.4,0,.2,1), background-color 0.3s ease, border-color 0.3s ease;
        }

        .topbar-toggle {
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--topbar-icon);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            margin-right: 16px;
            transition: all 0.2s ease;
        }
        .topbar-toggle:hover { background: var(--topbar-hover-bg); }

        .topbar-title {
            font-weight: 600;
            font-size: 1rem;
            color: var(--topbar-text);
            transition: color 0.3s ease;
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Dark mode toggle button */
        .theme-toggle {
            background: none;
            border: 1px solid var(--topbar-border);
            border-radius: 8px;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--topbar-icon);
            font-size: 1.1rem;
            transition: all 0.2s ease;
            position: relative;
        }
        .theme-toggle:hover {
            background: var(--topbar-hover-bg);
            color: var(--topbar-text);
            border-color: var(--pm-primary);
        }
        .theme-toggle .bi-sun-fill { display: none; }
        .theme-toggle .bi-moon-fill { display: inline; }
        [data-bs-theme="dark"] .theme-toggle .bi-sun-fill { display: inline; }
        [data-bs-theme="dark"] .theme-toggle .bi-moon-fill { display: none; }

        /* ===== Main Content ===== */
        .admin-main {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 24px;
            min-height: calc(100vh - var(--topbar-height));
            transition: margin-left 0.3s cubic-bezier(.4,0,.2,1), background-color 0.3s ease;
        }

        /* ===== Override Bootstrap card for dark mode ===== */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            background: var(--card-bg);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        [data-bs-theme="dark"] .card {
            border: 1px solid #2a2a40;
        }

        [data-bs-theme="dark"] .table { color: var(--text-primary); }
        [data-bs-theme="dark"] .table-light { background-color: #1f1f35 !important; color: var(--text-primary); }
        [data-bs-theme="dark"] .table-light th { background-color: #1f1f35 !important; color: var(--text-primary) !important; }
        [data-bs-theme="dark"] .table-hover tbody tr:hover { background-color: rgba(111,66,193,0.08); }

        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select,
        [data-bs-theme="dark"] .input-group-text {
            background-color: #1f1f35;
            border-color: #2a2a40;
            color: var(--text-primary);
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            border-color: var(--pm-primary);
            box-shadow: 0 0 0 0.2rem rgba(111,66,193,0.25);
        }

        [data-bs-theme="dark"] .btn-light {
            background-color: #2a2a40;
            border-color: #3a3a55;
            color: var(--text-primary);
        }
        [data-bs-theme="dark"] .btn-light:hover {
            background-color: #3a3a55;
        }

        [data-bs-theme="dark"] .btn-outline-secondary {
            border-color: #3a3a55;
            color: var(--text-muted);
        }
        [data-bs-theme="dark"] .btn-outline-secondary:hover {
            background-color: #2a2a40;
            color: var(--text-primary);
        }

        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1f1f35;
            border-color: #2a2a40;
        }
        [data-bs-theme="dark"] .dropdown-item {
            color: var(--text-primary);
        }
        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #2a2a40;
        }
        [data-bs-theme="dark"] .dropdown-divider {
            border-color: #2a2a40;
        }

        [data-bs-theme="dark"] .text-muted {
            color: var(--text-muted) !important;
        }
        [data-bs-theme="dark"] .text-dark {
            color: var(--text-primary) !important;
        }

        [data-bs-theme="dark"] .alert {
            border: 1px solid rgba(255,255,255,0.1);
        }

        [data-bs-theme="dark"] .card-header.bg-white,
        [data-bs-theme="dark"] .bg-white {
            background-color: var(--card-bg) !important;
        }
        [data-bs-theme="dark"] .bg-light {
            background-color: #1f1f35 !important;
        }

        [data-bs-theme="dark"] .progress {
            background-color: #2a2a40;
        }

        [data-bs-theme="dark"] .list-group-item {
            background-color: var(--card-bg);
            border-color: #2a2a40;
            color: var(--text-primary);
        }

        [data-bs-theme="dark"] .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Pagination */
        .page-item.active .page-link {
            background-color: var(--pm-primary);
            border-color: var(--pm-primary);
        }
        .page-link {
            transition: all 0.2s ease;
        }
        .page-link:hover {
            color: var(--pm-primary);
        }
        [data-bs-theme="dark"] .page-link {
            background-color: #1f1f35;
            border-color: #2a2a40;
            color: var(--text-primary);
        }
        [data-bs-theme="dark"] .page-link:hover {
            background-color: var(--pm-primary);
            border-color: var(--pm-primary);
            color: #fff;
        }
        [data-bs-theme="dark"] .page-item.active .page-link {
            background-color: var(--pm-primary);
            border-color: var(--pm-primary);
            color: #fff;
        }
        [data-bs-theme="dark"] .page-item.disabled .page-link {
            background-color: #141422;
            border-color: #2a2a40;
            color: #4a4a60;
        }

        /* ===== Responsive: mobile ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1035;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
            .sidebar-overlay.show { display: block; }
            .admin-topbar { left: 0 !important; }
            .admin-main { margin-left: 0 !important; }

            /* No collapse on mobile */
            .sidebar-collapsed .admin-sidebar {
                width: var(--sidebar-width) !important;
            }
            .sidebar-collapsed .sidebar-brand-text,
            .sidebar-collapsed .sidebar-section,
            .sidebar-collapsed .nav-text {
                opacity: 1;
                width: auto;
                pointer-events: auto;
            }
            .sidebar-collapsed .sidebar-brand {
                padding: 16px 20px;
                justify-content: flex-start;
            }
            .sidebar-collapsed .sidebar-brand i { margin-right: 10px; }
            .sidebar-collapsed .sidebar-nav .nav-item a {
                justify-content: flex-start;
                padding: 10px 14px;
            }
            .sidebar-collapsed .sidebar-nav .nav-item a i { margin-right: 10px; }
            .sidebar-collapsed .sidebar-nav .nav-item a::after { display: none; }
            .sidebar-collapsed .sidebar-nav .nav-item a .badge {
                position: static;
                margin-left: auto;
                transform: none;
            }
            .sidebar-collapse-btn { display: none !important; }
        }

        /* ===== Utility ===== */
        .bg-pm-gradient { background: var(--pm-gradient); }

        .btn-pm {
            background: var(--pm-gradient);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .btn-pm:hover { box-shadow: 0 4px 12px rgba(111,66,193,0.3); color: #fff; }

        .stat-card {
            border-left: 4px solid var(--pm-primary);
        }
        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--pm-primary);
        }

        .lucky-number-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--pm-gradient);
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
            <i class="bi bi-star-fill"></i>
            <span class="sidebar-brand-text">Promoção Modelo</span>
        </a>

        <div class="sidebar-section">Principal</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" data-title="Dashboard" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> <span class="nav-text">Dashboard</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Cadastros</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('admin.participantes.index') }}" data-title="Participantes" class="{{ request()->routeIs('admin.participantes.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> <span class="nav-text">Participantes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.cupons.index') }}" data-title="Cupons Fiscais" class="{{ request()->routeIs('admin.cupons.*') && !request()->routeIs('admin.cupons.erro') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> <span class="nav-text">Cupons Fiscais</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.numeros.index') }}" data-title="Números da Sorte" class="{{ request()->routeIs('admin.numeros.*') ? 'active' : '' }}">
                    <i class="bi bi-stars"></i> <span class="nav-text">Números da Sorte</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Processamento</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('admin.cupons.erro') }}" data-title="Cupons com Erro" class="{{ request()->routeIs('admin.cupons.erro') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i> <span class="nav-text">Cupons com Erro</span>
                    @php $qtdErros = \App\Models\CupomFiscal::where('status', 'erro')->count(); @endphp
                    @if($qtdErros > 0)
                        <span class="badge bg-danger">{{ $qtdErros }}</span>
                    @endif
                </a>
            </li>
        </ul>

        @php $adminLogado = Auth::guard('admin')->user(); @endphp

        <div class="sidebar-section">Configuração</div>
        <ul class="sidebar-nav">
            @if($adminLogado->temPermissao('perm_produtos'))
            <li class="nav-item">
                <a href="{{ route('admin.produtos') }}" data-title="Produtos" class="{{ request()->routeIs('admin.produtos*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> <span class="nav-text">Produtos</span>
                </a>
            </li>
            @endif
            @if($adminLogado->temPermissao('perm_faq'))
            <li class="nav-item">
                <a href="{{ route('admin.faqs.index') }}" data-title="FAQ" class="{{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                    <i class="bi bi-question-circle"></i> <span class="nav-text">FAQ</span>
                </a>
            </li>
            @endif
            @if($adminLogado->temPermissao('perm_configuracoes'))
            <li class="nav-item">
                <a href="{{ route('admin.settings') }}" data-title="Configurações" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> <span class="nav-text">Configurações</span>
                </a>
            </li>
            @endif
        </ul>

        <div class="sidebar-section">Sistema</div>
        <ul class="sidebar-nav">
            @if($adminLogado->is_super_admin)
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" data-title="Administradores" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i> <span class="nav-text">Administradores</span>
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a href="{{ route('home') }}" data-title="Ver Site" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> <span class="nav-text">Ver Site</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" data-title="Sair" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();" class="sidebar-logout">
                    <i class="bi bi-box-arrow-left"></i> <span class="nav-text">Sair</span>
                </a>
                <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">@csrf</form>
            </li>
        </ul>

        <!-- Sidebar Collapse Button -->
        <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Minimizar menu">
            <i class="bi bi-chevron-double-left"></i>
        </button>
    </aside>

    <!-- Topbar -->
    <header class="admin-topbar">
        <button class="topbar-toggle" id="sidebarToggle" title="Menu">
            <i class="bi bi-list"></i>
        </button>
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="topbar-right">
            @php
                $adminUser = Auth::guard('admin')->user();
                $unreadCount = $adminUser ? $adminUser->unreadNotifications()->count() : 0;
            @endphp
            @if($unreadCount > 0)
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-warning position-relative" title="Notificações">
                    <i class="bi bi-bell-fill"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $unreadCount }}
                    </span>
                </a>
            @endif

            <!-- Dark Mode Toggle -->
            <button class="theme-toggle" id="themeToggle" title="Alternar tema claro/escuro">
                <i class="bi bi-moon-fill"></i>
                <i class="bi bi-sun-fill"></i>
            </button>

            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i>{{ $adminUser?->name ?? 'Admin' }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-left me-2"></i>Sair
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Verifique os erros abaixo:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleMobile = document.getElementById('sidebarToggle');
        const collapseBtn = document.getElementById('sidebarCollapseBtn');
        const themeBtn = document.getElementById('themeToggle');
        const html = document.documentElement;

        // ===== Mobile sidebar toggle =====
        toggleMobile.addEventListener('click', () => {
            const isMobile = window.innerWidth < 992;
            if (isMobile) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                // On desktop, toggle collapse
                document.body.classList.toggle('sidebar-collapsed');
                const collapsed = document.body.classList.contains('sidebar-collapsed');
                localStorage.setItem('admin-sidebar-collapsed', collapsed);
                collapseBtn.title = collapsed ? 'Expandir menu' : 'Minimizar menu';
            }
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        // ===== Sidebar collapse (desktop) =====
        collapseBtn.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            const collapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('admin-sidebar-collapsed', collapsed);
            collapseBtn.title = collapsed ? 'Expandir menu' : 'Minimizar menu';
        });

        // Restore sidebar state — transfer from html (early) to body (runtime)
        if (localStorage.getItem('admin-sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
            collapseBtn.title = 'Expandir menu';
        }
        html.classList.remove('sidebar-collapsed-early');

        // Re-enable transitions after layout is stable (next frame)
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                html.classList.remove('no-transition');
            });
        });

        // ===== Dark mode toggle =====
        function setTheme(theme) {
            html.setAttribute('data-bs-theme', theme);
            localStorage.setItem('admin-theme', theme);
        }

        themeBtn.addEventListener('click', () => {
            const current = html.getAttribute('data-bs-theme');
            setTheme(current === 'dark' ? 'light' : 'dark');
        });

        // ===== Close sidebar on route navigation (mobile) =====
        sidebar.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            });
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
