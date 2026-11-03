<?php
const METODE = ['Virtual Account BCA', 'Virtual Account Mandiri', 'QRIS', 'GoPay (E-Wallet)'];
const STATUS_LABEL = [
    'pending'   => ['Menunggu Pembayaran', 'b-warn'],
    'paid'      => ['Lunas', 'b-ok'],
    'failed'    => ['Gagal', 'b-err'],
    'cancelled' => ['Dibatalkan', 'b-gray'],
];

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rp($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function tgl($d, $time = false) {
    if (!$d) return '-';
    return date($time ? 'd M Y H:i' : 'd M Y', strtotime($d));
}
function status_badge($s) {
    [$label, $cls] = STATUS_LABEL[$s] ?? [$s, 'b-gray'];
    return '<span class="badge ' . $cls . '">' . e($label) . '</span>';
}
function redirect($url) { header('Location: ' . $url); exit; }

function user() { return $_SESSION['user'] ?? null; }
function require_login($role = null) {
    $u = user();
    if (!$u) redirect('login.php');
    if ($role && $u['role'] !== $role) redirect('index.php');
    return $u;
}

function flash($msg = null, $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return; }
    if (!empty($_SESSION['flash'])) {
        [$m, $t] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return '<div class="alert alert-' . e($t) . '">' . e($m) . '</div>';
    }
    return '';
}

// CSRF sederhana
function csrf_field() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}
function csrf_check() {
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        http_response_code(400);
        die('Token tidak valid. Muat ulang halaman.');
    }
}

/**
 * Query laporan keuangan dengan filter.
 * Tanggal laporan = tanggal bayar (jika ada), selain itu tanggal pesanan dibuat.
 */
function report_rows(PDO $pdo, array $f) {
    $sql = "SELECT o.*, u.nama AS siswa, p.metode, p.ref_gateway,
                   COALESCE(p.paid_at, o.created_at) AS tanggal
            FROM orders o
            JOIN users u ON u.id = o.user_id
            LEFT JOIN payments p ON p.order_id = o.id AND p.status = 'success'
            WHERE 1=1";
    $args = [];
    if (!empty($f['from']))    { $sql .= " AND DATE(COALESCE(p.paid_at, o.created_at)) >= ?"; $args[] = $f['from']; }
    if (!empty($f['to']))      { $sql .= " AND DATE(COALESCE(p.paid_at, o.created_at)) <= ?"; $args[] = $f['to']; }
    if (!empty($f['status']))  { $sql .= " AND o.status = ?"; $args[] = $f['status']; }
    if (!empty($f['service'])) { $sql .= " AND o.service_id = ?"; $args[] = (int)$f['service']; }
    if (!empty($f['metode']))  { $sql .= " AND p.metode = ?"; $args[] = $f['metode']; }
    $sql .= " ORDER BY tanggal ASC, o.id ASC";
    $st = $pdo->prepare($sql);
    $st->execute($args);
    return $st->fetchAll();
}

function report_filter_from_get() {
    return [
        'from'    => $_GET['from'] ?? '',
        'to'      => $_GET['to'] ?? '',
        'status'  => array_key_exists('status', $_GET) ? $_GET['status'] : 'paid',
        'service' => $_GET['service'] ?? '',
        'metode'  => $_GET['metode'] ?? '',
    ];
}
