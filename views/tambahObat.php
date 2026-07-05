<?php
session_start();
require_once '../config/database.php';

// ── RBAC: hanya ADMIN yang boleh tambah obat ──────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?error=Silakan login terlebih dahulu!");
    exit;
}
if ($_SESSION['role_nama'] !== 'ADMIN') {
    header("Location: dashboard.php?error=Anda tidak punya akses ke halaman ini!");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Obat Baru</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        label { font-weight: bold; display: block; margin-top: 14px; margin-bottom: 4px; color: #555; }
        input[type=text], input[type=number], select, textarea {
            width: 100%; padding: 8px 10px; border: 1px solid #ccc;
            border-radius: 4px; box-sizing: border-box; font-size: 14px;
        }
        textarea { resize: vertical; height: 80px; }
        .btn-wrap { margin-top: 20px; display: flex; gap: 10px; }
        .btn-simpan { background: #28a745; color: white; border: none; padding: 9px 20px; border-radius: 4px; cursor: pointer; font-size: 14px; }
        .btn-simpan:hover { background: #218838; }
        .btn-batal { background: #6c757d; color: white; padding: 9px 20px; border-radius: 4px; text-decoration: none; font-size: 14px; }
        .required { color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>Tambah Data Obat Baru</h2>

    <form action="../proses/prosesObat.php?aksi=tambah" method="POST">

        <label>Nama Obat <span class="required">*</span></label>
        <input type="text" name="nama_obat" placeholder="contoh: Paracetamol" required>

        <label>Kategori <span class="required">*</span></label>
        <input type="text" name="kategori" placeholder="contoh: Analgesik, Antibiotik, Vitamin" required>

        <label>Bentuk <span class="required">*</span></label>
        <select name="bentuk" required>
            <option value="">-- Pilih Bentuk --</option>
            <option value="Tablet">Tablet</option>
            <option value="Kapsul">Kapsul</option>
            <option value="Sirup">Sirup</option>
            <option value="Serbuk">Serbuk</option>
            <option value="Salep">Salep</option>
            <option value="Tetes">Tetes</option>
        </select>

        <label>Dosis <span class="required">*</span></label>
        <input type="text" name="dosis" placeholder="contoh: 500mg, 10ml" required>

        <label>Satuan <span class="required">*</span></label>
        <input type="text" name="satuan" placeholder="contoh: Strip, Botol, Box" required>

        <label>Jumlah Stok <span class="required">*</span></label>
        <input type="number" name="stok" min="0" value="0" required>

        <label>Deskripsi / Keterangan</label>
        <textarea name="deskripsi" placeholder="contoh: Obat penurun demam, diminum 3x sehari sesudah makan"></textarea>

        <div class="btn-wrap">
            <button type="submit" class="btn-simpan">💾 Simpan Obat</button>
            <a href="kelolaObat.php" class="btn-batal">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
