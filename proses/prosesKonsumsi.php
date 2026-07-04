<?php
session_start();
require_once '../config/database.php';
$pdo = Database::connect();

$aksi = $_GET['aksi'] ?? '';

if ($aksi == 'catat' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil ID user yang sedang login dari session buatan Anggota 1
    $user_id = $_SESSION['user_id'] ?? null; 
    $master_id = $_POST['master_id'];
    $waktu_jadwal = $_POST['waktu_jadwal']; 
    $status = $_POST['status']; 
    
    $waktu_diminum = ($status == 'Sudah Diminum') ? date('Y-m-d H:i:s') : null;

    // Pastikan user sudah login sebelum mencatat
    if (!$user_id) {
        header("Location: ../index.php?error=Silakan login terlebih dahulu");
        exit;
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

    // --- SEKARANG KITA MASUKKAN USER_ID KE DALAM QUERY ---
    $stmt = $pdo->prepare("INSERT INTO riwayat_obat (user_id, obat_id, waktu_jadwal, status, is_notified, waktu_diminum) VALUES (?, ?, ?, ?, 0, ?)");
    $stmt->execute([$user_id, $master_id, $waktu_jadwal, $status, $waktu_diminum]);

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    if ($status == 'Sudah Diminum') {
        $stmt_update = $pdo->prepare("UPDATE master_obat SET stok = stok - 1 WHERE master_id = ? AND stok > 0");
        $stmt_update->execute([$master_id]);
    }

    header("Location: ../views/riwayat.php?pesan=berhasil");
    exit;
} else {
    header("Location: ../views/riwayat.php");
    exit;
}
?>