<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?error=Silakan login terlebih dahulu!");
    exit;
}

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
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Obat - PengingatObat</title>
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
            --accent: #3688C9; --accent-hover: #3659C9; --success: #28a745; --danger: #dc3545;
        }
        [data-theme="dark"] {
            --bg-body: #0d0d1a; --bg-card: #1a1a30; --bg-navbar: #141428;
            --text-primary: #f8f9fa; --text-secondary: #e0e0e8; --text-muted: #a8a8b8;
            --bg-input: #242442; --text-input: #ffffff;
            --border-color: #343459;
            --table-striped: rgba(255,255,255,0.03); --table-hover: rgba(255,255,255,0.05);
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.4); --shadow-md: 0 4px 16px rgba(0,0,0,0.6);
            --accent: #3688C9; --success: #28a745; --danger: #dc3545;
        }
        * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg-body); color: var(--text-primary); margin: 0; padding: 20px; }
        .card { background-color: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; }
        .form-control, .form-select { background-color: var(--bg-input); color: var(--text-input); border: 1px solid var(--border-color); padding: 8px 12px; border-radius: 8px; }
        .form-control:focus, .form-select:focus { background-color: var(--bg-input); color: var(--text-input); border-color: var(--accent); box-shadow: 0 0 0 3px rgba(54,136,201,0.15); }
        .table { --bs-table-bg: transparent; color: var(--text-primary); }
        .text-muted { --bs-text-opacity: 1; color: var(--text-muted) !important; }
        table { background: var(--bg-card); color: var(--text-primary); }
        th, td { border-color: var(--border-color) !important; }
        a { color: var(--accent); }
    </style>
</head>
<body>
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
        <h2 style="margin: 0;">Kelola Data Obat</h2>
        <a href="dashboard.php" style="background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color); padding: 8px 16px; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 0.9rem;"><i class="fa-solid fa-arrow-left"></i>Kembali ke Dashboard</a>
    </div>

    <?php if ($stok_menipis > 0): ?>
        <div class="card" style="padding: 15px; margin-bottom: 20px; border-left: 4px solid var(--danger); background: rgba(220,53,69,0.1); color: var(--danger);">
            <strong><i class="fa-solid fa-triangle-exclamation me-1"></i>Peringatan!</strong> Ada <?= $stok_menipis ?> jenis obat yang stoknya di bawah 5.
        </div>
    <?php endif; ?>

    <form method="GET" class="card" style="margin-bottom: 20px; padding: 15px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <input type="text" name="search" class="form-control" placeholder="Cari nama atau kategori..." value="<?= htmlspecialchars($search) ?>" style="width: 200px; padding: 6px 10px;">
        <select name="bentuk" class="form-select" style="width: auto; padding: 6px 10px;">
            <option value="">-- Semua Bentuk --</option>
            <option value="Tablet" <?= $bentuk == 'Tablet' ? 'selected' : '' ?>>Tablet</option>
            <option value="Kapsul" <?= $bentuk == 'Kapsul' ? 'selected' : '' ?>>Kapsul</option>
            <option value="Sirup" <?= $bentuk == 'Sirup' ? 'selected' : '' ?>>Sirup</option>
        </select>
        <button type="submit" class="form-control" style="width: auto; cursor: pointer; background: var(--accent); color: white; border: none; padding: 6px 16px; border-radius: 6px;"><i class="fa-solid fa-search me-1"></i>Cari</button>
        <a href="kelolaObat.php"><button type="button" class="form-control" style="width: auto; cursor: pointer; background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color); padding: 6px 16px; border-radius: 6px;">Reset</button></a>
    </form>

    <div style="margin-bottom: 15px;">
        <a href="tambahObat.php" style="background: var(--success); color: white; padding: 8px 16px; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; font-size: 0.9rem;"><i class="fa-solid fa-plus"></i>Tambah Obat Baru</a>
    </div>
    
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <tr style="background: var(--table-striped);">
            <th style="color: var(--text-secondary);">No</th>
            <th style="color: var(--text-secondary);">Nama Obat</th>
            <th style="color: var(--text-secondary);">Kategori</th>
            <th style="color: var(--text-secondary);">Bentuk</th>
            <th style="color: var(--text-secondary);">Dosis</th>
            <th style="color: var(--text-secondary);">Satuan</th>
            <th style="color: var(--text-secondary);">Stok</th>
            <th style="color: var(--text-secondary);">Aksi</th>
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