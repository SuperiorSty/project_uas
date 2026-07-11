<?php
/**
 * Cron job untuk menandai jadwal yang terlewat.
 * Jalankan setiap 15-30 menit via cron scheduler.
 */

require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConn();

// Tandai jadwal yang sudah lewat jamnya sebagai "Terlewat"
$stmt = $conn->prepare("
    SELECT jr.id_jadwal, jr.jam_minum, jr.id_obat_user, o.user_id
    FROM jadwal_reminder jr
    JOIN obat o ON jr.id_obat_user = o.id_obat_user
    WHERE jr.status_hari_ini = 0
      AND jr.jam_minum < CURTIME()
");
$stmt->execute();
$terlewat = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($terlewat as $row) {
    // Update status jadwal jadi terlewat (99 = kode terlewat)
    $stmt1 = $conn->prepare("UPDATE jadwal_reminder SET status_hari_ini = 99 WHERE id_jadwal = ?");
    $stmt1->bind_param("i", $row['id_jadwal']);
    $stmt1->execute();

    // Catat ke riwayat_obat
    $waktu_jadwal = date('Y-m-d') . ' ' . $row['jam_minum'];
    $stmt2 = $conn->prepare("
        INSERT INTO riwayat_obat (user_id, id_obat_user, waktu_jadwal, status, is_notified)
        VALUES (?, ?, ?, 'Terlewat', 1)
    ");
    $stmt2->bind_param("iis", $row['user_id'], $row['id_obat_user'], $waktu_jadwal);
    $stmt2->execute();
}
