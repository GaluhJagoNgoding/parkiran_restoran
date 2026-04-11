<?php
/**
 * Insert Sample Transaction Data - Simple Version
 */

require_once 'app/config/database.php';

$db = Database::connect();

echo "📝 Membuat Sample Data Transaksi...\n\n";

// Disable FK check temporarily
mysqli_query($db, "SET FOREIGN_KEY_CHECKS=0");

// Clear existing data
mysqli_query($db, "DELETE FROM tb_transaksi");
mysqli_query($db, "ALTER TABLE tb_transaksi AUTO_INCREMENT = 1");

// Make sure we have kendaraan
$check = mysqli_query($db, "SELECT COUNT(*) cnt FROM tb_kendaraan");
$row = mysqli_fetch_assoc($check);

if ($row['cnt'] == 0) {
    echo "➕ Membuat sample kendaraan...\n";
    $kendaraan = [
        "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, status) VALUES ('A001', 'Motor', 'Hitam', 'Budi', 1)",
        "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, status) VALUES ('A002', 'Motor', 'Putih', 'Ani', 1)",
        "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, status) VALUES ('B001', 'Mobil', 'Merah', 'Citra', 1)",
        "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, status) VALUES ('B002', 'Mobil', 'Biru', 'Deni', 1)",
    ];
    
    foreach ($kendaraan as $sql) {
        mysqli_query($db, $sql);
    }
    echo "✓ Sample kendaraan dibuat\n\n";
}

// Insert sample transaksi
$transaksi_sql = [
    "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk, waktu_keluar) 
     VALUES (1, 1, 1, 1, 'selesai', 6000, DATE_SUB(NOW(), INTERVAL 8 HOUR), DATE_SUB(NOW(), INTERVAL 7 HOUR))",
    
    "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk, waktu_keluar) 
     VALUES (2, 1, 1, 1, 'selesai', 7500, DATE_SUB(NOW(), INTERVAL 6 HOUR), DATE_SUB(NOW(), INTERVAL 5 HOUR))",
    
    "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk, waktu_keluar) 
     VALUES (3, 1, 2, 2, 'selesai', 35000, DATE_SUB(NOW(), INTERVAL 5 HOUR), DATE_SUB(NOW(), INTERVAL 4 HOUR))",
    
    "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk, waktu_keluar) 
     VALUES (4, 1, 2, 2, 'selesai', 40000, DATE_SUB(NOW(), INTERVAL 4 HOUR), DATE_SUB(NOW(), INTERVAL 3 HOUR))",
    
    "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk, waktu_keluar) 
     VALUES (1, 1, 1, 1, 'selesai', 6000, DATE_SUB(NOW(), INTERVAL 3 HOUR), DATE_SUB(NOW(), INTERVAL 2 HOUR))",
    
    "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk, waktu_keluar) 
     VALUES (3, 1, 3, 3, 'selesai', 50000, DATE_SUB(NOW(), INTERVAL 2 HOUR), DATE_SUB(NOW(), INTERVAL 1 HOUR))",
    
    "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk, waktu_keluar) 
     VALUES (2, 1, 1, 1, 'selesai', 6000, DATE_SUB(NOW(), INTERVAL 1 HOUR), NOW())",
    
    "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk, waktu_keluar) 
     VALUES (4, 1, 3, 3, 'selesai', 45000, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 23 HOUR))",
];

$inserted = 0;
foreach ($transaksi_sql as $sql) {
    if (mysqli_query($db, $sql)) {
        $inserted++;
    }
}

mysqli_query($db, "SET FOREIGN_KEY_CHECKS=1");

echo "✓ Insert $inserted transaksi sample\n\n";

// Verify data
$q_all = mysqli_query($db, "SELECT COUNT(*) cnt FROM tb_transaksi");
$all = mysqli_fetch_assoc($q_all);

$q_selesai = mysqli_query($db, "SELECT COUNT(*) cnt, SUM(biaya_total) total FROM tb_transaksi WHERE status='selesai'");
$selesai = mysqli_fetch_assoc($q_selesai);

$q_breakdown = mysqli_query($db, "SELECT k.jenis_kendaraan, COUNT(*) cnt, SUM(t.biaya_total) total 
                                  FROM tb_transaksi t 
                                  JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                                  WHERE t.status='selesai'
                                  GROUP BY k.jenis_kendaraan");

echo "📊 STATISTIK DATA:\n";
echo "├─ Total transaksi: " . $all['cnt'] . "\n";
echo "├─ Transaksi selesai: " . $selesai['cnt'] . "\n";
echo "├─ Total pendapatan: Rp " . number_format($selesai['total'], 0, ',', '.') . "\n";
echo "└─ Breakdown jenis:\n";

while ($bd = mysqli_fetch_assoc($q_breakdown)) {
    echo "   • {$bd['jenis_kendaraan']}: {$bd['cnt']} transaksi, Rp " . number_format($bd['total'], 0, ',', '.') . "\n";
}

echo "\n✨ Setup selesai! Sample data siap untuk testing.\n";
echo "\n🎯 Test URLs:\n";
echo "   1. Login Owner: http://localhost/parkiran_restoran/\n";
echo "      User: owner / owner123\n";
echo "   2. Lihat Rekap: http://localhost/parkiran_restoran/index.php?url=transaksi/rekap\n";
echo "   3. Lihat Transaksi (Read-Only): http://localhost/parkiran_restoran/index.php?url=transaksi/index\n";

mysqli_close($db);
?>
