<?php
require 'inc/config.php';
$u = require_login('siswa');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'batal') {
    csrf_check();
    $pdo->prepare("UPDATE orders SET status='cancelled' WHERE id=? AND user_id=? AND status IN ('pending','failed')")
        ->execute([(int)$_POST['id'], $u['id']]);
    flash('Pesanan dibatalkan.');
    redirect('pesanan.php');
}
$st = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$st->execute([$u['id']]);
$list = $st->fetchAll();
$title = 'Pesanan Saya';
include 'inc/header.php';
?>
<h1>Pesanan Saya</h1><div class="sub">Riwayat pemesanan dan pembayaran.</div>
<div class="tw"><table>
<tr><th>Invoice</th><th>Layanan</th><th>Jadwal</th><th class="r">Total</th><th>Status</th><th></th></tr>
<?php foreach ($list as $o): ?>
<tr>
  <td><?= e($o['invoice_no']) ?></td>
  <td><?= e($o['service_name']) ?> <small>(x<?= (int)$o['qty'] ?>)</small></td>
  <td><?= tgl($o['jadwal']) ?></td>
  <td class="r"><?= rp($o['total']) ?></td>
  <td><?= status_badge($o['status']) ?></td>
  <td>
  <?php if (in_array($o['status'], ['pending', 'failed'])): ?>
    <a class="btn btn-sm" href="bayar.php?id=<?= (int)$o['id'] ?>"><?= $o['status'] === 'failed' ? 'Coba Lagi' : 'Bayar' ?></a>
    <form class="inline" method="post" onsubmit="return confirm('Batalkan pesanan ini?')"><?= csrf_field() ?>
      <input type="hidden" name="aksi" value="batal"><input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
      <button class="btn-sm btn-r">Batal</button></form>
  <?php elseif ($o['status'] === 'paid'): ?>
    <a class="btn btn-sm btn-o" href="invoice.php?id=<?= (int)$o['id'] ?>">Invoice</a>
  <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$list): ?><tr><td colspan="6" style="text-align:center;color:var(--mut)">Belum ada pesanan.</td></tr><?php endif; ?>
</table></div>
<?php include 'inc/footer.php'; ?>
