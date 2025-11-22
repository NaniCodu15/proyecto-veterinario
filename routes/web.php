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
Route::middleware(['auth'])->group(function () {

    // Ruta protegida: panel de control general atendido por DashboardController@index para el módulo principal.
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::middleware('admin')->group(function () {
        // Ruta protegida: eliminación de historia clínica (HistoriaClinicaController@destroy) desde el módulo de historias.
        Route::delete('historia_clinicas/{id}', [HistoriaClinicaController::class,'destroy'])->name('historia_clinicas.destroy');
        // Ruta protegida: eliminación de cita (CitaController@destroy) para gestión administrativa.
        Route::delete('citas/{cita}', [CitaController::class, 'destroy'])->name('citas.destroy');
        // Ruta protegida: generación de respaldo (BackupController@generate) para el módulo de copias de seguridad.
        Route::post('backups/generate', [BackupController::class, 'generate'])->name('backups.generate');
        // Ruta protegida: listado de respaldos (BackupController@index) para el módulo de copias de seguridad.
        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
    });

    Route::middleware('asistente')->group(function () {
        // Ruta protegida: creación y actualización de historias clínicas por asistentes.
        Route::post('historia_clinicas', [HistoriaClinicaController::class,'store'])->name('historia_clinicas.store');
        Route::put('historia_clinicas/{id}', [HistoriaClinicaController::class,'update'])->name('historia_clinicas.update');

        // Rutas protegidas: gestión de consultas.
        Route::get('consultas', [ConsultaController::class, 'index'])->name('consultas.index');
        Route::post('consultas', [ConsultaController::class, 'store'])->name('consultas.store');
        Route::get('consultas/{consulta}', [ConsultaController::class, 'show'])->name('consultas.show');
        Route::put('consultas/{consulta}', [ConsultaController::class, 'update'])->name('consultas.update');
        Route::delete('consultas/{consulta}', [ConsultaController::class, 'destroy'])->name('consultas.destroy');

        // Rutas protegidas: registro y edición de citas.
        Route::get('citas/create', [CitaController::class, 'create'])->name('citas.create');
        Route::post('citas', [CitaController::class, 'store'])->name('citas.store');
        Route::get('citas/{cita}/edit', [CitaController::class, 'edit'])->name('citas.edit');
        Route::put('citas/{cita}', [CitaController::class, 'update'])->name('citas.update');
        Route::patch('citas/{cita}', [CitaController::class, 'update'])->name('citas.update.patch');
        // Ruta protegida: actualización de estado de cita (CitaController@updateEstado) para flujo de gestión de citas.
        Route::patch('citas/{cita}/estado', [CitaController::class, 'updateEstado'])->name('citas.estado');

        // Ruta protegida: listado de consultas de una historia (ConsultaController@porHistoria) en el módulo de consultas.
        Route::get('historia_clinicas/{historia}/consultas', [ConsultaController::class, 'porHistoria'])->name('historia_clinicas.consultas');
    });

    // Ruta protegida: listado JSON de citas (CitaController@list) para el módulo de citas.
    Route::get('citas/list', [CitaController::class, 'list'])->name('citas.list');
    // Ruta protegida: próximas citas pendientes (CitaController@upcoming) del módulo de citas.
    Route::get('citas/upcoming', [CitaController::class, 'upcoming'])->name('citas.upcoming');
    // Ruta protegida: consulta y detalle de citas.
    Route::get('citas', [CitaController::class, 'index'])->name('citas.index');
    Route::get('citas/{cita}', [CitaController::class, 'show'])->name('citas.show');

    // Rutas REST protegidas: CRUD de mascotas gestionadas por MascotaController para el módulo de mascotas.
    Route::resource('mascotas', MascotaController::class);
    // Rutas REST protegidas: CRUD de propietarios gestionadas por PropietarioController para el módulo de clientes.
    Route::resource('propietarios', PropietarioController::class);
    // Rutas REST protegidas: CRUD de vacunas gestionadas por VacunaController para el módulo de inmunizaciones.
    Route::resource('vacunas', VacunaController::class);

    // Ruta protegida: listado AJAX de historias clínicas (HistoriaClinicaController@list) para el módulo de historias.
    Route::get('historia_clinicas/list', [HistoriaClinicaController::class,'list'])->name('historia_clinicas.list');
    // Ruta protegida: vista detallada de historia clínica (HistoriaClinicaController@ver) utilizada en el módulo de historias.
    Route::get('historia_clinicas/{id}/ver', [HistoriaClinicaController::class, 'ver'])->name('historia_clinicas.ver');
    // Ruta protegida: generación/visualización de PDF (HistoriaClinicaController@pdf) para historias clínicas.
    Route::get('historia_clinicas/{historia}/pdf', [HistoriaClinicaController::class, 'pdf'])->name('historia_clinicas.pdf');
    // Ruta protegida: consulta específica de historia clínica (HistoriaClinicaController@show) para edición.
    Route::get('historia_clinicas/{id}', [HistoriaClinicaController::class,'show'])->name('historia_clinicas.show');

});
