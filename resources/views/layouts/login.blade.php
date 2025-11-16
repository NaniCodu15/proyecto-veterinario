<!DOCTYPE html>
<html lang="es">
<head>
    {{-- Layout simplificado exclusivo para la pantalla de autenticación --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi App</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    {{-- Contenedor donde se incrusta el formulario de login --}}
    @yield('content')
</body>
</html>
