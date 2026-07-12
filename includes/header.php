<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;
$nama_user = $_SESSION['nama_user'] ?? '';
$is_admin = ($_SESSION['role_nama'] ?? '') === 'ADMIN';
$current_page = basename($_SERVER['PHP_SELF']);

// ── Cek notifikasi pengingat ──
$tampil_popup = false;
$popup_judul = '';
$popup_tipe = '';
$popup_items = [];

if ($is_logged_in) {
    require_once __DIR__ . '/../config/database.php';
    $db = new Database();
    $conn = $db->getConn();

    // Cek jadwal 0–30 menit ke depan
    $stmt = $conn->prepare("
        SELECT jr.id_jadwal, jr.jam_minum, jr.id_obat_user, m.nama_obat,
               TIMESTAMPDIFF(MINUTE, CURTIME(), jr.jam_minum) AS menit_menuju
        FROM jadwal_reminder jr
        JOIN obat o ON jr.id_obat_user = o.id_obat_user
        JOIN master_obat m ON o.id_obat = m.id_obat
        WHERE o.user_id = ? AND jr.status_hari_ini = 0
          AND TIME_TO_SEC(TIMEDIFF(jr.jam_minum, CURTIME())) BETWEEN 0 AND 1800
        ORDER BY jr.jam_minum ASC
        LIMIT 3
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $upcoming_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!empty($upcoming_list)) {
        $tampil_popup = true;
        $popup_judul = 'Waktunya Minum Obat!';
        $popup_tipe = 'upcoming';
        $popup_items = $upcoming_list;
    } else {
        // Cek jadwal terlewat
        $stmt = $conn->prepare("
            SELECT jr.jam_minum, jr.id_obat_user, m.nama_obat
            FROM jadwal_reminder jr
            JOIN obat o ON jr.id_obat_user = o.id_obat_user
            JOIN master_obat m ON o.id_obat = m.id_obat
            WHERE o.user_id = ? AND jr.status_hari_ini = 99
              AND DATE(jr.created_at) = CURDATE()
            ORDER BY jr.jam_minum DESC
            LIMIT 3
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $terlewat_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (!empty($terlewat_list)) {
            $tampil_popup = true;
            $popup_judul = 'Ada Jadwal Terlewat';
            $popup_tipe = 'terlewat';
            $popup_items = $terlewat_list;
        }
    }
}
?><!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Obat &mdash; Pantau Jadwal Minum Obat</title>
    <link rel="stylesheet" href="<?= $base ?? '.' ?>/assets/css/style.css">
    <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'light')</script>
</head>
<body>
<div class="page-shell">

<nav class="navbar">
    <div class="navbar-inner">
        <a href="<?= $base ?? '.' ?>/index.php" class="navbar-brand">
            <span class="brand-icon material-symbols-sharp">medication</span>
            <span class="brand-text">Pengingat Obat</span>
        </a>

        <div class="nav-links">
            <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="<?= $base ?? '.' ?>/index.php">Beranda</a>
            <a class="nav-link <?= ($current_page == 'tentang.php') ? 'active' : '' ?>" href="<?= $base ?? '.' ?>/views/tentang.php">Tentang Kami</a>
            <a class="nav-link <?= ($current_page == 'kontak.php') ? 'active' : '' ?>" href="<?= $base ?? '.' ?>/views/kontak.php">Kontak</a>
        </div>

        <div class="nav-right">
            <button class="theme-toggle" onclick="toggleTheme()" title="Mode Gelap">
                <span class="material-symbols-sharp" id="navThemeIcon">dark_mode</span>
            </button>

            <?php if ($is_logged_in): ?>
            <div class="user-dropdown" id="userDropdown">
                <button class="user-dropdown-btn" onclick="toggleDropdown()">
                    <span class="avatar"><?= strtoupper(substr($nama_user, 0, 1)) ?></span>
                    <span class="user-name-text"><?= htmlspecialchars($nama_user) ?></span>
                </button>
                <div class="dropdown-menu">
                    <?php if ($is_admin): ?>
                    <a class="dropdown-item" href="<?= $base ?? '.' ?>/views/dashboardApoteker.php">Dashboard</a>
                    <?php else: ?>
                    <a class="dropdown-item" href="<?= $base ?? '.' ?>/views/dasboard.php">Dashboard</a>
                    <?php endif; ?>
                    <div class="dropdown-divider" style="height:1px;background:var(--outline-variant);margin:4px 0"></div>
                    <a class="dropdown-item text-danger" href="<?= $base ?? '.' ?>/proses/prosesLogout.php">Keluar</a>
                </div>
            </div>
            <?php else: ?>
            <a class="btn btn-outline btn-sm" href="<?= $base ?? '.' ?>/views/masuk.php">Masuk</a>
            <a class="btn btn-primary btn-sm" href="<?= $base ?? '.' ?>/views/daftar.php">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?php $is_dashboard_page = in_array($current_page, ['dasboard.php', 'dashboardApoteker.php']); ?>
<?php if ($tampil_popup && !$is_dashboard_page): ?>
<div class="modal-overlay" id="notifOverlay">
    <div class="modal-card">
        <div class="modal-card-header">
            <span class="material-symbols-sharp" style="font-size:40px;color:<?= $popup_tipe === 'terlewat' ? 'var(--error)' : 'var(--secondary)' ?>;">
                <?= $popup_tipe === 'terlewat' ? 'warning' : 'notifications_active' ?>
            </span>
            <h3><?= $popup_judul ?></h3>
        </div>
        <div class="modal-card-body">
            <?php foreach ($popup_items as $item): ?>
            <div class="notif-item">
                <span><?= htmlspecialchars($item['nama_obat']) ?></span>
                <span class="text-muted"><?= substr($item['jam_minum'], 0, 5) ?> WITA</span>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="modal-card-footer">
            <a href="<?= ($base ?? '.') ?>/views/<?= $is_admin ? 'dashboardApoteker.php' : 'dasboard.php' ?>" class="btn btn-primary btn-full" onclick="this.closest('.modal-overlay').remove()">
                <span class="material-symbols-sharp">dashboard</span> Lihat Dashboard
            </a>
            <button onclick="this.closest('.modal-overlay').remove()" class="btn btn-full" style="background:var(--surface-container);color:var(--on-surface-variant);margin-top:8px">Tutup</button>
        </div>
    </div>
</div>
<style>
.modal-overlay {
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,0.6);
    display: flex; align-items: center; justify-content: center;
    padding: var(--space-4);
}
.modal-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    max-width: 420px; width: 100%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    overflow: hidden;
}
.modal-card-header {
    text-align: center; padding: var(--space-5) var(--space-4) 0;
}
.modal-card-header h3 {
    margin: var(--space-2) 0 0;
    font-weight: var(--fw-bold);
}
.modal-card-body {
    padding: var(--space-4);
}
.notif-item {
    display: flex; justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--outline-variant);
}
.notif-item:last-child { border-bottom: none; }
.modal-card-footer {
    padding: 0 var(--space-4) var(--space-4);
}
.btn-full { width: 100%; justify-content: center; }
</style>
<script>document.getElementById('notifOverlay')?.addEventListener('click',function(e){if(e.target===this)this.remove()})</script>
<?php endif; ?>
