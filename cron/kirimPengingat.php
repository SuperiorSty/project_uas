<?php
/**
 * Cron job untuk mengirim pengingat minum obat via email.
 * Jalankan setiap 15-30 menit via cron scheduler.
 * 
 * Contoh crontab (setiap 30 menit):
 *   php /path/to/cron/kirimPengingat.php
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';

$db = new Database();
$conn = $db->getConn();

// Cari jadwal dalam 15-30 menit ke depan yang belum diminum & belum dinotifikasi
$stmt = $conn->prepare("
    SELECT jr.id_jadwal, jr.jam_minum, o.user_id, u.email, u.nama_lengkap, m.nama_obat
    FROM jadwal_reminder jr
    JOIN obat o ON jr.id_obat_user = o.id_obat_user
    JOIN users u ON o.user_id = u.id
    JOIN master_obat m ON o.id_obat = m.id_obat
    WHERE jr.status_hari_ini = 0
      AND TIME_TO_SEC(TIMEDIFF(jr.jam_minum, CURTIME())) BETWEEN 0 AND 1800
");
$stmt->execute();
$jadwal = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($jadwal as $row) {
    $jam = substr($row['jam_minum'], 0, 5);
    $pesan = "Halo {$row['nama_lengkap']},\n\nJangan lupa minum {$row['nama_obat']} pukul {$jam} WITA.\n\nTetap sehat!";
    $subject = "Pengingat Minum Obat — {$row['nama_obat']}";

    if (Mailer::send($row['email'], $subject, $pesan)) {
        // Catat notifikasi di riwayat_obat
        $waktu_jadwal = date('Y-m-d') . ' ' . $row['jam_minum'];
        $stmt2 = $conn->prepare("
            INSERT INTO riwayat_obat (user_id, id_obat_user, waktu_jadwal, status, is_notified)
            VALUES (?, ?, ?, 'Ditunda', 1)
        ");
        $stmt2->bind_param("iis", $row['user_id'], $row['id_obat_user'], $waktu_jadwal);
        $stmt2->execute();
    }
}
