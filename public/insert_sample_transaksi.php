<?php
/**
 * Insert Sample Transaction Data untuk testing
 */

require_once 'app/config/database.php';

$db = Database::connect();

echo "📝 Insert Sample Transaksi...\n";

// Cek kendaraan yang ada
$q_kendaraan = mysqli_query($db, "SELECT id_kendaraan FROM tb_kendaraan LIMIT 1");

if (!$q_kendaraan || mysqli_num_rows($q_kendaraan) == 0) {
    echo "⚠️ Tidak ada kendaraan, membuat sample kendaraan dulu...\n";
    
    // Insert sample kendaraan
    $sample_kendaraan = [
        "('A001', 'Motor', 'Hitam', 'Budi', 1)",
        "('A002', 'Motor', 'Putih', 'Ani', 1)",
        "('B001', 'Mobil', 'Merah', 'Citra', 1)",
    ];
    
    foreach ($sample_kendaraan as $kend) {
        $sql = "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik, status) VALUES $kend";
        if (mysqli_query($db, $sql)) {
            echo "✓ Kendaraan ditambahkan\n";
        }
    }
}

echo "
    $kendaraan_list = [];
    while ($k = mysqli_fetch_assoc($q_kendaraan)) {
        $kendaraan_list[] = $k;
    }

    // Delete transaksi lama untuk fresh data
    mysqli_query($db, "DELETE FROM tb_transaksi");
    mysqli_query($db, "ALTER TABLE tb_transaksi AUTO_INCREMENT = 1");

    // Insert 10 transaksi sample dengan berbagai status
    $transaksi_data = [
        [1, 1, 1, 1, 'selesai', 6000, 5], // Motor
        [2, 1, 2, 1, 'selesai', 8000, 4],
        [3, 1, 1, 2, 'selesai', 10000, 3],
        [4, 1, 2, 2, 'selesai', 12000, 2],
        [5, 1, 3, 3, 'selesai', 15000, 1],
        [1, 2, 1, 1, 'selesai', 35000, 5], // Mobil
        [2, 2, 2, 1, 'selesai', 40000, 4],
        [3, 2, 1, 2, 'selesai', 45000, 3],
        [4, 2, 3, 3, 'masuk', 0, -1], // Masih parkir
        [5, 1, 2, 1, 'selesai', 6000, 2], // Motor lagi
    ];

    $inserted = 0;
    foreach ($transaksi_data as $data) {
        list($id_kend, $id_user, $id_area, $id_tarif, $status, $biaya, $jam_lalu) = $data;
        
        if ($status === 'selesai') {
            $insert_sql = "INSERT INTO tb_transaksi 
                          (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk, waktu_keluar) 
                          VALUES ($id_kend, $id_user, $id_area, $id_tarif, '$status', $biaya, 
                                  DATE_SUB(NOW(), INTERVAL $jam_lalu HOUR), 
                                  DATE_SUB(NOW(), INTERVAL " . ($jam_lalu - 1) . " HOUR))";
        } else {
            $insert_sql = "INSERT INTO tb_transaksi 
                          (id_kendaraan, id_user, id_area, id_tarif, status, biaya_total, waktu_masuk) 
                          VALUES ($id_kend, $id_user, $id_area, $id_tarif, '$status', $biaya, 
                                  DATE_SUB(NOW(), INTERVAL $jam_lalu HOUR))";
        }

        if (mysqli_query($db, $insert_sql)) {
            $inserted++;
        }
    }

    echo "✓ Berhasil insert $inserted transaksi sample\n";

    // Verify
    $q_verify = mysqli_query($db, "SELECT COUNT(*) cnt FROM tb_transaksi");
    $verify = mysqli_fetch_assoc($q_verify);
    echo "✓ Total transaksi sekarang: " . $verify['cnt'] . "\n";

    $q_selesai = mysqli_query($db, "SELECT COUNT(*) cnt, SUM(biaya_total) total FROM tb_transaksi WHERE status='selesai'");
    $selesai = mysqli_fetch_assoc($q_selesai);
    echo "✓ Transaksi selesai: " . $selesai['cnt'] . "\n";
    echo "✓ Total pendapatan: Rp " . number_format($selesai['total'], 0, ',', '.') . "\n";

    echo "\n✨ Sample data berhasil ditambahkan!\n";
    echo "Silahkan akses: http://localhost/parkiran_restoran/index.php?url=transaksi/rekap\n";

} else {
    echo "✗ Error: Tidak ada kendaraan di database\n";
}

mysqli_close($db);
?>
