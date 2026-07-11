<?php
/**
 * Cron job untuk:
 * 1. Menandai jadwal terlewat + kirim email peringatan
 * 2. Kirim email notifikasi stok menipis
 * Jalankan setiap 15-30 menit via cron scheduler.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';

$db = new Database();
$conn = $db->getConn();

// ═══════════════════════════════════════════════════════════
// 1. Tandai jadwal terlewat + kirim email
//    Dashboard sudah tandai status_hari_ini = 99,
//    jadi kita cari yg status IN (0,99) dan belum ada
//    riwayat 'Terlewat' untuk hindari duplikat email.
// ═══════════════════════════════════════════════════════════
$stmt = $conn->prepare("
    SELECT jr.id_jadwal, jr.jam_minum, jr.id_obat_user, o.user_id,
           u.email, u.nama_lengkap, m.nama_obat
    FROM jadwal_reminder jr
    JOIN obat o ON jr.id_obat_user = o.id_obat_user
    JOIN users u ON o.user_id = u.id
    JOIN master_obat m ON o.id_obat = m.id_obat
    LEFT JOIN riwayat_obat ro ON ro.user_id = o.user_id
        AND ro.id_obat_user = jr.id_obat_user
        AND ro.waktu_jadwal = CONCAT(CURDATE(), ' ', jr.jam_minum)
        AND ro.status = 'Terlewat'
    WHERE jr.status_hari_ini IN (0, 99)
      AND jr.jam_minum < CURTIME()
      AND ro.id IS NULL
");
$stmt->execute();
$terlewat = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($terlewat as $row) {
    $jam = substr($row['jam_minum'], 0, 5);

    // Update status jadwal jadi terlewat
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

    // Kirim email peringatan
    $pesan = "Halo {$row['nama_lengkap']},\n\nAnda terlewat minum {$row['nama_obat']} pukul {$jam} WITA. Segera minum obat Anda!\n\nTetap sehat, ForestView Health.";
    Mailer::send($row['email'], "Peringatan: Terlewat Minum Obat {$row['nama_obat']}", $pesan);
}

// ═══════════════════════════════════════════════════════════
// 2. Kirim email notifikasi stok menipis (1x per hari)
// ═══════════════════════════════════════════════════════════
$stmt = $conn->prepare("
    SELECT o.id_obat_user, o.jumlah_stok, o.minimal_notif_stok,
           o.notif_stok_sent_at, u.email, u.nama_lengkap, m.nama_obat
    FROM obat o
    JOIN users u ON o.user_id = u.id
    JOIN master_obat m ON o.id_obat = m.id_obat
    WHERE o.jumlah_stok <= o.minimal_notif_stok
      AND (o.notif_stok_sent_at IS NULL OR o.notif_stok_sent_at < CURDATE())
");
$stmt->execute();
$stok_menipis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($stok_menipis as $row) {
    $pesan = "Halo {$row['nama_lengkap']},\n\nStok {$row['nama_obat']} Anda tersisa {$row['jumlah_stok']}.\nSilakan kontrol kembali ke apotek untuk mendapatkan obat sebelum stok habis.\n\nTetap sehat, ForestView Health.";
    Mailer::send($row['email'], "Stok Obat Menipis {$row['nama_obat']}", $pesan);

    $stmt3 = $conn->prepare("UPDATE obat SET notif_stok_sent_at = CURDATE() WHERE id_obat_user = ?");
    $stmt3->bind_param("i", $row['id_obat_user']);
    $stmt3->execute();
}
