<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     *
     * @return \Illuminate\View\View Vista `auth.login` para capturar las credenciales.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Autentica al usuario validando sus credenciales y gestionando la sesión.
     *
     * @param Request $request Solicitud con los campos `email` y `password` validados.
     * @return \Illuminate\Http\RedirectResponse Redirección al dashboard en caso de éxito o regreso al formulario con errores.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard'); // Aquí decides a dónde irá después de login
        }

        return back()->withErrors([
            'email' => 'Las credenciales no son correctas.',
        ]);
    }

    /**
     * Cierra la sesión del usuario autenticado y limpia los datos de la sesión.
     *
     * @param Request $request Solicitud actual para invalidar y regenerar el token de sesión.
     * @return \Illuminate\Http\RedirectResponse Redirección al formulario de login.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
