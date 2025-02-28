<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'sku' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'specification' => ['nullable', 'string'], // Es opcional
            'price' => ['required', 'numeric'],
            'previous_price' => ['required', 'numeric'],
            'discount' => ['required', 'numeric'],
            'stock' => ['required', 'numeric'],

            // Campos booleanos
            'on_sale' => ['required', 'boolean'],
            'on_promotion' => ['required', 'boolean'],
            'active' => ['required', 'boolean'],

            'subgroups' => ['required', 'string'],
            'filters' => ['required', 'string'],
            // Imágenes opcionales
            'image1' => 'nullable|image|mimes:jpeg,png,jpg|max:10048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg|max:10048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg|max:10048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg|max:10048',
            'manufacturer_url' => ['nullable', 'string', 'url'],
        ];
    }
}
