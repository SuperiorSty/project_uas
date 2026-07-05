<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role_nama'] ?? '') !== 'ADMIN') {
    header("Location: ../index.php?error=Akses ditolak!");
    exit;
}

$pdo = Database::connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: kelolaObat.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM master_obat WHERE master_id = ?");
$stmt->execute([$id]);
$obat = $stmt->fetch();

if (!$obat) {
    header("Location: kelolaObat.php?error=Data obat tidak ditemukan!");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Obat - <?= htmlspecialchars($obat['nama_obat']) ?></title>
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
            <a href="kelolaObat.php" class="btn btn-sm rounded-pill px-3" style="background: var(--accent); color: white;"><i class="fa-solid fa-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="form-card p-4">
        <h5 class="fw-bold mb-4"><i class="fa-solid fa-pen me-2" style="color: var(--accent);"></i>Edit Data Obat</h5>
        <form action="../proses/prosesObat.php?aksi=edit" method="POST">
            <input type="hidden" name="master_id" value="<?= $obat['master_id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold small">Nama Obat</label>
                <input type="text" name="nama_obat" class="form-control" value="<?= htmlspecialchars($obat['nama_obat']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Kategori</label>
                <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($obat['kategori']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Bentuk</label>
                <select name="bentuk" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="Tablet" <?= $obat['bentuk'] == 'Tablet' ? 'selected' : '' ?>>Tablet</option>
                    <option value="Kapsul" <?= $obat['bentuk'] == 'Kapsul' ? 'selected' : '' ?>>Kapsul</option>
                    <option value="Sirup" <?= $obat['bentuk'] == 'Sirup' ? 'selected' : '' ?>>Sirup</option>
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Dosis</label>
                    <input type="text" name="dosis" class="form-control" value="<?= htmlspecialchars($obat['dosis']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Satuan</label>
                    <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($obat['satuan']) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Stok</label>
                <input type="number" name="stok" class="form-control" min="0" value="<?= (int)$obat['stok'] ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars($obat['deskripsi'] ?? '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn px-4 py-2" style="background: var(--accent); color: white; border-radius: 10px;">
                    <i class="fa-solid fa-save me-1"></i>Simpan Perubahan
                </button>
                <a href="kelolaObat.php" class="btn px-4 py-2" style="background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 10px;">Batal</a>
            </div>
        </form>
    </div>
</div>

<footer><p>&copy; 2026 Kelompok UAS ITB STIKOM Bali. PengingatObat.</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
