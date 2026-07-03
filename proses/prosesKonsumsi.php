<?php
session_start();
require_once '../config/database.php';
$pdo = Database::connect();

$aksi = $_GET['aksi'] ?? '';

if ($aksi == 'catat' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $master_id = $_POST['master_id'];
    $waktu_jadwal = $_POST['waktu_jadwal']; 
    $status = $_POST['status']; // Nilainya bisa: 'Sudah Diminum', 'Terlewat', atau 'Ditunda'
    
    // Waktu diminum HANYA dicatat jika statusnya 'Sudah Diminum'
    $waktu_diminum = ($status == 'Sudah Diminum') ? date('Y-m-d H:i:s') : null;

    // Nonaktifkan foreign key checks sebelum insert demi sinkronisasi dengan Anggota 2
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // Lakukan insert data riwayat
    $stmt = $pdo->prepare("INSERT INTO riwayat_obat (obat_id, waktu_jadwal, status, is_notified, waktu_diminum) VALUES (?, ?, ?, 0, ?)");
    $stmt->execute([$master_id, $waktu_jadwal, $status, $waktu_diminum]);

    // Aktifkan kembali foreign key checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Stok HANYA dipotong jika obat benar-benar 'Sudah Diminum'
    if ($status == 'Sudah Diminum') {
        $stmt_update = $pdo->prepare("UPDATE master_obat SET stok = stok - 1 WHERE master_id = ? AND stok > 0");
        $stmt_update->execute([$master_id]);
    }

    // Redirect kembali ke halaman riwayat setelah berhasil
    header("Location: ../views/riwayat.php?pesan=berhasil");
    exit;
} else {
    header("Location: ../views/riwayat.php");
    exit;
}
?>