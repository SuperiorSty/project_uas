<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/masuk.php?error=Silakan login terlebih dahulu!");
    exit;
}

$db = new Database();
$conn = $db->getConn();
$user_id = $_SESSION['user_id'];

$id_obat = (int)($_POST['id_obat'] ?? 0);
$jumlah_stok = (int)($_POST['jumlah_stok'] ?? 0);
$frekuensi = (int)($_POST['frekuensi'] ?? 1);
$tipe_jadwal = $_POST['tipe_jadwal'] ?? 'spesifik';
$minimal_notif_stok = (int)($_POST['minimal_notif_stok'] ?? 3);

if ($id_obat <= 0) {
    header("Location: ../views/katalogObat.php?error=Obat tidak valid.");
    exit;
}

$jam_minum_list = [];

if ($tipe_jadwal === 'spesifik') {
    $jam_minum_list = $_POST['jam_minum'] ?? [];
    $jam_minum_list = array_filter(array_map('trim', $jam_minum_list));
} else {
    $jam_pertama = trim($_POST['jam_pertama'] ?? '');
    $gap_jam = (float)($_POST['gap_jam'] ?? 0);
    if (empty($jam_pertama) || $gap_jam <= 0) {
        header("Location: ../views/setPengingat.php?id_obat=$id_obat&error=Jam pertama dan gap harus diisi.");
        exit;
    }
    // Generate times based on first dose + gap
    $parts = explode(':', $jam_pertama);
    $total_menit = (int)$parts[0] * 60 + (int)($parts[1] ?? 0);
    $gap_menit = $gap_jam * 60;
    for ($i = 0; $i < $frekuensi; $i++) {
        $menit = ($total_menit + $i * $gap_menit) % (24 * 60);
        $jam_minum_list[] = sprintf('%02d:%02d', intdiv($menit, 60), $menit % 60);
    }
}

if (empty($jam_minum_list)) {
    header("Location: ../views/setPengingat.php?id_obat=$id_obat&error=Jam minum harus diisi.");
    exit;
}

try {
    $conn->begin_transaction();

    // Insert into obat (user's personal medicine config)
    $stmt = $conn->prepare("INSERT INTO obat (user_id, id_obat, jumlah_stok, frekuensi, tipe_jadwal, gap_jam, minimal_notif_stok) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $gap_jam_db = $tipe_jadwal === 'interval' ? (float)($_POST['gap_jam'] ?? 0) : null;
    $stmt->bind_param("iiiisdi", $user_id, $id_obat, $jumlah_stok, $frekuensi, $tipe_jadwal, $gap_jam_db, $minimal_notif_stok);
    $stmt->execute();
    $id_obat_user = $stmt->insert_id;

    // Insert jadwal_reminder records
    $stmt = $conn->prepare("INSERT INTO jadwal_reminder (id_obat_user, jam_minum) VALUES (?, ?)");
    foreach ($jam_minum_list as $jam) {
        $stmt->bind_param("is", $id_obat_user, $jam);
        $stmt->execute();
    }

    $conn->commit();
    header("Location: ../views/dasboard.php?success=Obat berhasil ditambahkan dengan jadwal pengingat!");
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../views/setPengingat.php?id_obat=$id_obat&error=Gagal menyimpan data. " . urlencode($e->getMessage()));
}
exit;
