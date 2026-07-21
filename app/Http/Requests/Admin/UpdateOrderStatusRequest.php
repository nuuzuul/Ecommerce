<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([
                    'diproses',
                    'siap_diambil',
                    'dikirim',
                    'selesai',
                    'dibatalkan',
                ]),
            ],

            'note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $order = $this->route('order');
                $status = $this->string('status')->toString();

                if (! $order) {
                    return;
                }

                if ($order->status === 'dibatalkan') {
                    $validator->errors()->add(
                        'status',
                        'Pesanan yang sudah dibatalkan tidak dapat diaktifkan kembali.'
                    );

                    return;
                }

                if (
                    $order->status === 'selesai'
                    && $status === 'dibatalkan'
                ) {
                    $validator->errors()->add(
                        'status',
                        'Pesanan yang sudah selesai tidak dapat dibatalkan.'
                    );
                }

                if (
                    $order->delivery_method === 'pickup'
                    && $status === 'dikirim'
                ) {
                    $validator->errors()->add(
                        'status',
                        'Pesanan ambil sendiri tidak dapat diberi status dikirim.'
                    );
                }

                if (
                    $order->delivery_method === 'delivery'
                    && $status === 'siap_diambil'
                ) {
                    $validator->errors()->add(
                        'status',
                        'Pesanan kirim tidak dapat diberi status siap diambil.'
                    );
                }

                if (
                    ! in_array($status, ['diproses', 'dibatalkan'], true)
                    && $order->payment_status !== 'sudah_bayar'
                ) {
                    $validator->errors()->add(
                        'status',
                        'Pembayaran harus diverifikasi sebelum pesanan dilanjutkan.'
                    );
                }
            },
        ];
    }
}