<?php
// Konfigurasi database (default XAMPP)
define('DB_HOST', 'localhost');
define('DB_NAME', 'belajarku');
define('DB_USER', 'root');
define('DB_PASS', '');

date_default_timezone_set('Asia/Jakarta');
session_start();

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal. Pastikan MySQL menyala dan database.sql sudah diimport. Detail: ' . htmlspecialchars($e->getMessage()));
}

require __DIR__ . '/functions.php';
