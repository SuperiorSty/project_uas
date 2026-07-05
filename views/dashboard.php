<?php
session_start();
require_once '../config/database.php';
$db = Database::connect();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['nama_user'];

// Jadwal hari ini (fix: JOIN ke obat, bukan master_obat)
$stmt_jadwal = $db->prepare("
    SELECT r.*, o.nama_obat, o.dosis, o.aturan_pakai
    FROM riwayat_obat r
    JOIN obat o ON r.obat_id = o.id
    WHERE r.user_id = ? AND DATE(r.waktu_jadwal) = CURDATE()
    ORDER BY r.waktu_jadwal ASC
");
$stmt_jadwal->execute([$user_id]);
$jadwal_today = $stmt_jadwal->fetchAll();

// Statistik
$stmt_diminum = $db->prepare("SELECT COUNT(*) FROM riwayat_obat WHERE user_id = ? AND status = 'Sudah Diminum'");
$stmt_diminum->execute([$user_id]);
$jumlah_diminum = $stmt_diminum->fetchColumn();

$stmt_terlewat = $db->prepare("SELECT COUNT(*) FROM riwayat_obat WHERE user_id = ? AND status = 'Terlewat'");
$stmt_terlewat->execute([$user_id]);
$jumlah_terlewat = $stmt_terlewat->fetchColumn();

$total_jadwal = $jumlah_diminum + $jumlah_terlewat;
$persentase_kepatuhan = $total_jadwal > 0 ? round(($jumlah_diminum / $total_jadwal) * 100) : 0;

// Obat aktif (fix: JOIN ke obat)
$stmt_obat = $db->prepare("
    SELECT DISTINCT o.id, o.nama_obat, o.dosis, o.aturan_pakai
    FROM riwayat_obat r
    JOIN obat o ON r.obat_id = o.id
    WHERE r.user_id = ?
    ORDER BY o.nama_obat ASC
");
$stmt_obat->execute([$user_id]);
$obat_aktif = $stmt_obat->fetchAll();

// Riwayat lengkap (semua baris)
$stmt_riwayat = $db->prepare("
    SELECT r.*, o.nama_obat, o.dosis
    FROM riwayat_obat r
    JOIN obat o ON r.obat_id = o.id
    WHERE r.user_id = ?
    ORDER BY r.waktu_jadwal DESC
");
$stmt_riwayat->execute([$user_id]);
$riwayat_list = $stmt_riwayat->fetchAll();
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PengingatObat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'light')</script>
    <style>
        /* ── CSS Variables ── */
        :root {
            --bg-body: #f0f2f5; --bg-card: #ffffff; --bg-navbar: #ffffff; --bg-sidebar: #ffffff;
            --text-primary: #1a1a2e; --text-secondary: #495057; --text-muted: #6c757d;
            --bg-input: #ffffff; --text-input: #1a1a2e;
            --border-color: #e8e8ef;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06); --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
            --accent: #3688C9; --accent-hover: #3659C9; --success: #28a745; --danger: #dc3545; --warning: #ffc107;
            --radius-lg: 12px; --radius-md: 8px; --radius-pill: 20px;
            --sidebar-width: 250px;
        }
        [data-theme="dark"] {
            --bg-body: #0d0d1a; --bg-card: #1a1a30; --bg-navbar: #141428; --bg-sidebar: #12122a;
            --text-primary: #f8f9fa; --text-secondary: #e0e0e8; --text-muted: #a8a8b8;
            --bg-input: #242442; --text-input: #ffffff;
            --border-color: #343459;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.4); --shadow-md: 0 4px 16px rgba(0,0,0,0.6);
        }

        /* ── Global Reset ── */
        * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        body { background-color: var(--bg-body); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: var(--text-primary); margin: 0; }
        .card { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); color: var(--text-primary); }
        .form-control, .form-select { background-color: var(--bg-input); color: var(--text-input); border-color: var(--border-color); }
        .form-control:focus, .form-select:focus { background-color: var(--bg-input); color: var(--text-input); border-color: var(--accent); box-shadow: 0 0 0 3px rgba(54,136,201,0.15); }
        .table { --bs-table-bg: transparent !important; color: var(--text-primary); }
        .text-muted { --bs-text-opacity: 1; color: var(--text-muted) !important; }

        /* ── Navbar ── */
        .navbar { background-color: var(--bg-navbar) !important; border-bottom: 1px solid var(--border-color); position: fixed; top: 0; left: 0; right: 0; z-index: 1030; height: 60px; }
        .navbar-brand h2 { color: var(--accent); font-weight: 700; margin: 0; }

        /* ── Profile Dropdown ── */
        .btn-profile { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); color: var(--text-primary); }
        .btn-profile:hover { border-color: var(--accent); box-shadow: var(--shadow-sm); }
        .dropdown-menu { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: 0 8px 30px rgba(0,0,0,0.12); padding: 8px; }
        .dropdown-item { color: var(--text-secondary); border-radius: var(--radius-md); padding: 8px 12px; }
        .dropdown-item:hover { background-color: var(--bg-body); color: var(--text-primary); }
        .dropdown-item.active-dropdown { background-color: var(--accent); color: white !important; }

        /* ── Search Toggle ── */
        .navbar-search { display: flex; align-items: center; }
        .navbar-search-input { border: 1px solid var(--border-color); border-radius: var(--radius-pill); background-color: var(--bg-body); color: var(--text-primary); padding: 4px 12px; font-size: 0.85rem; width: 0; opacity: 0; transition: all 0.3s ease; }
        .navbar-search-input.open { width: 180px; opacity: 1; margin-left: 8px; }
        .navbar-search-input:focus { outline: none; border-color: var(--accent); }
        .search-toggle-btn { background: none; border: none; color: var(--text-secondary); font-size: 1.1rem; cursor: pointer; padding: 4px 8px; border-radius: var(--radius-md); }
        .search-toggle-btn:hover { color: var(--accent); }

        /* ── Layout ── */
        .wrapper { display: flex; padding-top: 60px; min-height: 100vh; }
        .main-content { margin-left: var(--sidebar-width); flex: 1; padding: 24px 32px; }

        /* ── Sidebar ── */
        .sidebar { width: var(--sidebar-width); background-color: var(--bg-sidebar); border-right: 1px solid var(--border-color); position: fixed; top: 60px; left: 0; bottom: 0; overflow-y: auto; padding: 20px 14px; z-index: 1020; }
        .sidebar-section { margin-bottom: 28px; }
        .sidebar-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.2px; color: var(--text-muted); font-weight: 700; margin-bottom: 10px; padding: 0 12px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: var(--radius-md); color: var(--text-muted); text-decoration: none; transition: all 0.2s ease; font-size: 0.88rem; font-weight: 500; }
        .sidebar-link:hover { background-color: var(--bg-body); color: var(--text-primary); }
        .sidebar-link i { width: 20px; text-align: center; color: var(--text-muted); font-size: 0.95rem; }
        .sidebar-link.active { background: var(--accent); color: white; font-weight: 600; }
        .sidebar-link.active i { color: white; }
        .sidebar-obat-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: var(--radius-md); font-size: 0.85rem; transition: all 0.2s ease; }
        .sidebar-obat-item:hover { background-color: var(--bg-body); }

        /* ── Theme Toggle ── */
        .theme-toggle-dropdown { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; cursor: pointer; border-radius: var(--radius-md); color: var(--text-primary); }
        .theme-toggle-dropdown:hover { background-color: var(--bg-body); }

        /* ── Pengingat Timeline ── */
        .timeline-item { display: flex; align-items: center; gap: 16px; padding: 14px 0; border-bottom: 1px solid var(--border-color); }
        .timeline-item:last-child { border-bottom: none; }
        .timeline-time { min-width: 60px; font-weight: 700; font-size: 1rem; color: var(--text-primary); }
        .timeline-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .timeline-dot-completed { background-color: var(--success); }
        .timeline-dot-pending { background-color: var(--warning); animation: pulse 2s infinite; }
        .timeline-dot-missed { background-color: var(--danger); }
        @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.6; transform: scale(1.2); } }
        .timeline-content { flex: 1; }
        .timeline-content h6 { margin: 0; font-weight: 600; color: var(--text-primary); font-size: 0.95rem; }
        .timeline-content small { color: var(--text-muted); font-size: 0.8rem; }
        .btn-minum { background-color: var(--success); color: white; border: none; border-radius: var(--radius-pill); padding: 5px 16px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
        .btn-minum:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(40,167,69,0.3); }
        .btn-minum:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* ── Empty State ── */
        .empty-state { text-align: center; padding: 40px 20px; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 160px; }
        .empty-state i { font-size: 2.5rem; margin-bottom: 14px; opacity: 0.35; }
        .empty-state h6 { font-weight: 600; color: var(--text-secondary); }
        .empty-state p { color: var(--text-muted); }
        .btn-primary-solid { display: inline-flex; align-items: center; gap: 6px; background-color: var(--accent); color: white; border: none; border-radius: var(--radius-md); padding: 8px 20px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
        .btn-primary-solid:hover { background-color: var(--accent-hover); color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(54,136,201,0.3); }

        /* ── Stat Cards ── */
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
        .stat-card { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); padding: 20px 12px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 6px; }
        .stat-card h3 { font-weight: 700; margin: 0; font-size: 1.5rem; color: var(--text-primary); }
        .stat-card .stat-label { color: var(--text-muted); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .score-circle { width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; color: white; }

        /* ── Riwayat Table ── */
        .table-riwayat { font-size: 0.85rem; }
        .table-riwayat th { font-weight: 600; color: var(--text-secondary); border-bottom: 2px solid var(--border-color) !important; }
        .table-riwayat td { color: var(--text-primary); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .badge-status { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-diminum { background-color: #d4edda; color: #155724; }
        .badge-terlewat { background-color: #f8d7da; color: #721c24; }
        .badge-ditunda { background-color: #e2e3e5; color: #383d41; }
        [data-theme="dark"] .badge-diminum { background-color: rgba(40,167,69,0.2); color: #75b798; }
        [data-theme="dark"] .badge-terlewat { background-color: rgba(220,53,69,0.2); color: #ea868f; }
        [data-theme="dark"] .badge-ditunda { background-color: rgba(108,117,125,0.2); color: #adb5bd; }

        /* ── Footer ── */
        footer { background-color: var(--bg-navbar); border-top: 1px solid var(--border-color); padding: 16px 0; text-align: center; }
        footer p { color: var(--text-muted); margin: 0; font-size: 0.8rem; }

        /* ── Hash Navigation ── */
        .page-content { display: block; }
        .page-content[style*="display:none"] { display: none !important; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 16px; }
            .stat-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="../index.php">
                <h2 class="m-0 fs-5"><i class="fa-solid fa-capsules me-2"></i>PengingatObat</h2>
            </a>
            <div class="d-flex align-items-center gap-2">
                <div class="navbar-search">
                    <button class="search-toggle-btn" onclick="toggleSearch()" title="Cari Obat"><i class="fa-solid fa-search"></i></button>
                    <input type="text" id="navbarSearchInput" class="navbar-search-input" placeholder="Cari obat..." onkeypress="if(event.key==='13' && this.value.trim()) window.location='../index.php?keyword='+encodeURIComponent(this.value)">
                </div>
                <div class="dropdown">
                    <button class="btn btn-profile d-flex align-items-center gap-2 px-3 py-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-user fs-5"></i>
                        <span class="fw-semibold text-capitalize small"><?= htmlspecialchars($nama_user) ?></span>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 10px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow mt-2 p-2 rounded-3" style="min-width: 220px;">
                        <li><a class="dropdown-item py-2 rounded-2 active-dropdown" href="dashboard.php"><i class="fa-solid fa-gauge me-2"></i> Dashboard</a></li>
                        <li><a class="dropdown-item py-2 rounded-2" href="../index.php"><i class="fa-solid fa-store me-2" style="color: var(--accent);"></i> Katalog Obat</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><div class="theme-toggle-dropdown" onclick="toggleTheme()" style="cursor: pointer;"><span class="small"><i class="fa-solid fa-moon me-2" id="themeIcon"></i> Mode Gelap</span><div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" id="themeSwitch" style="pointer-events: none;"></div></div></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger fw-semibold rounded-2" href="../proses/prosesLogout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="wrapper">
        <aside class="sidebar">
            <div class="sidebar-section">
                <div class="sidebar-label">Menu Utama</div>
                <a href="#beranda" class="sidebar-link active" data-page="beranda"><i class="fa-solid fa-house"></i>Beranda</a>
                <a href="#riwayat" class="sidebar-link" data-page="riwayat"><i class="fa-solid fa-clock-history"></i>Riwayat</a>
                <a href="tambahObat.php" class="sidebar-link"><i class="fa-solid fa-plus-circle"></i>Tambah Obat</a>
                <a href="../index.php" class="sidebar-link"><i class="fa-solid fa-search"></i>Cari Obat</a>
            </div>
            <div class="sidebar-section">
                <div class="sidebar-label">Obat Aktif</div>
                <?php if (count($obat_aktif) > 0): ?>
                    <?php foreach ($obat_aktif as $o): ?>
                        <div class="sidebar-obat-item" style="color: var(--text-primary);">
                            <i class="fa-solid fa-capsules" style="color: var(--accent); font-size: 0.85rem;"></i>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 500;"><?= htmlspecialchars($o['nama_obat']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($o['dosis']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="padding: 8px 12px; color: var(--text-muted); font-size: 0.8rem;"><i class="fa-solid fa-pills me-1"></i>Belum ada obat aktif</div>
                <?php endif; ?>
            </div>
        </aside>

        <main class="main-content">

            <!-- ═══════ PAGE: BERANDA ═══════ -->
            <div id="page-beranda" class="page-content">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="fw-bold m-0">Halo, <?= htmlspecialchars($nama_user) ?>!</h4>
                        <small class="text-muted">Pantau jadwal minum obat harianmu</small>
                    </div>
                </div>

                <!-- PENGINGAT -->
                <section class="card p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold m-0"><i class="fa-solid fa-bell me-2" style="color: var(--accent);"></i>Pengingat Minum Obat Hari Ini</h6>
                        <span class="badge rounded-pill px-3 py-2" style="background-color: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color); font-weight: 500;">
                            <i class="fa-regular fa-calendar me-1"></i><?= date('d M Y') ?>
                        </span>
                    </div>
                    <?php if (count($jadwal_today) > 0): ?>
                        <?php foreach ($jadwal_today as $item):
                            $waktu = date('H:i', strtotime($item['waktu_jadwal']));
                            $is_completed = $item['status'] === 'Sudah Diminum';
                            $is_missed = $item['status'] === 'Terlewat';
                            $dot_class = $is_completed ? 'timeline-dot-completed' : ($is_missed ? 'timeline-dot-missed' : 'timeline-dot-pending');
                        ?>
                            <div class="timeline-item">
                                <div class="timeline-time"><?= $waktu ?></div>
                                <div class="timeline-dot <?= $dot_class ?>"></div>
                                <div class="timeline-content">
                                    <h6><?= htmlspecialchars($item['nama_obat']) ?> <small class="text-muted"><?= htmlspecialchars($item['dosis']) ?></small></h6>
                                    <small>Aturan: <?= htmlspecialchars($item['aturan_pakai'] ?? '-') ?> —
                                        <?php if ($is_completed): ?>
                                            <span style="color: var(--success);"><i class="fa-solid fa-check-circle me-1"></i>Sudah diminum</span>
                                        <?php elseif ($is_missed): ?>
                                            <span style="color: var(--danger);"><i class="fa-solid fa-times-circle me-1"></i>Terlewat</span>
                                        <?php else: ?>
                                            <span style="color: var(--warning);"><i class="fa-solid fa-hourglass-half me-1"></i>Belum diminum</span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                <?php if (!$is_completed && !$is_missed): ?>
                                    <form action="../proses/prosesKonsumsi.php?aksi=catat" method="POST" style="margin:0;">
                                        <input type="hidden" name="riwayat_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="status" value="Sudah Diminum">
                                        <button type="submit" class="btn-minum"><i class="fa-solid fa-check me-1"></i>Minum</button>
                                    </form>
                                <?php elseif ($is_missed): ?>
                                    <form action="../proses/prosesKonsumsi.php?aksi=catat" method="POST" style="margin:0;">
                                        <input type="hidden" name="riwayat_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="status" value="Sudah Diminum">
                                        <button type="submit" class="btn-minum" style="background-color: var(--accent);"><i class="fa-solid fa-redo me-1"></i>Catat</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn-minum" disabled><i class="fa-solid fa-check me-1"></i>Selesai</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-calendar-day"></i>
                            <h6 class="fw-bold">Belum Ada Jadwal untuk Hari Ini</h6>
                            <p class="mb-3 small">Cari obat dari katalog dan tambahkan ke obatmu untuk mulai memantau jadwal.</p>
                            <a href="../index.php" class="btn-primary-solid">Cari Obat</a>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- STATISTIK -->
                <div class="stat-grid mb-4">
                    <div class="stat-card">
                        <?php $sc = '#dc3545'; if ($persentase_kepatuhan >= 80) $sc = '#28a745'; elseif ($persentase_kepatuhan >= 50) $sc = '#ffc107'; ?>
                        <div class="score-circle" style="background-color: <?= $sc ?>;"><?= $persentase_kepatuhan ?>%</div>
                        <span class="stat-label">Kepatuhan</span>
                    </div>
                    <div class="stat-card">
                        <h3 style="color: var(--success);"><?= $jumlah_diminum ?></h3>
                        <i class="fa-solid fa-check-circle" style="color: var(--success); font-size: 1rem;"></i>
                        <span class="stat-label">Diminum</span>
                    </div>
                    <div class="stat-card">
                        <h3 style="color: var(--danger);"><?= $jumlah_terlewat ?></h3>
                        <i class="fa-solid fa-times-circle" style="color: var(--danger); font-size: 1rem;"></i>
                        <span class="stat-label">Terlewat</span>
                    </div>
                    <div class="stat-card">
                        <h3 style="color: var(--accent);"><?= $total_jadwal ?></h3>
                        <i class="fa-solid fa-list" style="color: var(--accent); font-size: 1rem;"></i>
                        <span class="stat-label">Total</span>
                    </div>
                </div>
            </div>

            <!-- ═══════ PAGE: RIWAYAT ═══════ -->
            <div id="page-riwayat" class="page-content" style="display:none;">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="fw-bold m-0"><i class="fa-solid fa-clock-history me-2" style="color: var(--accent);"></i>Riwayat Konsumsi</h4>
                        <small class="text-muted">Semua catatan kepatuhan minum obat</small>
                    </div>
                </div>
                <section class="card p-4">
                    <?php if (count($riwayat_list) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-riwayat mb-0">
                                <thead><tr><th>Obat</th><th>Dosis</th><th>Jadwal</th><th>Diminum</th><th>Status</th></tr></thead>
                                <tbody>
                                    <?php $no = 1; foreach ($riwayat_list as $r):
                                        $badge = 'badge-ditunda';
                                        if ($r['status'] == 'Sudah Diminum') $badge = 'badge-diminum';
                                        if ($r['status'] == 'Terlewat') $badge = 'badge-terlewat';
                                    ?>
                                        <tr>
                                            <td class="fw-semibold"><?= htmlspecialchars($r['nama_obat']) ?></td>
                                            <td><span class="badge-status <?= $badge ?>" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-secondary); font-weight: 400;"><?= htmlspecialchars($r['dosis']) ?></span></td>
                                            <td class="small"><?= date('d M H:i', strtotime($r['waktu_jadwal'])) ?></td>
                                            <td class="small"><?= $r['waktu_diminum'] ? date('d M H:i', strtotime($r['waktu_diminum'])) : '-' ?></td>
                                            <td><span class="badge-status <?= $badge ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state" style="padding: 20px;">
                            <i class="fa-solid fa-folder-open" style="font-size: 2rem;"></i>
                            <p class="small mb-0">Belum ada riwayat konsumsi.</p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

        </main>
    </div>

    <footer><p>&copy; 2026 Kelompok UAS ITB STIKOM Bali. PengingatObat.</p></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function(){const s=localStorage.getItem('theme')||'light';updateUI(s);navigate()})();
        function toggleTheme(){const h=document.documentElement;const n=h.getAttribute('data-theme')==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem('theme',n);updateUI(n)}
        function updateUI(t){const i=document.getElementById('themeIcon'),sw=document.getElementById('themeSwitch');if(i)i.className=t==='dark'?'fa-solid fa-sun':'fa-solid fa-moon';if(sw)sw.checked=t==='dark'}
        function toggleSearch(){const i=document.getElementById('navbarSearchInput');i.classList.toggle('open');if(i.classList.contains('open'))i.focus()}
        function navigate(){const page=location.hash.replace('#','')||'beranda';document.querySelectorAll('.page-content').forEach(el=>el.style.display='none');const el=document.getElementById('page-'+page);if(el)el.style.display='block';document.querySelectorAll('.sidebar-link[data-page]').forEach(a=>{a.classList.toggle('active',a.dataset.page===page)})}
        window.addEventListener('hashchange',navigate);
    </script>
</body>
</html>
