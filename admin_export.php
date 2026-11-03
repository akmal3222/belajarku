<?php
// Ekspor laporan ke Excel (.xls berbasis tabel HTML, terbuka langsung di Microsoft Excel / LibreOffice)
require 'inc/config.php';
require_login('admin');
$f = report_filter_from_get();
$rows = report_rows($pdo, $f);
$total = array_sum(array_column($rows, 'total'));

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="laporan-keuangan-' . date('Ymd') . '.xls"');
echo "\xEF\xBB\xBF";
?>
<html><head><meta charset="utf-8"></head><body>
<table border="1">
<tr><td colspan="8"><b>LAPORAN KEUANGAN BELAJARKU</b></td></tr>
<tr><td colspan="8">Periode: <?= e($f['from'] ?: 'awal') ?> s/d <?= e($f['to'] ?: 'sekarang') ?> | Dicetak: <?= date('d M Y H:i') ?></td></tr>
<tr><th>No</th><th>Tanggal</th><th>Invoice</th><th>Siswa</th><th>Layanan</th><th>Metode</th><th>Status</th><th>Total (Rp)</th></tr>
<?php foreach ($rows as $i => $o): ?>
<tr><td><?= $i + 1 ?></td><td><?= tgl($o['tanggal']) ?></td><td><?= e($o['invoice_no']) ?></td><td><?= e($o['siswa']) ?></td>
<td><?= e($o['service_name']) ?> x<?= (int)$o['qty'] ?></td><td><?= e($o['metode'] ?? '-') ?></td>
<td><?= e(STATUS_LABEL[$o['status']][0]) ?></td><td><?= (int)$o['total'] ?></td></tr>
<?php endforeach; ?>
<tr><td colspan="7"><b>TOTAL</b></td><td><b><?= (int)$total ?></b></td></tr>
</table></body></html>
