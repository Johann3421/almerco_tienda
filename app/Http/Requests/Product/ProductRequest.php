<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:255', 'unique:products'],
            'part_number' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'numeric'],
            'discount' => ['required', 'numeric'],
            'previous_price' => ['required', 'numeric'],
            'manufacturer_url' => ['required', 'string', 'url'],

            // Campos booleanos
            'on_sale' => ['required', 'boolean'],
            'on_promotion' => ['required', 'boolean'],
            'active' => ['required', 'boolean'],

            'subgroups' => ['required', 'array'],
            'filters' => ['required', 'array'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es requerido',
            'slug.required' => 'El slug del producto es requerido',
        ];
    }
}
