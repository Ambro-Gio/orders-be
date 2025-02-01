<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexOrderRequest extends FormRequest
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
            'date_start' => [
                'bail',
                'date_format:Y-m-d\TH:i:s.u\Z',
            ],
            'date_end' => [
                'bail',
                'date_format:Y-m-d\TH:i:s.u\Z',
                'after:date_start'
            ],
            'name' => ['bail', 'string', 'max:255'],
            'description' => ['bail', 'string'],
        ];
    }
}
