<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductVisibilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'visibility' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'El id del producto es requerido',
            'id.integer' => 'El id del producto debe ser un número entero',
            'visibility.required' => 'La visibilidad del producto es requerida',
            'visibility.boolean' => 'La visibilidad del producto debe ser un booleano',
        ];
    }
}
