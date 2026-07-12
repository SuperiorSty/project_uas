-- ============================================================
-- Database: db_pengingat_obat
-- Export date: 2026-07-11 10:43:47
-- 
-- Login:
--   Admin: admin / admin123
--   Pasien: pasien / pasien123
--   Pasien: devra / (didaftarkan via form registrasi)
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_pengingat_obat;
USE db_pengingat_obat;

-- -----------------------------------------------------------
-- Table: roles
-- -----------------------------------------------------------
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_role` (`nama_role`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `roles` (`id`, `nama_role`) VALUES ('1', 'ADMIN');
INSERT INTO `roles` (`id`, `nama_role`) VALUES ('2', 'PASIEN');

-- -----------------------------------------------------------
-- Table: users
-- -----------------------------------------------------------
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `nama_lengkap`, `created_at`) VALUES ('1', '1', 'admin', 'admin@forestview.com', '$2y$10$BvsDSv3PmJDw8gjko4RqR.UjUxcFFNblfYxj7lDkavacTLETFfNFi', 'Admin Apoteker', '2026-07-11 13:54:10');
INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `nama_lengkap`, `created_at`) VALUES ('2', '2', 'pasien', 'pasien@email.com', '$2y$10$B0A/T3yJ.aqKSxzhJAlJWuWajzkHYCKeaxHwDWRcRy7I23PHMDQU6', 'Pasien Demo', '2026-07-11 13:54:10');
INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `nama_lengkap`, `created_at`) VALUES ('3', '2', 'devra', 'satyagodd@gmail.com', '$2y$10$k85R/r80Axsp/no7Gl82OupoFlfDG8Coxo0RHe/hIk5VIeU9JM7bS', 'gede satya devra widyanatha', '2026-07-11 16:47:48');

-- -----------------------------------------------------------
-- Table: master_obat
-- -----------------------------------------------------------
CREATE TABLE `master_obat` (
  `id_obat` int NOT NULL AUTO_INCREMENT,
  `nama_obat` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `deskripsi` text,
  PRIMARY KEY (`id_obat`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `master_obat` (`id_obat`, `nama_obat`, `kategori`, `deskripsi`) VALUES ('1', 'Paracetamol', 'Analgesik', 'Obat pereda demam dan nyeri ringan hingga sedang.');
INSERT INTO `master_obat` (`id_obat`, `nama_obat`, `kategori`, `deskripsi`) VALUES ('2', 'Amoxicillin', 'Antibiotik', 'Antibiotik golongan penisilin untuk infeksi bakteri.');
INSERT INTO `master_obat` (`id_obat`, `nama_obat`, `kategori`, `deskripsi`) VALUES ('3', 'Ibuprofen', 'Anti-inflamasi', 'Obat anti radang non-steroid untuk nyeri dan demam.');
INSERT INTO `master_obat` (`id_obat`, `nama_obat`, `kategori`, `deskripsi`) VALUES ('4', 'Omeprazole', 'Antasida', 'Obat untuk menurunkan produksi asam lambung.');
INSERT INTO `master_obat` (`id_obat`, `nama_obat`, `kategori`, `deskripsi`) VALUES ('5', 'Cetirizine', 'Antihistamin', 'Obat alergi untuk meredakan gejala rinitis alergi.');
INSERT INTO `master_obat` (`id_obat`, `nama_obat`, `kategori`, `deskripsi`) VALUES ('6', 'Metformin', 'Antidiabetes', 'Obat diabetes tipe 2 untuk mengontrol gula darah.');
INSERT INTO `master_obat` (`id_obat`, `nama_obat`, `kategori`, `deskripsi`) VALUES ('7', 'Amlodipine', 'Antihipertensi', 'Obat tekanan darah tinggi golongan CCB.');
INSERT INTO `master_obat` (`id_obat`, `nama_obat`, `kategori`, `deskripsi`) VALUES ('8', 'Salbutamol', 'Bronkodilator', 'Obat asma untuk melebarkan saluran pernapasan.');

-- -----------------------------------------------------------
-- Table: obat
-- -----------------------------------------------------------
CREATE TABLE `obat` (
  `id_obat_user` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `id_obat` int NOT NULL,
  `jumlah_stok` int NOT NULL DEFAULT '0',
  `frekuensi` tinyint NOT NULL DEFAULT '1' COMMENT '1x, 2x, atau 3x sehari',
  `tipe_jadwal` enum('interval','spesifik') NOT NULL DEFAULT 'spesifik',
  `gap_jam` decimal(4,1) DEFAULT NULL COMMENT 'Gap antar jam (jam) untuk tipe interval',
  `minimal_notif_stok` int NOT NULL DEFAULT '3',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_obat_user`),
  KEY `user_id` (`user_id`),
  KEY `id_obat` (`id_obat`),
  CONSTRAINT `obat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `obat_ibfk_2` FOREIGN KEY (`id_obat`) REFERENCES `master_obat` (`id_obat`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `obat` (`id_obat_user`, `user_id`, `id_obat`, `jumlah_stok`, `frekuensi`, `tipe_jadwal`, `gap_jam`, `minimal_notif_stok`, `created_at`) VALUES ('1', '2', '3', '5', '2', 'spesifik', NULL, '1', '2026-07-11 14:08:07');
INSERT INTO `obat` (`id_obat_user`, `user_id`, `id_obat`, `jumlah_stok`, `frekuensi`, `tipe_jadwal`, `gap_jam`, `minimal_notif_stok`, `created_at`) VALUES ('2', '2', '3', '6', '2', 'spesifik', NULL, '1', '2026-07-11 14:09:26');

-- -----------------------------------------------------------
-- Table: jadwal_reminder
-- -----------------------------------------------------------
CREATE TABLE `jadwal_reminder` (
  `id_jadwal` int NOT NULL AUTO_INCREMENT,
  `id_obat_user` int NOT NULL,
  `jam_minum` time NOT NULL,
  `status_hari_ini` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'False = belum diminum, True = sudah diminum',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_jadwal`),
  KEY `id_obat_user` (`id_obat_user`),
  CONSTRAINT `jadwal_reminder_ibfk_1` FOREIGN KEY (`id_obat_user`) REFERENCES `obat` (`id_obat_user`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `jadwal_reminder` (`id_jadwal`, `id_obat_user`, `jam_minum`, `status_hari_ini`, `created_at`) VALUES ('1', '1', '08:00:00', '1', '2026-07-11 14:08:07');
INSERT INTO `jadwal_reminder` (`id_jadwal`, `id_obat_user`, `jam_minum`, `status_hari_ini`, `created_at`) VALUES ('2', '1', '20:00:00', '0', '2026-07-11 14:08:07');
INSERT INTO `jadwal_reminder` (`id_jadwal`, `id_obat_user`, `jam_minum`, `status_hari_ini`, `created_at`) VALUES ('3', '2', '08:00:00', '0', '2026-07-11 14:09:26');
INSERT INTO `jadwal_reminder` (`id_jadwal`, `id_obat_user`, `jam_minum`, `status_hari_ini`, `created_at`) VALUES ('4', '2', '20:00:00', '0', '2026-07-11 14:09:26');

-- -----------------------------------------------------------
-- Table: riwayat_obat
-- -----------------------------------------------------------
CREATE TABLE `riwayat_obat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `id_obat_user` int DEFAULT NULL,
  `nama_obat` varchar(100) DEFAULT NULL,
  `waktu_jadwal` datetime NOT NULL,
  `status` enum('Sudah Diminum','Terlewat','Ditunda') NOT NULL DEFAULT 'Ditunda',
  `is_notified` tinyint(1) DEFAULT '0',
  `waktu_diminum` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fk_riwayat_obat_user` (`id_obat_user`),
  CONSTRAINT `fk_riwayat_obat_user` FOREIGN KEY (`id_obat_user`) REFERENCES `obat` (`id_obat_user`) ON DELETE SET NULL,
  CONSTRAINT `riwayat_obat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `riwayat_obat` (`id`, `user_id`, `id_obat_user`, `nama_obat`, `waktu_jadwal`, `status`, `is_notified`, `waktu_diminum`) VALUES ('1', '2', '1', NULL, '2026-07-11 08:00:00', 'Sudah Diminum', '0', '2026-07-11 06:10:36');
INSERT INTO `riwayat_obat` (`id`, `user_id`, `id_obat_user`, `nama_obat`, `waktu_jadwal`, `status`, `is_notified`, `waktu_diminum`) VALUES ('8', '3', NULL, NULL, '2026-07-11 18:00:00', 'Sudah Diminum', '0', '2026-07-11 09:54:59');

