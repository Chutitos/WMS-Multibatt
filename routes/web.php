<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Warehouse\PreparationController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //ADMIN
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');
        Route::get('/usuarios/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');
        Route::get('/usuarios/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/usuarios/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ADMIN + JEFE
    Route::middleware(['role:admin,jefe_bodega'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/liberar', [OrderController::class, 'liberar'])->name('orders.liberar');
    });

    // ADMIN + BODEGUERO
    Route::middleware(['role:admin,bodeguero'])->group(function () {
        Route::get('/bodega', [PreparationController::class, 'index'])->name('bodega.index');
        Route::get('/bodega/preparando', [PreparationController::class, 'preparando'])->name('bodega.preparando');
        Route::get('/bodega/listo', [PreparationController::class, 'listo'])->name('bodega.listo');
        Route::patch('/orders/{order}/preparar', [PreparationController::class, 'preparar'])->name('orders.preparar');
        Route::patch('/orders/{order}/confirmar', [PreparationController::class, 'confirmar'])->name('orders.confirmar');
        Route::patch('/orders/{order}/entregar', [PreparationController::class, 'entregar'])->name('orders.entregar');
    });
});

require __DIR__ . '/auth.php';
