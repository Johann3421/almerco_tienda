<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'icon' => '',
            'featured' => 'required|boolean',
            'active' => 'required|boolean',
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
            'name.required' => 'El nombre es requerido',
            'description.required' => 'La descripción es requerida',
            'slug.required' => 'El slug es requerido',
            'slug.unique' => 'El slug ya existe',
            'image.required' => 'La imagen es requerida',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, svg',
            'image.max' => 'La imagen no debe pesar más de 6MB',
            'featured.required' => 'El campo destacado es requerido',
        ];
    }
}
