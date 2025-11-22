<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function redirectNoPermission()
    {
        return redirect()->route('dashboard')->with('error', 'No tienes permiso');
    }

    protected function userHasRole(array $roles): bool
    {
        $user = Auth::user();

        return $user !== null && $user->hasAnyRole($roles);
    }
}
