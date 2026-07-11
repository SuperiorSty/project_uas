<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/masuk.php?error=Silakan login terlebih dahulu!");
    exit;
}

$db = new Database();
$conn = $db->getConn();
$aksi = $_GET['aksi'] ?? '';

if ($aksi == 'catat' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $id_jadwal = (int)($_POST['id_jadwal'] ?? 0);
    $id_obat_user = (int)($_POST['id_obat_user'] ?? 0);
    $jam_minum = $_POST['jam_minum'] ?? '';

    if ($id_jadwal <= 0 || $id_obat_user <= 0) {
        header("Location: ../views/dasboard.php?error=Data tidak valid.");
        exit;
    }

    $waktu_diminum = date('Y-m-d H:i:s');
    $waktu_jadwal = date('Y-m-d') . ' ' . $jam_minum;

    try {
        // Cek apakah jadwal sudah lewat (terlambat)
        $stmt = $conn->prepare("SELECT jam_minum FROM jadwal_reminder WHERE id_jadwal = ?");
        $stmt->bind_param("i", $id_jadwal);
        $stmt->execute();
        $jadwal_row = $stmt->get_result()->fetch_assoc();
        $is_late = $jadwal_row && $jadwal_row['jam_minum'] < date('H:i:s');

        $conn->begin_transaction();

        // Update jadwal_reminder status (2 = terlambat, 1 = tepat waktu)
        $new_status = $is_late ? 2 : 1;
        $stmt = $conn->prepare("UPDATE jadwal_reminder SET status_hari_ini = ? WHERE id_jadwal = ? AND id_obat_user = ?");
        $stmt->bind_param("iii", $new_status, $id_jadwal, $id_obat_user);
        $stmt->execute();

        // Ambil nama_obat untuk disimpan di riwayat
        $nama_obat = '';
        $stmt = $conn->prepare("SELECT m.nama_obat FROM obat o JOIN master_obat m ON o.id_obat = m.id_obat WHERE o.id_obat_user = ?");
        $stmt->bind_param("i", $id_obat_user);
        $stmt->execute();
        $row_nama = $stmt->get_result()->fetch_assoc();
        $nama_obat = $row_nama['nama_obat'] ?? '';

        // Insert riwayat_obat dengan nama_obat sebagai teks
        $stmt = $conn->prepare("INSERT INTO riwayat_obat (user_id, id_obat_user, nama_obat, waktu_jadwal, status, waktu_diminum) VALUES (?, ?, ?, ?, 'Sudah Diminum', ?)");
        $stmt->bind_param("iisss", $user_id, $id_obat_user, $nama_obat, $waktu_jadwal, $waktu_diminum);
        $stmt->execute();

        // Kurangi stok
        $stmt = $conn->prepare("UPDATE obat SET jumlah_stok = GREATEST(0, jumlah_stok - 1) WHERE id_obat_user = ?");
        $stmt->bind_param("i", $id_obat_user);
        $stmt->execute();

        $conn->commit();
        header("Location: ../views/dasboard.php?success=Konsumsi berhasil dicatat!");
    } catch (Exception $e) {
        @$conn->rollback();
        header("Location: ../views/dasboard.php?error=Gagal mencatat konsumsi.");
    }
    exit;
} else {
    header("Location: ../views/dasboard.php");
    exit;
}
