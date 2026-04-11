-- ============================================================
-- Setup Database Sistem Parkir Restoran
-- ============================================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `parkiran_restoran`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `parkiran_restoran`;

-- ============================================================
-- 1. TABEL TB_USER
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_user` (
    `id_user` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'petugas', 'owner') NOT NULL DEFAULT 'petugas',
    `status_aktif` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. TABEL TB_KENDARAAN
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_kendaraan` (
    `id_kendaraan` INT AUTO_INCREMENT PRIMARY KEY,
    `plat_nomor` VARCHAR(20) UNIQUE NOT NULL,
    `jenis_kendaraan` VARCHAR(50) NOT NULL,
    `warna` VARCHAR(30) NOT NULL,
    `pemilik` VARCHAR(100) NOT NULL,
    `status` INT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. TABEL TB_TARIF
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_tarif` (
    `id_tarif` INT AUTO_INCREMENT PRIMARY KEY,
    `jenis_kendaraan` VARCHAR(50) NOT NULL,
    `tarif_per_jam` INT NOT NULL,
    `tarif_per_hari` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. TABEL TB_AREA
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_area` (
    `id_area` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_area` VARCHAR(100) NOT NULL,
    `kapasitas` INT NOT NULL,
    `lokasi` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. TABEL TB_TRANSAKSI
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_transaksi` (
    `id_parkir` INT AUTO_INCREMENT PRIMARY KEY,
    `id_kendaraan` INT NOT NULL,
    `id_user` INT,
    `id_area` INT,
    `id_tarif` INT,
    `waktu_masuk` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `waktu_keluar` DATETIME,
    `status` VARCHAR(20) DEFAULT 'masuk',
    `biaya_total` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan`(`id_kendaraan`),
    FOREIGN KEY (`id_user`) REFERENCES `tb_user`(`id_user`),
    FOREIGN KEY (`id_area`) REFERENCES `tb_area`(`id_area`),
    FOREIGN KEY (`id_tarif`) REFERENCES `tb_tarif`(`id_tarif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. TABEL TB_LOG_AKTIVITAS
-- ============================================================
CREATE TABLE IF NOT EXISTS `tb_log_aktivitas` (
    `id_log` INT AUTO_INCREMENT PRIMARY KEY,
    `id_user` INT DEFAULT NULL,
    `aktivitas` VARCHAR(255) NOT NULL,
    `waktu_aktivitas` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_user`) REFERENCES `tb_user`(`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DATA USER DEFAULT
-- ============================================================

-- Hapus user lama jika ada
DELETE FROM `tb_user`;
ALTER TABLE `tb_user` AUTO_INCREMENT = 1;

-- Insert user default
INSERT INTO `tb_user` (`username`, `password`, `role`, `status_aktif`) VALUES 
('admin', 'admin123', 'admin', 1),
('petugas', 'petugas123', 'petugas', 1),
('owner', 'owner123', 'owner', 1);

-- ============================================================
-- DATA TARIF DEFAULT
-- ============================================================

-- Hapus tarif lama jika ada
DELETE FROM `tb_tarif`;
ALTER TABLE `tb_tarif` AUTO_INCREMENT = 1;

-- Insert tarif default
INSERT INTO `tb_tarif` (`jenis_kendaraan`, `tarif_per_jam`, `tarif_per_hari`) VALUES 
('Motor', 3000, 20000),
('Mobil', 5000, 40000),
('Truk', 10000, 80000);

-- ============================================================
-- DATA AREA DEFAULT
-- ============================================================

-- Hapus area lama jika ada
DELETE FROM `tb_area`;
ALTER TABLE `tb_area` AUTO_INCREMENT = 1;

-- Insert area default
INSERT INTO `tb_area` (`nama_area`, `kapasitas`, `lokasi`) VALUES 
('Area A', 30, 'Depan Restoran'),
('Area B', 25, 'Samping Restoran'),
('Area C', 40, 'Belakang Restoran');

-- ============================================================
-- VERIFIKASI
-- ============================================================

SELECT '=== VERIFIKASI SETUP ===' as Info;
SELECT CONCAT('User: ', COUNT(*)) as Statistics FROM `tb_user`;
SELECT CONCAT('Tarif: ', COUNT(*)) as Statistics FROM `tb_tarif`;
SELECT CONCAT('Area: ', COUNT(*)) as Statistics FROM `tb_area`;

SELECT 'Daftar User:' as Info;
SELECT id_user, username, role, status_aktif FROM `tb_user`;

SELECT 'Setup selesai! Gunakan kredensial berikut untuk login:' as Info;
SELECT '- Admin: admin / admin123' as Login;
SELECT '- Petugas: petugas / petugas123' as Login;
SELECT '- Owner: owner / owner123' as Login;
