# Tambak Rasyid

Aplikasi web Laravel untuk manajemen tambak, keuangan, POS, RBAC, dan export laporan Excel.

## Stack

- Laravel 13
- Laravel Breeze Blade
- spatie/laravel-permission
- maatwebsite/excel
- TailwindCSS, Alpine.js, Chart.js, Gridstack.js
- MySQL

## Fitur

- Authentication Breeze Blade.
- RBAC: Super Admin, Admin, Kasir.
- Manajemen kolam: CRUD, grid dashboard, drag & drop, resize, status warna panen.
- Keuangan: pemasukan, pengeluaran, kategori, saldo, line chart bulanan, pie chart kategori.
- Produk: kategori produk, CRUD produk, harga, SKU, stok, status aktif.
- POS: cart, qty, subtotal otomatis, diskon, cash, kembalian, validasi stok, struk print.
- Export Excel: laporan keuangan dan penjualan.

## Instalasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Atur database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tambak_rasyit
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi, seeder, dan build asset:

```bash
php artisan migrate:fresh --seed
npm.cmd run build
php artisan serve
```

## Akun Demo

Semua password: `password`

- Super Admin: `superadmin@tambak.test`
- Admin: `admin@tambak.test`
- Kasir: `kasir@tambak.test`

## Verifikasi Terakhir

```bash
php artisan migrate:fresh --seed
php artisan test
npm.cmd run build
php artisan view:cache
```

Hasil terakhir: test lulus 25/25 dan build Vite berhasil.
