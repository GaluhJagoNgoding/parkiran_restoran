<?php
/**
 * Setup Database Simple - Sistem Parkir Restoran
 */

// Koneksi langsung tanpa database
$host = "localhost";
$user = "root";
$pass = "";
$db_name = "parkiran_restoran";

// Buat koneksi
$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("ERROR: Koneksi gagal: " . mysqli_connect_error());
}

echo "<h2>🔧 Setup Database Parkiran Restoran</h2><hr>";

// 1. Buat database
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (mysqli_query($conn, $sql_create_db)) {
    echo "✓ Database '$db_name' berhasil dibuat<br>";
} else {
    echo "✗ Error membuat database: " . mysqli_error($conn) . "<br>";
}

// 2. Pilih database
mysqli_select_db($conn, $db_name);

// 3. Buat tabel-tabel
$tables = [
    "tb_user" => "CREATE TABLE IF NOT EXISTS tb_user (
        id_user INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(100) NOT NULL,
        role ENUM('admin', 'petugas', 'owner') NOT NULL DEFAULT 'petugas',
        status_aktif TINYINT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "tb_kendaraan" => "CREATE TABLE IF NOT EXISTS tb_kendaraan (
        id_kendaraan INT AUTO_INCREMENT PRIMARY KEY,
        plat_nomor VARCHAR(20) UNIQUE NOT NULL,
        jenis_kendaraan VARCHAR(50) NOT NULL,
        warna VARCHAR(30) NOT NULL,
        pemilik VARCHAR(100) NOT NULL,
        status INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "tb_tarif" => "CREATE TABLE IF NOT EXISTS tb_tarif (
        id_tarif INT AUTO_INCREMENT PRIMARY KEY,
        jenis_kendaraan VARCHAR(50) NOT NULL,
        tarif_per_jam INT NOT NULL,
        tarif_per_hari INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "tb_area" => "CREATE TABLE IF NOT EXISTS tb_area (
        id_area INT AUTO_INCREMENT PRIMARY KEY,
        nama_area VARCHAR(100) NOT NULL,
        kapasitas INT NOT NULL,
        lokasi TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "tb_transaksi" => "CREATE TABLE IF NOT EXISTS tb_transaksi (
        id_parkir INT AUTO_INCREMENT PRIMARY KEY,
        id_kendaraan INT NOT NULL,
        id_user INT,
        id_area INT,
        id_tarif INT,
        waktu_masuk DATETIME DEFAULT CURRENT_TIMESTAMP,
        waktu_keluar DATETIME,
        status VARCHAR(20) DEFAULT 'masuk',
        biaya_total INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_kendaraan) REFERENCES tb_kendaraan(id_kendaraan),
        FOREIGN KEY (id_user) REFERENCES tb_user(id_user),
        FOREIGN KEY (id_area) REFERENCES tb_area(id_area),
        FOREIGN KEY (id_tarif) REFERENCES tb_tarif(id_tarif)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    
    "tb_log_aktivitas" => "CREATE TABLE IF NOT EXISTS tb_log_aktivitas (
        id_log INT AUTO_INCREMENT PRIMARY KEY,
        id_user INT DEFAULT NULL,
        aktivitas VARCHAR(255) NOT NULL,
        waktu_aktivitas TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_user) REFERENCES tb_user(id_user) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
];

echo "<h3>📊 Membuat Tabel...</h3>";
foreach ($tables as $name => $sql) {
    if (mysqli_query($conn, $sql)) {
        echo "✓ Tabel <strong>$name</strong> berhasil dibuat<br>";
    } else {
        echo "✗ Error tabel <strong>$name</strong>: " . mysqli_error($conn) . "<br>";
    }
}

// 4. Clear dan insert user
echo "<h3>👤 Setup User Default...</h3>";
// Disable foreign key untuk bisa delete
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
mysqli_query($conn, "DELETE FROM tb_user");
mysqli_query($conn, "ALTER TABLE tb_user AUTO_INCREMENT = 1");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

$users = [
    ['admin', 'admin123', 'admin'],
    ['petugas', 'petugas123', 'petugas'],
    ['owner', 'owner123', 'owner']
];

foreach ($users as $u) {
    $sql = "INSERT INTO tb_user (username, password, role, status_aktif) VALUES ('{$u[0]}', '{$u[1]}', '{$u[2]}', 1)";
    if (mysqli_query($conn, $sql)) {
        echo "✓ User <strong>{$u[0]}</strong> berhasil dibuat<br>";
    } else {
        echo "✗ Error user <strong>{$u[0]}</strong>: " . mysqli_error($conn) . "<br>";
    }
}

// 5. Setup tarif
echo "<h3>💰 Setup Tarif Default...</h3>";
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
mysqli_query($conn, "DELETE FROM tb_transaksi");
mysqli_query($conn, "DELETE FROM tb_log_aktivitas");
mysqli_query($conn, "DELETE FROM tb_tarif");
mysqli_query($conn, "ALTER TABLE tb_tarif AUTO_INCREMENT = 1");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

$tarifs = [
    ['Motor', 3000, 20000],
    ['Mobil', 5000, 40000],
    ['Truk', 10000, 80000]
];

foreach ($tarifs as $t) {
    $sql = "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam, tarif_per_hari) VALUES ('{$t[0]}', {$t[1]}, {$t[2]})";
    if (mysqli_query($conn, $sql)) {
        echo "✓ Tarif <strong>{$t[0]}</strong> berhasil dibuat<br>";
    } else {
        echo "✗ Error tarif <strong>{$t[0]}</strong>: " . mysqli_error($conn) . "<br>";
    }
}

// 6. Setup area
echo "<h3>📍 Setup Area Default...</h3>";
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
mysqli_query($conn, "DELETE FROM tb_area");
mysqli_query($conn, "ALTER TABLE tb_area AUTO_INCREMENT = 1");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

$areas = [
    ['Area A', 30, 'Depan Restoran'],
    ['Area B', 25, 'Samping Restoran'],
    ['Area C', 40, 'Belakang Restoran']
];

foreach ($areas as $a) {
    $sql = "INSERT INTO tb_area (nama_area, kapasitas, lokasi) VALUES ('{$a[0]}', {$a[1]}, '{$a[2]}')";
    if (mysqli_query($conn, $sql)) {
        echo "✓ Area <strong>{$a[0]}</strong> berhasil dibuat<br>";
    } else {
        echo "✗ Error area <strong>{$a[0]}</strong>: " . mysqli_error($conn) . "<br>";
    }
}

// 7. Verifikasi
echo "<hr><h3>✅ Verifikasi Data</h3>";
$count_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tb_user"));
$count_tarif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tb_tarif"));
$count_area = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM tb_area"));

echo "User terbuat: <strong>" . $count_user['cnt'] . "</strong><br>";
echo "Tarif terbuat: <strong>" . $count_tarif['cnt'] . "</strong><br>";
echo "Area terbuat: <strong>" . $count_area['cnt'] . "</strong><br>";

echo "<hr><h3>🎯 Kredensial Login</h3><style>
.cred { background: #e7f3ff; padding: 12px; border-left: 4px solid #2d6cdf; margin: 10px 0; font-family: monospace; }
</style>";
echo "<div class='cred'><strong>Admin:</strong> username: <code>admin</code> | password: <code>admin123</code></div>";
echo "<div class='cred'><strong>Petugas:</strong> username: <code>petugas</code> | password: <code>petugas123</code></div>";
echo "<div class='cred'><strong>Owner:</strong> username: <code>owner</code> | password: <code>owner123</code></div>";

echo "<hr><h3>✨ Setup berhasil! Silahkan login di halaman berikut:</h3>";
echo "<a href='index.php?url=auth/index' style='background: #2d6cdf; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block;'>
        Buka Halaman Login →
      </a>";

mysqli_close($conn);
?>
