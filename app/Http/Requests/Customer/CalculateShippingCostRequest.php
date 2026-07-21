<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CalculateShippingCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() === true;
    }

    public function rules(): array
    {
        return [
            'destination_token' => [
                'required',
                'string',
                'max:10000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'destination_token.required' =>
                'Pilih lokasi tujuan terlebih dahulu.',
        ];
    }
}