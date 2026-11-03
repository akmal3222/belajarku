<?php $u = user(); $title = $title ?? 'BelajarKu'; ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> - BelajarKu</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<nav class="no-print">
  <a class="brand" href="index.php">📚 BelajarKu</a>
  <a href="index.php">Layanan</a>
  <?php if (!$u): ?>
    <a href="login.php">Masuk</a><a href="register.php">Daftar</a>
  <?php elseif ($u['role'] === 'admin'): ?>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="admin_layanan.php">Kelola Layanan</a>
    <a href="admin_pesanan.php">Pesanan</a>
    <a href="admin_laporan.php">Laporan Keuangan</a>
  <?php else: ?>
    <a href="pesanan.php">Pesanan Saya</a>
  <?php endif; ?>
  <?php if ($u): ?>
    <span class="who"><?= e($u['nama']) ?> (<?= e($u['role']) ?>)</span>
    <a href="logout.php">Keluar</a>
  <?php endif; ?>
</nav>
<main>
<?= flash() ?>
