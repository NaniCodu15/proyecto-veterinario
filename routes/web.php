<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\PropietarioController;
use App\Http\Controllers\VacunaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BackupController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Ruta pública: muestra el formulario de autenticación (Auth\LoginController@showLoginForm) para el módulo de acceso.
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Ruta pública: procesa las credenciales enviadas (Auth\LoginController@login) para iniciar sesión.
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
// Ruta pública: finaliza la sesión activa (Auth\LoginController@logout) sin requerir middleware adicional.
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas por autenticación: todo el bloque requiere sesión activa mediante el middleware `auth`.
Route::middleware(['auth', 'role:admin,asistente'])->group(function () {

    // Ruta protegida: panel de control general atendido por DashboardController@index para el módulo principal.
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    // Rutas de lectura de citas disponibles para ambos roles.
    Route::get('citas/list', [CitaController::class, 'list'])->name('citas.list');
    Route::get('citas/upcoming', [CitaController::class, 'upcoming'])->name('citas.upcoming');
    Route::get('citas', [CitaController::class, 'index'])->name('citas.index');

    // Rutas de lectura de historias clínicas disponibles para ambos roles.
    Route::get('historia_clinicas/list', [HistoriaClinicaController::class,'list'])->name('historia_clinicas.list');
    Route::get('historia_clinicas/{id}/ver', [HistoriaClinicaController::class, 'ver'])->name('historia_clinicas.ver');
    Route::get('historia_clinicas/{historia}/pdf', [HistoriaClinicaController::class, 'pdf'])->name('historia_clinicas.pdf');
    Route::get('historia_clinicas/{id}', [HistoriaClinicaController::class,'show'])->name('historia_clinicas.show');
    Route::get('historia_clinicas/{historia}/consultas', [ConsultaController::class, 'porHistoria'])->name('historia_clinicas.consultas');

    // Rutas de lectura de consultas.
    Route::get('consultas', [ConsultaController::class, 'index'])->name('consultas.index');
    Route::get('consultas/{consulta}', [ConsultaController::class, 'show'])->name('consultas.show');

    // Rutas protegidas: CRUD de mascotas gestionadas por MascotaController para el módulo de mascotas.
    Route::resource('mascotas', MascotaController::class);
    // Rutas protegidas: CRUD de propietarios gestionadas por PropietarioController para el módulo de clientes.
    Route::resource('propietarios', PropietarioController::class);
    // Rutas protegidas: CRUD de vacunas gestionadas por VacunaController para el módulo de inmunizaciones.
    Route::resource('vacunas', VacunaController::class);

});

// Rutas exclusivas del rol asistente: creación y edición de historias, consultas y citas.
Route::middleware(['auth', 'role:asistente'])->group(function () {
    Route::post('historia_clinicas', [HistoriaClinicaController::class,'store'])->name('historia_clinicas.store');
    Route::put('historia_clinicas/{id}', [HistoriaClinicaController::class,'update'])->name('historia_clinicas.update');

    Route::post('consultas', [ConsultaController::class, 'store'])->name('consultas.store');
    Route::put('consultas/{consulta}', [ConsultaController::class, 'update'])->name('consultas.update');
    Route::delete('consultas/{consulta}', [ConsultaController::class, 'destroy'])->name('consultas.destroy');

    Route::post('citas', [CitaController::class, 'store'])->name('citas.store');
    Route::put('citas/{cita}', [CitaController::class, 'update'])->name('citas.update');
    Route::patch('citas/{cita}/estado', [CitaController::class, 'updateEstado'])->name('citas.estado');
});

// Rutas exclusivas del rol admin: eliminación y copias de seguridad.
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::delete('historia_clinicas/{id}', [HistoriaClinicaController::class,'destroy'])->name('historia_clinicas.destroy');
    Route::delete('citas/{cita}', [CitaController::class, 'destroy'])->name('citas.destroy');

    // Ruta protegida: generación de respaldo (BackupController@generate) para el módulo de copias de seguridad.
    Route::post('backups/generate', [BackupController::class, 'generate'])->name('backups.generate');
    // Ruta protegida: listado de respaldos (BackupController@index) para el módulo de copias de seguridad.
    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
});
