<?php
require 'inc/config.php';
require_login('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = $_POST['aksi'] ?? '';
    if ($aksi === 'hapus') {
        $pdo->prepare('DELETE FROM services WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('Layanan dihapus.');
    } else {
        $nama = trim($_POST['nama'] ?? '');
        $tipe = in_array($_POST['tipe'] ?? '', ['Les Privat', 'Kursus Online']) ? $_POST['tipe'] : 'Les Privat';
        $satuan = in_array($_POST['satuan'] ?? '', ['pertemuan', 'paket']) ? $_POST['satuan'] : 'pertemuan';
        $harga = max(0, (int)($_POST['harga'] ?? 0));
        $desk = trim($_POST['deskripsi'] ?? '');
        if ($nama === '') { flash('Nama layanan wajib diisi.', 'err'); }
        elseif (!empty($_POST['id'])) {
            $pdo->prepare('UPDATE services SET nama=?,tipe=?,harga=?,satuan=?,deskripsi=? WHERE id=?')
                ->execute([$nama, $tipe, $harga, $satuan, $desk, (int)$_POST['id']]);
            flash('Layanan diperbarui.');
        } else {
            $pdo->prepare('INSERT INTO services (nama,tipe,harga,satuan,deskripsi) VALUES (?,?,?,?,?)')
                ->execute([$nama, $tipe, $harga, $satuan, $desk]);
            flash('Layanan ditambahkan.');
        }
    }
    redirect('admin_layanan.php');
}
$edit = null;
if (!empty($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM services WHERE id = ?');
    $st->execute([(int)$_GET['edit']]);
    $edit = $st->fetch() ?: null;
}
$list = $pdo->query('SELECT * FROM services ORDER BY id')->fetchAll();
$title = 'Kelola Layanan';
include 'inc/header.php';
?>
<h1>Kelola Layanan</h1>
<div class="card" style="margin-bottom:18px"><h2><?= $edit ? 'Edit' : 'Tambah' ?> Layanan</h2>
<form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= e($edit['id'] ?? '') ?>">
  <div class="row">
    <div><label>Nama</label><input name="nama" required value="<?= e($edit['nama'] ?? '') ?>"></div>
    <div><label>Tipe</label><select name="tipe">
      <?php foreach (['Les Privat', 'Kursus Online'] as $t): ?><option <?= ($edit['tipe'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?></select></div>
    <div><label>Harga (Rp)</label><input name="harga" type="number" min="0" required value="<?= e($edit['harga'] ?? '') ?>"></div>
    <div><label>Satuan</label><select name="satuan">
      <?php foreach (['pertemuan', 'paket'] as $t): ?><option <?= ($edit['satuan'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?></select></div>
  </div>
  <label>Deskripsi</label><textarea name="deskripsi" rows="2"><?= e($edit['deskripsi'] ?? '') ?></textarea>
  <p><button>Simpan</button> <?php if ($edit): ?><a class="btn btn-o" href="admin_layanan.php">Batal</a><?php endif; ?></p>
</form></div>
<div class="tw"><table>
<tr><th>Nama</th><th>Tipe</th><th class="r">Harga</th><th></th></tr>
<?php foreach ($list as $s): ?>
<tr><td><?= e($s['nama']) ?></td><td><?= e($s['tipe']) ?></td><td class="r"><?= rp($s['harga']) ?> / <?= e($s['satuan']) ?></td>
<td><a class="btn btn-sm" href="admin_layanan.php?edit=<?= (int)$s['id'] ?>">Edit</a>
<form class="inline" method="post" onsubmit="return confirm('Hapus layanan ini?')"><?= csrf_field() ?>
<input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$s['id'] ?>"><button class="btn-sm btn-r">Hapus</button></form></td></tr>
<?php endforeach; ?>
</table></div>
<?php include 'inc/footer.php'; ?>
