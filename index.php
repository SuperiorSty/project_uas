<?php
session_start();
require_once __DIR__ . '/config/database.php';

$keyword = $_GET['keyword'] ?? '';
$master_obat = [];
try {
    $db = Database::connect();
    if (!empty($keyword)) {
        $stmt = $db->prepare("SELECT * FROM master_obat WHERE nama_obat LIKE :kw OR kategori LIKE :kw ORDER BY nama_obat ASC LIMIT 12");
        $stmt->execute(['kw' => "%$keyword%"]);
    } else {
        $stmt = $db->query("SELECT * FROM master_obat ORDER BY nama_obat ASC LIMIT 6");
    }
    $master_obat = $stmt->fetchAll();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PengingatObat - Kelola Jadwal Minum Obat</title>
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
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.06); --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
            --accent: #3688C9; --accent-hover: #3659C9;
        }
        [data-theme="dark"] {
            --bg-body: #0d0d1a; --bg-card: #1a1a30; --bg-navbar: #141428;
            --text-primary: #f8f9fa; --text-secondary: #e0e0e8; --text-muted: #a8a8b8;
            --bg-input: #242442; --text-input: #ffffff;
            --border-color: #343459;
            --table-striped: rgba(255,255,255,0.03); --table-hover: rgba(255,255,255,0.05);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.4); --shadow-md: 0 4px 16px rgba(0,0,0,0.6);
        }
        * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        .card { background-color: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); }
        .form-control, .form-select { background-color: var(--bg-input); color: var(--text-input); border-color: var(--border-color); }
        .form-control:focus, .form-select:focus { background-color: var(--bg-input); color: var(--text-input); border-color: var(--accent); box-shadow: 0 0 0 3px rgba(54,136,201,0.15); }
        .table { --bs-table-bg: transparent; color: var(--text-primary); }
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
        .navbar-search { display: flex; align-items: center; }
        .navbar-search-input {
            border: 1px solid var(--border-color);
            border-radius: 20px;
            background-color: var(--bg-body);
            color: var(--text-primary);
            padding: 4px 12px;
            font-size: 0.85rem;
            width: 0;
            opacity: 0;
            transition: all 0.3s ease;
        }
        .navbar-search-input.open { width: 180px; opacity: 1; margin-left: 8px; }
        .navbar-search-input:focus { outline: none; border-color: var(--accent); }
        .search-toggle-btn {
            background: none; border: none; color: var(--text-secondary); font-size: 1.1rem; cursor: pointer; padding: 4px 8px; border-radius: 8px;
        }
        .search-toggle-btn:hover { color: var(--accent); }
        .card-custom {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
        }
        .btn-blue {
            background-color: var(--accent); color: white; border-radius: 20px; border: none;
        }
        .btn-blue:hover { background-color: var(--accent-hover); }
        .btn-outline-accent {
            border: 1px solid var(--accent); color: var(--accent); border-radius: 20px;
        }
        .btn-outline-accent:hover { background-color: var(--accent); color: white; }
        .dropdown-menu { background-color: var(--bg-card); border: 1px solid var(--border-color); }
        .dropdown-item { color: var(--text-secondary); }
        .dropdown-item:hover { background-color: var(--bg-body); color: var(--text-primary); }
        .theme-toggle-dropdown {
            display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; cursor: pointer; border-radius: 8px; color: var(--text-primary);
        }
        .theme-toggle-dropdown:hover { background-color: var(--bg-body); }

        .hero-banner {
            background: var(--accent);
            padding: 48px 40px;
            border-radius: 20px;
            color: white;
        }
        .obat-card {
            background-color: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.2s ease;
            cursor: pointer;
            display: block;
            text-decoration: none;
            color: inherit;
        }
        .obat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-sm); }
        .badge-primary-custom { background-color: var(--accent); color: white; }
        .badge-secondary-custom { background-color: #36B6C9; color: white; }
        .theme-toggle-mini {
            background: none; border: 1px solid var(--border-color); border-radius: 50%; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-secondary); font-size: 0.9rem;
        }
        .theme-toggle-mini:hover { border-color: var(--accent); color: var(--accent); }
        footer {
            background-color: var(--bg-navbar);
            border-top: 1px solid var(--border-color);
            padding: 20px 0;
            margin-top: auto;
        }
        footer p { color: var(--text-muted); margin: 0; font-size: 0.85rem; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <h2 class="m-0 fs-5"><i class="fa-solid fa-capsules me-2"></i>PengingatObat</h2>
        </a>
        <div class="ms-auto d-flex align-items-center gap-2">
            <div class="navbar-search">
                <button class="search-toggle-btn" onclick="toggleSearch()" title="Cari Obat">
                    <i class="fa-solid fa-search"></i>
                </button>
                <input type="text" id="navbarSearchInput" class="navbar-search-input" placeholder="Cari obat..." onkeypress="if(event.key==='13' && this.value.trim()) window.location='?keyword='+encodeURIComponent(this.value)">
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="dropdown">
                    <button class="btn btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-3" type="button" data-bs-toggle="dropdown" style="background: var(--bg-card); border: 1px solid var(--border-color); color: var(--text-primary);">
                        <i class="fa-solid fa-circle-user fs-5"></i>
                        <span class="fw-semibold text-capitalize small"><?= htmlspecialchars($_SESSION['nama_user']); ?></span>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 10px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow mt-2 p-2 rounded-3" style="min-width: 220px;">
                        <li><a class="dropdown-item py-2 rounded-2" href="views/dashboard.php"><i class="fa-solid fa-gauge me-2" style="color: var(--accent);"></i> Dashboard</a></li>
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
                        <li><a class="dropdown-item py-2 text-danger fw-semibold rounded-2" href="proses/prosesLogout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <button class="theme-toggle-mini" onclick="toggleTheme()" title="Mode Gelap">
                    <i class="fa-solid fa-moon" id="themeIconGuest"></i>
                </button>
                <a href="views/login.php" class="btn btn-blue btn-sm rounded-pill px-4 fw-bold shadow-sm me-1">
                    Masuk
                </a>
                <a href="registrasi.php" class="btn btn-outline-accent btn-sm rounded-pill px-3 fw-bold">
                    Daftar
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container my-4">

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm"><?= htmlspecialchars($_GET['error']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm"><?= htmlspecialchars($_GET['success']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <div class="hero-banner mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-2">Tebus Resep Praktis, Sembuh Lebih Cepat</h2>
                <p class="mb-0 opacity-75">Cari info obat di katalog kami dan kelola waktu minum obat secara otomatis.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <i class="fa-solid fa-capsules" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>

    <div class="card-custom p-4">
        <h5 class="fw-bold mb-4"><i class="fa-solid fa-book-medical me-2" style="color: var(--accent);"></i>Katalog Master Obat</h5>

        <?php if (count($master_obat) > 0): ?>
            <div class="row g-3">
                <?php foreach ($master_obat as $obat): ?>
                    <div class="col-md-4 mb-2">
                        <a href="views/detailObat.php?id=<?= $obat['master_id'] ?>" class="obat-card">
                            <span class="badge badge-primary-custom align-self-start mb-2"><?= htmlspecialchars($obat['kategori']) ?></span>
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($obat['nama_obat']) ?> <?= htmlspecialchars($obat['dosis']) ?></h6>
                            <p class="small mb-1" style="color: var(--text-muted);">
                                <i class="fa-solid fa-capsules me-1"></i><?= htmlspecialchars($obat['bentuk']) ?> — <?= htmlspecialchars($obat['satuan']) ?>
                            </p>
                            <?php if (!empty($obat['deskripsi'])): ?>
                                <p class="small mb-0" style="color: var(--text-secondary);"><?= htmlspecialchars($obat['deskripsi']) ?></p>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="obat-card">
                        <span class="badge badge-primary-custom align-self-start mb-2">Analgesik</span>
                        <h6 class="fw-bold mb-1">Paracetamol 500mg</h6>
                        <p class="small mb-0" style="color: var(--text-muted);">Meredakan demam dan gejala nyeri ringan sampai sedang.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="obat-card">
                        <span class="badge badge-secondary-custom align-self-start mb-2">Antibiotik</span>
                        <h6 class="fw-bold mb-1">Amoxicillin 500mg</h6>
                        <p class="small mb-0" style="color: var(--text-muted);">Mengobati berbagai macam infeksi bakteri.</p>
                    </div>
                </div>
            </div>
            <p class="text-muted small mt-3"><i class="fa-solid fa-info-circle me-1"></i>Data master obat belum tersedia.</p>
        <?php endif; ?>
    </div>

</div>

<footer>
    <div class="container text-center">
        <p>&copy; 2026 Kelompok UAS ITB STIKOM Bali. PengingatObat.</p>
    </div>
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
        const icons = document.querySelectorAll('#themeIcon, #themeIconGuest');
        const sw = document.getElementById('themeSwitch');
        icons.forEach(el => { if(el) el.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon'; });
        if (sw) sw.checked = theme === 'dark';
    }
    function toggleSearch() {
        const inp = document.getElementById('navbarSearchInput');
        inp.classList.toggle('open');
        if (inp.classList.contains('open')) inp.focus();
    }
</script>
</body>
</html>
