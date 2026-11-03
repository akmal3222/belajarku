<?php
require 'inc/config.php';
$title = 'Daftar';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = trim($_POST['nama'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';
    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $err = 'Nama dan email wajib diisi dengan benar.';
    elseif (strlen($pass) < 6) $err = 'Password minimal 6 karakter.';
    else {
        $c = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $c->execute([$email]);
        if ($c->fetch()) $err = 'Email sudah terdaftar.';
        else {
            $pdo->prepare('INSERT INTO users (nama,email,password,role) VALUES (?,?,?,?)')
                ->execute([$nama, $email, password_hash($pass, PASSWORD_DEFAULT), 'siswa']);
            $id = $pdo->lastInsertId();
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int)$id, 'nama' => $nama, 'email' => $email, 'role' => 'siswa'];
            redirect('index.php');
        }
    }
}
include 'inc/header.php';
?>
<div class="card form"><h2>Daftar Akun Siswa</h2>
<?php if ($err): ?><div class="alert alert-err"><?= e($err) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  <label>Nama Lengkap</label><input name="nama" required value="<?= e($_POST['nama'] ?? '') ?>">
  <label>Email</label><input name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>">
  <label>Password (min. 6 karakter)</label><input name="password" type="password" minlength="6" required>
  <p><button>Daftar</button></p>
</form></div>
<?php include 'inc/footer.php'; ?>
