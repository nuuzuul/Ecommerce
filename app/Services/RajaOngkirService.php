<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class RajaOngkirService
{
    /**
     * Mencari lokasi domestik berdasarkan nama kota,
     * kecamatan, kelurahan, atau kode pos.
     */
    public function searchDomesticDestination(
        string $keyword,
        int $limit = 10,
        int $offset = 0
    ): array {
        $keyword = trim($keyword);

        if ($keyword === '') {
            throw new InvalidArgumentException(
                'Kata kunci lokasi tidak boleh kosong.'
            );
        }

        $limit = max(1, min($limit, 20));
        $offset = max(0, $offset);

        try {
            $response = $this->client()->get(
                '/destination/domestic-destination',
                [
                    'search' => $keyword,
                    'limit' => $limit,
                    'offset' => $offset,
                ]
            );

            if ($response->failed()) {
                throw new RuntimeException(
                    $this->errorMessage(
                        $response->json(),
                        'Gagal mencari lokasi tujuan.'
                    )
                );
            }

            return $response->json('data') ?? [];
        } catch (RuntimeException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            throw new RuntimeException(
                'RajaOngkir sedang tidak dapat dihubungi. '
                . 'Silakan coba beberapa saat lagi.'
            );
        }
    }

    /**
     * Menghitung pilihan ongkir domestik.
     */
    public function calculateDomesticCost(
        int $originId,
        int $destinationId,
        int $weight,
        ?string $couriers = null,
        ?string $price = null
    ): array {
        if ($originId <= 0) {
            throw new InvalidArgumentException(
                'Lokasi asal pengiriman belum valid.'
            );
        }

        if ($destinationId <= 0) {
            throw new InvalidArgumentException(
                'Lokasi tujuan pengiriman belum valid.'
            );
        }

        if ($weight <= 0) {
            throw new InvalidArgumentException(
                'Berat pengiriman harus lebih dari 0 gram.'
            );
        }

        $couriers ??= config('rajaongkir.couriers');
        $price ??= config('rajaongkir.price');

        try {
            $response = $this->client()
                ->asForm()
                ->post(
                    '/calculate/domestic-cost',
                    [
                        'origin' => $originId,
                        'destination' => $destinationId,
                        'weight' => $weight,
                        'courier' => $couriers,
                        'price' => $price,
                    ]
                );

            if ($response->failed()) {
                throw new RuntimeException(
                    $this->errorMessage(
                        $response->json(),
                        'Gagal menghitung ongkos kirim.'
                    )
                );
            }

            return $response->json('data') ?? [];
        } catch (RuntimeException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            throw new RuntimeException(
                'Ongkos kirim belum dapat dihitung. '
                . 'Silakan coba beberapa saat lagi.'
            );
        }
    }

    /**
     * Menghitung ongkir dari lokasi asal yang tersimpan di konfigurasi.
     */
    public function calculateFromStore(
        int $destinationId,
        int $weight,
        ?string $couriers = null
    ): array {
        $originId = (int) config('rajaongkir.origin_id');

        if ($originId <= 0) {
            throw new RuntimeException(
                'Lokasi asal toko belum dikonfigurasi.'
            );
        }

        return $this->calculateDomesticCost(
            originId: $originId,
            destinationId: $destinationId,
            weight: $weight,
            couriers: $couriers
        );
    }

    /**
     * HTTP client untuk seluruh permintaan RajaOngkir.
     */
    private function client(): PendingRequest
    {
        $apiKey = config('rajaongkir.api_key');
        $baseUrl = rtrim(
            (string) config('rajaongkir.base_url'),
            '/'
        );

        if (! $apiKey) {
            throw new RuntimeException(
                'API key RajaOngkir belum dikonfigurasi.'
            );
        }

        return Http::baseUrl($baseUrl)
            ->withHeaders([
                'key' => $apiKey,
            ])
            ->acceptJson()
            ->timeout(
                (int) config('rajaongkir.timeout', 15)
            )
            ->retry(
                times: 2,
                sleepMilliseconds: 500,
                throw: false
            );
    }

    /**
     * Mengambil pesan error dari respons RajaOngkir.
     */
    private function errorMessage(
        ?array $response,
        string $fallback
    ): string {
        return data_get(
            $response,
            'meta.message',
            $fallback
        );
    }
}