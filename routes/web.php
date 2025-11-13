<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\PropietarioController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\VacunaController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ✅ Rutas públicas para Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ✅ Rutas protegidas
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

    Route::get('citas/list', [CitaController::class, 'list'])->name('citas.list');
    Route::patch('citas/{cita}/estado', [CitaController::class, 'updateEstado'])->name('citas.estado');

    Route::resource('consultas', ConsultaController::class);
    Route::resource('citas', CitaController::class);
    Route::resource('mascotas', MascotaController::class);
    Route::resource('propietarios', PropietarioController::class);
    Route::resource('tratamientos', TratamientoController::class);
    Route::resource('vacunas', VacunaController::class);

    Route::post('backups', [BackupController::class, 'store'])->name('backups.store');

    // ✅ Rutas Historia Clínica con AJAX
    Route::get('historia_clinicas/list', [HistoriaClinicaController::class,'list'])->name('historia_clinicas.list');
    Route::get('historia_clinicas/{id}', [HistoriaClinicaController::class,'show'])->name('historia_clinicas.show');
    Route::post('historia_clinicas', [HistoriaClinicaController::class,'store'])->name('historia_clinicas.store');
    Route::put('historia_clinicas/{id}', [HistoriaClinicaController::class,'update'])->name('historia_clinicas.update');
    Route::delete('historia_clinicas/{id}', [HistoriaClinicaController::class,'destroy'])->name('historia_clinicas.destroy');
    Route::get('historia_clinicas/{historia}/consultas', [ConsultaController::class, 'porHistoria'])->name('historia_clinicas.consultas');

});
