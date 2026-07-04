<?php
session_start();
require_once '../config/database.php';
$pdo = Database::connect();

// Jika belum login, tendang balik ke halaman login (index.php)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$current_user_id = $_SESSION['user_id']; 

// --- AMBIL DAFTAR RIWAYAT KONSUMSI OBAT (HANYA UNTUK USER YANG SEDANG LOGIN) ---
$sql_riwayat = "SELECT r.*, m.nama_obat, m.dosis, m.bentuk 
                FROM riwayat_obat r
                INNER JOIN master_obat m ON r.obat_id = m.master_id
                WHERE r.user_id = ? 
                ORDER BY r.waktu_jadwal DESC LIMIT 20";

$stmt_riwayat = $pdo->prepare($sql_riwayat);
$stmt_riwayat->execute([$current_user_id]);
$riwayat_list = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Konsumsi Obat</title>
</head>
<body style="font-family: sans-serif; padding: 20px; background-color: #f4f7f6;">

    <h2>Log Riwayat Konsumsi Obat Anda</h2>

    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <tr style="background-color: #007bff; color: white;">
            <th>No</th>
            <th>Nama Obat</th>
            <th>Dosis / Bentuk</th>
            <th>Waktu Jadwal</th>
            <th>Waktu Realisasi Minum</th>
            <th>Status</th>
        </tr>
        <?php $no = 1; foreach ($riwayat_list as $row): ?>
            <tr>
                <td style="text-align: center;"><?= $no++ ?></td>
                <td><strong><?= htmlspecialchars($row['nama_obat']) ?></strong></td>
                <td><?= htmlspecialchars($row['dosis']) ?> (<?= htmlspecialchars($row['bentuk']) ?>)</td>
                <td><?= htmlspecialchars($row['waktu_jadwal']) ?></td>
                <td><?= $row['waktu_diminum'] ? htmlspecialchars($row['waktu_diminum']) : '<em style="color:gray;">Tidak tercatat (Terlewat/Ditunda)</em>' ?></td>
                <td style="text-align: center;">
                    <?php 
                        $bg_color = '#6c757d'; // Default ditunda (abu-abu)
                        if ($row['status'] == 'Sudah Diminum') $bg_color = '#28a745'; // Hijau
                        if ($row['status'] == 'Terlewat') $bg_color = '#dc3545'; // Merah
                    ?>
                    <span style="padding: 5px 10px; border-radius: 3px; font-weight: bold; color: white; background-color: <?= $bg_color ?>;">
                        <?= htmlspecialchars($row['status']) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        
        <?php if (count($riwayat_list) == 0): ?>
            <tr>
                <td colspan="6" style="text-align:center; padding: 20px; color: gray;">Belum ada riwayat konsumsi obat.</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>