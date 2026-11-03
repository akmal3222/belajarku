<?php
require 'inc/config.php';
$title = 'Masuk';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $st->execute([strtolower(trim($_POST['email'] ?? ''))]);
    $row = $st->fetch();
    if ($row && password_verify($_POST['password'] ?? '', $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $row['id'], 'nama' => $row['nama'], 'email' => $row['email'], 'role' => $row['role']];
        redirect($row['role'] === 'admin' ? 'admin_dashboard.php' : 'index.php');
    }
    $err = 'Email atau password salah.';
}
include 'inc/header.php';
?>
<div class="card form"><h2>Masuk</h2>
<?php if ($err): ?><div class="alert alert-err"><?= e($err) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  <label>Email</label><input name="email" type="email" required>
  <label>Password</label><input name="password" type="password" required>
  <p><button>Masuk</button></p>
</form>
<div class="alert alert-info" style="font-size:12px">Akun demo:<br>Admin: admin@belajarku.id / admin123<br>Siswa: siswa@belajarku.id / siswa123</div>
<small>Belum punya akun? <a href="register.php">Daftar</a></small></div>
<?php include 'inc/footer.php'; ?>
