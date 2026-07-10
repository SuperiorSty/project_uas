<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';

$pdo = Database::connect();

// Cari jadwal dalam 30 menit ke depan yang belum dinotifikasi
$stmt = $pdo->prepare("
    SELECT r.id, r.waktu_jadwal, u.email, u.nama_lengkap, o.nama_obat
    FROM riwayat_obat r
    JOIN users u ON r.user_id = u.users_id
    JOIN obat o ON r.obat_id = o.id
    WHERE r.waktu_jadwal BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 MINUTE)
      AND r.is_notified = 0
");
$stmt->execute();
$jadwal = $stmt->fetchAll();

foreach ($jadwal as $row) {
    $jam = date('H:i', strtotime($row['waktu_jadwal']));
    $pesan = "Halo {$row['nama_lengkap']},\n\nJangan lupa minum {$row['nama_obat']} pukul {$jam} WITA.\n\nTetap sehat!";
    $subject = "Pengingat Minum Obat — {$row['nama_obat']}";

    if (Mailer::send($row['email'], $subject, $pesan)) {
        $update = $pdo->prepare("UPDATE riwayat_obat SET is_notified = 1 WHERE id = ?");
        $update->execute([$row['id']]);
    }
}
