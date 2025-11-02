<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi App</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Si estamos en el login, carga login.css --}}
    @if (Request::is('login'))
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endif

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    @yield('content')
</body>
</html>
