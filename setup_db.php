<?php
/**
 * Setup Database - Sistem Parkir Restoran
 * Version: Simple & Safe
 */

$host = "localhost";
$user = "root";
$pass = "";
$db_name = "parkiran_restoran";

$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("ERROR: Koneksi gagal: " . mysqli_connect_error());
}

echo "🔧 Setup Database Parkiran Restoran\n";
echo "=====================================\n\n";

// 1. Buat database
$sql = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (mysqli_query($conn, $sql)) {
    echo "✓ Database berhasil dibuat\n";
} else {
    echo "✗ Error database: " . mysqli_error($conn) . "\n";
    exit;
}

// 2. Pilih database
if (!mysqli_select_db($conn, $db_name)) {
    echo "✗ Error memilih database\n";
    exit;
}

// 3. Drop tables lama (jika ada)
echo "\n📊 Membuat Tabel...\n";
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

$drop_tables = ["tb_log_aktivitas", "tb_transaksi", "tb_kendaraan", "tb_area", "tb_tarif", "tb_user"];
foreach ($drop_tables as $table) {
    mysqli_query($conn, "DROP TABLE IF EXISTS `$table`");
}

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

// 4. Buat tabel tb_user
$sql_user = "
CREATE TABLE `tb_user` (
    `id_user` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `password` VARCHAR(100) NOT NULL,
    `role` ENUM('admin', 'petugas', 'owner') DEFAULT 'petugas',
    `status_aktif` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

if (mysqli_query($conn, $sql_user)) {
    echo "✓ Tabel tb_user berhasil dibuat\n";
} else {
    echo "✗ Error tb_user: " . mysqli_error($conn) . "\n";
}

// 5. Buat tabel tb_kendaraan
$sql_kendaraan = "
CREATE TABLE `tb_kendaraan` (
    `id_kendaraan` INT AUTO_INCREMENT PRIMARY KEY,
    `plat_nomor` VARCHAR(20) UNIQUE NOT NULL,
    `jenis_kendaraan` VARCHAR(50) NOT NULL,
    `warna` VARCHAR(30) NOT NULL,
    `pemilik` VARCHAR(100) NOT NULL,
    `status` INT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

if (mysqli_query($conn, $sql_kendaraan)) {
    echo "✓ Tabel tb_kendaraan berhasil dibuat\n";
} else {
    echo "✗ Error tb_kendaraan: " . mysqli_error($conn) . "\n";
}

// 6. Buat tabel tb_tarif
$sql_tarif = "
CREATE TABLE `tb_tarif` (
    `id_tarif` INT AUTO_INCREMENT PRIMARY KEY,
    `jenis_kendaraan` VARCHAR(50) NOT NULL,
    `tarif_per_jam` INT NOT NULL,
    `tarif_per_hari` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

if (mysqli_query($conn, $sql_tarif)) {
    echo "✓ Tabel tb_tarif berhasil dibuat\n";
} else {
    echo "✗ Error tb_tarif: " . mysqli_error($conn) . "\n";
}

// 7. Buat tabel tb_area
$sql_area = "
CREATE TABLE `tb_area` (
    `id_area` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_area` VARCHAR(100) NOT NULL,
    `kapasitas` INT NOT NULL,
    `lokasi` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

if (mysqli_query($conn, $sql_area)) {
    echo "✓ Tabel tb_area berhasil dibuat\n";
} else {
    echo "✗ Error tb_area: " . mysqli_error($conn) . "\n";
}

// 8. Buat tabel tb_transaksi
$sql_transaksi = "
CREATE TABLE `tb_transaksi` (
    `id_parkir` INT AUTO_INCREMENT PRIMARY KEY,
    `id_kendaraan` INT NOT NULL,
    `id_user` INT,
    `id_area` INT,
    `id_tarif` INT,
    `waktu_masuk` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `waktu_keluar` DATETIME NULL,
    `status` VARCHAR(20) DEFAULT 'masuk',
    `biaya_total` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan`(`id_kendaraan`),
    FOREIGN KEY (`id_user`) REFERENCES `tb_user`(`id_user`),
    FOREIGN KEY (`id_area`) REFERENCES `tb_area`(`id_area`),
    FOREIGN KEY (`id_tarif`) REFERENCES `tb_tarif`(`id_tarif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

if (mysqli_query($conn, $sql_transaksi)) {
    echo "✓ Tabel tb_transaksi berhasil dibuat\n";
} else {
    echo "✗ Error tb_transaksi: " . mysqli_error($conn) . "\n";
}

// 9. Buat tabel tb_log_aktivitas
$sql_log = "
CREATE TABLE `tb_log_aktivitas` (
    `id_log` INT AUTO_INCREMENT PRIMARY KEY,
    `id_user` INT DEFAULT NULL,
    `aktivitas` VARCHAR(255) NOT NULL,
    `waktu_aktivitas` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_user`) REFERENCES `tb_user`(`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
";

if (mysqli_query($conn, $sql_log)) {
    echo "✓ Tabel tb_log_aktivitas berhasil dibuat\n";
} else {
    echo "✗ Error tb_log_aktivitas: " . mysqli_error($conn) . "\n";
}

// 10. Insert user default
echo "\n👤 Setup User Default...\n";
$users = [
    ['admin', 'admin123', 'admin'],
    ['petugas', 'petugas123', 'petugas'],
    ['owner', 'owner123', 'owner']
];

foreach ($users as $u) {
    $username = mysqli_real_escape_string($conn, $u[0]);
    $password = mysqli_real_escape_string($conn, $u[1]);
    $role = mysqli_real_escape_string($conn, $u[2]);
    
    $sql_insert = "INSERT INTO `tb_user` (`username`, `password`, `role`, `status_aktif`) " .
                  "VALUES ('$username', '$password', '$role', 1)";
    
    if (mysqli_query($conn, $sql_insert)) {
        echo "✓ User $username berhasil dibuat\n";
    } else {
        echo "✗ Error user: " . mysqli_error($conn) . "\n";
    }
}

// 11. Insert tarif default
echo "\n💰 Setup Tarif Default...\n";
$tarifs = [
    ['Motor', 3000, 20000],
    ['Mobil', 5000, 40000],
    ['Truk', 10000, 80000]
];

foreach ($tarifs as $t) {
    $jenis = mysqli_real_escape_string($conn, $t[0]);
    $harga_jam = (int)$t[1];
    $harga_hari = (int)$t[2];
    
    $sql_insert = "INSERT INTO `tb_tarif` (`jenis_kendaraan`, `tarif_per_jam`, `tarif_per_hari`) " .
                  "VALUES ('$jenis', $harga_jam, $harga_hari)";
    
    if (mysqli_query($conn, $sql_insert)) {
        echo "✓ Tarif $jenis berhasil dibuat\n";
    } else {
        echo "✗ Error tarif: " . mysqli_error($conn) . "\n";
    }
}

// 12. Insert area default
echo "\n📍 Setup Area Default...\n";
$areas = [
    ['Area A', 30, 'Depan Restoran'],
    ['Area B', 25, 'Samping Restoran'],
    ['Area C', 40, 'Belakang Restoran']
];

foreach ($areas as $a) {
    $nama = mysqli_real_escape_string($conn, $a[0]);
    $kapasitas = (int)$a[1];
    $lokasi = mysqli_real_escape_string($conn, $a[2]);
    
    $sql_insert = "INSERT INTO `tb_area` (`nama_area`, `kapasitas`, `lokasi`) " .
                  "VALUES ('$nama', $kapasitas, '$lokasi')";
    
    if (mysqli_query($conn, $sql_insert)) {
        echo "✓ Area $nama berhasil dibuat\n";
    } else {
        echo "✗ Error area: " . mysqli_error($conn) . "\n";
    }
}

// 13. Verifikasi
echo "\n✅ Verifikasi Data\n";
echo "==================\n";

$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM `tb_user`");
$row = mysqli_fetch_assoc($result);
echo "User terbuat: " . $row['cnt'] . "\n";

$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM `tb_tarif`");
$row = mysqli_fetch_assoc($result);
echo "Tarif terbuat: " . $row['cnt'] . "\n";

$result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM `tb_area`");
$row = mysqli_fetch_assoc($result);
echo "Area terbuat: " . $row['cnt'] . "\n";

echo "\n🎯 Kredensial Login\n";
echo "===================\n";
echo "Admin  : admin / admin123\n";
echo "Petugas: petugas / petugas123\n";
echo "Owner  : owner / owner123\n";

echo "\n✨ Setup berhasil!\n";
echo "Buka: http://localhost/parkiran_restoran/index.php?url=auth/index\n";

mysqli_close($conn);
?>
