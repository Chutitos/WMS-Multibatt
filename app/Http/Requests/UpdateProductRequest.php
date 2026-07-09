<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->route('product'))],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('products', 'barcode')->ignore($this->route('product'))],
            'name' => 'required|string|max:255',
            'marca' => 'nullable|string|max:100',
            'tipo' => ['nullable', Rule::in(array_keys(\App\Models\Product::TIPOS))],
            'voltaje' => 'nullable|string|max:20',
            'capacidad_ah' => 'nullable|integer|min:1|max:5000',
            'meses_recarga' => 'required|integer|min:1|max:36',
            'stock_minimo' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ];
    }
}
