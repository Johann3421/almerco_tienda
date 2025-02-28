<?php

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'customer_address' => ['required', 'string', 'max:255'],
            'customer_document_number' => ['required', 'string', 'max:20'],
            'customer_document_type' => ['required', 'numeric', 'max:2'],
            'order_status' => ['required', 'in:Pendiente de Pago,Pendiente de Entrega,Completado,Cancelado,Reembolsado'],
            'observation' => ['string'],
            'delivery_date' => ['date'],
            'items' => ['required', 'array'],
        ];
    }
}
