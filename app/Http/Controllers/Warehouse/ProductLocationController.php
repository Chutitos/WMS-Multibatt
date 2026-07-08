<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductLocation;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;

class ProductLocationController extends Controller
{
    public function index()
    {
        // Se listan todas (incluidas las agotadas en 0) para poder corregir
        // un error de carga aunque ya no tengan existencia disponible.
        $productLocations = ProductLocation::with(['product', 'warehouseLocation'])
            ->orderByDesc('cantidad')
            ->orderBy('fecha_ingreso')
            ->paginate(20);

        return view('product-locations.index', compact('productLocations'));
    }

    public function create()
    {
        $products = Product::where('active', true)->orderBy('name')->get();
        $locations = WarehouseLocation::where('activa', true)->orderBy('nombre')->get();

        return view('product-locations.create', compact('products', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_location_id' => 'required|exists:warehouse_locations,id',
            'lote' => 'nullable|string|max:100',
            'fecha_ingreso' => 'required|date',
            'cantidad' => 'required|integer|min:1',
        ]);

        ProductLocation::create($validated);

        return redirect()->route('product-locations.index')
            ->with('success', 'Existencia asignada a la ubicación correctamente.');
    }

    public function edit(ProductLocation $productLocation)
    {
        $products = Product::where('active', true)->orderBy('name')->get();
        $locations = WarehouseLocation::where('activa', true)->orderBy('nombre')->get();

        return view('product-locations.edit', compact('productLocation', 'products', 'locations'));
    }

    public function update(Request $request, ProductLocation $productLocation)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_location_id' => 'required|exists:warehouse_locations,id',
            'lote' => 'nullable|string|max:100',
            'fecha_ingreso' => 'required|date',
            'cantidad' => 'required|integer|min:0',
        ]);

        $productLocation->update($validated);

        return redirect()->route('product-locations.index')
            ->with('success', 'Existencia actualizada correctamente.');
    }

    public function destroy(ProductLocation $productLocation)
    {
        $productLocation->delete();

        return redirect()->route('product-locations.index')
            ->with('success', 'Existencia eliminada.');
    }
}
