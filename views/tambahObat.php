<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?error=Silakan login terlebih dahulu!");
    exit;
}
$is_admin = ($_SESSION['role_nama'] ?? '') === 'ADMIN';
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Obat - PengingatObat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>document.documentElement.setAttribute('data-theme',localStorage.getItem('theme')||'light')</script>
    <style>
        :root {
            --bg-body: #f0f2f5; --bg-card: #ffffff; --bg-navbar: #ffffff;
            --text-primary: #1a1a2e; --text-secondary: #495057; --text-muted: #6c757d;
            --bg-input: #ffffff; --text-input: #1a1a2e;
            --border-color: #e8e8ef; --shadow-sm: 0 2px 8px rgba(0,0,0,0.06); --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
            --accent: #3688C9; --accent-hover: #3659C9;
        }
        [data-theme="dark"] {
            --bg-body: #0d0d1a; --bg-card: #1a1a30; --bg-navbar: #141428;
            --text-primary: #f8f9fa; --text-secondary: #e0e0e8; --text-muted: #a8a8b8;
            --bg-input: #242442; --text-input: #ffffff;
            --border-color: #343459; --shadow-sm: 0 2px 8px rgba(0,0,0,0.4); --shadow-md: 0 4px 16px rgba(0,0,0,0.6);
        }
        * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        .card { background-color: var(--bg-card); border-color: var(--border-color); color: var(--text-primary); }
        .form-control, .form-select { background-color: var(--bg-input); color: var(--text-input); border-color: var(--border-color); }
        .form-control:focus, .form-select:focus { background-color: var(--bg-input); color: var(--text-input); border-color: var(--accent); box-shadow: 0 0 0 3px rgba(54,136,201,0.15); }
        .text-muted { --bs-text-opacity: 1; color: var(--text-muted) !important; }
        body {
            background-color: var(--bg-body); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary); min-height: 100vh; display: flex; flex-direction: column;
        }
        .navbar { background-color: var(--bg-navbar) !important; border-bottom: 1px solid var(--border-color); }
        .navbar-brand h2 { color: var(--accent); font-weight: 700; }
        .form-card {
            background-color: var(--bg-card); border: 1px solid var(--border-color);
            border-radius: 16px; box-shadow: var(--shadow-md); max-width: 520px; margin: 0 auto;
        }
        footer { background-color: var(--bg-navbar); border-top: 1px solid var(--border-color); padding: 20px 0; margin-top: auto; text-align: center; }
        footer p { color: var(--text-muted); margin: 0; font-size: 0.85rem; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="../index.php"><h2 class="m-0 fs-5"><i class="fa-solid fa-capsules me-2"></i>PengingatObat</h2></a>
        <div class="d-flex align-items-center gap-2">
            <a href="dashboard.php" class="btn btn-sm rounded-pill px-3" style="background: var(--accent); color: white;"><i class="fa-solid fa-arrow-left me-1"></i>Dashboard</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <?php if (!$is_admin): ?>
        <div class="form-card p-5 text-center">
            <i class="fa-solid fa-lock" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 16px;"></i>
            <h5 class="fw-bold">Fitur Khusus Admin</h5>
            <p class="small" style="color: var(--text-muted);">Halaman ini hanya untuk Admin menambah data ke master obat. Sebagai pasien, kamu bisa menambahkan obat dari <a href="../index.php" style="color: var(--accent);">katalog obat</a>.</p>
            <a href="../index.php" class="btn btn-sm rounded-pill px-4" style="background: var(--accent); color: white;">Ke Katalog Obat</a>
        </div>
    <?php else: ?>
        <div class="form-card p-4">
            <h5 class="fw-bold mb-4"><i class="fa-solid fa-plus-circle me-2" style="color: var(--accent);"></i>Tambah Data Obat Baru</h5>
            <form action="../proses/prosesObat.php?aksi=tambah" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Obat</label>
                    <input type="text" name="nama_obat" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Kategori</label>
                    <input type="text" name="kategori" class="form-control" placeholder="Contoh: Analgesik, Vitamin" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Bentuk</label>
                    <select name="bentuk" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="Tablet">Tablet</option>
                        <option value="Kapsul">Kapsul</option>
                        <option value="Sirup">Sirup</option>
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Dosis</label>
                        <input type="text" name="dosis" class="form-control" placeholder="500mg, 10ml" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Satuan</label>
                        <input type="text" name="satuan" class="form-control" placeholder="Strip, Botol" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold small">Stok</label>
                    <input type="number" name="stok" class="form-control" min="0" required>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn px-4 py-2" style="background: var(--accent); color: white; border-radius: 10px;">
                        <i class="fa-solid fa-save me-1"></i>Simpan
                    </button>
                    <a href="kelolaObat.php" class="btn px-4 py-2" style="background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 10px;">Batal</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<footer><p>&copy; 2026 Kelompok UAS ITB STIKOM Bali. PengingatObat.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
