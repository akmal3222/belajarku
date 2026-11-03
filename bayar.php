<?php
require 'inc/config.php';
$u = require_login('siswa');
$st = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$st->execute([(int)($_GET['id'] ?? 0), $u['id']]);
$o = $st->fetch();
if (!$o) redirect('pesanan.php');
if ($o['status'] === 'paid') redirect('invoice.php?id=' . $o['id']);
if ($o['status'] === 'cancelled') { flash('Pesanan sudah dibatalkan.', 'err'); redirect('pesanan.php'); }

$err = '';
// ===== PAYMENT GATEWAY SIMULATOR =====
// Pada sistem nyata, bagian ini digantikan callback/webhook dari Midtrans/Xendit.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $metode = $_POST['metode'] ?? '';
    $hasil = $_POST['hasil'] ?? '';
    if (!in_array($metode, METODE, true) || !in_array($hasil, ['success', 'failed'], true)) {
        $err = 'Pilih metode pembayaran.';
    } else {
        $pdo->beginTransaction();
        $ref = 'SIM-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        $pdo->prepare('INSERT INTO payments (order_id,metode,ref_gateway,amount,status,paid_at) VALUES (?,?,?,?,?,NOW())')
            ->execute([$o['id'], $metode, $ref, $o['total'], $hasil]);
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')
            ->execute([$hasil === 'success' ? 'paid' : 'failed', $o['id']]);
        $pdo->commit();
        if ($hasil === 'success') { flash('Pembayaran berhasil. Terima kasih!'); redirect('invoice.php?id=' . $o['id']); }
        $err = 'Pembayaran gagal / kedaluwarsa. Silakan coba lagi.';
        $o['status'] = 'failed';
    }
}
$va = '88' . str_pad($o['id'], 4, '0', STR_PAD_LEFT) . str_pad($u['id'], 4, '0', STR_PAD_LEFT) . '0001';
$title = 'Pembayaran';
include 'inc/header.php';
?>
<div class="card form" style="max-width:560px">
    <h2>Pembayaran</h2>
    <?php if ($err): ?><div class="alert alert-err"><?= e($err) ?></div><?php endif; ?>
    <div class="sub"><?= e($o['invoice_no']) ?> &middot; <?= e($o['service_name']) ?> x<?= (int)$o['qty'] ?></div>
    <div class="price" style="font-size:28px"><?= rp($o['total']) ?></div>
    <form method="post"><?= csrf_field() ?>
        <label>Pilih Metode Pembayaran</label>
        <div class="pm">
            <?php foreach (METODE as $i => $m): ?>
            <label><input type="radio" name="metode" value="<?= e($m) ?>" <?= $i === 0 ? 'checked' : '' ?>>
                <?= e($m) ?></label>
            <?php endforeach; ?>
        </div>
        <div class="sim"><b>🧪 Payment Gateway Simulator</b>
            <div id="instr" style="margin:8px 0"></div>
            <small>Ini hanya simulasi. Tombol di bawah meniru callback status dari payment gateway.</small>
            <p><button class="btn-g" name="hasil" value="success">Simulasikan Bayar Berhasil</button>
                <button class="btn-r" name="hasil" value="failed">Simulasikan Gagal</button>
            </p>
        </div>
    </form>
</div>
<script>
const va = '<?= e($va) ?>';

function instr() {
    const m = document.querySelector('[name=metode]:checked').value,
        b = document.getElementById('instr');
    if (m === 'QRIS') b.innerHTML = 'Scan QR berikut (dummy):<div class="qr"></div>';
    else if (m.startsWith('Virtual')) b.innerHTML = 'Nomor Virtual Account: <b style="font-size:18px">' + va + '</b>';
    else b.textContent = 'Notifikasi pembayaran dikirim ke aplikasi GoPay Anda (dummy).';
}
document.querySelectorAll('[name=metode]').forEach(r => r.onchange = instr);
instr();
</script>
<?php include 'inc/footer.php'; ?>