<?php
require 'inc/config.php';
$u = require_login();
$st = $pdo->prepare("SELECT o.*, us.nama AS siswa, us.email, p.metode, p.ref_gateway, p.paid_at
                     FROM orders o JOIN users us ON us.id = o.user_id
                     LEFT JOIN payments p ON p.order_id = o.id AND p.status = 'success'
                     WHERE o.id = ?");
$st->execute([(int)($_GET['id'] ?? 0)]);
$o = $st->fetch();
if (!$o || ($u['role'] === 'siswa' && (int)$o['user_id'] !== (int)$u['id'])) redirect('index.php');
$title = 'Invoice ' . $o['invoice_no'];
include 'inc/header.php';
$back = $u['role'] === 'admin' ? 'admin_pesanan.php' : 'pesanan.php';
?>
<div class="no-print" style="max-width:760px;margin:0 auto 14px;display:flex;justify-content:space-between">
  <a class="btn btn-o" href="<?= $back ?>">← Kembali</a>
  <button onclick="window.print()">🖨️ Cetak Invoice</button>
</div>
<div class="invoice">
  <div class="hd">
    <div><div class="logo">📚 BelajarKu</div><small>Jasa Les Privat &amp; Kursus Online</small></div>
    <div class="r"><h2 style="margin:0">INVOICE</h2><b><?= e($o['invoice_no']) ?></b><br><small>Tanggal: <?= tgl($o['created_at']) ?></small></div>
  </div>
  <div style="display:flex;justify-content:space-between;margin-bottom:18px">
    <div><small style="color:var(--mut)">Ditagihkan kepada</small><br><b><?= e($o['siswa']) ?></b><br><?= e($o['email']) ?></div>
    <div><?= $o['status'] === 'paid' ? '<span class="stamp">LUNAS</span>' : '<span class="stamp u">BELUM LUNAS</span>' ?></div>
  </div>
  <table>
    <tr><th>Deskripsi</th><th>Jadwal Mulai</th><th class="r">Harga</th><th class="r">Qty</th><th class="r">Subtotal</th></tr>
    <tr><td><?= e($o['service_name']) ?><br><small><?= e($o['tipe']) ?></small></td><td><?= tgl($o['jadwal']) ?></td>
        <td class="r"><?= rp($o['harga']) ?></td><td class="r"><?= (int)$o['qty'] ?></td><td class="r"><?= rp($o['total']) ?></td></tr>
    <tr><td colspan="4" class="r"><b>TOTAL</b></td><td class="r"><b><?= rp($o['total']) ?></b></td></tr>
  </table>
  <p style="margin-top:18px;font-size:14px">
    <b>Metode Pembayaran:</b> <?= e($o['metode'] ?? '-') ?><br>
    <b>Waktu Bayar:</b> <?= tgl($o['paid_at'], true) ?><br>
    <b>Ref. Gateway:</b> <?= e($o['ref_gateway'] ?? '-') ?></p>
  <p style="color:var(--mut);font-size:12px;margin-top:30px">Terima kasih telah belajar bersama BelajarKu. Invoice ini dibuat oleh sistem.</p>
</div>
<?php include 'inc/footer.php'; ?>
