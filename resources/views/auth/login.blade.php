{{-- Vista de autenticación para acceder al sistema --}}
@extends('layouts.app')

@section('title', 'Iniciar sesión')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
{{-- Contenedor principal centrado del login --}}
<div class="login-container">
    <div class="login-form">
        {{-- Sección con el logo institucional --}}
        <div class="login-logo">
            <img src="{{ asset('images/logoVet.png') }}" alt="Dra. Alfaro" class="logo" />
        </div>

        {{-- Mensaje de error cuando las credenciales son inválidas --}}
        @if($errors->has('email'))
            <div class="login-error" role="alert">Las credenciales no son correctas.</div>
        @endif

        {{-- Formulario de inicio de sesión --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group">
                {{-- Campo de correo electrónico --}}
                <i class="fa-solid fa-envelope icon"></i>
                <input type="email" name="email" id="email" placeholder=" " required>
                <label for="email">Email:</label>
            </div>

            <div class="input-group password-group">
                {{-- Campo de contraseña --}}
                <i class="fa-solid fa-lock icon"></i>
                <input type="password" name="password" id="password" placeholder=" " required>
                <button type="button" class="toggle-password" aria-label="Mostrar u ocultar contraseña">
                    <i class="fa-regular fa-eye"></i>
                </button>
                <label for="password">Contraseña:</label>
            </div>

            {{-- Acción para enviar las credenciales --}}
            <button type="submit">Iniciar Sesión</button>
        </form>

    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/login.js') }}"></script>
@endpush
