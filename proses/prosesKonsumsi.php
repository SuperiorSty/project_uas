<?php
session_start();
require_once '../config/database.php';
$db = new Database();
$conn = $db->getConn();

$aksi = $_GET['aksi'] ?? '';

if ($aksi == 'catat' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $riwayat_id = (int)($_POST['riwayat_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (!$user_id || $riwayat_id <= 0) {
        header("Location: ../views/masuk.php?error=Silakan login terlebih dahulu");
        exit;
    }

    $waktu_diminum = ($status == 'Sudah Diminum') ? date('Y-m-d H:i:s') : null;

    $stmt = $conn->prepare("UPDATE riwayat_obat SET status = ?, waktu_diminum = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $status, $waktu_diminum, $riwayat_id, $user_id);
    $stmt->execute();

    header("Location: ../views/dasboard.php");
    exit;
} else {
    header("Location: ../views/dasboard.php");
    exit;
}
?>