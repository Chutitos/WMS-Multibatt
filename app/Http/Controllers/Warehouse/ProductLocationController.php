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
            'product_id' => ['required', Rule::exists('products', 'id')->where('active', true)],
            'warehouse_location_id' => ['required', Rule::exists('warehouse_locations', 'id')->where('activa', true)],
            'lote' => 'nullable|string|max:100',
            'fecha_ingreso' => 'required|date',
            'cantidad' => 'required|integer|min:1',
        ], [
            'product_id.exists' => 'El producto seleccionado no existe o está inactivo.',
            'warehouse_location_id.exists' => 'La ubicación seleccionada no existe o está inactiva.',
        ]);

        DB::transaction(function () use ($validated) {
            $productLocation = ProductLocation::create($validated);

            ProductLocationEvent::create([
                'product_location_id' => $productLocation->id,
                'user_id' => Auth::id(),
                'accion' => 'creada',
                'detalle' => $this->descripcionExistencia($productLocation),
            ]);
        });

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
            'lote' => 'nullable|string|max:100',
            'fecha_ingreso' => 'required|date',
            'cantidad' => 'required|integer|min:0',
        ], [
            'product_id.exists' => 'El producto seleccionado no existe o está inactivo.',
            'warehouse_location_id.exists' => 'La ubicación seleccionada no existe o está inactiva.',
        ]);

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

    private function descripcionExistencia(ProductLocation $productLocation): string
    {
        $productLocation->loadMissing(['product', 'warehouseLocation']);

        $lote = $productLocation->lote ? ", lote {$productLocation->lote}" : '';

        return "{$productLocation->product->name} en {$productLocation->warehouseLocation->nombre} "
            . "({$productLocation->warehouseLocation->codigo}){$lote}, "
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
            'lote' => $productLocation->lote ?: '-',
            'fecha ingreso' => $productLocation->fecha_ingreso->format('d-m-Y'),
            'cantidad' => $productLocation->cantidad,
        ];
    }
}
