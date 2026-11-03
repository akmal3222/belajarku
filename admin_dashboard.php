<?php
require 'inc/config.php';
require_login('admin');
$total = (int)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='paid'")->fetchColumn();
$bulan = (int)$pdo->query("SELECT COALESCE(SUM(o.total),0) FROM orders o JOIN payments p ON p.order_id=o.id AND p.status='success'
                           WHERE DATE_FORMAT(p.paid_at,'%Y-%m') = DATE_FORMAT(NOW(),'%Y-%m')")->fetchColumn();
$lunas = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='paid'")->fetchColumn();
$pending = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
$siswa = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='siswa'")->fetchColumn();
$per = $pdo->query("SELECT service_name, SUM(total) AS t FROM orders WHERE status='paid' GROUP BY service_name ORDER BY t DESC")->fetchAll();
$max = max(1, ...array_map(fn($r) => (int)$r['t'], $per ?: [['t' => 1]]));
$title = 'Dashboard Admin';
include 'inc/header.php';
?>
<h1>Dashboard Admin</h1><div class="sub">Ringkasan bisnis.</div>
<div class="stats">
  <div class="card stat"><div class="l">Total Pendapatan</div><div class="n"><?= rp($total) ?></div></div>
  <div class="card stat"><div class="l">Pendapatan Bulan Ini</div><div class="n"><?= rp($bulan) ?></div></div>
  <div class="card stat"><div class="l">Pesanan Lunas</div><div class="n"><?= $lunas ?></div></div>
  <div class="card stat"><div class="l">Menunggu Pembayaran</div><div class="n"><?= $pending ?></div></div>
  <div class="card stat"><div class="l">Jumlah Siswa</div><div class="n"><?= $siswa ?></div></div>
</div>
<div class="card"><h2>Pendapatan per Layanan</h2>
<?php foreach ($per as $r): ?>
  <div style="margin:8px 0;font-size:14px"><?= e($r['service_name']) ?> <b style="float:right"><?= rp($r['t']) ?></b>
  <div class="bar"><div style="width:<?= $r['t'] / $max * 100 ?>%"></div></div></div>
<?php endforeach; if (!$per) echo '<small>Belum ada data.</small>'; ?>
</div>
<?php include 'inc/footer.php'; ?>
