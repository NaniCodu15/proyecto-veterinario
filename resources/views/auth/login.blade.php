@extends('layouts.login')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<div class="login-container">
    {{-- Tarjeta central que contiene el formulario y los mensajes --}}
    <div class="login-form">
        {{-- Logo --}}
        <div class="login-logo">
            <img src="{{ asset('images/logoVet.png') }}" alt="Dra. Alfaro" class="logo" />
        </div>

        {{-- Mensaje de error --}}
        @if($errors->has('email'))
            <div class="login-error" role="alert">Las credenciales no son correctas.</div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Campo de correo electrónico con iconografía de apoyo --}}
            <div class="input-group">
                <i class="fa-solid fa-envelope icon"></i>
                <input type="email" name="email" id="email" placeholder=" " required>
                <label for="email">Email:</label>
            </div>

            {{-- Campo de contraseña con estilo similar al email para mantener consistencia visual --}}
            <div class="input-group">
                <i class="fa-solid fa-lock icon"></i>
                <input type="password" name="password" id="password" placeholder=" " required>
                <label for="password">Contraseña:</label>
            </div>

            <button type="submit">Iniciar Sesión</button>
        </form>

{{-- Script para ocultar el mensaje de error al escribir --}}
        <script>
            // Selecciona todos los inputs del formulario
            const inputs = document.querySelectorAll('.login-form input');
            const errorDiv = document.querySelector('.login-form .login-error');

            if(errorDiv){
                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        // Oculta el mensaje de error al escribir
                        errorDiv.style.display = 'none';
                    });
                });
            }
        </script>

    </div> 
</div> 
@endsection
