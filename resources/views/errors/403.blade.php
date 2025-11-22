@extends('layouts.app')

@section('content')
    <div class="unauthorized">
        <div class="container" style="padding: 40px; text-align:center;">
            <h1 style="color:#e3342f;">403 | Acceso no autorizado</h1>
            <p>Lo sentimos, no tienes permisos para acceder a esta sección.</p>
            <a href="{{ route('dashboard') }}" style="color:#1d4ed8; text-decoration:underline;">Volver al panel</a>
        </div>
    </div>
@endsection
