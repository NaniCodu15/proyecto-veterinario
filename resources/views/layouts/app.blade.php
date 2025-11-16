<!DOCTYPE html>
<html lang="es">
<head>
    {{-- Metadatos base compartidos por todas las pantallas autenticadas y públicas --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi App</title>

    {{-- Se alterna la hoja de estilos principal dependiendo si es la pantalla de login o el dashboard --}}
    @if (Request::is('login'))
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endif

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')
</head>
<body class="dashboard-layout">
    {{-- Slot principal donde cada vista inyecta su contenido --}}
    @yield('content')

    @stack('scripts')
</body>
</html>
