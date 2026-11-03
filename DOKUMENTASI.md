# Dokumentasi Perancangan: BelajarKu

Diagram memakai sintaks Mermaid (tampil di GitHub, VS Code dengan ekstensi Markdown Mermaid, atau https://mermaid.live untuk diekspor ke gambar).

## 1. Deskripsi Sistem

BelajarKu adalah website pemesanan jasa les privat dan kursus online (matematika, bahasa Inggris, pemrograman, desain, keterampilan digital). Siswa memilih layanan, memesan, membayar lewat payment gateway (simulator), dan mendapat invoice. Admin mengelola layanan dan memantau laporan keuangan.

## 2. Aktor

| Aktor | Hak akses |
|---|---|
| Admin | Kelola layanan, lihat semua pesanan, dashboard, laporan keuangan (filter, PDF, Excel, cetak), cetak invoice |
| Siswa | Registrasi/login, lihat layanan, buat pesanan, bayar, batalkan pesanan, lihat riwayat, cetak invoice |

## 3. Kebutuhan Fungsional

1. Registrasi dan login berbasis peran.
2. CRUD layanan oleh admin.
3. Pemesanan layanan (jumlah, jadwal, catatan) dengan total otomatis.
4. Pembayaran lewat payment gateway simulator (VA BCA, VA Mandiri, QRIS, GoPay).
5. Cetak invoice setelah pembayaran.
6. Laporan keuangan dengan filter dan ekspor PDF/Excel.
7. Dashboard ringkasan pendapatan.

## 4. Use Case Diagram

```mermaid
flowchart LR
  S([Siswa]) --- U1(Registrasi / Login)
  S --- U2(Lihat Layanan)
  S --- U3(Buat Pesanan)
  S --- U4(Bayar via Gateway)
  S --- U5(Lihat Riwayat Pesanan)
  S --- U6(Cetak Invoice)
  A([Admin]) --- U1
  A --- U7(Kelola Layanan)
  A --- U8(Lihat Semua Pesanan)
  A --- U9(Lihat Dashboard)
  A --- U10(Laporan Keuangan)
  A --- U6
  U10 -.include.-> U11(Filter Laporan)
  U10 -.include.-> U12(Ekspor PDF / Excel)
  U4 -.include.-> G([Payment Gateway Simulator])
```

## 5. Flowchart Alur Pemesanan dan Pembayaran

```mermaid
flowchart TD
  A([Mulai]) --> B[Siswa login]
  B --> C[Pilih layanan]
  C --> D[Isi jumlah, jadwal, catatan]
  D --> E[Pesanan dibuat: status pending]
  E --> F[Pilih metode pembayaran]
  F --> G{Hasil dari gateway}
  G -- Berhasil --> H[Catat payments success, status paid]
  H --> I[Tampilkan dan cetak invoice]
  I --> Z([Selesai])
  G -- Gagal --> J[Catat payments failed, status failed]
  J --> K{Coba lagi?}
  K -- Ya --> F
  K -- Tidak --> L[Pesanan dibatalkan]
  L --> Z
```

## 6. ERD

```mermaid
erDiagram
  USERS ||--o{ ORDERS : membuat
  SERVICES ||--o{ ORDERS : dipesan_dalam
  ORDERS ||--o{ PAYMENTS : memiliki

  USERS {
    int id PK
    varchar nama
    varchar email UK
    varchar password
    enum role "admin, siswa"
    datetime created_at
  }
  SERVICES {
    int id PK
    varchar nama
    enum tipe "Les Privat, Kursus Online"
    int harga
    enum satuan "pertemuan, paket"
    text deskripsi
  }
  ORDERS {
    int id PK
    varchar invoice_no UK
    int user_id FK
    int service_id FK
    varchar service_name
    int harga
    int qty
    int total
    date jadwal
    text catatan
    enum status "pending, paid, failed, cancelled"
    datetime created_at
  }
  PAYMENTS {
    int id PK
    int order_id FK
    varchar metode
    varchar ref_gateway
    int amount
    enum status "success, failed"
    datetime paid_at
  }
```

Catatan desain: `orders` menyimpan salinan nama dan harga layanan saat pemesanan, sehingga invoice dan laporan lama tidak berubah bila layanan diedit atau dihapus.

## 7. Struktur Tabel

Lihat `database.sql` untuk DDL lengkap beserta data contoh.

## 8. Rancangan Halaman

| Halaman | Aktor | Fungsi |
|---|---|---|
| index.php | Publik | Daftar layanan |
| login.php / register.php | Publik | Autentikasi |
| pesan.php | Siswa | Form pemesanan |
| bayar.php | Siswa | Pembayaran (simulator gateway) |
| pesanan.php | Siswa | Riwayat dan pembatalan |
| invoice.php | Siswa, Admin | Invoice cetak |
| admin_dashboard.php | Admin | Statistik |
| admin_layanan.php | Admin | CRUD layanan |
| admin_pesanan.php | Admin | Semua pesanan |
| admin_laporan.php | Admin | Laporan keuangan |

## 9. Skenario Pengujian

| No | Skenario | Hasil yang diharapkan |
|---|---|---|
| 1 | Daftar akun siswa baru | Akun dibuat, otomatis login |
| 2 | Login dengan password salah | Pesan error |
| 3 | Siswa membuka halaman admin | Dialihkan ke beranda |
| 4 | Siswa memesan layanan | Pesanan pending, total = harga x jumlah |
| 5 | Bayar, pilih "Berhasil" | Status Lunas, invoice tampil |
| 6 | Bayar, pilih "Gagal" | Status Gagal, bisa dicoba lagi |
| 7 | Cetak invoice | Dialog print/PDF terbuka tanpa menu navigasi |
| 8 | Admin tambah/edit/hapus layanan | Daftar layanan berubah |
| 9 | Laporan: filter tanggal dan layanan | Tabel dan total sesuai filter |
| 10 | Laporan: ekspor PDF dan Excel | File terunduh dengan data terfilter |
