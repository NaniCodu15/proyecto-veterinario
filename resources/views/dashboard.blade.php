<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Meta base -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Título dinámico -->
    <title>@yield('title', 'Panel Principal')</title>

    <!-- Estilos globales del dashboard -->
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- Estilos por módulo -->
    @if (!empty($css))
        <link rel="stylesheet" href="{{ asset('css/' . $css) }}">
    @endif

    <!-- Íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-+0YF4VgyQ8Zb6kZPwQBGiN4dwY58CL2boiE/Y4FzFwFgfljfsU6qIBl0ZVjvHX4QsFjWvRt6pxlJ47C0d3d5mA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="dashboard-layout">
    <div class="dashboard-container">
        <!-- Barra lateral principal -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/logoVet.png') }}" alt="Logotipo de la veterinaria" class="sidebar-logo">
            </div>

            <nav aria-label="Navegación principal">
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('dashboard.blade.php') }}" class="nav-link">
                            <i class="fas fa-home"></i>
                            <span>Inicio</span>
                        </a>
                    </li>
                    <li class="sidebar-item sidebar-item--has-submenu">
                        <a href="{{ route('historias.clinicas') }}" class="nav-link">
                            <i class="fas fa-notes-medical"></i>
                            <span>Historias Clínicas</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('historias.registradas') }}" class="nav-link nav-link--sublayer">
                                    <i class="fas fa-folder-open"></i>
                                    <span>Historias registradas</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-item sidebar-item--has-submenu">
                        <a href="{{ route('citas') }}" class="nav-link">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Citas</span>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="{{ route('citas.agendadas') }}" class="nav-link nav-link--sublayer">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Citas agendadas</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            @php
                $user = auth()->user();
                $userName = $user?->name ?? 'Usuario';
                $userEmail = $user?->email ?? 'usuario@correo.com';
                $avatarUrl = $user?->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=7aa8ff&color=ffffff';
            @endphp

            <div class="user-card">
                <div class="user-card__info">
                    <div class="user-card__avatar">
                        <img src="{{ $avatarUrl }}" alt="Avatar de {{ $userName }}">
                    </div>
                    <div class="user-card__details">
                        <span class="user-card__name">{{ $userName }}</span>
                        <span class="user-card__email">{{ $userEmail }}</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="user-card__logout">
                    @csrf
                    <button type="submit" aria-label="Cerrar sesión">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenido dinámico -->
        <main class="content" id="main-content">
            <header class="content__header">
                <h1 class="content__title">@yield('title', 'Panel Principal')</h1>
            </header>
            <section class="content__body">
                @yield('content')
            </section>
        </main>
    </div>

    <!-- Scripts por módulo -->
    @if (!empty($js))
        <script src="{{ asset('js/' . $js) }}" defer></script>
    @endif
</body>
</html>
