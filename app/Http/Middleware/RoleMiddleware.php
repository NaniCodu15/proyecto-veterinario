<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $currentRole = strtolower((string) ($user->role ?? ''));
        $allowedRoles = array_map('strtolower', $roles);

        if (!in_array($currentRole, $allowedRoles, true)) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
