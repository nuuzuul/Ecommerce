# Ringkasan Perbaikan Kanrejawataa

- Detail pesanan diperbaiki agar responsif dan tidak melebar horizontal.
- Bukti pembayaran sekarang dilayani oleh route terautentikasi dan tetap dapat dilihat setelah pembayaran diverifikasi atau pesanan selesai.
- Fitur hapus akun beserta route dan tampilan dihapus.
- Aksi lihat, edit, dan hapus pada tabel admin diubah menjadi ikon.
- Logo navbar berada di `public/images/branding/logo.svg`.
- Favicon berada di `public/images/branding/favicon.svg`.
- Petunjuk branding lengkap tersedia di `docs/BRANDING.md`.
- File utama yang diperbaiki sudah menggunakan format kode vertikal dan indentasi yang lebih mudah dibaca.

Setelah ekstrak, jalankan:

```bash
composer install
npm install
php artisan optimize:clear
php artisan storage:link
npm run build
```
