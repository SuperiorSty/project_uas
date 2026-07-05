-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.46-0ubuntu0.24.04.3 - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             12.17.0.7270
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for db_pengingat_obat
CREATE DATABASE IF NOT EXISTS `db_pengingat_obat` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `db_pengingat_obat`;

-- Dumping structure for table db_pengingat_obat.master_obat
CREATE TABLE IF NOT EXISTS `master_obat` (
  `master_id` int NOT NULL AUTO_INCREMENT,
  `nama_obat` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `bentuk` varchar(50) DEFAULT NULL,
  `dosis` varchar(50) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `stok` int DEFAULT '0',
  `deskripsi` text,
  PRIMARY KEY (`master_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_pengingat_obat.master_obat: ~4 rows (approximately)
INSERT INTO `master_obat` (`master_id`, `nama_obat`, `kategori`, `bentuk`, `dosis`, `satuan`, `stok`, `deskripsi`) VALUES
	(1, 'Paracetamol', 'Obat Demam', 'Tablet', '500mg', 'Strip', 49, NULL),
	(2, 'Amoxicillin', 'Antibiotik', 'Kapsul', '250mg', 'Strip', 19, NULL),
	(3, 'OBH Combi', 'Obat Batuk', 'Sirup', '100ml', 'Botol', 14, NULL),
	(4, 'Bodrexin', 'Obat Demam Anak', 'Tablet', '100mg', 'Box', 3, NULL);

-- Dumping structure for table db_pengingat_obat.obat
CREATE TABLE IF NOT EXISTS `obat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `dosis` varchar(50) NOT NULL,
  `aturan_pakai` enum('Sebelum Makan','Sesudah Makan','Bersama Makan') NOT NULL,
  `file_resep_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `obat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_pengingat_obat.obat: ~0 rows (approximately)

-- Dumping structure for table db_pengingat_obat.riwayat_obat
CREATE TABLE IF NOT EXISTS `riwayat_obat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `obat_id` int NOT NULL,
  `waktu_jadwal` datetime NOT NULL,
  `status` enum('Sudah Diminum','Terlewat','Ditunda') NOT NULL,
  `is_notified` tinyint(1) DEFAULT '0',
  `waktu_diminum` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_pengingat_obat.riwayat_obat: ~7 rows (approximately)
INSERT INTO `riwayat_obat` (`id`, `user_id`, `obat_id`, `waktu_jadwal`, `status`, `is_notified`, `waktu_diminum`) VALUES
	(1, 3, 1, '2026-07-03 08:00:00', 'Sudah Diminum', 0, '2026-07-03 16:03:04'),
	(2, 3, 3, '2026-07-03 08:00:00', 'Sudah Diminum', 0, '2026-07-03 16:03:34'),
	(3, 3, 2, '2026-07-03 01:20:00', 'Terlewat', 0, NULL),
	(4, 3, 1, '2026-07-03 08:00:00', 'Ditunda', 0, NULL),
	(5, 4, 2, '2026-07-03 08:00:00', 'Sudah Diminum', 0, '2026-07-03 16:05:00'),
	(6, 4, 1, '2026-07-11 08:00:00', 'Terlewat', 0, NULL),
	(7, 4, 4, '2026-07-03 08:00:00', 'Ditunda', 0, NULL);

-- Dumping structure for table db_pengingat_obat.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `roles_id` int NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(50) NOT NULL,
  PRIMARY KEY (`roles_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_pengingat_obat.roles: ~2 rows (approximately)
INSERT INTO `roles` (`roles_id`, `nama_role`) VALUES
	(1, 'ADMIN'),
	(2, 'PASIEN');

-- Dumping structure for table db_pengingat_obat.users
CREATE TABLE IF NOT EXISTS `users` (
  `users_id` int NOT NULL AUTO_INCREMENT,
  `roles_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`users_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `roles_id` (`roles_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`roles_id`) REFERENCES `roles` (`roles_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table db_pengingat_obat.users: ~2 rows (approximately)
INSERT INTO `users` (`users_id`, `roles_id`, `username`, `password`, `nama_lengkap`, `email`, `created_at`) VALUES
	(3, 1, 'ghanen', '$2y$10$9eqMFoZgpd9UldZW8L28a.BYwfbyq8IJCLxpApETQz3BW.Clv9bdu', 'ganendra redanayasa', 'ganendra@gmail.com', '2026-07-03 14:12:08'),
	(4, 2, 'devra', '$2y$10$ufGtYGjwmUUbsJsB0tdvkeAzkEC237UwCTlPFCHSdclpULwRXMnsO', 'devra', 'devra@gmail.com', '2026-07-04 08:52:25');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
