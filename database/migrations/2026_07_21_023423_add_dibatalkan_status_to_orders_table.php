<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE orders
            MODIFY status ENUM(
                'diproses',
                'siap_diambil',
                'dikirim',
                'selesai',
                'dibatalkan'
            )
            NOT NULL DEFAULT 'diproses'
        ");
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('status', 'dibatalkan')
            ->update([
                'status' => 'diproses',
            ]);

        DB::statement("
            ALTER TABLE orders
            MODIFY status ENUM(
                'diproses',
                'siap_diambil',
                'dikirim',
                'selesai'
            )
            NOT NULL DEFAULT 'diproses'
        ");
    }
};