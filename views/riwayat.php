<?php
session_start();
require_once '../config/database.php';
$pdo = Database::connect();

// Jika belum login, tendang balik ke halaman login (index.php)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$current_user_id = $_SESSION['user_id']; 

// --- AMBIL DAFTAR RIWAYAT KONSUMSI OBAT (HANYA UNTUK USER YANG SEDANG LOGIN) ---
$sql_riwayat = "SELECT r.*, o.nama_obat, o.dosis
                FROM riwayat_obat r
                INNER JOIN obat o ON r.obat_id = o.id
                WHERE r.user_id = ? 
                ORDER BY r.waktu_jadwal DESC LIMIT 20";

$stmt_riwayat = $pdo->prepare($sql_riwayat);
$stmt_riwayat->execute([$current_user_id]);
$riwayat_list = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Konsumsi Obat - PengingatObat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'light')</script>
    <style>
        :root {
            --bg-body: #f0f2f5; --bg-card: #ffffff; --bg-navbar: #ffffff;
            --text-primary: #1a1a2e; --text-secondary: #495057; --text-muted: #6c757d;
            --bg-input: #ffffff; --text-input: #1a1a2e;
            --border-color: #e8e8ef;
            --table-striped: rgba(0,0,0,0.02); --table-hover: rgba(0,0,0,0.04);
            --shadow-sm: 0 4px 12px rgba(0,0,0,0.05); --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
            --accent: #3688C9; --accent-hover: #3659C9;
        }
        [data-theme="dark"] {
            --bg-body: #0d0d1a; --bg-card: #1a1a30; --bg-navbar: #141428;
            --text-primary: #f8f9fa; --text-secondary: #e0e0e8; --text-muted: #a8a8b8;
            --bg-input: #242442; --text-input: #ffffff;
            --border-color: #343459;
            --table-striped: rgba(255,255,255,0.03); --table-hover: rgba(255,255,255,0.05);
            --shadow-sm: 0 4px 12px rgba(0,0,0,0.4); --shadow-md: 0 4px 16px rgba(0,0,0,0.6);
        }
        * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        body { background-color: var(--bg-body); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: var(--text-primary); }
        .card { background-color: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); }
        .form-control, .form-select { background-color: var(--bg-input); color: var(--text-input); border-color: var(--border-color); }
        .form-control:focus, .form-select:focus { background-color: var(--bg-input); color: var(--text-input); border-color: var(--accent); box-shadow: 0 0 0 3px rgba(54,136,201,0.15); }
        .table { --bs-table-bg: transparent; color: var(--text-primary); }
        .table > :not(caption) > * > * { background-color: var(--bg-card); border-bottom-color: var(--border-color); }
        .text-muted { --bs-text-opacity: 1; color: var(--text-muted) !important; }
        .navbar { background-color: var(--bg-navbar) !important; border-bottom: 1px solid var(--border-color) !important; }
        .navbar-brand h2 { color: var(--accent); font-weight: 700; }
        .card-custom { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 15px; box-shadow: var(--shadow-sm); }
        .page-header-box { 
            background: var(--accent); 
            padding: 40px; 
            border-radius: 20px; 
            color: white; 
        }
        .btn-profile { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 10px; transition: all 0.3s ease; color: var(--text-primary); }
        .btn-profile:hover { background-color: var(--bg-body); }
        .dropdown-menu { background-color: var(--bg-card); border: 1px solid var(--border-color); }
        .dropdown-item { color: var(--text-secondary); }
        .dropdown-item:hover { background-color: var(--bg-body); color: var(--text-primary); }
        .dropdown-item.active-dropdown { background-color: var(--accent); color: white !important; }
        .badge-diminum { background-color: #28a745; color: white; font-weight: 600; }
        .badge-terlewat { background-color: #dc3545; color: white; font-weight: 600; }
        .badge-ditunda { background-color: #6c757d; color: white; font-weight: 600; }
        footer { background-color: var(--bg-navbar); border-top: 1px solid var(--border-color); padding: 20px 0; margin-top: auto; }
        footer p { color: var(--text-muted); margin: 0; }
        .theme-toggle-dropdown {
            display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; cursor: pointer; border-radius: 8px; color: var(--text-primary);
        }
        .theme-toggle-dropdown:hover { background-color: var(--bg-body); }
    </style>
</head>
<body>

    <nav class="navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <h2 class="m-0 fs-5"><i class="fa-solid fa-capsules me-2"></i>PengingatObat</h2>
            </a>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-profile d-flex align-items-center gap-2 px-3 py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-circle-user fs-5"></i>
                        <span class="fw-semibold text-capitalize small">
                            <?= htmlspecialchars($_SESSION['nama_user']); ?>
                        </span>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 10px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow mt-2 p-2 rounded-3" style="min-width: 220px;">
                        <li><a class="dropdown-item py-2 rounded-2" href="dashboard.php"><i class="fa-solid fa-gauge me-2" style="color: var(--accent);"></i> Dashboard</a></li>
                        <li><a class="dropdown-item py-2 rounded-2 active-dropdown" href="riwayat.php"><i class="fa-solid fa-clock-history me-2"></i> Riwayat</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <div class="theme-toggle-dropdown" onclick="toggleTheme()" style="cursor: pointer;">
                                <span class="small"><i class="fa-solid fa-moon me-2" id="themeIcon"></i>Mode Gelap</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" id="themeSwitch" style="pointer-events: none;">
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger fw-semibold rounded-2" href="../proses/prosesLogout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <div class="page-header-box mb-5 shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="fw-bold mb-2"><i class="fa-solid fa-notes-medical me-2"></i>Log Riwayat Konsumsi</h1>
                    <p class="lead mb-0">Semua data kepatuhan minum obat Anda tercatat secara otomatis demi memantau proses kesembuhan.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="dashboard.php" class="btn fw-bold rounded-pill px-4 py-2 shadow-sm" style="background: rgba(255,255,255,0.2); color: white;">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="text-center" style="width: 70px;">No</th>
                            <th>Nama Obat</th>
                            <th>Dosis</th>
                            <th>Waktu Jadwal</th>
                            <th>Waktu Realisasi Minum</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($riwayat_list as $row): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <span class="fw-bold" style="color: var(--text-primary);"><?= htmlspecialchars($row['nama_obat']) ?></span>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1.5" style="background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color);"><?= htmlspecialchars($row['dosis']) ?></span>
                                </td>
                                <td class="text-muted small">
                                    <i class="fa-regular fa-calendar me-1"></i> <?= htmlspecialchars($row['waktu_jadwal']) ?>
                                </td>
                                <td class="small">
                                    <?php if ($row['waktu_diminum']): ?>
                                        <span class="text-success fw-semibold">
                                            <i class="fa-solid fa-check-double me-1"></i> <?= htmlspecialchars($row['waktu_diminum']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted text-opacity-50 italic">
                                            <i class="fa-solid fa-minus me-1"></i> <em class="text-secondary opacity-75">Tidak tercatat (Terlewat/Ditunda)</em>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $badge_class = 'badge-ditunda'; 
                                        if ($row['status'] == 'Sudah Diminum') $badge_class = 'badge-diminum'; 
                                        if ($row['status'] == 'Terlewat') $badge_class = 'badge-terlewat'; 
                                    ?>
                                    <span class="badge <?= $badge_class ?> rounded-pill px-3 py-2 shadow-sm text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($riwayat_list) == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-2 mb-3 text-secondary opacity-50 d-block"></i>
                                    Belum ada catatan riwayat konsumsi obat yang tersimpan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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
            updateUI(saved);
        })();
        function toggleTheme() {
            const html = document.documentElement;
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateUI(next);
        }
        function updateUI(theme) {
            const icon = document.getElementById('themeIcon');
            const sw = document.getElementById('themeSwitch');
            if (icon) icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            if (sw) sw.checked = theme === 'dark';
        }
    </script>
</body>
</html>