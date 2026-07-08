<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;

class WarehouseLocationController extends Controller
{
    public function index()
    {
        $locations = WarehouseLocation::with(['productLocations' => function ($query) {
            $query->where('cantidad', '>', 0)->with('product')->orderBy('fecha_ingreso');
        }])->orderBy('id')->get();

        return view('locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:warehouse_locations,codigo',
        ]);

        $location = WarehouseLocation::create([
            'nombre' => $validated['nombre'],
            'codigo' => $validated['codigo'],
            'pos_x' => 20,
            'pos_y' => 20,
            'width' => 130,
            'height' => 90,
        ]);

        return response()->json($location);
    }

    public function update(Request $request, WarehouseLocation $warehouseLocation)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'codigo' => 'sometimes|string|max:50|unique:warehouse_locations,codigo,' . $warehouseLocation->id,
            'pos_x' => 'sometimes|integer',
            'pos_y' => 'sometimes|integer',
            'width' => 'sometimes|integer|min:60',
            'height' => 'sometimes|integer|min:60',
            'activa' => 'sometimes|boolean',
        ]);

        // No se puede desactivar una ubicación que todavía tiene productos:
        // quedaría stock "invisible" para asignaciones y correcciones.
        if (array_key_exists('activa', $validated) && ! $request->boolean('activa')) {
            $tieneStock = $warehouseLocation->productLocations()->where('cantidad', '>', 0)->exists();

            if ($tieneStock) {
                return response()->json([
                    'message' => 'No puedes desactivar una ubicación que aún tiene productos guardados. Reubica o corrige sus existencias primero.',
                ], 422);
            }
        }

        $warehouseLocation->update($validated);

        return response()->json($warehouseLocation);
    }
}
