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
            'description' => 'nullable|string',
            'active' => 'boolean',
        ];
    }
}
