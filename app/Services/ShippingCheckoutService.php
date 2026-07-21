<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use JsonException;
use RuntimeException;

class ShippingCheckoutService
{
    public function __construct(
        private readonly RajaOngkirService $rajaOngkir
    ) {
    }

    /**
     * Memverifikasi lokasi, berat, kurir, layanan,
     * dan tarif ongkir sebelum checkout.
     */
    public function verify(
        Cart $cart,
        string $destinationToken,
        string $shippingOptionToken
    ): array {
        $destination = $this->decryptToken(
            token: $destinationToken,
            field: 'destination_token'
        );

        $selectedOption = $this->decryptToken(
            token: $shippingOptionToken,
            field: 'shipping_option_token'
        );

        $destinationId = (int) (
            $destination['id'] ?? 0
        );

        $optionDestinationId = (int) (
            $selectedOption['destination']['id'] ?? 0
        );

        if (
            $destinationId <= 0
            || $destinationId !== $optionDestinationId
        ) {
            throw ValidationException::withMessages([
                'destination_token' =>
                    'Lokasi tujuan tidak sesuai. '
                    . 'Silakan pilih lokasi kembali.',
            ]);
        }

        $generatedAt = (int) (
            $selectedOption['generated_at'] ?? 0
        );

        if (
            $generatedAt <= 0
            || $generatedAt
                < now()->subMinutes(30)->timestamp
        ) {
            throw ValidationException::withMessages([
                'shipping_option_token' =>
                    'Pilihan ongkir sudah kedaluwarsa. '
                    . 'Silakan cek ongkir kembali.',
            ]);
        }

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang masih kosong.',
            ]);
        }

        $invalidWeightItem = $cart->items->first(
            fn ($item): bool =>
                ! $item->variant
                || (int) $item->variant->weight_grams <= 0
        );

        if ($invalidWeightItem) {
            throw ValidationException::withMessages([
                'cart' =>
                    'Terdapat produk yang belum mempunyai '
                    . 'berat pengiriman.',
            ]);
        }

        $totalWeight = (int) $cart->items->sum(
            fn ($item): int =>
                (int) $item->variant->weight_grams
                * (int) $item->quantity
        );

        $tokenWeight = (int) (
            $selectedOption['total_weight_grams'] ?? 0
        );

        if (
            $totalWeight <= 0
            || $totalWeight !== $tokenWeight
        ) {
            throw ValidationException::withMessages([
                'shipping_option_token' =>
                    'Isi atau berat keranjang telah berubah. '
                    . 'Silakan cek ongkir kembali.',
            ]);
        }

        try {
            $currentCosts = $this->rajaOngkir
                ->calculateFromStore(
                    destinationId: $destinationId,
                    weight: $totalWeight
                );
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages([
                'shipping_option_token' =>
                    $exception->getMessage(),
            ]);
        }

        $selectedCourierCode = strtolower(
            trim(
                (string) (
                    $selectedOption['courier_code'] ?? ''
                )
            )
        );

        $selectedService = trim(
            (string) (
                $selectedOption['service'] ?? ''
            )
        );

        $currentOption = collect($currentCosts)
            ->first(function (array $cost) use (
                $selectedCourierCode,
                $selectedService
            ): bool {
                $courierCode = strtolower(
                    trim(
                        (string) (
                            $cost['code'] ?? ''
                        )
                    )
                );

                $service = trim(
                    (string) (
                        $cost['service'] ?? ''
                    )
                );

                return $courierCode
                    === $selectedCourierCode
                    && strcasecmp(
                        $service,
                        $selectedService
                    ) === 0;
            });

        if (! $currentOption) {
            throw ValidationException::withMessages([
                'shipping_option_token' =>
                    'Layanan pengiriman yang dipilih '
                    . 'sudah tidak tersedia. '
                    . 'Silakan pilih layanan lain.',
            ]);
        }

        $currentCost = (int) (
            $currentOption['cost'] ?? 0
        );

        $tokenCost = (int) (
            $selectedOption['cost'] ?? 0
        );

        if (
            $currentCost <= 0
            || $currentCost !== $tokenCost
        ) {
            throw ValidationException::withMessages([
                'shipping_option_token' =>
                    'Tarif ongkir telah berubah dari Rp '
                    . number_format(
                        $tokenCost,
                        0,
                        ',',
                        '.'
                    )
                    . ' menjadi Rp '
                    . number_format(
                        $currentCost,
                        0,
                        ',',
                        '.'
                    )
                    . '. Silakan pilih ulang layanan pengiriman.',
            ]);
        }

        return [
            'destination_id' => $destinationId,

            'destination_label' => (string) (
                $destination['label'] ?? ''
            ),

            'destination_province' => (string) (
                $destination['province_name'] ?? ''
            ),

            'destination_city' => (string) (
                $destination['city_name'] ?? ''
            ),

            'destination_district' => (string) (
                $destination['district_name'] ?? ''
            ),

            'destination_subdistrict' => (string) (
                $destination['subdistrict_name'] ?? ''
            ),

            'destination_postal_code' => (string) (
                $destination['zip_code'] ?? ''
            ),

            'courier_code' => strtolower(
                (string) (
                    $currentOption['code'] ?? ''
                )
            ),

            'courier_name' => (string) (
                $currentOption['name'] ?? ''
            ),

            'courier_service' => (string) (
                $currentOption['service'] ?? ''
            ),

            'courier_description' => (string) (
                $currentOption['description'] ?? ''
            ),

            'shipping_etd' => (string) (
                $currentOption['etd'] ?? ''
            ),

            'total_weight_grams' => $totalWeight,

            'shipping_cost' => $currentCost,
        ];
    }

    private function decryptToken(
        string $token,
        string $field
    ): array {
        try {
            $payload = json_decode(
                Crypt::decryptString($token),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (
            DecryptException | JsonException $exception
        ) {
            throw ValidationException::withMessages([
                $field =>
                    'Data pengiriman tidak valid. '
                    . 'Silakan pilih kembali.',
            ]);
        }

        if (! is_array($payload)) {
            throw ValidationException::withMessages([
                $field =>
                    'Format data pengiriman tidak valid.',
            ]);
        }

        return $payload;
    }
}