<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'products' => ['required', 'array'],
            'products.*.name' => ['required_with:products', 'string', 'max:255'],
            'products.*.price' => ['required_with:products', 'numeric'],
            'products.*.quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
