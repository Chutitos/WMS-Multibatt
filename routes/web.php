<?php

use App\Http\Controllers\Admin\ErpIntegrationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\WarehouseLocationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Warehouse\PreparationController;
use App\Http\Controllers\Warehouse\ProductLocationController;
use App\Http\Controllers\Warehouse\PickingController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// La raíz lleva directo al trabajo: al dashboard si hay sesión, al
// login si no. Va en un controlador (no closure) para que
// `php artisan route:cache` funcione en el deploy.
Route::get('/', [DashboardController::class, 'root']);

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

        // Eliminar una existencia es solo de admin: jefe y bodeguero
        // corrigen editando (queda trazado), no borrando.
        Route::delete('/existencias/{productLocation}', [ProductLocationController::class, 'destroy'])->name('product-locations.destroy');

        Route::get('/integracion', [ErpIntegrationController::class, 'index'])->name('erp.index');
    });

    // ADMIN + JEFE: gestión operativa de la estructura física de bodega.
    // El bodeguero SOLO SACA baterías (picking): agregar o corregir
    // existencias es responsabilidad del jefe o del admin.
    Route::middleware(['role:admin,jefe_bodega'])->group(function () {
        Route::post('/ubicaciones', [WarehouseLocationController::class, 'store'])->name('locations.store');
        Route::patch('/ubicaciones/{warehouseLocation}', [WarehouseLocationController::class, 'update'])->name('locations.update');

        Route::get('/existencias/historial', [ProductLocationController::class, 'historial'])->name('product-locations.historial');
        Route::get('/existencias/create', [ProductLocationController::class, 'create'])->name('product-locations.create');
        Route::post('/existencias', [ProductLocationController::class, 'store'])->name('product-locations.store');
        Route::get('/existencias/{productLocation}/edit', [ProductLocationController::class, 'edit'])->name('product-locations.edit');
        Route::patch('/existencias/{productLocation}', [ProductLocationController::class, 'update'])->name('product-locations.update');
    });

    // Mapa de bodega: los 3 roles pueden consultarlo; solo admin puede editarlo
    // (crear/mover ubicaciones vive en el grupo admin de arriba).
    Route::get('/ubicaciones', [WarehouseLocationController::class, 'index'])->name('locations.index');
    Route::get('/ubicaciones/{warehouseLocation}', [WarehouseLocationController::class, 'show'])->name('locations.show');

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
    Route::get('/orders/{order}/imprimir', [OrderController::class, 'imprimir'])->name('orders.imprimir');

    // ADMIN + BODEGUERO
    Route::middleware(['role:admin,bodeguero'])->group(function () {
        Route::get('/bodega', [PreparationController::class, 'index'])->name('bodega.index');
        Route::get('/bodega/preparando', [PreparationController::class, 'preparando'])->name('bodega.preparando');
        Route::get('/bodega/listo', [PreparationController::class, 'listo'])->name('bodega.listo');
        Route::patch('/orders/{order}/preparar', [PreparationController::class, 'preparar'])->name('orders.preparar');
        Route::patch('/orders/{order}/confirmar', [PreparationController::class, 'confirmar'])->name('orders.confirmar');
        Route::patch('/orders/{order}/entregar', [PreparationController::class, 'entregar'])->name('orders.entregar');

        Route::get('/orders/{order}/picking', [PickingController::class, 'show'])->name('orders.picking');
        Route::post('/orders/{order}/picking/escanear', [PickingController::class, 'escanear'])->name('orders.picking.escanear');
    });

    // Existencias en modo consulta: los 3 roles pueden ver dónde está
    // cada batería (el bodeguero lo necesita para encontrarlas).
    Route::middleware(['role:admin,jefe_bodega,bodeguero'])->group(function () {
        Route::get('/existencias', [ProductLocationController::class, 'index'])->name('product-locations.index');
    });
});

require __DIR__ . '/auth.php';
