# Kanrejawataa

Kanrejawataa adalah proyek akhir **single-store ecommerce** berbasis Laravel untuk penjualan kue kering dan kue tradisional Makassar.

## Fitur utama

- Laravel Breeze: register, login, logout, reset password, dan profil.
- Dua role: `admin` dan `pembeli`.
- CRUD kategori dan produk.
- Kategori **Kue Kering** memakai dua varian wajib: `500 gram` dan `1 kg`.
- Kategori **Kue Tradisional Makassar** memakai satu harga tanpa menampilkan ukuran.
- Upload foto produk.
- Katalog dengan pencarian, filter kategori/harga/stok, pengurutan, dan pagination.
- Keranjang dan checkout.
- Pengantaran `Ambil sendiri` atau `Dikirimkan`.
- Pembayaran manual melalui transfer bank atau QRIS dengan upload bukti pembayaran.
- Status pembayaran: `belum_bayar`, `menunggu_verifikasi`, `sudah_bayar`.
- Status pesanan: `diproses`, lalu `siap_diambil` atau `dikirim`, kemudian `selesai`.
- Dashboard admin, produk terlaris, total penjualan, stok menipis, dan pesanan terbaru.
- REST API produk di `/api/products`.
- Tracking riwayat status pesanan.
- Form Request validation dan tampilan responsif.

## Persyaratan

- PHP 8.3+
- Composer
- Node.js dan npm
- MySQL/MariaDB (Laragon/XAMPP dapat digunakan)

## Instalasi lokal

1. Ekstrak proyek dan masuk ke folder:

```powershell
cd kanrejawataa
```

2. Pasang dependensi PHP dan JavaScript:

```powershell
composer install
npm install
```

3. Buat file `.env`:

```powershell
copy .env.example .env
php artisan key:generate
```

4. Buat database MySQL bernama `kanrejawataa`, lalu sesuaikan bagian `DB_*` di `.env`.

5. Buat tabel dan data awal:

```powershell
php artisan migrate:fresh --seed
php artisan storage:link
```

6. Jalankan aplikasi pada dua terminal:

```powershell
php artisan serve
```

```powershell
npm run dev
```

Alternatif produksi lokal:

```powershell
npm run build
php artisan serve
```

## Akun demo

### Admin

- Email: `admin@kanrejawataa.test`
- Password: `password`

### Pembeli

- Email: `pembeli@kanrejawataa.test`
- Password: `password`

Ganti password akun demo sebelum aplikasi digunakan di luar lingkungan testing.

## Alur penggunaan

### Pembeli

1. Register/login.
2. Pilih produk dan varian ukuran bila produk merupakan kue kering.
3. Tambah ke keranjang.
4. Checkout dan pilih pengantaran.
5. Pilih transfer bank atau QRIS.
6. Unggah bukti pembayaran pada detail pesanan.
7. Pantau status pembayaran dan tracking pesanan.

### Admin

1. Login menggunakan akun admin.
2. Kelola kategori dan produk.
3. Lihat bukti pembayaran dan lakukan verifikasi.
4. Perbarui status sesuai metode pengantaran:
   - Ambil sendiri: `diproses → siap_diambil → selesai`.
   - Dikirimkan: `diproses → dikirim → selesai`.
5. Lihat rekap pada dashboard.

## Struktur view utama

```text
resources/views/
├── layouts/
│   ├── store.blade.php
│   ├── account.blade.php
│   ├── admin.blade.php
│   └── guest.blade.php
├── partials/
├── components/
├── home/
├── products/
├── cart/
├── checkout/
├── orders/
├── account/
├── admin/
└── auth/
```

## Konfigurasi toko

Biaya pengiriman, rekening, dan alamat pengambilan dapat diubah melalui `.env`:

```env
KANREJAWATAA_DELIVERY_FEE=15000
KANREJAWATAA_BANK_NAME="Bank BCA"
KANREJAWATAA_BANK_ACCOUNT="1234567890"
KANREJAWATAA_BANK_HOLDER="Kanrejawataa"
KANREJAWATAA_PICKUP_ADDRESS="Makassar, Sulawesi Selatan"
```

## Catatan

- Folder `vendor/` dan `node_modules/` tidak disertakan dalam paket pengumpulan. Jalankan `composer install` dan `npm install` setelah ekstrak.
- Folder `public/build/` sudah disertakan agar aset hasil build tersedia.
- Untuk upload gambar, perintah `php artisan storage:link` wajib dijalankan.
