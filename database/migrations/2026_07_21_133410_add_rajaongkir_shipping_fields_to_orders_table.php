<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            /*
            | Semua nama wilayah disimpan agar detail pesanan lama
            | tidak bergantung pada respons API di masa mendatang.
            */

            $table->unsignedBigInteger('destination_id')
                ->nullable()
                ->after('shipping_address');

            $table->string('destination_label')
                ->nullable()
                ->after('destination_id');

            $table->string('destination_province', 100)
                ->nullable()
                ->after('destination_label');

            $table->string('destination_city', 100)
                ->nullable()
                ->after('destination_province');

            $table->string('destination_district', 100)
                ->nullable()
                ->after('destination_city');

            $table->string('destination_subdistrict', 100)
                ->nullable()
                ->after('destination_district');

            $table->string('destination_postal_code', 10)
                ->nullable()
                ->after('destination_subdistrict');

            /*
            |--------------------------------------------------------------------------
            | layanan pengiriman
            |--------------------------------------------------------------------------
            */

            $table->string('courier_code', 30)
                ->nullable()
                ->after('destination_postal_code');

            $table->string('courier_name', 100)
                ->nullable()
                ->after('courier_code');

            $table->string('courier_service', 100)
                ->nullable()
                ->after('courier_name');

            $table->string('courier_description')
                ->nullable()
                ->after('courier_service');

            $table->string('shipping_etd', 50)
                ->nullable()
                ->after('courier_description');

            /*
            |--------------------------------------------------------------------------
            | Berat total pesanan
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('total_weight_grams')
                ->default(0)
                ->after('shipping_etd');
        });

        Schema::table('order_items', function (Blueprint $table) {
            /*
            | Berat varian dapat berubah kemudian. Karena itu berat saat
            | transaksi disimpan juga pada detail order.
            */

            $table->unsignedInteger('unit_weight_grams')
                ->default(0)
                ->after('variant_label');

            $table->unsignedInteger('total_weight_grams')
                ->default(0)
                ->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'unit_weight_grams',
                'total_weight_grams',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'destination_id',
                'destination_label',
                'destination_province',
                'destination_city',
                'destination_district',
                'destination_subdistrict',
                'destination_postal_code',
                'courier_code',
                'courier_name',
                'courier_service',
                'courier_description',
                'shipping_etd',
                'total_weight_grams',
            ]);
        });
    }
};