<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'cliente_nombre' => 'required|string|max:255',
            'rut_cliente' => 'nullable|string|max:20',
            'tipo_entrega' => 'required|string',
            'observaciones' => 'nullable|string',
            'productos' => 'required|array|min:1',
            // distinct evita el mismo producto en dos líneas de la orden: el
            // picking no podría distinguir a cuál línea aplicar cada escaneo.
            'productos.*.product_id' => 'required|integer|exists:products,id|distinct',
            'productos.*.cantidad' => 'required|integer|min:1',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'productos.*.product_id.distinct' => 'No puedes agregar el mismo producto en dos líneas — súmalas en una sola.',
        ];
    }
}
