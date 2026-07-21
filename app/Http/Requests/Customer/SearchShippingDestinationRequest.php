<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class SearchShippingDestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() === true;
    }

    public function rules(): array
    {
        return [
            'search' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],

            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'search.required' =>
                'Masukkan nama kota, kecamatan, atau kelurahan.',

            'search.min' =>
                'Kata kunci lokasi minimal 3 karakter.',
        ];
    }
}