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
