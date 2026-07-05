<?php
session_start();
require_once '../config/database.php';

$pdo = Database::connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: ../index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM master_obat WHERE master_id = ?");
$stmt->execute([$id]);
$obat = $stmt->fetch();

if (!$obat) {
    header("Location: ../index.php?error=Data obat tidak ditemukan!");
    exit;
}

$is_logged_in = isset($_SESSION['user_id']);
$is_admin = $is_logged_in && ($_SESSION['role_nama'] ?? '') === 'ADMIN';
$is_pasien = $is_logged_in && !$is_admin;
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Obat - <?= htmlspecialchars($obat['nama_obat']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'light')</script>
    <style>
        :root {
            --bg-body: #f0f2f5; --bg-card: #ffffff; --bg-navbar: #ffffff;
            --text-primary: #1a1a2e; --text-secondary: #495057; --text-muted: #6c757d;
            --bg-input: #ffffff; --text-input: #1a1a2e;
            --border-color: #e8e8ef;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06); --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
            --accent: #3688C9; --accent-hover: #3659C9;
            --success: #28a745; --danger: #dc3545; --warning: #ffc107;
        }
        [data-theme="dark"] {
            --bg-body: #0d0d1a; --bg-card: #1a1a30; --bg-navbar: #141428;
            --text-primary: #f8f9fa; --text-secondary: #e0e0e8; --text-muted: #a8a8b8;
            --bg-input: #242442; --text-input: #ffffff;
            --border-color: #343459;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.4); --shadow-md: 0 4px 16px rgba(0,0,0,0.6);
        }
        * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        .card { background-color: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); }
        .form-control, .form-select { background-color: var(--bg-input); color: var(--text-input); border-color: var(--border-color); }
        .form-control:focus, .form-select:focus { background-color: var(--bg-input); color: var(--text-input); border-color: var(--accent); box-shadow: 0 0 0 3px rgba(54,136,201,0.15); }
        .text-muted { --bs-text-opacity: 1; color: var(--text-muted) !important; }
        body {
            background-color: var(--bg-body);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: var(--bg-navbar) !important;
            border-bottom: 1px solid var(--border-color);
        }
        .navbar-brand h2 { color: var(--accent); font-weight: 700; }
        .detail-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            max-width: 600px;
            margin: 0 auto;
        }
        .detail-header {
            background: var(--accent);
            border-radius: 20px 20px 0 0;
            padding: 32px;
            color: white;
            text-align: center;
        }
        .detail-header i { font-size: 2.5rem; margin-bottom: 8px; opacity: 0.9; }
        .detail-body { padding: 28px 32px; }
        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-color);
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label {
            min-width: 130px;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        .detail-value { color: var(--text-primary); font-size: 0.95rem; }
        .badge-stok {
            display: inline-block; padding: 4px 14px; border-radius: 20px; font-weight: 600; font-size: 0.8rem;
        }
        .badge-ok { background: #d4edda; color: #155724; }
        .badge-warn { background: #f8d7da; color: #721c24; }
        .btn-action {
            border-radius: 12px; padding: 10px 24px; font-weight: 600; font-size: 0.9rem; border: none; transition: all 0.2s ease; text-decoration: none; display: inline-block;
        }
        .btn-action:hover { transform: translateY(-1px); box-shadow: var(--shadow-md); }
        .theme-toggle-mini {
            background: none; border: 1px solid var(--border-color); border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-secondary); font-size: 0.9rem; text-decoration: none;
        }
        .theme-toggle-mini:hover { border-color: var(--accent); color: var(--accent); }
        footer {
            background-color: var(--bg-navbar); border-top: 1px solid var(--border-color); padding: 20px 0; margin-top: auto; text-align: center;
        }
        footer p { color: var(--text-muted); margin: 0; font-size: 0.85rem; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <h2 class="m-0 fs-5"><i class="fa-solid fa-capsules me-2"></i>PengingatObat</h2>
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="javascript:void(0)" class="theme-toggle-mini" onclick="toggleTheme()" title="Mode Gelap">
                <i class="fa-solid fa-moon" id="themeIcon"></i>
            </a>
            <?php if ($is_logged_in): ?>
                <a href="dashboard.php" class="btn btn-sm rounded-pill px-3" style="background: var(--accent); color: white;">
                    <i class="fa-solid fa-gauge me-1"></i>Dashboard
                </a>
            <?php else: ?>
                <a href="../views/login.php" class="btn btn-sm rounded-pill px-3" style="background: var(--accent); color: white;">
                    <i class="fa-solid fa-right-to-bracket me-1"></i>Masuk
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="detail-card">
        <div class="detail-header">
            <i class="fa-solid fa-capsules"></i>
            <h4 class="fw-bold mb-1"><?= htmlspecialchars($obat['nama_obat']) ?></h4>
            <p class="mb-0 opacity-75"><?= htmlspecialchars($obat['dosis']) ?> — <?= htmlspecialchars($obat['bentuk']) ?></p>
        </div>
        <div class="detail-body">
            <div class="detail-row">
                <span class="detail-label">Kategori</span>
                <span class="detail-value">
                    <span class="badge rounded-pill px-3 py-1" style="background: var(--accent); color: white;"><?= htmlspecialchars($obat['kategori']) ?></span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Bentuk</span>
                <span class="detail-value"><?= htmlspecialchars($obat['bentuk'] ?? '-') ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dosis</span>
                <span class="detail-value"><?= htmlspecialchars($obat['dosis'] ?? '-') ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Satuan</span>
                <span class="detail-value"><?= htmlspecialchars($obat['satuan'] ?? '-') ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Stok</span>
                <span class="detail-value">
                    <span class="badge-stok <?= ($obat['stok'] ?? 0) < 5 ? 'badge-warn' : 'badge-ok' ?>">
                        <?= (int)($obat['stok'] ?? 0) ?> <?= htmlspecialchars($obat['satuan'] ?? '') ?>
                        <?= ($obat['stok'] ?? 0) < 5 ? '— Stok Menipis' : '— Stok Aman' ?>
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Deskripsi</span>
                <span class="detail-value"><?= nl2br(htmlspecialchars($obat['deskripsi'] ?? '-')) ?></span>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
                <?php if ($is_admin): ?>
                    <a href="editObat.php?id=<?= $obat['master_id'] ?>" class="btn-action" style="background: #e0a800; color: white;">
                        <i class="fa-solid fa-pen me-1"></i>Edit Obat
                    </a>
                    <a href="../proses/prosesObat.php?aksi=hapus&id=<?= $obat['master_id'] ?>" class="btn-action" style="background: var(--danger); color: white;" onclick="return confirm('Yakin ingin menghapus?')">
                        <i class="fa-solid fa-trash me-1"></i>Hapus
                    </a>
                <?php elseif ($is_pasien): ?>
                    <form action="../proses/prosesObat.php?aksi=tambah_ke_obat_saya" method="POST" style="display: inline;">
                        <input type="hidden" name="master_id" value="<?= $obat['master_id'] ?>">
                        <input type="hidden" name="nama_obat" value="<?= htmlspecialchars($obat['nama_obat']) ?>">
                        <input type="hidden" name="dosis" value="<?= htmlspecialchars($obat['dosis']) ?>">
                        <div class="mb-2">
                            <label class="small fw-semibold" style="color: var(--text-secondary);">Aturan Pakai:</label>
                            <select name="aturan_pakai" class="form-select form-select-sm mt-1" required style="border-radius: 8px; border: 1px solid var(--border-color); background: var(--bg-body); color: var(--text-primary); max-width: 220px;">
                                <option value="">-- Pilih --</option>
                                <option value="Sebelum Makan">Sebelum Makan</option>
                                <option value="Sesudah Makan">Sesudah Makan</option>
                                <option value="Bersama Makan">Bersama Makan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-action" style="background: var(--success); color: white;">
                            <i class="fa-solid fa-plus me-1"></i>Tambah ke Obat Saya
                        </button>
                    </form>
                <?php else: ?>
                    <a href="../views/login.php" class="btn-action" style="background: var(--accent); color: white;">
                        <i class="fa-solid fa-right-to-bracket me-1"></i>Masuk untuk Menambahkan
                    </a>
                <?php endif; ?>
                <a href="../index.php" class="btn-action" style="background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color);">
                    <i class="fa-solid fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2026 Kelompok UAS ITB STIKOM Bali. PengingatObat.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const saved = localStorage.getItem('theme') || 'light';
            const icon = document.getElementById('themeIcon');
            if (icon) icon.className = saved === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        })();
    function toggleTheme() {
        const html = document.documentElement;
        const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
        const icon = document.getElementById('themeIcon');
        if (icon) icon.className = next === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    }
</script>
</body>
</html>
