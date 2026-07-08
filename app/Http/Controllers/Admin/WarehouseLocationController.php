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
            'nombre' => 'nullable|string|max:255',
            'codigo' => 'nullable|string|max:50|unique:warehouse_locations,codigo',
        ]);

        // Un clic = un estante: si no viene nombre/código se generan solos
        // ("Estante 5" / "E-05") y después se puede renombrar desde el mapa.
        if (empty($validated['codigo'])) {
            $numero = $this->siguienteNumeroDisponible();
            $validated['codigo'] = sprintf('E-%02d', $numero);
            $validated['nombre'] = $validated['nombre'] ?? "Estante {$numero}";
        } elseif (empty($validated['nombre'])) {
            $validated['nombre'] = $validated['codigo'];
        }

        // Cada estante nuevo aparece en un lugar distinto del mapa para que
        // no queden apilados uno encima de otro al crearlos con un clic.
        $orden = WarehouseLocation::count();

        $location = WarehouseLocation::create([
            'nombre' => $validated['nombre'],
            'codigo' => $validated['codigo'],
            'pos_x' => 20 + ($orden % 6) * 145,
            'pos_y' => 20 + intdiv($orden, 6) * 105,
            'width' => 130,
            'height' => 90,
        ]);

        return response()->json($location);
    }

    private function siguienteNumeroDisponible(): int
    {
        $numero = WarehouseLocation::pluck('codigo')
            ->map(fn ($codigo) => preg_match('/(\d+)\s*$/', $codigo, $m) ? (int) $m[1] : 0)
            ->max() ?? 0;

        do {
            $numero++;
            $codigo = sprintf('E-%02d', $numero);
        } while (WarehouseLocation::where('codigo', $codigo)->exists());

        return $numero;
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
