-- BelajarKu: Website Pemesanan Jasa Les Privat dan Kursus Online
-- Import lewat phpMyAdmin (tab Import) atau: mysql -u root < database.sql

CREATE DATABASE IF NOT EXISTS belajarku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE belajarku;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','siswa') NOT NULL DEFAULT 'siswa',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(120) NOT NULL,
  tipe ENUM('Les Privat','Kursus Online') NOT NULL,
  harga INT NOT NULL,
  satuan ENUM('pertemuan','paket') NOT NULL DEFAULT 'pertemuan',
  deskripsi TEXT
) ENGINE=InnoDB;

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_no VARCHAR(30) NOT NULL UNIQUE,
  user_id INT NOT NULL,
  service_id INT NULL,
  service_name VARCHAR(120) NOT NULL,
  tipe VARCHAR(30) NOT NULL,
  harga INT NOT NULL,
  qty INT NOT NULL,
  total INT NOT NULL,
  jadwal DATE NOT NULL,
  catatan TEXT,
  status ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  metode VARCHAR(40) NOT NULL,
  ref_gateway VARCHAR(30) NOT NULL,
  amount INT NOT NULL,
  status ENUM('success','failed') NOT NULL,
  paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Akun demo: admin@belajarku.id / admin123 ; siswa@belajarku.id / siswa123
INSERT INTO users (id,nama,email,password,role) VALUES
(1,'Administrator','admin@belajarku.id','$2y$10$j8t3IszXEpM1TPJAY9PeoeRkG5tHG15fjI7UggFVMmnBJN5Z83UdK','admin'),
(2,'Budi Siswa','siswa@belajarku.id','$2y$10$kwoYV1bfD1Q.IXd.MY592.4auNIUCP88fZ1oXXsc4H37kyYvEgMEe','siswa');

INSERT INTO services (id,nama,tipe,harga,satuan,deskripsi) VALUES
(1,'Les Matematika SMA','Les Privat',120000,'pertemuan','Tutor datang/online, materi sesuai kurikulum dan persiapan UTBK.'),
(2,'Les Bahasa Inggris','Les Privat',100000,'pertemuan','Conversation, grammar, persiapan TOEFL/IELTS.'),
(3,'Kursus Pemrograman Web','Kursus Online',450000,'paket','HTML, CSS, JavaScript dan PHP dari dasar hingga proyek.'),
(4,'Kursus Desain Grafis','Kursus Online',350000,'paket','Canva, Figma dan dasar desain visual untuk pemula.'),
(5,'Kursus Keterampilan Digital','Kursus Online',250000,'paket','Microsoft Office, Google Workspace, dan digital marketing dasar.');

-- Data contoh transaksi lunas (agar laporan tidak kosong)
INSERT INTO orders (id,invoice_no,user_id,service_id,service_name,tipe,harga,qty,total,jadwal,status,created_at) VALUES
(1,'INV-20261101-0001',2,1,'Les Matematika SMA','Les Privat',120000,4,480000,'2026-11-05','paid','2026-11-01 09:10:00'),
(2,'INV-20261102-0002',2,3,'Kursus Pemrograman Web','Kursus Online',450000,1,450000,'2026-11-08','paid','2026-11-02 13:00:00');

INSERT INTO payments (order_id,metode,ref_gateway,amount,status,paid_at) VALUES
(1,'QRIS','SIM-A1B2C3',480000,'success','2026-11-01 09:15:00'),
(2,'Virtual Account BCA','SIM-D4E5F6',450000,'success','2026-11-02 13:04:00');
