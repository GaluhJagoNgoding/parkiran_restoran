<?php
/**
 * Setup Database - Jalankan sekali untuk membuat tabel
 */

// Memuat konfigurasi database
require_once 'app/config/database.php';

// Menghubungkan ke database
$db = Database::connect();

echo "<h2>🔧 Setup Sistem Parkir Restoran</h2>";
echo "<hr>";

// Buat tabel tb_user
$create_user = "CREATE TABLE IF NOT EXISTS tb_user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    role ENUM('admin', 'petugas', 'owner') NOT NULL DEFAULT 'petugas',
    status_aktif TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// Buat tabel tb_kendaraan dengan kolom yang benar
$create_kendaraan = "CREATE TABLE IF NOT EXISTS tb_kendaraan (
    id_kendaraan INT AUTO_INCREMENT PRIMARY KEY,
    plat_nomor VARCHAR(20) UNIQUE NOT NULL,
    jenis_kendaraan VARCHAR(50) NOT NULL,
    warna VARCHAR(30) NOT NULL,
    pemilik VARCHAR(100) NOT NULL,
    status INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// Buat tabel tb_transaksi
$create_transaksi = "CREATE TABLE IF NOT EXISTS tb_transaksi (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// Buat tabel tb_tarif
$create_tarif = "CREATE TABLE IF NOT EXISTS tb_tarif (
    id_tarif INT AUTO_INCREMENT PRIMARY KEY,
    jenis_kendaraan VARCHAR(50) NOT NULL,
    tarif_per_jam INT NOT NULL,
    tarif_per_hari INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// Buat tabel tb_area
$create_area = "CREATE TABLE IF NOT EXISTS tb_area (
    id_area INT AUTO_INCREMENT PRIMARY KEY,
    nama_area VARCHAR(100) NOT NULL,
    kapasitas INT NOT NULL,
    lokasi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// Buat tabel tb_log_aktivitas
$create_log = "CREATE TABLE IF NOT EXISTS tb_log_aktivitas (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT DEFAULT NULL,
    aktivitas VARCHAR(255) NOT NULL,
    waktu_aktivitas TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES tb_user(id_user) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

// Daftar tabel dan query pembuatannya
// Execute table creation
$tables = [
    'tb_user' => $create_user,
    'tb_kendaraan' => $create_kendaraan,
    'tb_tarif' => $create_tarif,
    'tb_area' => $create_area,
    'tb_transaksi' => $create_transaksi,
    'tb_log_aktivitas' => $create_log
];

// Loop untuk mengeksekusi query pembuatan tabel satu per satu
foreach ($tables as $name => $sql) {
    if (mysqli_query($db, $sql)) {
        echo "✓ Tabel <strong>$name</strong> berhasil dibuat<br>";
    }
    else {
        echo "✗ Error membuat <strong>$name</strong>: " . mysqli_error($db) . "<br>";
    }
}

echo "<hr>";

// Cek apakah user sudah ada untuk menghindari duplikasi data sample
// Insert sample user
$check_user = mysqli_query($db, "SELECT COUNT(*) as cnt FROM tb_user");
$count = mysqli_fetch_assoc($check_user);

// Jika tabel user kosong, tambahkan data default (Admin, Petugas, Owner)
if ($count['cnt'] == 0) {
    echo "<h3>📝 Menambah Sample User...</h3>";
    $insert_users = [
        "INSERT INTO tb_user (username, password, role, status_aktif) VALUES ('admin', 'admin123', 'admin', 1)",
        "INSERT INTO tb_user (username, password, role, status_aktif) VALUES ('petugas', 'petugas123', 'petugas', 1)",
        "INSERT INTO tb_user (username, password, role, status_aktif) VALUES ('owner', 'owner123', 'owner', 1)"
    ];

    foreach ($insert_users as $sql) {
        if (mysqli_query($db, $sql)) {
            echo "✓ User sample berhasil ditambahkan<br>";
        }
    }
}
else {
    echo "ℹ️ User sudah ada di database<br>";
}

echo "<hr>";

// Cek apakah data tarif sudah ada
// Insert sample tarif
$check_tarif = mysqli_query($db, "SELECT COUNT(*) as cnt FROM tb_tarif");
$count_tarif = mysqli_fetch_assoc($check_tarif);

// Jika kosong, tambahkan tarif dasar untuk Motor, Mobil, dan Truk
if ($count_tarif['cnt'] == 0) {
    echo "<h3>💰 Menambah Sample Tarif...</h3>";
    $insert_tarif = [
        "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam, tarif_per_hari) VALUES ('Motor', 3000, 20000)",
        "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam, tarif_per_hari) VALUES ('Mobil', 5000, 40000)",
        "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam, tarif_per_hari) VALUES ('Truk', 10000, 80000)"
    ];

    foreach ($insert_tarif as $sql) {
        if (mysqli_query($db, $sql)) {
            echo "✓ Tarif sample berhasil ditambahkan<br>";
        }
    }
}
else {
    echo "ℹ️ Tarif sudah ada di database<br>";
}

echo "<hr>";

// Cek apakah data area parkir sudah ada
// Insert sample area
$check_area = mysqli_query($db, "SELECT COUNT(*) as cnt FROM tb_area");
$count_area = mysqli_fetch_assoc($check_area);

// Jika kosong, tambahkan 3 area sample
if ($count_area['cnt'] == 0) {
    echo "<h3>📍 Menambah Sample Area...</h3>";
    $insert_area = [
        "INSERT INTO tb_area (nama_area, kapasitas, lokasi) VALUES ('Area A', 30, 'Depan Restoran')",
        "INSERT INTO tb_area (nama_area, kapasitas, lokasi) VALUES ('Area B', 25, 'Samping Restoran')",
        "INSERT INTO tb_area (nama_area, kapasitas, lokasi) VALUES ('Area C', 40, 'Belakang Restoran')"
    ];

    foreach ($insert_area as $sql) {
        if (mysqli_query($db, $sql)) {
            echo "✓ Area sample berhasil ditambahkan<br>";
        }
    }
}
else {
    echo "ℹ️ Area sudah ada di database<br>";
}

echo "<hr>";

// Mencatat log pertama kali sistem di-setup
// Tambah sample log jika kosong
$check_log = mysqli_query($db, "SELECT COUNT(*) as cnt FROM tb_log_aktivitas");
if ($check_log) {
    $count_log = mysqli_fetch_assoc($check_log);
    if ($count_log['cnt'] == 0) {
        echo "<h3>📝 Menambah Sample Log...</h3>";
        $insert_log = "INSERT INTO tb_log_aktivitas (id_user, aktivitas) VALUES (1, 'Setup script dijalankan, data sample ditambahkan')";
        if (mysqli_query($db, $insert_log))
            echo "✓ Sample log berhasil ditambahkan<br>";
    }
}

// Menampilkan informasi login default untuk pengguna
echo "<h2>✅ Setup Selesai!</h2>";
echo "<p style='font-size: 16px; color: #27ae60; font-weight: bold;'>Silakan login dengan credential berikut:</p>";
echo "<table border='1' cellpadding='10' style='margin: 20px 0;'>";
echo "<tr><th>Role</th><th>Username</th><th>Password</th></tr>";
echo "<tr><td>Admin</td><td>admin</td><td>admin123</td></tr>";
echo "<tr><td>Petugas</td><td>petugas</td><td>petugas123</td></tr>";
echo "<tr><td>Owner</td><td>owner</td><td>owner123</td></tr>";
echo "</table>";
echo "<p style='margin-top: 30px;'><a href='public/index.php?url=auth/index' style='background-color: #2980b9; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🔐 Ke Halaman Login</a></p>";
?>