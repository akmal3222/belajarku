<?php
require 'inc/config.php';
require_login('admin');
$list = $pdo->query('SELECT o.*, u.nama AS siswa FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC')->fetchAll();
$title = 'Semua Pesanan';
include 'inc/header.php';
?>
<h1>Semua Pesanan</h1>
<div class="tw"><table>
<tr><th>Invoice</th><th>Siswa</th><th>Layanan</th><th class="r">Total</th><th>Status</th><th></th></tr>
<?php foreach ($list as $o): ?>
<tr><td><?= e($o['invoice_no']) ?></td><td><?= e($o['siswa']) ?></td><td><?= e($o['service_name']) ?> x<?= (int)$o['qty'] ?></td>
<td class="r"><?= rp($o['total']) ?></td><td><?= status_badge($o['status']) ?></td>
<td><?php if ($o['status'] === 'paid'): ?><a class="btn btn-sm btn-o" href="invoice.php?id=<?= (int)$o['id'] ?>">Invoice</a><?php endif; ?></td></tr>
<?php endforeach; ?>
</table></div>
<?php include 'inc/footer.php'; ?>
