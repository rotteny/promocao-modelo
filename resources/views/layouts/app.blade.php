<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Promoção Modelo')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --pm-primary: #6f42c1;
            --pm-primary-dark: #5a32a3;
            --pm-secondary: #fd7e14;
            --pm-gradient: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
        }

        .bg-pm-gradient {
            background: var(--pm-gradient);
        }

        .btn-pm {
            background: var(--pm-gradient);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 1.8rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .btn-pm:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(111, 66, 193, 0.4);
            color: #fff;
        }

        .btn-pm-outline {
            border: 2px solid var(--pm-primary);
            color: var(--pm-primary);
            font-weight: 600;
            padding: 0.55rem 1.8rem;
            border-radius: 50px;
            background: transparent;
            transition: all 0.3s ease;
        }

        .btn-pm-outline:hover {
            background: var(--pm-primary);
            color: #fff;
            transform: translateY(-2px);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
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

        .stat-card {
            border-left: 4px solid var(--pm-primary);
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--pm-primary);
        }

        footer {
            background: #212529;
            color: #adb5bd;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-pm-gradient sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-star-fill me-2"></i>Promoção Modelo
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i>Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('regulamento') ? 'active' : '' }}" href="{{ route('regulamento') }}">
                            <i class="bi bi-file-text me-1"></i>Regulamento
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('faq') ? 'active' : '' }}" href="{{ route('faq') }}">
                            <i class="bi bi-question-circle me-1"></i>FAQ
                        </a>
                    </li>

                    {{-- Links do Participante --}}
                    @auth('web')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-grid-1x2 me-1"></i>Meus Números
                            </a>
                        </li>
                        @if($promocaoAtiva ?? false)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('cupom.*') ? 'active' : '' }}" href="{{ route('cupom.create') }}">
                                    <i class="bi bi-receipt me-1"></i>Cadastrar Cupom
                                </a>
                            </li>
                        @endif
                    @endauth

                    {{-- Link do Admin --}}
                    @auth('admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-shield-lock me-1"></i>Admin
                            </a>
                        </li>
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @if(Auth::guard('admin')->check())
                        {{-- Dropdown Admin --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-shield-check me-1"></i>{{ Auth::guard('admin')->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Painel Admin
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-left me-2"></i>Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @elseif(Auth::guard('web')->check())
                        {{-- Dropdown Participante --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>{{ Auth::guard('web')->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-grid-1x2 me-2"></i>Meus Números
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-left me-2"></i>Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        {{-- Guest --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light btn-sm ms-2 mt-1 fw-semibold" href="{{ route('register') }}">
                                Cadastre-se
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Banner de Promoção Fictícia -->
    <div class="disclaimer-banner bg-warning text-dark py-2 border-bottom border-warning-subtle">
        <div class="container text-center">
            <small class="fw-semibold">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <strong>PROJETO FICTÍCIO</strong> — Este sistema é uma demonstração de portfólio. Não se trata de uma promoção real.
                Nenhum dado possui validade jurídica ou comercial.
            </small>
        </div>
    </div>

    <!-- Alerts -->
    <div class="container mt-3">
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
    </div>

    <!-- Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-5 py-4">
        <div class="container text-center">
            <p class="mb-1">
                <i class="bi bi-star-fill me-1"></i><strong>Promoção Modelo</strong>
            </p>
            <p class="small mb-2">&copy; {{ date('Y') }} - Todos os direitos reservados. Consulte o <a href="{{ route('regulamento') }}" class="text-light">regulamento</a>.</p>
            <div class="border-top border-secondary pt-2 mt-2">
                <p class="small mb-0 opacity-75">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>Projeto fictício para fins de portfólio.</strong> Esta promoção não é real e não possui qualquer validade jurídica ou comercial.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
