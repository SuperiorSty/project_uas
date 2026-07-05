<?php
session_start();
require_once '../config/database.php';

// ── RBAC: harus login ─────────────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?error=Silakan login terlebih dahulu!");
    exit;
}

$pdo = Database::connect();
$isAdmin = ($_SESSION['role_nama'] === 'ADMIN');

// ── Pesan sukses dari session ─────────────────────────────────────────────
$pesan_sukses = $_SESSION['pesan_sukses'] ?? '';
unset($_SESSION['pesan_sukses']);
$pesan_error = $_GET['error'] ?? '';

// ── Notifikasi stok menipis ───────────────────────────────────────────────
$stmt_stok = $pdo->query("SELECT nama_obat, stok FROM master_obat WHERE stok < 5");
$obat_menipis = $stmt_stok->fetchAll();

// ── Pencarian & filter ────────────────────────────────────────────────────
$search = trim($_GET['search'] ?? '');
$bentuk = $_GET['bentuk'] ?? '';

$sql = "SELECT * FROM master_obat WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (nama_obat LIKE :search OR kategori LIKE :search)";
    $params[':search'] = "%$search%";
}
if (!empty($bentuk)) {
    $sql .= " AND bentuk = :bentuk";
    $params[':bentuk'] = $bentuk;
}
$sql .= " ORDER BY nama_obat ASC";

$stmt_data = $pdo->prepare($sql);
$stmt_data->execute($params);
$obat_list = $stmt_data->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Obat</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; }
        h2 { color: #333; }
        .alert { padding: 12px 16px; border-radius: 5px; margin-bottom: 14px; font-size: 14px; }
        .alert-danger  { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-warn    { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .filter-box { background: white; padding: 14px 16px; border-radius: 6px; margin-bottom: 16px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        input[type=text], select { padding: 7px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .btn { padding: 7px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; border: none; cursor: pointer; }
        .btn-cari   { background: #007bff; color: white; }
        .btn-reset  { background: #6c757d; color: white; }
        .btn-tambah { background: #28a745; color: white; display: inline-block; margin-bottom: 12px; padding: 8px 18px; border-radius: 4px; text-decoration: none; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 6px; overflow: hidden; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        th { background: #343a40; color: white; padding: 11px 12px; text-align: left; font-size: 13px; }
        td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 13px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f8f9fa; }
        .stok-warn { color: #dc3545; font-weight: bold; }
        .aksi a { margin-right: 6px; font-size: 13px; text-decoration: none; }
        .aksi a:hover { text-decoration: underline; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .badge-admin { background: #cce5ff; color: #004085; }
    </style>
</head>
<body>
<div class="container">
    <h2>💊 Kelola Data Obat
        <span class="badge badge-admin"><?= htmlspecialchars($_SESSION['role_nama']) ?></span>
    </h2>

    <?php if ($pesan_error): ?>
        <div class="alert alert-danger">❌ <?= htmlspecialchars($pesan_error) ?></div>
    <?php endif; ?>

    <?php if ($pesan_sukses): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($pesan_sukses) ?></div>
    <?php endif; ?>

    <?php if (!empty($obat_menipis)): ?>
        <div class="alert alert-warn">
            <strong>⚠️ Peringatan Stok Menipis!</strong> <?= count($obat_menipis) ?> obat stoknya di bawah 5:
            <?php foreach ($obat_menipis as $om): ?>
                <strong><?= htmlspecialchars($om['nama_obat']) ?></strong> (sisa <?= $om['stok'] ?>)<?= $om !== end($obat_menipis) ? ', ' : '' ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="GET" class="filter-box">
        <input type="text" name="search" placeholder="🔍 Cari nama atau kategori..." value="<?= htmlspecialchars($search) ?>" style="width:220px;">
        <select name="bentuk">
            <option value="">-- Semua Bentuk --</option>
            <?php foreach (['Tablet','Kapsul','Sirup','Serbuk','Salep','Tetes'] as $b): ?>
                <option value="<?= $b ?>" <?= $bentuk == $b ? 'selected' : '' ?>><?= $b ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-cari">Cari</button>
        <a href="kelolaObat.php" class="btn btn-reset">Reset</a>
    </form>

    <?php if ($isAdmin): ?>
        <a href="tambahObat.php" class="btn-tambah">➕ Tambah Obat Baru</a>
    <?php endif; ?>

    <table>
        <tr>
            <th>No</th>
            <th>Nama Obat</th>
            <th>Kategori</th>
            <th>Bentuk</th>
            <th>Dosis</th>
            <th>Satuan</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        <?php if (empty($obat_list)): ?>
            <tr><td colspan="8" style="text-align:center; padding:20px; color:#999;">Tidak ada data yang ditemukan.</td></tr>
        <?php else: ?>
            <?php $no = 1; foreach ($obat_list as $row): ?>
            <tr>
                <td style="text-align:center"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_obat']) ?></td>
                <td><?= htmlspecialchars($row['kategori']) ?></td>
                <td><?= htmlspecialchars($row['bentuk'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['dosis'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['satuan'] ?? '-') ?></td>
                <td class="<?= $row['stok'] < 5 ? 'stok-warn' : '' ?>" style="text-align:center">
                    <?= $row['stok'] ?> <?= $row['stok'] < 5 ? '⚠️' : '' ?>
                </td>
                <td class="aksi">
                    <a href="detailObat.php?id=<?= $row['master_id'] ?>" style="color:#17a2b8">Detail</a>
                    <?php if ($isAdmin): ?>
                        <a href="editObat.php?id=<?= $row['master_id'] ?>" style="color:#e0a800">Edit</a>
                        <a href="../proses/prosesObat.php?aksi=hapus&id=<?= $row['master_id'] ?>"
                           onclick="return confirm('Yakin ingin menghapus <?= htmlspecialchars(addslashes($row['nama_obat'])) ?>?')"
                           style="color:#dc3545">Hapus</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
