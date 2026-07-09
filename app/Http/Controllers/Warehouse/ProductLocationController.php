<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductLocation;
use App\Models\ProductLocationEvent;
use App\Models\WarehouseLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductLocationController extends Controller
{
    public function index(Request $request)
    {
        // Se listan todas (incluidas las agotadas en 0) para poder corregir
        // un error de carga aunque ya no tengan existencia disponible.
        $query = ProductLocation::with(['product', 'warehouseLocation'])
            ->orderByDesc('cantidad')
            ->orderBy('fecha_ingreso');

        if ($request->filled('q')) {
            $texto = $request->string('q');
            $query->where(function ($q) use ($texto) {
                $q->whereHas('product', function ($p) use ($texto) {
                    $p->where('name', 'like', "%{$texto}%")
                        ->orWhere('sku', 'like', "%{$texto}%")
                        ->orWhere('marca', 'like', "%{$texto}%");
                })->orWhereHas('warehouseLocation', function ($u) use ($texto) {
                    $u->where('nombre', 'like', "%{$texto}%")
                        ->orWhere('codigo', 'like', "%{$texto}%");
                });
            });
        }

        if ($request->boolean('recarga')) {
            // El plazo de recarga es por producto (meses_recarga), así que
            // se resuelve en PHP; el volumen de una bodega física lo permite.
            $ids = $query->clone()
                ->where('cantidad', '>', 0)
                ->get()
                ->filter(fn ($pl) => $pl->necesitaRecarga())
                ->pluck('id');

            $query->whereIn('id', $ids);
        }

        $productLocations = $query->paginate(20)->withQueryString();

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
            'product_id' => ['required', Rule::exists('products', 'id')->where('active', true)],
            'warehouse_location_id' => ['required', Rule::exists('warehouse_locations', 'id')->where('activa', true)],
            'columna' => 'nullable|integer|min:1|required_with:nivel',
            'nivel' => 'nullable|integer|min:1|required_with:columna',
            'lote' => 'nullable|string|max:100',
            'fecha_ingreso' => 'required|date',
            'cantidad' => 'required|integer|min:1',
        ], [
            'product_id.exists' => 'El producto seleccionado no existe o está inactivo.',
            'warehouse_location_id.exists' => 'La ubicación seleccionada no existe o está inactiva.',
            'columna.required_with' => 'Si indicas el nivel también debes indicar la columna.',
            'nivel.required_with' => 'Si indicas la columna también debes indicar el nivel.',
        ]);

        $this->validarPuesto($validated);

        DB::transaction(function () use ($validated) {
            $productLocation = ProductLocation::create($validated);

            ProductLocationEvent::create([
                'product_location_id' => $productLocation->id,
                'user_id' => Auth::id(),
                'accion' => 'creada',
                'detalle' => $this->descripcionExistencia($productLocation),
            ]);
        });

        // Si la asignación se hizo desde la grilla del estante, se vuelve
        // ahí para ver el pallet recién puesto en su puesto.
        if ($request->boolean('volver_al_estante')) {
            return redirect()->route('locations.show', $validated['warehouse_location_id'])
                ->with('success', 'Pallet asignado al puesto correctamente.');
        }

        return redirect()->route('product-locations.index')
            ->with('success', 'Existencia asignada a la ubicación correctamente.');
    }

    public function edit(ProductLocation $productLocation)
    {
        // Se incluye el producto/ubicación actual aunque esté inactivo, para
        // que el formulario no llegue con el select vacío y se cambie el
        // dato sin querer al guardar.
        $products = Product::where('active', true)
            ->orWhere('id', $productLocation->product_id)
            ->orderBy('name')
            ->get();
        $locations = WarehouseLocation::where('activa', true)
            ->orWhere('id', $productLocation->warehouse_location_id)
            ->orderBy('nombre')
            ->get();

        return view('product-locations.edit', compact('productLocation', 'products', 'locations'));
    }

    public function update(Request $request, ProductLocation $productLocation)
    {
        // Mantener el producto/ubicación actual está permitido aunque esté
        // inactivo (el registro ya existía); cambiar a uno inactivo, no.
        $reglaProducto = Rule::exists('products', 'id');
        if ((int) $request->input('product_id') !== $productLocation->product_id) {
            $reglaProducto->where('active', true);
        }

        $reglaUbicacion = Rule::exists('warehouse_locations', 'id');
        if ((int) $request->input('warehouse_location_id') !== $productLocation->warehouse_location_id) {
            $reglaUbicacion->where('activa', true);
        }

        $validated = $request->validate([
            'product_id' => ['required', $reglaProducto],
            'warehouse_location_id' => ['required', $reglaUbicacion],
            'columna' => 'nullable|integer|min:1|required_with:nivel',
            'nivel' => 'nullable|integer|min:1|required_with:columna',
            'lote' => 'nullable|string|max:100',
            'fecha_ingreso' => 'required|date',
            'cantidad' => 'required|integer|min:0',
        ], [
            'product_id.exists' => 'El producto seleccionado no existe o está inactivo.',
            'warehouse_location_id.exists' => 'La ubicación seleccionada no existe o está inactiva.',
            'columna.required_with' => 'Si indicas el nivel también debes indicar la columna.',
            'nivel.required_with' => 'Si indicas la columna también debes indicar el nivel.',
        ]);

        // Si el formulario no traía los campos de puesto se conservan los
        // valores actuales en vez de borrarlos.
        $validated['columna'] = $validated['columna'] ?? null;
        $validated['nivel'] = $validated['nivel'] ?? null;

        $this->validarPuesto($validated, $productLocation);

        $antes = $this->camposComparables($productLocation);

        DB::transaction(function () use ($productLocation, $validated, $antes) {
            $productLocation->update($validated);
            $productLocation->refresh();

            $despues = $this->camposComparables($productLocation);

            $cambios = [];
            foreach ($antes as $campo => $valorAntes) {
                if ((string) $valorAntes !== (string) $despues[$campo]) {
                    $cambios[] = "{$campo}: {$valorAntes} → {$despues[$campo]}";
                }
            }

            if ($cambios !== []) {
                ProductLocationEvent::create([
                    'product_location_id' => $productLocation->id,
                    'user_id' => Auth::id(),
                    'accion' => 'editada',
                    'detalle' => implode('; ', $cambios),
                ]);
            }
        });

        return redirect()->route('product-locations.index')
            ->with('success', 'Existencia actualizada correctamente.');
    }

    public function destroy(ProductLocation $productLocation)
    {
        DB::transaction(function () use ($productLocation) {
            // El evento guarda el snapshot en texto porque al eliminar la
            // existencia su FK queda en null (nullOnDelete).
            ProductLocationEvent::create([
                'product_location_id' => $productLocation->id,
                'user_id' => Auth::id(),
                'accion' => 'eliminada',
                'detalle' => $this->descripcionExistencia($productLocation),
            ]);

            $productLocation->delete();
        });

        return redirect()->route('product-locations.index')
            ->with('success', 'Existencia eliminada.');
    }

    public function historial()
    {
        $eventos = ProductLocationEvent::with('user')
            ->latest()
            ->paginate(30);

        return view('product-locations.historial', compact('eventos'));
    }

    /**
     * Reglas del puesto físico: debe caber dentro del rack (columnas x
     * niveles) y en cada puesto va un solo pallet con existencia.
     *
     * @param array<string, mixed> $validated
     */
    private function validarPuesto(array $validated, ?ProductLocation $excluir = null): void
    {
        if (empty($validated['columna']) || empty($validated['nivel'])) {
            return;
        }

        $rack = WarehouseLocation::find($validated['warehouse_location_id']);

        if ($validated['columna'] > $rack->columnas || $validated['nivel'] > $rack->niveles) {
            throw ValidationException::withMessages([
                'columna' => "Ese puesto no existe: \"{$rack->nombre}\" tiene {$rack->columnas} columna(s) y {$rack->niveles} nivel(es).",
            ]);
        }

        $ocupado = ProductLocation::where('warehouse_location_id', $rack->id)
            ->where('columna', $validated['columna'])
            ->where('nivel', $validated['nivel'])
            ->where('cantidad', '>', 0)
            ->when($excluir, fn ($q) => $q->where('id', '!=', $excluir->id))
            ->exists();

        if ($ocupado) {
            throw ValidationException::withMessages([
                'columna' => "El puesto columna {$validated['columna']}, nivel {$validated['nivel']} ya tiene un pallet con existencia.",
            ]);
        }
    }

    private function descripcionExistencia(ProductLocation $productLocation): string
    {
        $productLocation->loadMissing(['product', 'warehouseLocation']);

        $lote = $productLocation->lote ? ", lote {$productLocation->lote}" : '';
        $puesto = $productLocation->puesto() ? " ({$productLocation->puesto()})" : '';

        return "{$productLocation->product->name} en {$productLocation->warehouseLocation->nombre} "
            . "({$productLocation->warehouseLocation->codigo}){$puesto}{$lote}, "
            . "ingreso {$productLocation->fecha_ingreso->format('d-m-Y')}: "
            . "{$productLocation->cantidad} unidades";
    }

    /**
     * @return array<string, string|int>
     */
    private function camposComparables(ProductLocation $productLocation): array
    {
        $productLocation->load(['product', 'warehouseLocation']);

        return [
            'producto' => $productLocation->product->name,
            'ubicación' => $productLocation->warehouseLocation->codigo,
            'puesto' => $productLocation->puesto() ?: '-',
            'lote' => $productLocation->lote ?: '-',
            'fecha ingreso' => $productLocation->fecha_ingreso->format('d-m-Y'),
            'cantidad' => $productLocation->cantidad,
        ];
    }
}
