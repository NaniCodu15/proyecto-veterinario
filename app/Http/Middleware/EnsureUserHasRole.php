<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Verifica que el usuario autenticado tenga alguno de los roles requeridos.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        $roles = collect($roles)
            ->flatMap(fn ($role) => explode(',', (string) $role))
            ->map(fn ($role) => trim($role))
            ->filter()
            ->values();

        if (!$user || ($roles->isNotEmpty() && !$user->hasRole($roles->all()))) {
            abort(403, 'No tienes permisos para acceder a este recurso.');
        }

        return $next($request);
    }
}
