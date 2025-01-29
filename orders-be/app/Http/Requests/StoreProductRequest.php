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
        $user = auth()->user();
        return $user && $user->tokenCan("admin");
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
            'products.*.price' => ['required_with:products', 'numeric', 'min:0'],
            'products.*.quantity' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
