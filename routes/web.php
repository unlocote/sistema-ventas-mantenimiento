<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EncuestaController;
use App\Http\Controllers\AsignarEncuestaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ResponderEncuestaController;


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth:cliente'])->group(function () {
    Route::resource('surveys-answer', ResponderEncuestaController::class)->except(['create', 'destroy']);
});

Route::middleware(['auth:empleado', 'empleado.rol:Administrador'])->group(function () {
    Route::resource('employees', EmpleadoController::class);
    Route::resource('positions', CargoController::class);
    Route::get('/contracts/{id}/download', [EmpleadoController::class, 'download'])->name('contracts.download');
});

Route::middleware(['auth:empleado', 'empleado.rol:Coordinador,Administrador'])->group(function () {
    Route::resource('providers', ProveedorController::class);
    Route::resource('products', ProductoController::class);
    Route::resource('surveys', EncuestaController::class);
    Route::resource('surveys-assign', AsignarEncuestaController::class)->except(['create', 'show', 'edit']);
    
});

Route::middleware(['auth:empleado', 'empleado.rol:Coordinador,Administrador,Vendedor'])->group(function () {
    Route::resource('clients', ClienteController::class);
    Route::resource('sales', VentaController::class);
    Route::resource('purchases', CompraController::class);
});



Route::get('/', [HomeController::class, 'index'])
    ->middleware('multi.auth:empleado,cliente')
    ->name('home');
/*
Route::resource('employees', EmpleadoController::class);
Route::resource('positions', CargoController::class);
Route::get('/contracts/{id}/download', [EmpleadoController::class, 'download'])->name('contracts.download');
Route::resource('providers', ProveedorController::class);
Route::resource('products', ProductoController::class);
Route::resource('clients', ClienteController::class);
Route::resource('sales', VentaController::class);
*/





