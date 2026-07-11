<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;
$nama_user = $_SESSION['nama_user'] ?? '';
$is_admin = ($_SESSION['role_nama'] ?? '') === 'ADMIN';
$current_page = basename($_SERVER['PHP_SELF']);
?><!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ForestView Health &mdash; Pantau Jadwal Minum Obat</title>
    <link rel="stylesheet" href="<?= $base ?? '.' ?>/assets/css/style.css">
    <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'light')</script>
</head>
<body>
<div class="page-shell">

<nav class="navbar">
    <div class="navbar-inner">
        <a href="<?= $base ?? '.' ?>/index.php" class="navbar-brand">
            <span class="brand-icon material-symbols-sharp">forest</span>
            <span class="brand-text">ForestView Health</span>
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
