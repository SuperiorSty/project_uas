<?php
session_start();
require_once '../config/database.php';

// ── RBAC: harus login dulu ──────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?error=Silakan login terlebih dahulu!");
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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Obat - <?= htmlspecialchars($obat['nama_obat']) ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 580px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; color: #333; border-bottom: 2px solid #17a2b8; padding-bottom: 10px; }
        .field { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .field:last-child { border-bottom: none; }
        .field-label { font-weight: bold; color: #555; min-width: 130px; font-size: 13px; }
        .field-value { color: #222; font-size: 14px; }
        .badge-stok { display: inline-block; padding: 3px 12px; border-radius: 20px; font-weight: bold; font-size: 13px; }
        .badge-ok { background: #d4edda; color: #155724; }
        .badge-warn { background: #f8d7da; color: #721c24; }
        .btn-wrap { margin-top: 20px; display: flex; gap: 10px; }
        .btn { padding: 8px 18px; border-radius: 4px; text-decoration: none; font-size: 13px; }
        .btn-edit { background: #e0a800; color: white; }
        .btn-back { background: #6c757d; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h2>🔍 Detail Obat</h2>

    <div class="field">
        <span class="field-label">Nama Obat</span>
        <span class="field-value"><?= htmlspecialchars($obat['nama_obat']) ?></span>
    </div>
    <div class="field">
        <span class="field-label">Kategori</span>
        <span class="field-value"><?= htmlspecialchars($obat['kategori']) ?></span>
    </div>
    <div class="field">
        <span class="field-label">Bentuk</span>
        <span class="field-value"><?= htmlspecialchars($obat['bentuk'] ?? '-') ?></span>
    </div>
    <div class="field">
        <span class="field-label">Dosis</span>
        <span class="field-value"><?= htmlspecialchars($obat['dosis'] ?? '-') ?></span>
    </div>
    <div class="field">
        <span class="field-label">Satuan</span>
        <span class="field-value"><?= htmlspecialchars($obat['satuan'] ?? '-') ?></span>
    </div>
    <div class="field">
        <span class="field-label">Stok</span>
        <span class="field-value">
            <span class="badge-stok <?= $obat['stok'] < 5 ? 'badge-warn' : 'badge-ok' ?>">
                <?= $obat['stok'] ?> <?= htmlspecialchars($obat['satuan'] ?? '') ?>
                <?= $obat['stok'] < 5 ? '⚠️ Stok Menipis' : '✅ Stok Aman' ?>
            </span>
        </span>
    </div>
    <div class="field">
        <span class="field-label">Deskripsi</span>
        <span class="field-value"><?= nl2br(htmlspecialchars($obat['deskripsi'] ?? '-')) ?></span>
    </div>

    <div class="btn-wrap">
        <?php if ($_SESSION['role_nama'] === 'ADMIN'): ?>
            <a href="editObat.php?id=<?= $obat['master_id'] ?>" class="btn btn-edit">✏️ Edit</a>
        <?php endif; ?>
        <a href="kelolaObat.php" class="btn btn-back">← Kembali</a>
    </div>
</div>
</body>
</html>
