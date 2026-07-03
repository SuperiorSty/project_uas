<?php
session_start();
// Memanggil file konfigurasi database
require_once '../config/database.php'; 

// Memanggil fungsi connect() dari Class Database buatan temanmu
$pdo = Database::connect();

// --- BAGIAN 3: NOTIFIKASI STOK MENIPIS ---
$stmt_stok = $pdo->query("SELECT COUNT(*) FROM master_obat WHERE stok < 5");
$stok_menipis = $stmt_stok->fetchColumn();

// --- BAGIAN 2: FITUR PENCARIAN & FILTER ---
$search = $_GET['search'] ?? '';
$bentuk = $_GET['bentuk'] ?? '';

// Query dasar (WHERE 1=1 memudahkan penambahan kondisi AND)
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

// --- BAGIAN 1: BACA DATA (READ) ---
$stmt_data = $pdo->prepare($sql);
$stmt_data->execute($params);
$obat_list = $stmt_data->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Obat</title>
</head>
<body style="font-family: sans-serif; padding: 20px;">
    <h2>Kelola Data Obat</h2>

    <?php if ($stok_menipis > 0): ?>
        <div style='background-color: #ffcccc; color: #cc0000; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #cc0000;'>
            <strong>Peringatan!</strong> Ada <?= $stok_menipis ?> jenis obat yang stoknya di bawah 5.
        </div>
    <?php endif; ?>

    <form method="GET" style="margin-bottom: 20px; padding: 15px; background-color: #f1f1f1; border-radius: 5px;">
        <input type="text" name="search" placeholder="Cari nama atau kategori..." value="<?= htmlspecialchars($search) ?>" style="padding: 5px; width: 200px;">
        <select name="bentuk" style="padding: 5px;">
            <option value="">-- Semua Bentuk --</option>
            <option value="Tablet" <?= $bentuk == 'Tablet' ? 'selected' : '' ?>>Tablet</option>
            <option value="Kapsul" <?= $bentuk == 'Kapsul' ? 'selected' : '' ?>>Kapsul</option>
            <option value="Sirup" <?= $bentuk == 'Sirup' ? 'selected' : '' ?>>Sirup</option>
        </select>
        <button type="submit" style="padding: 6px 15px; cursor: pointer;">Cari</button>
        <a href="kelolaObat.php"><button type="button" style="padding: 6px 15px; cursor: pointer;">Reset</button></a>
    </form>

    <div style="margin-bottom: 15px;">
        <a href="tambahObat.php" style="background-color: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; border-radius: 3px;">+ Tambah Obat Baru</a>
    </div>
    
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <tr style="background-color: #eaeaea;">
            <th>No</th>
            <th>Nama Obat</th>
            <th>Kategori</th>
            <th>Bentuk</th>
            <th>Dosis</th>
            <th>Satuan</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
        <?php 
        $no = 1;
        foreach ($obat_list as $row): 
        ?>
            <tr>
                <td style="text-align: center;"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_obat']) ?></td>
                <td><?= htmlspecialchars($row['kategori']) ?></td>
                <td><?= htmlspecialchars($row['bentuk']) ?></td>
                <td><?= htmlspecialchars($row['dosis']) ?></td>
                <td><?= htmlspecialchars($row['satuan']) ?></td>
                <td style="text-align: center; font-weight: bold; <?= $row['stok'] < 5 ? 'color: red;' : '' ?>">
                    <?= htmlspecialchars($row['stok']) ?>
                </td>
                <td style="text-align: center;">
                    <a href="editObat.php?id=<?= $row['master_id'] ?>" style="color: blue; text-decoration: none;">Edit</a> | 
                    <a href="../proses/prosesObat.php?aksi=hapus&id=<?= $row['master_id'] ?>" onclick="return confirm('Yakin ingin menghapus obat ini?')" style="color: red; text-decoration: none;">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (count($obat_list) == 0): ?>
            <tr>
                <td colspan="8" style="text-align:center; padding: 20px;">Data obat tidak ditemukan atau belum ada data.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>