# BelajarKu: Website Pemesanan Jasa Les Privat dan Kursus Online

Aplikasi web PHP native + MySQL. Layanan: les matematika, bahasa Inggris, pemrograman, desain, dan keterampilan digital.

## Cara Menjalankan (XAMPP)

1. Install dan jalankan **XAMPP** (nyalakan **Apache** dan **MySQL**).
2. Salin folder `belajarku` ke `C:\xampp\htdocs\`.
3. Buka `http://localhost/phpmyadmin` → tab **Import** → pilih `database.sql` → **Go**.
4. Buka `http://localhost/belajarku/`.
5. Jika password MySQL bukan kosong, ubah `inc/config.php`.

## Akun Demo

| Peran | Email | Password |
|---|---|---|
| Admin | admin@belajarku.id | admin123 |
| Siswa | siswa@belajarku.id | siswa123 |

## Pemenuhan Syarat Tugas

| # | Syarat | Lokasi |
|---|---|---|
| 1 | Payment gateway (simulator) | `bayar.php`: pilih VA BCA/Mandiri, QRIS, GoPay, lalu simulasikan berhasil/gagal. Tercatat di tabel `payments` |
| 2 | Cetak invoice | `invoice.php`: tombol Cetak (print / Save as PDF) |
| 3 | Laporan keuangan | `admin_laporan.php`: filter tanggal, status, layanan, metode. Ekspor PDF, Excel (`admin_export.php`), cetak |
| 4 | Aktor minimal 2 | **Admin** dan **Siswa** (role-based access) |

## Struktur File

```
belajarku/
├── database.sql          skema + data contoh
├── inc/                  config, fungsi bantu, header, footer
├── assets/style.css
├── index.php             daftar layanan (publik)
├── login.php, register.php, logout.php
├── pesan.php             siswa: buat pesanan
├── pesanan.php           siswa: riwayat, batal
├── bayar.php             siswa: payment gateway simulator
├── invoice.php           siswa/admin: invoice cetak
├── admin_dashboard.php   admin: ringkasan
├── admin_layanan.php     admin: CRUD layanan
├── admin_pesanan.php     admin: semua pesanan
├── admin_laporan.php     admin: laporan keuangan
└── admin_export.php      admin: ekspor Excel
```

## Keamanan yang Diterapkan

- Password di-hash (`password_hash`), login memakai `session_regenerate_id`.
- Semua query memakai prepared statement (PDO), output di-escape (`htmlspecialchars`).
- Token CSRF di semua form POST.
- Akses halaman dibatasi per peran, siswa hanya bisa melihat pesanan miliknya.

## Catatan

- Ekspor PDF memakai library jsPDF dari CDN (butuh internet). Tanpa internet, pakai tombol Cetak → Save as PDF.
- Pembayaran adalah simulasi. Untuk gateway sungguhan (Midtrans/Xendit), ganti blok "PAYMENT GATEWAY SIMULATOR" di `bayar.php` dengan pemanggilan API dan webhook.
