<?php
session_start();
require_once '../config/database.php';

// ── RBAC: hanya ADMIN yang boleh edit obat ──────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?error=Silakan login terlebih dahulu!");
    exit;
}
if ($_SESSION['role_nama'] !== 'ADMIN') {
    header("Location: dashboard.php?error=Anda tidak punya akses ke halaman ini!");
    exit;
}

$pdo = Database::connect();

// Ambil id dari URL, pastikan valid
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: kelolaObat.php");
    exit;
}

// Ambil data obat yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM master_obat WHERE master_id = ?");
$stmt->execute([$id]);
$obat = $stmt->fetch();

// Kalau id tidak ditemukan di database, balik ke halaman daftar
if (!$obat) {
    header("Location: kelolaObat.php?error=Data obat tidak ditemukan!");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Obat - <?= htmlspecialchars($obat['nama_obat']) ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #333; border-bottom: 2px solid #e0a800; padding-bottom: 10px; }
        label { font-weight: bold; display: block; margin-top: 14px; margin-bottom: 4px; color: #555; }
        input[type=text], input[type=number], select, textarea {
            width: 100%; padding: 8px 10px; border: 1px solid #ccc;
            border-radius: 4px; box-sizing: border-box; font-size: 14px;
        }
        textarea { resize: vertical; height: 80px; }
        .btn-wrap { margin-top: 20px; display: flex; gap: 10px; }
        .btn-simpan { background: #e0a800; color: white; border: none; padding: 9px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-simpan:hover { background: #c49200; }
        .btn-batal { background: #6c757d; color: white; padding: 9px 20px; border-radius: 4px; text-decoration: none; font-size: 14px; }
        .info { background: #fff3cd; border: 1px solid #e0a800; border-radius: 4px; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <h2>✏️ Edit Data Obat</h2>

    <div class="info">
        Kamu sedang mengedit: <strong><?= htmlspecialchars($obat['nama_obat']) ?></strong>
    </div>

    <form action="../proses/prosesObat.php?aksi=edit" method="POST">
        <!-- hidden field untuk kirim id ke prosesObat.php -->
        <input type="hidden" name="master_id" value="<?= $obat['master_id'] ?>">

        <label>Nama Obat <span style="color:red">*</span></label>
        <input type="text" name="nama_obat" value="<?= htmlspecialchars($obat['nama_obat']) ?>" required>

        <label>Kategori <span style="color:red">*</span></label>
        <input type="text" name="kategori" placeholder="contoh: Analgesik, Antibiotik, Vitamin"
               value="<?= htmlspecialchars($obat['kategori']) ?>" required>

        <label>Bentuk <span style="color:red">*</span></label>
        <select name="bentuk" required>
            <option value="">-- Pilih Bentuk --</option>
            <?php foreach (['Tablet','Kapsul','Sirup','Serbuk','Salep','Tetes'] as $b): ?>
                <option value="<?= $b ?>" <?= $obat['bentuk'] == $b ? 'selected' : '' ?>><?= $b ?></option>
            <?php endforeach; ?>
        </select>

        <label>Dosis <span style="color:red">*</span></label>
        <input type="text" name="dosis" placeholder="contoh: 500mg, 10ml"
               value="<?= htmlspecialchars($obat['dosis']) ?>" required>

        <label>Satuan <span style="color:red">*</span></label>
        <input type="text" name="satuan" placeholder="contoh: Strip, Botol, Box"
               value="<?= htmlspecialchars($obat['satuan']) ?>" required>

        <label>Jumlah Stok <span style="color:red">*</span></label>
        <input type="number" name="stok" min="0" value="<?= htmlspecialchars($obat['stok']) ?>" required>

        <label>Deskripsi / Keterangan</label>
        <textarea name="deskripsi" placeholder="Contoh: Obat penurun demam, diminum 3x sehari"><?= htmlspecialchars($obat['deskripsi'] ?? '') ?></textarea>

        <div class="btn-wrap">
            <button type="submit" class="btn-simpan">💾 Simpan Perubahan</button>
            <a href="kelolaObat.php" class="btn-batal">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
