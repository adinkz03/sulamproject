-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table masjidkamek.deaths
CREATE TABLE IF NOT EXISTS `deaths` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `time` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `islamic_date` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_deaths_user_id` (`user_id`),
  CONSTRAINT `fk_deaths_user_boot` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.deaths: ~0 rows (approximately)

-- Dumping structure for table masjidkamek.death_notifications
CREATE TABLE IF NOT EXISTS `death_notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `deceased_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ic_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_death` date NOT NULL,
  `place_of_death` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cause_of_death` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_of_kin_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_of_kin_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reported_by` int unsigned DEFAULT NULL,
  `verified` tinyint(1) DEFAULT '0',
  `verified_by` int unsigned DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reported_by` (`reported_by`),
  KEY `verified_by` (`verified_by`),
  KEY `idx_date_of_death` (`date_of_death`),
  CONSTRAINT `death_notifications_ibfk_1` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`),
  CONSTRAINT `death_notifications_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.death_notifications: ~0 rows (approximately)

-- Dumping structure for table masjidkamek.dependent
CREATE TABLE IF NOT EXISTS `dependent` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tanggungan_user_id` (`user_id`),
  CONSTRAINT `fk_tanggungan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.dependent: ~0 rows (approximately)

-- Dumping structure for table masjidkamek.donations
CREATE TABLE IF NOT EXISTS `donations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General Donation',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.donations: ~1 rows (approximately)
INSERT INTO `donations` (`id`, `title`, `description`, `image_path`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'asasasasa', 'dasdfasdasdas', 'assets/uploads/donation_1764217629_f1fa2088.png', 1, '2025-11-27 04:27:09', '2025-11-27 04:27:09');

-- Dumping structure for table masjidkamek.events
CREATE TABLE IF NOT EXISTS `events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'New Event',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.events: ~1 rows (approximately)
INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `event_time`, `location`, `image_path`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'asdasdas', 'asdasdas', '2025-11-11', '14:29:00', 'asdsa', 'assets/uploads/event_1764217646_d31a808e.png', 1, '2025-11-27 04:27:26', '2025-11-27 04:27:26');

-- Dumping structure for table masjidkamek.financial_deposit_accounts
CREATE TABLE IF NOT EXISTS `financial_deposit_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `receipt_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tx_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `received_from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cash','bank','cheque') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `geran_kerajaan` decimal(12,2) unsigned DEFAULT '0.00',
  `sumbangan_derma` decimal(12,2) unsigned DEFAULT '0.00',
  `tabung_masjid` decimal(12,2) unsigned DEFAULT '0.00',
  `kutipan_jumaat_sadak` decimal(12,2) unsigned DEFAULT '0.00',
  `kutipan_aidilfitri_aidiladha` decimal(12,2) unsigned DEFAULT '0.00',
  `sewa_peralatan_masjid` decimal(12,2) unsigned DEFAULT '0.00',
  `hibah_faedah_bank` decimal(12,2) unsigned DEFAULT '0.00',
  `faedah_simpanan_tetap` decimal(12,2) unsigned DEFAULT '0.00',
  `sewa_rumah_kedai_tadika_menara` decimal(12,2) unsigned DEFAULT '0.00',
  `lain_lain_terimaan` decimal(12,2) unsigned DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tx_date` (`tx_date`),
  KEY `idx_receipt_number` (`receipt_number`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.financial_deposit_accounts: ~28 rows (approximately)
INSERT INTO `financial_deposit_accounts` (`id`, `receipt_number`, `tx_date`, `description`, `received_from`, `payment_method`, `payment_reference`, `geran_kerajaan`, `sumbangan_derma`, `tabung_masjid`, `kutipan_jumaat_sadak`, `kutipan_aidilfitri_aidiladha`, `sewa_peralatan_masjid`, `hibah_faedah_bank`, `faedah_simpanan_tetap`, `sewa_rumah_kedai_tadika_menara`, `lain_lain_terimaan`, `created_at`, `updated_at`) VALUES
	(1, 'RR/2025/0001', '2025-01-03', 'Kutipan Jumaat Minggu Pertama', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1250.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(2, 'RR/2025/0002', '2025-01-05', 'Sumbangan Ikhlas Dermawan', 'Haji Ahmad bin Abdullah', 'bank', 'TRF20250105001', 0.00, 5000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(3, 'RR/2025/0003', '2025-01-10', 'Kutipan Jumaat Minggu Kedua', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1420.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(4, 'RR/2025/0004', '2025-01-12', 'Sewa Dewan Serbaguna - Majlis Kesyukuran', 'Encik Kamal bin Hassan', 'bank', 'CHQ8023456', 0.00, 0.00, 0.00, 0.00, 0.00, 350.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-08 12:16:23'),
	(5, 'RR/2025/0005', '2025-01-15', 'Geran JAIM Tahun 2025', 'Jabatan Agama Islam Melaka', 'bank', 'TRF20250115890', 10000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(6, 'RR/2025/0006', '2025-01-17', 'Kutipan Jumaat Minggu Ketiga', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1380.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(7, 'RR/2025/0007', '2025-01-20', 'Derma Peralatan Masjid', 'Puan Siti Nurhaliza', 'bank', 'TRF20250120234', 0.00, 2500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(8, 'RR/2025/0008', '2025-01-22', 'Tabung Pembinaan Masjid', 'Dermawan Anonymous', 'cash', NULL, 0.00, 0.00, 3000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(9, 'RR/2025/0009', '2025-01-24', 'Kutipan Jumaat Minggu Keempat', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1510.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(10, 'RR/2025/0010', '2025-01-27', 'Faedah Simpanan Tetap - Bank Islam', 'Bank Islam Malaysia Berhad', 'bank', 'INT20250127', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 458.50, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(11, 'RR/2025/0011', '2025-01-31', 'Kutipan Jumaat Minggu Kelima', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1290.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(12, 'RR/2025/0012', '2025-02-01', 'Sewa Kedai Tingkat Bawah - Bulan Feb', 'Kedai Runcit Pak Ali', 'bank', 'TRF20250201567', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1200.00, 0.00, '2025-12-02 13:50:27', NULL),
	(13, 'RR/2025/0013', '2025-02-03', 'Hibah Dari Bank Muamalat', 'Bank Muamalat Malaysia Berhad', 'bank', 'HIB20250203', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 125.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(14, 'RR/2025/0014', '2025-02-07', 'Kutipan Jumaat Minggu Pertama Feb', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1445.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(15, 'RR/2025/0015', '2025-02-10', 'Sewa Peralatan PA System - Majlis Perkahwinan', 'Encik Razak bin Osman', 'cash', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 150.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(16, 'RR/2025/0016', '2025-02-14', 'Kutipan Jumaat Minggu Kedua Feb', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1520.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(17, 'RR/2025/0017', '2025-02-15', 'Derma Dari Syarikat Perniagaan', 'Syarikat XYZ Sdn Bhd', 'bank', 'CHQ9087654', 0.00, 8000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-08 12:16:23'),
	(18, 'RR/2025/0018', '2025-02-18', 'Sewa Dewan - Kelas Pendidikan Islam', 'Pusat Tahfiz An-Nur', 'bank', 'TRF20250218890', 0.00, 0.00, 0.00, 0.00, 0.00, 500.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(19, 'RR/2025/0019', '2025-02-21', 'Kutipan Jumaat Minggu Ketiga Feb', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1365.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(20, 'RR/2025/0020', '2025-02-25', 'Jualan Hasil Program Masjid', 'Program Majlis Tahunan', 'cash', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 680.00, '2025-12-02 13:50:27', NULL),
	(21, 'RR/2025/0021', '2025-02-28', 'Kutipan Jumaat Minggu Keempat Feb', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1480.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(22, 'RR/2025/0022', '2025-03-01', 'Sewa Rumah Imam - Bulan Mac', 'Imam Masjid', 'bank', 'TRF20250301123', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 800.00, 0.00, '2025-12-02 13:50:27', NULL),
	(23, 'RR/2025/0023', '2025-03-05', 'Geran Khas Pembinaan Surau', 'Kerajaan Negeri Melaka', 'bank', 'TRF20250305999', 15000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(24, 'RR/2025/0024', '2025-03-07', 'Kutipan Jumaat Minggu Pertama Mac', 'Jemaah Masjid', 'cash', NULL, 0.00, 0.00, 0.00, 1555.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', NULL),
	(25, 'RR/2025/0025', '2025-03-10', 'Derma Pembinaan Tadika', 'Datuk Seri Mahmud', 'bank', 'CHQ1234567', 0.00, 10000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-08 12:16:23'),
	(26, NULL, '2023-11-03', 'Kutipan Jumaat Minggu 1', NULL, 'cash', NULL, 0.00, 0.00, 0.00, 1200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-11 03:42:31', NULL),
	(27, NULL, '2023-11-05', 'Sumbangan Ikhlas', NULL, 'cash', NULL, 0.00, 500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-11 03:42:31', NULL),
	(28, NULL, '2023-11-10', 'Sewa Dewan Serbaguna', NULL, 'cash', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 300.00, 0.00, 0.00, 0.00, 0.00, '2025-12-11 03:42:31', NULL);

-- Dumping structure for table masjidkamek.financial_payment_accounts
CREATE TABLE IF NOT EXISTS `financial_payment_accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `voucher_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tx_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paid_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payee_ic` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payee_bank_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payee_bank_account` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('cash','bank','cheque') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `payment_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `perayaan_islam` decimal(12,2) unsigned DEFAULT '0.00',
  `pengimarahan_aktiviti_masjid` decimal(12,2) unsigned DEFAULT '0.00',
  `penyelenggaraan_masjid` decimal(12,2) unsigned DEFAULT '0.00',
  `keperluan_kelengkapan_masjid` decimal(12,2) unsigned DEFAULT '0.00',
  `gaji_upah_saguhati_elaun` decimal(12,2) unsigned DEFAULT '0.00',
  `sumbangan_derma` decimal(12,2) unsigned DEFAULT '0.00',
  `mesyuarat_jamuan` decimal(12,2) unsigned DEFAULT '0.00',
  `utiliti` decimal(12,2) unsigned DEFAULT '0.00',
  `alat_tulis_percetakan` decimal(12,2) unsigned DEFAULT '0.00',
  `pengangkutan_perjalanan` decimal(12,2) unsigned DEFAULT '0.00',
  `caj_bank` decimal(12,2) unsigned DEFAULT '0.00',
  `lain_lain_perbelanjaan` decimal(12,2) unsigned DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tx_date` (`tx_date`),
  KEY `idx_voucher_number` (`voucher_number`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.financial_payment_accounts: ~38 rows (approximately)
INSERT INTO `financial_payment_accounts` (`id`, `voucher_number`, `tx_date`, `description`, `paid_to`, `payee_ic`, `payee_bank_name`, `payee_bank_account`, `payment_method`, `payment_reference`, `perayaan_islam`, `pengimarahan_aktiviti_masjid`, `penyelenggaraan_masjid`, `keperluan_kelengkapan_masjid`, `gaji_upah_saguhati_elaun`, `sumbangan_derma`, `mesyuarat_jamuan`, `utiliti`, `alat_tulis_percetakan`, `pengangkutan_perjalanan`, `caj_bank`, `lain_lain_perbelanjaan`, `created_at`, `updated_at`) VALUES
	(1, 'MADU/2025/0001', '2025-01-04', 'Bayaran Bil Elektrik - Bulan Disember 2024', 'TNB Melaka', NULL, NULL, NULL, 'bank', 'TRF20250104001', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 385.50, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(2, 'MADU/2025/0002', '2025-01-05', 'Bayaran Bil Air - Bulan Disember 2024', 'SAMB Melaka', NULL, NULL, NULL, 'bank', 'TRF20250105002', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 125.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(3, 'MADU/2025/0003', '2025-01-07', 'Saguhati Penceramah Kuliah Jumaat', 'Ustaz Mohamad bin Ahmad', '750812-10-5432', 'Bank Islam', '1234567890123', 'bank', 'TRF20250107003', 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(4, 'MADU/2025/0004', '2025-01-08', 'Pembelian Alat Tulis Pejabat', 'Kedai Alat Tulis Mesra', NULL, NULL, NULL, 'cash', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 125.80, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(5, 'MADU/2025/0005', '2025-01-10', 'Penyelenggaraan Kipas Siling Dewan', 'Syarikat Elektrik Jaya', NULL, NULL, NULL, 'bank', 'CHQ7890123', 0.00, 0.00, 450.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(6, 'MADU/2025/0006', '2025-01-12', 'Gaji Kakitangan Masjid - Bulan Januari', 'Encik Roslan bin Hassan (Imam)', '680523-10-1234', 'Maybank', '5678901234567', 'bank', 'TRF20250112006', 0.00, 0.00, 0.00, 0.00, 1500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(7, 'MADU/2025/0007', '2025-01-12', 'Gaji Kakitangan Masjid - Bulan Januari', 'Encik Ibrahim bin Yusof (Bilal)', '720815-10-5678', 'CIMB Bank', '8901234567890', 'bank', 'TRF20250112007', 0.00, 0.00, 0.00, 0.00, 800.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(8, 'MADU/2025/0008', '2025-01-12', 'Gaji Kakitangan Masjid - Bulan Januari', 'Puan Fatimah binti Abdullah (Pembersih)', '850920-10-9012', 'RHB Bank', '2345678901234', 'bank', 'TRF20250112008', 0.00, 0.00, 0.00, 0.00, 600.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(9, 'MADU/2025/0009', '2025-01-15', 'Pembelian Karpet Masjid Baru', 'Kedai Karpet Al-Hijrah', NULL, NULL, NULL, 'bank', 'CHQ8901234', 0.00, 0.00, 0.00, 2800.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(10, 'MADU/2025/0010', '2025-01-18', 'Bayaran Percetakan Banner Program Tahunan', 'Percetakan Mutiara', NULL, NULL, NULL, 'cash', NULL, 0.00, 350.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(11, 'MADU/2025/0011', '2025-01-20', 'Saguhati Penceramah Kuliah Jumaat', 'Ustazah Aisyah binti Zainal', '821205-10-3456', 'Bank Muamalat', '4567890123456', 'bank', 'TRF20250120011', 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(12, 'MADU/2025/0012', '2025-01-22', 'Derma Bantuan Keluarga Asnaf', 'Keluarga Encik Ahmad bin Salleh', '650710-10-7890', NULL, NULL, 'cash', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 300.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(13, 'MADU/2025/0013', '2025-01-25', 'Penyelenggaraan Aircond Dewan', 'Syarikat Penghawa Dingin Sejuk', NULL, NULL, NULL, 'bank', 'CHQ9012345', 0.00, 0.00, 850.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(14, 'MADU/2025/0014', '2025-01-27', 'Belanja Jamuan Mesyuarat Jawatankuasa', 'Restoran Nasi Kandar Pelita', NULL, NULL, NULL, 'cash', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 280.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(15, 'MADU/2025/0015', '2025-01-29', 'Caj Pengurusan Akaun Bank - Bulan Januari', 'Bank Islam Malaysia Berhad', NULL, NULL, NULL, 'bank', 'AUTO-DEBIT', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 15.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(16, 'MADU/2025/0016', '2025-02-02', 'Bayaran Bil Elektrik - Bulan Januari 2025', 'TNB Melaka', NULL, NULL, NULL, 'bank', 'TRF20250202016', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 412.30, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(17, 'MADU/2025/0017', '2025-02-03', 'Bayaran Bil Air - Bulan Januari 2025', 'SAMB Melaka', NULL, NULL, NULL, 'bank', 'TRF20250203017', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 138.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(18, 'MADU/2025/0018', '2025-02-05', 'Pembelian Al-Quran dan Buku Terjemahan', 'Kedai Buku Pustaka Islamiah', NULL, NULL, NULL, 'bank', 'CHQ0123456', 0.00, 0.00, 0.00, 1200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(19, 'MADU/2025/0019', '2025-02-08', 'Saguhati Penceramah Kuliah Jumaat', 'Ustaz Abdullah bin Omar', '770315-10-2345', 'Bank Rakyat', '6789012345678', 'bank', 'TRF20250208019', 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(20, 'MADU/2025/0020', '2025-02-10', 'Belanja Pengangkutan Program Lawatan', 'Syarikat Bas Sinar Jaya', NULL, NULL, NULL, 'cash', NULL, 0.00, 550.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(21, 'MADU/2025/0021', '2025-02-12', 'Gaji Kakitangan Masjid - Bulan Februari', 'Encik Roslan bin Hassan (Imam)', '680523-10-1234', 'Maybank', '5678901234567', 'bank', 'TRF20250212021', 0.00, 0.00, 0.00, 0.00, 1500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(22, 'MADU/2025/0022', '2025-02-12', 'Gaji Kakitangan Masjid - Bulan Februari', 'Encik Ibrahim bin Yusof (Bilal)', '720815-10-5678', 'CIMB Bank', '8901234567890', 'bank', 'TRF20250212022', 0.00, 0.00, 0.00, 0.00, 800.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(23, 'MADU/2025/0023', '2025-02-12', 'Gaji Kakitangan Masjid - Bulan Februari', 'Puan Fatimah binti Abdullah (Pembersih)', '850920-10-9012', 'RHB Bank', '2345678901234', 'bank', 'TRF20250212023', 0.00, 0.00, 0.00, 0.00, 600.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(24, 'MADU/2025/0024', '2025-02-14', 'Perbelanjaan Program Maulidur Rasul', 'Pelbagai Vendor', NULL, NULL, NULL, 'cash', NULL, 1500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(25, 'MADU/2025/0025', '2025-02-16', 'Bayaran Perkhidmatan Internet - Bulan Februari', 'TM Unifi', NULL, NULL, NULL, 'bank', 'TRF20250216025', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 159.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(26, 'MADU/2025/0026', '2025-02-20', 'Penyelenggaraan Cat Dinding Luar Masjid', 'Syarikat Cat & Dekorasi', NULL, NULL, NULL, 'bank', 'CHQ1234567', 0.00, 0.00, 2500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(27, 'MADU/2025/0027', '2025-02-22', 'Saguhati Penceramah Kuliah Jumaat', 'Ustaz Zainuddin bin Ali', '791018-10-4567', 'Bank Islam', '7890123456789', 'bank', 'TRF20250222027', 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(28, 'MADU/2025/0028', '2025-02-25', 'Derma Bantuan Keluarga Asnaf', 'Keluarga Puan Maimunah binti Hassan', '721125-10-8901', NULL, NULL, 'cash', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 400.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(29, 'MADU/2025/0029', '2025-02-27', 'Caj Pengurusan Akaun Bank - Bulan Februari', 'Bank Islam Malaysia Berhad', NULL, NULL, NULL, 'bank', 'AUTO-DEBIT', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 15.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(30, 'MADU/2025/0030', '2025-03-01', 'Pembelian Peralatan Sound System Baru', 'Kedai Elektronik Harmoni', NULL, NULL, NULL, 'bank', 'CHQ2345678', 0.00, 0.00, 0.00, 3500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(31, 'MADU/2025/0031', '2025-03-04', 'Bayaran Bil Elektrik - Bulan Februari 2025', 'TNB Melaka', NULL, NULL, NULL, 'bank', 'TRF20250304031', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 398.75, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(32, 'MADU/2025/0032', '2025-03-05', 'Bayaran Bil Air - Bulan Februari 2025', 'SAMB Melaka', NULL, NULL, NULL, 'bank', 'TRF20250305032', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 142.50, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(33, 'MADU/2025/0033', '2025-03-08', 'Saguhati Penceramah Kuliah Jumaat', 'Ustaz Hafiz bin Mahmud', '830722-10-5678', 'Bank Muamalat', '8901234567890', 'bank', 'TRF20250308033', 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(34, 'MADU/2025/0034', '2025-03-10', 'Belanja Perjalanan Mesyuarat Luar Negeri', 'Pengerusi Masjid - Encik Azman', '650210-10-1234', NULL, NULL, 'bank', 'TRF20250310034', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 850.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(35, 'MADU/2025/0035', '2025-03-12', 'Pembelian Penyaman Udara (Aircond) Bilik Imam', 'Syarikat Elektrik Sejuk Beku', NULL, NULL, NULL, 'bank', 'CHQ3456789', 0.00, 0.00, 0.00, 2200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-02 13:50:27', '2025-12-12 08:52:02'),
	(36, NULL, '2023-10-25', 'Pembelian Al-Quran Baru', NULL, NULL, NULL, NULL, 'cash', NULL, 0.00, 0.00, 0.00, 500.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-11 03:42:31', NULL),
	(37, NULL, '2023-10-26', 'Bayaran Bil Elektrik', NULL, NULL, NULL, NULL, 'cash', NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 250.00, 0.00, 0.00, 0.00, 0.00, '2025-12-11 03:42:31', NULL),
	(38, NULL, '2023-10-27', 'Saguhati Penceramah Jemputan', NULL, NULL, NULL, NULL, 'cash', NULL, 0.00, 0.00, 0.00, 0.00, 150.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-12-11 03:42:31', NULL);

-- Dumping structure for table masjidkamek.financial_settings
CREATE TABLE IF NOT EXISTS `financial_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `fiscal_year` year NOT NULL,
  `opening_cash_balance` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Baki Awal di Tangan',
  `opening_bank_balance` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Baki Awal di Bank',
  `effective_date` date NOT NULL COMMENT 'Tarikh berkuatkuasa baki awal',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_fiscal_year` (`fiscal_year`),
  KEY `idx_effective_date` (`effective_date`),
  KEY `fk_financial_settings_user` (`created_by`),
  CONSTRAINT `fk_financial_settings_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.financial_settings: ~1 rows (approximately)
INSERT INTO `financial_settings` (`id`, `fiscal_year`, `opening_cash_balance`, `opening_bank_balance`, `effective_date`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, '2025', 1000.00, 2000.00, '2025-01-01', 'Baki awal permulaan sistem', NULL, '2025-12-02 11:16:54', '2025-12-11 03:42:31');

-- Dumping structure for table masjidkamek.frontend_logs
CREATE TABLE IF NOT EXISTS `frontend_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('error','warning','info','debug') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'error',
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'error, ajax_error, resource_error, performance, user_action',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `stack_trace` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Page URL where error occurred',
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `os` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen_resolution` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_data` json DEFAULT NULL COMMENT 'Additional context data',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_level` (`level`),
  KEY `idx_type` (`type`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `frontend_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.frontend_logs: ~16 rows (approximately)
INSERT INTO `frontend_logs` (`id`, `user_id`, `session_id`, `level`, `type`, `message`, `stack_trace`, `url`, `user_agent`, `browser`, `os`, `screen_resolution`, `request_data`, `ip_address`, `created_at`) VALUES
	(1, NULL, '9anh4p2dtgl92qlqspsec4nbbl', 'info', 'test', 'Test log from PowerShell', NULL, NULL, 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-MY) WindowsPowerShell/5.1.26100.7019', NULL, NULL, NULL, '{"line": null, "extra": null, "column": null, "source": null}', '::1', '2025-12-06 12:52:49'),
	(2, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'error', 'javascript_error', 'Uncaught ReferenceError: nonExistentVariable is not defined', 'ReferenceError: nonExistentVariable is not defined\n    at triggerUndefinedError (http://localhost/sulamprojectex/test-frontend-logger.html:232:13)\n    at HTMLButtonElement.onclick (http://localhost/sulamprojectex/test-frontend-logger.html:133:55)', 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": 232, "extra": null, "column": 13, "source": "http://localhost/sulamprojectex/test-frontend-logger.html"}', '::1', '2025-12-06 12:57:12'),
	(3, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'error', 'javascript_error', 'Uncaught ReferenceError: nonExistentVariable is not defined', 'ReferenceError: nonExistentVariable is not defined\n    at triggerUndefinedError (http://localhost/sulamprojectex/test-frontend-logger.html:232:13)\n    at HTMLButtonElement.onclick (http://localhost/sulamprojectex/test-frontend-logger.html:133:55)', 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": 232, "extra": null, "column": 13, "source": "http://localhost/sulamprojectex/test-frontend-logger.html"}', '::1', '2025-12-06 12:59:51'),
	(4, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'error', 'javascript_error', 'Uncaught TypeError: Cannot read properties of null (reading \'toString\')', 'TypeError: Cannot read properties of null (reading \'toString\')\n    at triggerTypeError (http://localhost/sulamprojectex/test-frontend-logger.html:237:18)\n    at HTMLButtonElement.onclick (http://localhost/sulamprojectex/test-frontend-logger.html:134:50)', 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": 237, "extra": null, "column": 18, "source": "http://localhost/sulamprojectex/test-frontend-logger.html"}', '::1', '2025-12-06 12:59:58'),
	(5, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'error', 'javascript_error', 'Uncaught ReferenceError: nonExistentFunction is not defined', 'ReferenceError: nonExistentFunction is not defined\n    at eval (eval at triggerReferenceError (http://localhost/sulamprojectex/test-frontend-logger.html:30:13), <anonymous>:1:1)\n    at triggerReferenceError (http://localhost/sulamprojectex/test-frontend-logger.html:242:13)\n    at HTMLButtonElement.onclick (http://localhost/sulamprojectex/test-frontend-logger.html:135:55)', 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": 1, "extra": null, "column": 1, "source": ""}', '::1', '2025-12-06 12:59:59'),
	(6, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'error', 'promise_rejection', 'Unhandled Promise Rejection: Error: Test promise rejection', 'Error: Test promise rejection\n    at triggerPromiseRejection (http://localhost/sulamprojectex/test-frontend-logger.html:248:28)\n    at HTMLButtonElement.onclick (http://localhost/sulamprojectex/test-frontend-logger.html:142:57)', 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": null, "column": null, "source": null}', '::1', '2025-12-06 13:00:00'),
	(7, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'error', 'resource_error', 'Failed to load resource: http://localhost/nonexistent-image-1765026001524.jpg', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"type": "unknown", "tagName": "IMG"}, "column": null, "source": "http://localhost/nonexistent-image-1765026001524.jpg"}', '::1', '2025-12-06 13:00:01'),
	(8, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'error', 'resource_error', 'Failed to load resource: http://localhost/nonexistent-script-1765026001863.js', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"type": "unknown", "tagName": "SCRIPT"}, "column": null, "source": "http://localhost/nonexistent-script-1765026001863.js"}', '::1', '2025-12-06 13:00:01'),
	(9, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'error', 'custom', 'Manual error test', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"source": "test-page"}, "column": null, "source": null}', '::1', '2025-12-06 13:00:02'),
	(10, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'warning', 'custom', 'Manual warning test', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"source": "test-page"}, "column": null, "source": null}', '::1', '2025-12-06 13:00:03'),
	(11, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'info', 'custom', 'Manual info test', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"source": "test-page"}, "column": null, "source": null}', '::1', '2025-12-06 13:00:03'),
	(12, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'debug', 'custom', 'Manual debug test', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"source": "test-page"}, "column": null, "source": null}', '::1', '2025-12-06 13:00:03'),
	(13, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'info', 'custom', 'Button clicked: export', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"page": "/sulamprojectex/test-frontend-logger.html", "action": "export", "timestamp": "2025-12-06T13:00:08.010Z"}, "column": null, "source": null}', '::1', '2025-12-06 13:00:08'),
	(14, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'info', 'custom', 'Button clicked: delete', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"page": "/sulamprojectex/test-frontend-logger.html", "action": "delete", "timestamp": "2025-12-06T13:00:08.321Z"}, "column": null, "source": null}', '::1', '2025-12-06 13:00:08'),
	(15, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'info', 'custom', 'Button clicked: save', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"page": "/sulamprojectex/test-frontend-logger.html", "action": "save", "timestamp": "2025-12-06T13:00:08.571Z"}, "column": null, "source": null}', '::1', '2025-12-06 13:00:08'),
	(16, 2, 'b58cdvbmvni3ut1lmlhqrv6c09', 'warning', 'custom', 'Slow operation detected', NULL, 'http://localhost/sulamprojectex/test-frontend-logger.html', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'Chrome', 'Windows', '1536x865', '{"line": null, "extra": {"duration": 2004.10000000149, "operation": "simulateSlowOperation"}, "column": null, "source": null}', '::1', '2025-12-06 13:00:10');

-- Dumping structure for table masjidkamek.funeral_logistics
CREATE TABLE IF NOT EXISTS `funeral_logistics` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `death_notification_id` int NOT NULL,
  `burial_date` date DEFAULT NULL,
  `burial_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grave_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `arranged_by` int unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `death_notification_id` (`death_notification_id`),
  KEY `arranged_by` (`arranged_by`),
  CONSTRAINT `funeral_logistics_ibfk_1` FOREIGN KEY (`death_notification_id`) REFERENCES `death_notifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `funeral_logistics_ibfk_2` FOREIGN KEY (`arranged_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.funeral_logistics: ~0 rows (approximately)

-- Dumping structure for table masjidkamek.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `executed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table masjidkamek.migrations: ~7 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `executed_at`) VALUES
	(1, '004_add_relationship_to_next_of_kin.sql', '2025-12-11 03:42:31'),
	(2, '006_create_financial_tables.sql', '2025-12-11 03:42:31'),
	(3, '007_seed_financial_data.sql', '2025-12-11 03:42:31'),
	(4, '011_create_financial_settings_table.sql', '2025-12-11 03:42:31'),
	(5, '013_update_payment_method_enum.sql', '2025-12-11 03:42:32'),
	(6, '014_update_deathnotifications.sql', '2025-12-11 03:42:32'),
	(7, '015_update_funeral_logistics.sql', '2025-12-11 03:42:32');

-- Dumping structure for table masjidkamek.next_of_kin
CREATE TABLE IF NOT EXISTS `next_of_kin` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_next_of_kin_user_id` (`user_id`),
  CONSTRAINT `fk_next_of_kin_user_boot` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.next_of_kin: ~0 rows (approximately)

-- Dumping structure for table masjidkamek.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` enum('resident','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'resident',
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `marital_status` enum('single','married','divorced','widowed','others') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deceased` tinyint(1) NOT NULL DEFAULT '0',
  `income` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table masjidkamek.users: ~2 rows (approximately)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `roles`, `phone_number`, `address`, `marital_status`, `is_deceased`, `income`, `created_at`, `updated_at`) VALUES
	(1, 'user', 'user1234', 'yyy@gg.com', '$2y$10$ueHhzpVf1OuCqQdwZbHyheYQ4G88Xyt.iiu4xSRuDxMXnylE4LtdC', 'resident', NULL, NULL, NULL, 0, NULL, '2025-11-22 11:11:16', '2025-11-23 10:50:04'),
	(2, 'Admin User', 'admin', 'admin@example.com', '$2y$10$TpCoz.GGNzxB1kWd4ZRhX.lsJCj4fmuAm4oo9DiZ2HsLCcsEE.5IW', 'admin', NULL, NULL, NULL, 0, NULL, '2025-11-22 19:40:25', '2025-12-06 11:16:32');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
