<?php

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\WarehouseLocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Warehouse\PreparationController;
use App\Http\Controllers\Warehouse\ProductLocationController;
use App\Http\Controllers\Warehouse\PickingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// La raíz lleva directo al trabajo: al dashboard si hay sesión,
// al login si no (antes mostraba la página de bienvenida de Laravel).
Route::get('/', function () {
    return redirect(auth()->check() ? route('dashboard') : route('login'));
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
        Route::patch('/usuarios/{user}/toggle-activo', [UserController::class, 'toggleActivo'])->name('users.toggle-activo');

        Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
        Route::get('/productos/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/productos', [ProductController::class, 'store'])->name('products.store');
        Route::get('/productos/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::patch('/productos/{product}', [ProductController::class, 'update'])->name('products.update');

        Route::post('/ubicaciones', [WarehouseLocationController::class, 'store'])->name('locations.store');
        Route::patch('/ubicaciones/{warehouseLocation}', [WarehouseLocationController::class, 'update'])->name('locations.update');

        // Eliminar una existencia y ver su historial son solo de admin;
        // el bodeguero corrige editando (queda trazado), no borrando.
        Route::get('/existencias/historial', [ProductLocationController::class, 'historial'])->name('product-locations.historial');
        Route::delete('/existencias/{productLocation}', [ProductLocationController::class, 'destroy'])->name('product-locations.destroy');
    });

    // Mapa de bodega: los 3 roles pueden consultarlo; solo admin puede editarlo
    // (crear/mover ubicaciones vive en el grupo admin de arriba).
    Route::get('/ubicaciones', [WarehouseLocationController::class, 'index'])->name('locations.index');

    // ADMIN + JEFE
    Route::middleware(['role:admin,jefe_bodega'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::patch('/orders/{order}/liberar', [OrderController::class, 'liberar'])->name('orders.liberar');
        Route::patch('/orders/{order}/cancelar', [OrderController::class, 'cancelar'])->name('orders.cancelar');
    });

    // Detalle de orden: accesible por los 3 roles, la autorización fina
    // (bodeguero solo si la orden ya salió de "creado") vive en OrderPolicy::view().
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // ADMIN + BODEGUERO
    Route::middleware(['role:admin,bodeguero'])->group(function () {
        Route::get('/bodega', [PreparationController::class, 'index'])->name('bodega.index');
        Route::get('/bodega/preparando', [PreparationController::class, 'preparando'])->name('bodega.preparando');
        Route::get('/bodega/listo', [PreparationController::class, 'listo'])->name('bodega.listo');
        Route::patch('/orders/{order}/preparar', [PreparationController::class, 'preparar'])->name('orders.preparar');
        Route::patch('/orders/{order}/confirmar', [PreparationController::class, 'confirmar'])->name('orders.confirmar');
        Route::patch('/orders/{order}/entregar', [PreparationController::class, 'entregar'])->name('orders.entregar');

        Route::get('/existencias', [ProductLocationController::class, 'index'])->name('product-locations.index');
        Route::get('/existencias/create', [ProductLocationController::class, 'create'])->name('product-locations.create');
        Route::post('/existencias', [ProductLocationController::class, 'store'])->name('product-locations.store');
        Route::get('/existencias/{productLocation}/edit', [ProductLocationController::class, 'edit'])->name('product-locations.edit');
        Route::patch('/existencias/{productLocation}', [ProductLocationController::class, 'update'])->name('product-locations.update');

        Route::get('/orders/{order}/picking', [PickingController::class, 'show'])->name('orders.picking');
        Route::post('/orders/{order}/picking/escanear', [PickingController::class, 'escanear'])->name('orders.picking.escanear');
    });
});

require __DIR__ . '/auth.php';
