<?php

namespace Tests;

use App\Models\Product;
use App\Models\ProductLocation;
use App\Models\Role;
use App\Models\User;
use App\Models\WarehouseLocation;

trait CreatesWmsTestData
{
    protected function makeUser(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    protected function makeProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'sku' => 'SKU-' . uniqid(),
            'name' => 'Producto de prueba',
            'active' => true,
        ], $overrides));
    }

    protected function makeLocation(array $overrides = []): WarehouseLocation
    {
        return WarehouseLocation::create(array_merge([
            'nombre' => 'Ubicación de prueba',
            'codigo' => 'TEST-' . uniqid(),
        ], $overrides));
    }

    protected function stockProductAt(Product $product, WarehouseLocation $location, int $cantidad, ?string $fechaIngreso = null): ProductLocation
    {
        return ProductLocation::create([
            'product_id' => $product->id,
            'warehouse_location_id' => $location->id,
            'fecha_ingreso' => $fechaIngreso ?? now()->toDateString(),
            'cantidad' => $cantidad,
        ]);
    }
}
