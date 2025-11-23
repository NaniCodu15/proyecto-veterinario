<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // Si no está autenticado → enviar al login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Si el usuario no tiene rol asignado
        if ($user->role === null) {
            abort(403, 'Tu cuenta no tiene rol asignado.');
        }

        // Verificar si el rol del usuario coincide con los permitidos
        if (!in_array($user->role->name, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
