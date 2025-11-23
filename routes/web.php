<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\PropietarioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BackupController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin,asistente'])->group(function () {
    Route::get('citas/list', [CitaController::class, 'list'])->name('citas.list');
    Route::get('citas/upcoming', [CitaController::class, 'upcoming'])->name('citas.upcoming');
    Route::get('citas', [CitaController::class, 'index'])->name('citas.index');
    Route::get('citas/{cita}', [CitaController::class, 'show'])->name('citas.show')->whereNumber('cita');

    Route::get('historia_clinicas/list', [HistoriaClinicaController::class, 'list'])->name('historia_clinicas.list');
    Route::get('historia_clinicas/{id}/ver', [HistoriaClinicaController::class, 'ver'])
        ->name('historia_clinicas.ver')
        ->whereNumber('id');
    Route::get('historia_clinicas/{historia}/pdf', [HistoriaClinicaController::class, 'pdf'])
        ->name('historia_clinicas.pdf')
        ->whereNumber('historia');
    Route::get('historia_clinicas/{id}', [HistoriaClinicaController::class, 'show'])
        ->name('historia_clinicas.show')
        ->whereNumber('id');
    Route::get('historia_clinicas/{historia}/consultas', [ConsultaController::class, 'porHistoria'])
        ->name('historia_clinicas.consultas')
        ->whereNumber('historia');

    Route::get('consultas', [ConsultaController::class, 'index'])->name('consultas.index');
    Route::get('consultas/{consulta}', [ConsultaController::class, 'show'])
        ->name('consultas.show')
        ->whereNumber('consulta');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::delete('historia_clinicas/{id}', [HistoriaClinicaController::class, 'destroy'])
        ->name('historia_clinicas.destroy')
        ->whereNumber('id');

    Route::delete('consultas/{consulta}', [ConsultaController::class, 'destroy'])
        ->name('consultas.destroy')
        ->whereNumber('consulta');

    Route::delete('citas/{cita}', [CitaController::class, 'destroy'])
        ->name('citas.destroy')
        ->whereNumber('cita');

    Route::post('backups/generate', [BackupController::class, 'generate'])->name('backups.generate');
    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
});

Route::middleware(['auth', 'role:admin,asistente'])->group(function () {
    Route::post('historia_clinicas', [HistoriaClinicaController::class, 'store'])->name('historia_clinicas.store');
    Route::put('historia_clinicas/{id}', [HistoriaClinicaController::class, 'update'])
        ->name('historia_clinicas.update')
        ->whereNumber('id');

    Route::post('consultas', [ConsultaController::class, 'store'])->name('consultas.store');
    Route::put('consultas/{consulta}', [ConsultaController::class, 'update'])
        ->name('consultas.update')
        ->whereNumber('consulta');

    Route::get('citas/create', [CitaController::class, 'create'])->name('citas.create');
    Route::get('citas/{cita}/edit', [CitaController::class, 'edit'])
        ->name('citas.edit')
        ->whereNumber('cita');
    Route::post('citas', [CitaController::class, 'store'])->name('citas.store');
    Route::put('citas/{cita}', [CitaController::class, 'update'])
        ->name('citas.update')
        ->whereNumber('cita');
    Route::patch('citas/{cita}/estado', [CitaController::class, 'updateEstado'])
        ->name('citas.estado')
        ->whereNumber('cita');

    Route::resource('mascotas', MascotaController::class);
    Route::resource('propietarios', PropietarioController::class);

    // Ruta protegida: generación de respaldo (BackupController@generate) para el módulo de copias de seguridad.
    Route::post('backups/generate', [BackupController::class, 'generate'])->name('backups.generate');
    // Ruta protegida: listado de respaldos (BackupController@index) para el módulo de copias de seguridad.
    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');

    // Ruta protegida: listado AJAX de historias clínicas (HistoriaClinicaController@list) para el módulo de historias.
    Route::get('historia_clinicas/list', [HistoriaClinicaController::class,'list'])->name('historia_clinicas.list');
    // Ruta protegida: vista detallada de historia clínica (HistoriaClinicaController@ver) utilizada en el módulo de historias.
    Route::get('historia_clinicas/{id}/ver', [HistoriaClinicaController::class, 'ver'])->name('historia_clinicas.ver');
    // Ruta protegida: generación/visualización de PDF (HistoriaClinicaController@pdf) para historias clínicas.
    Route::get('historia_clinicas/{historia}/pdf', [HistoriaClinicaController::class, 'pdf'])->name('historia_clinicas.pdf');
    // Ruta protegida: consulta específica de historia clínica (HistoriaClinicaController@show) para edición.
    Route::get('historia_clinicas/{id}', [HistoriaClinicaController::class,'show'])->name('historia_clinicas.show');
    // Ruta protegida: creación de historia clínica (HistoriaClinicaController@store) mediante AJAX.
    Route::post('historia_clinicas', [HistoriaClinicaController::class,'store'])->name('historia_clinicas.store');
    // Ruta protegida: actualización de historia clínica (HistoriaClinicaController@update) con datos validados.
    Route::put('historia_clinicas/{id}', [HistoriaClinicaController::class,'update'])->name('historia_clinicas.update');
    // Ruta protegida: eliminación de historia clínica (HistoriaClinicaController@destroy) desde el módulo de historias.
    Route::delete('historia_clinicas/{id}', [HistoriaClinicaController::class,'destroy'])->name('historia_clinicas.destroy');
    // Ruta protegida: listado de consultas de una historia (ConsultaController@porHistoria) en el módulo de consultas.
    Route::get('historia_clinicas/{historia}/consultas', [ConsultaController::class, 'porHistoria'])->name('historia_clinicas.consultas');

});
