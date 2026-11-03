<?php
require 'inc/config.php';
$u = require_login('siswa');
$st = $pdo->prepare('SELECT * FROM services WHERE id = ?');
$st->execute([(int)($_GET['id'] ?? 0)]);
$s = $st->fetch();
if (!$s) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $qty = max(1, min(50, (int)($_POST['qty'] ?? 1)));
    $jadwal = $_POST['jadwal'] ?? date('Y-m-d');
    if (strtotime($jadwal) < strtotime(date('Y-m-d'))) $jadwal = date('Y-m-d');
    $pdo->prepare('INSERT INTO orders (invoice_no,user_id,service_id,service_name,tipe,harga,qty,total,jadwal,catatan,status)
                   VALUES (?,?,?,?,?,?,?,?,?,?,?)')
        ->execute([uniqid('TMP'), $u['id'], $s['id'], $s['nama'], $s['tipe'], $s['harga'], $qty, $s['harga'] * $qty, $jadwal, trim($_POST['catatan'] ?? ''), 'pending']);
    $id = (int)$pdo->lastInsertId();
    $inv = 'INV-' . date('Ymd') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    $pdo->prepare('UPDATE orders SET invoice_no = ? WHERE id = ?')->execute([$inv, $id]);
    redirect('bayar.php?id=' . $id);
}
$title = 'Pesan ' . $s['nama'];
include 'inc/header.php';
?>
<div class="card form"><h2>Pesan: <?= e($s['nama']) ?></h2>
<div class="sub"><?= e($s['tipe']) ?> &middot; <?= rp($s['harga']) ?> / <?= e($s['satuan']) ?></div>
<form method="post"><?= csrf_field() ?>
  <label><?= $s['satuan'] === 'paket' ? 'Jumlah Paket' : 'Jumlah Pertemuan' ?></label>
  <input id="qty" name="qty" type="number" min="1" max="50" value="1" required>
  <label>Tanggal Mulai</label><input name="jadwal" type="date" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required>
  <label>Catatan (opsional)</label><textarea name="catatan" rows="2"></textarea>
  <div class="price" id="tot"></div>
  <button>Buat Pesanan &amp; Lanjut Bayar</button>
</form></div>
<script>
const harga=<?= (int)$s['harga'] ?>;
const q=document.getElementById('qty'),t=document.getElementById('tot');
function upd(){t.textContent='Total: Rp '+(harga*(parseInt(q.value)||0)).toLocaleString('id-ID')}
q.oninput=upd;upd();
</script>
<?php include 'inc/footer.php'; ?>
