<?php
require 'inc/config.php';
require_login('admin');
$f = report_filter_from_get();
$rows = report_rows($pdo, $f);
$total = array_sum(array_column($rows, 'total'));
$services = $pdo->query('SELECT id,nama FROM services ORDER BY nama')->fetchAll();
$qs = http_build_query($f);
$title = 'Laporan Keuangan';
include 'inc/header.php';
?>
<h1>Laporan Keuangan</h1><div class="sub no-print">Filter data, lalu cetak atau ekspor ke PDF / Excel.</div>
<form class="card no-print" method="get" style="margin-bottom:16px">
  <div class="row">
    <div><label>Dari Tanggal</label><input type="date" name="from" value="<?= e($f['from']) ?>"></div>
    <div><label>Sampai Tanggal</label><input type="date" name="to" value="<?= e($f['to']) ?>"></div>
    <div><label>Status</label><select name="status"><option value="">Semua</option>
      <?php foreach (STATUS_LABEL as $k => $v): ?><option value="<?= $k ?>" <?= $f['status'] === $k ? 'selected' : '' ?>><?= e($v[0]) ?></option><?php endforeach; ?></select></div>
    <div><label>Layanan</label><select name="service"><option value="">Semua</option>
      <?php foreach ($services as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (string)$f['service'] === (string)$s['id'] ? 'selected' : '' ?>><?= e($s['nama']) ?></option><?php endforeach; ?></select></div>
    <div><label>Metode</label><select name="metode"><option value="">Semua</option>
      <?php foreach (METODE as $m): ?><option <?= $f['metode'] === $m ? 'selected' : '' ?>><?= e($m) ?></option><?php endforeach; ?></select></div>
  </div>
  <p><button>Terapkan Filter</button>
     <a class="btn btn-o" href="admin_laporan.php">Reset</a>
     <button type="button" class="btn-g" id="bPdf">📄 Ekspor PDF</button>
     <a class="btn btn-g" href="admin_export.php?<?= e($qs) ?>">📊 Ekspor Excel</a>
     <button type="button" class="btn-o" onclick="window.print()">🖨️ Cetak</button></p>
</form>
<div class="stats">
  <div class="card stat"><div class="l">Jumlah Transaksi</div><div class="n"><?= count($rows) ?></div></div>
  <div class="card stat"><div class="l">Total Nominal</div><div class="n"><?= rp($total) ?></div></div>
</div>
<div class="tw"><table id="tbl">
<thead><tr><th>No</th><th>Tanggal</th><th>Invoice</th><th>Siswa</th><th>Layanan</th><th>Metode</th><th>Status</th><th class="r">Total</th></tr></thead>
<tbody>
<?php foreach ($rows as $i => $o): ?>
<tr><td><?= $i + 1 ?></td><td><?= tgl($o['tanggal']) ?></td><td><?= e($o['invoice_no']) ?></td><td><?= e($o['siswa']) ?></td>
<td><?= e($o['service_name']) ?> x<?= (int)$o['qty'] ?></td><td><?= e($o['metode'] ?? '-') ?></td>
<td><?= e(STATUS_LABEL[$o['status']][0]) ?></td><td class="r"><?= rp($o['total']) ?></td></tr>
<?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="8" style="text-align:center;color:var(--mut)">Tidak ada data.</td></tr><?php endif; ?>
</tbody>
<tfoot><tr><td colspan="7" class="r"><b>TOTAL</b></td><td class="r"><b><?= rp($total) ?></b></td></tr></tfoot>
</table></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
document.getElementById('bPdf').onclick=function(){
  if(!window.jspdf){alert('Library PDF gagal dimuat (butuh internet). Gunakan tombol Cetak lalu Save as PDF.');return}
  const doc=new jspdf.jsPDF('l','mm','a4');
  doc.setFontSize(16);doc.text('Laporan Keuangan BelajarKu',14,15);
  doc.setFontSize(9);doc.text('Periode: <?= e($f['from'] ?: 'awal') ?> s/d <?= e($f['to'] ?: 'sekarang') ?>  |  Dicetak: <?= date('d M Y H:i') ?>',14,21);
  doc.autoTable({html:'#tbl',startY:26,styles:{fontSize:8},headStyles:{fillColor:[47,91,234]},footStyles:{fillColor:[240,243,250],textColor:0},columnStyles:{7:{halign:'right'}}});
  doc.save('laporan-keuangan.pdf');
};
</script>
<?php include 'inc/footer.php'; ?>
