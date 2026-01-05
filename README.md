# 🍱 Kaisei POS System

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-blue.svg)](https://alpinejs.dev)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-skyblue.svg)](https://tailwindcss.com)

**Kaisei** adalah sistem Point of Sale (POS) modern yang dirancang khusus untuk operasional restoran/kafe. Aplikasi ini memungkinkan pelanggan memesan langsung melalui tablet dan kasir mengelola transaksi secara real-time.

---

## ✨ Fitur Utama

- **Multi-Role Management**:
    - **Administrator/Kasir**: Mengelola menu, memantau pesanan masuk, dan konfirmasi pembayaran.
    - **Customer (Tablet Mode)**: Antarmuka khusus untuk pelanggan melakukan *self-ordering* dari meja.
- **Real-time Order Flow**: Menggunakan Laravel Echo & Reverb untuk sinkronisasi pesanan antara tablet pelanggan dan layar kasir tanpa refresh.
- **Smart Queue System**: Penomoran antrean otomatis yang direset setiap hari.
- **Payment Verification**: 
    - Mendukung metode **Cash** dan **Transfer**.
    - Fitur unggah bukti pembayaran untuk transaksi non-tunai.
- **Sales Analytics**: Pencatatan otomatis menu terlaris (`count_sold`) setiap kali transaksi selesai.
- **Dynamic Table Management**: Manajemen user dan menu yang responsif menggunakan Alpine.js.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Tailwind CSS & Alpine.js
- **Real-time**: Laravel Echo & Reverb
- **Database**: MySQL / MariaDB
- **Icons**: FontAwesome 6

---

## 🚀 Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan project di lokal:

1. **Clone Repository**
   ```bash
   git clone [https://github.com/username/kaisei-pos.git](https://github.com/username/kaisei-pos.git)
   cd kaisei-pos
2. **Install Dependensi**
   Instal semua paket PHP dan JavaScript yang diperlukan.
   ```bash
   composer install
   npm install
3. **Konfigurasi Environment Salin file**
   .env.example menjadi .env dan sesuaikan pengaturan database serta broadcast driver.
    ```bash
    cp .env.example .env
    php artisan key:generate
4. **Hubungkan Storage**
Jalankan perintah ini untuk membuat struktur tabel dan mengisi data role (Admin & Customer).
   ```bash
   php artisan storage:link
5. **Jalankan Aplikasi**
   Buka terminal dan jalankan server lokal Laravel:
   ```bash
   php artisan serve
   php artisan reverb:start
   npm run dev
# 📦 Sistem Pemesanan & Kasir (Laravel)

## 📁 Struktur Penting Proyek

Berikut adalah file dan folder utama yang memegang peran penting dalam alur sistem:

- `app/Http/Controllers/OrderController.php`  
  Mengatur logika inti alur pemesanan, konfirmasi pesanan, dan interaksi kasir.

- `app/Http/Controllers/UserController.php`  
  Menangani manajemen pengguna serta validasi role (hak akses) yang unik.

- `app/Models/Bill.php`  
  Menyimpan data transaksi, sistem antrean, dan status pembayaran.

- `resources/views/Dashboard/user.blade.php`  
  Tampilan frontend untuk manajemen user menggunakan **Alpine.js**.

---

## ⚡ Cara Kerja Real-Time (Laravel Reverb)

Proyek ini menggunakan **Laravel Reverb** untuk sinkronisasi data secara real-time tanpa refresh halaman.

### Alur Kerja:
1. Pelanggan melakukan konfirmasi pesanan melalui **Tablet**.
2. Server memicu event `OrderPlaced`.
3. Kasir menerima notifikasi pesanan baru secara otomatis di dashboard **tanpa reload halaman**.

---

## 🛠 Teknologi Utama
- Laravel
- Laravel Reverb (Real-time Event)
- Alpine.js
- Blade Template

---

## 🎯 Tujuan Sistem
- Mempercepat alur pemesanan
- Menghilangkan kebutuhan refresh manual
- Sinkronisasi data instan antara pelanggan dan kasir
