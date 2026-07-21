<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CalculateShippingCostRequest;
use App\Http\Requests\Customer\SearchShippingDestinationRequest;
use App\Models\Cart;
use App\Services\RajaOngkirService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use JsonException;
use RuntimeException;

class ShippingController extends Controller
{
    public function searchDestinations(
        SearchShippingDestinationRequest $request,
        RajaOngkirService $rajaOngkir
    ): JsonResponse {
        try {
            $locations = $rajaOngkir
                ->searchDomesticDestination(
                    keyword: $request->string('search')->toString(),
                    limit: $request->integer('limit', 10)
                );

            $data = collect($locations)
                ->map(function (array $location): array {
                    $snapshot = [
                        'id' => (int) (
                            $location['id'] ?? 0
                        ),

                        'label' => (string) (
                            $location['label'] ?? ''
                        ),

                        'province_name' => (string) (
                            $location['province_name'] ?? ''
                        ),

                        'city_name' => (string) (
                            $location['city_name'] ?? ''
                        ),

                        'district_name' => (string) (
                            $location['district_name'] ?? ''
                        ),

                        'subdistrict_name' => (string) (
                            $location['subdistrict_name'] ?? ''
                        ),

                        'zip_code' => (string) (
                            $location['zip_code'] ?? ''
                        ),
                    ];

                    return [
                        ...$snapshot,

                        'destination_token' =>
                            $this->encryptPayload($snapshot),
                    ];
                })
                ->filter(
                    fn (array $location): bool =>
                        $location['id'] > 0
                )
                ->values();

            return response()->json([
                'message' => 'Lokasi tujuan ditemukan.',
                'data' => $data,
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], 503);
        }
    }

    public function calculateCosts(
        CalculateShippingCostRequest $request,
        RajaOngkirService $rajaOngkir
    ): JsonResponse {
        $destination = $this->decryptDestination(
            $request->input('destination_token')
        );

        $cart = Cart::query()
            ->where(
                'user_id',
                $request->user()->id
            )
            ->with([
                'items.variant.product',
            ])
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Keranjang masih kosong.',
            ]);
        }

        $invalidWeightItem = $cart->items->first(
            fn ($item): bool =>
                (int) $item->variant->weight_grams <= 0
        );

        if ($invalidWeightItem) {
            throw ValidationException::withMessages([
                'cart' =>
                    'Berat produk '
                    . $invalidWeightItem
                        ->variant
                        ->product
                        ->name
                    . ' belum diatur.',
            ]);
        }

        $totalWeight = $cart->total_weight_grams;

        if ($totalWeight <= 0) {
            throw ValidationException::withMessages([
                'cart' =>
                    'Total berat keranjang belum valid.',
            ]);
        }

        try {
            $costs = $rajaOngkir->calculateFromStore(
                destinationId: $destination['id'],
                weight: $totalWeight
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], 503);
        }

        $shippingOptions = collect($costs)
            ->map(function (array $cost) use (
                $destination,
                $totalWeight
            ): array {
                $option = [
                    'courier_code' => strtolower(
                        (string) ($cost['code'] ?? '')
                    ),

                    'courier_name' => (string) (
                        $cost['name'] ?? ''
                    ),

                    'service' => (string) (
                        $cost['service'] ?? ''
                    ),

                    'description' => (string) (
                        $cost['description'] ?? ''
                    ),

                    'cost' => (int) (
                        $cost['cost'] ?? 0
                    ),

                    'etd' => (string) (
                        $cost['etd'] ?? ''
                    ),
                ];

                $tokenPayload = [
                    'destination' => $destination,

                    'total_weight_grams' =>
                        $totalWeight,

                    ...$option,

                    'generated_at' => now()->timestamp,
                ];

                return [
                    ...$option,

                    'formatted_cost' =>
                        'Rp '
                        . number_format(
                            $option['cost'],
                            0,
                            ',',
                            '.'
                        ),

                    'shipping_option_token' =>
                        $this->encryptPayload(
                            $tokenPayload
                        ),
                ];
            })
            ->filter(
                fn (array $option): bool =>
                    $option['courier_code'] !== ''
                    && $option['service'] !== ''
                    && $option['cost'] > 0
            )
            ->sortBy('cost')
            ->values();

        return response()->json([
            'message' => $shippingOptions->isEmpty()
                ? 'Layanan pengiriman tidak tersedia.'
                : 'Ongkos kirim berhasil dihitung.',

            'destination' => [
                'id' => $destination['id'],
                'label' => $destination['label'],
            ],

            'total_weight_grams' => $totalWeight,

            'formatted_weight' =>
                $this->formatWeight($totalWeight),

            'data' => $shippingOptions,
        ]);
    }

    private function decryptDestination(
        string $token
    ): array {
        try {
            $destination = json_decode(
                Crypt::decryptString($token),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (
            DecryptException | JsonException $exception
        ) {
            throw ValidationException::withMessages([
                'destination_token' =>
                    'Data lokasi tujuan tidak valid. '
                    . 'Silakan pilih lokasi kembali.',
            ]);
        }

        if (
            ! is_array($destination)
            || (int) ($destination['id'] ?? 0) <= 0
            || blank($destination['label'] ?? null)
        ) {
            throw ValidationException::withMessages([
                'destination_token' =>
                    'Data lokasi tujuan tidak lengkap.',
            ]);
        }

        return $destination;
    }

    private function encryptPayload(
        array $payload
    ): string {
        try {
            return Crypt::encryptString(
                json_encode(
                    $payload,
                    JSON_THROW_ON_ERROR
                )
            );
        } catch (JsonException $exception) {
            report($exception);

            throw new RuntimeException(
                'Data pengiriman gagal diproses.'
            );
        }
    }

    private function formatWeight(
        int $weight
    ): string {
        if ($weight >= 1000) {
            return number_format(
                $weight / 1000,
                2,
                ',',
                '.'
            ) . ' kg';
        }

        return number_format(
            $weight,
            0,
            ',',
            '.'
        ) . ' gram';
    }
}