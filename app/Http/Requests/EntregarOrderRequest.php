<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntregarOrderRequest extends FormRequest
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
        if ($this->route('order')->tipo_entrega === 'retiro') {
            return [
                'retirado_por_nombre' => 'required|string|max:255',
                'retirado_por_rut' => 'nullable|string|max:20',
            ];
        }

        return [
            'transportista' => 'required|string|max:255',
            'guia_despacho' => 'nullable|string|max:100',
        ];
    }
}
