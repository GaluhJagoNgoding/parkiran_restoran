<?php
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../config/database.php';

class DashboardController
{
    public function index()
    {
        Auth::check();

        $db = Database::connect();
        $role = $_SESSION['user']['role'] ?? 'admin';

        // hitung data umum (statistik)
        $user_result = mysqli_query($db, "SELECT COUNT(*) total FROM tb_user");
        $user = $user_result ? mysqli_fetch_assoc($user_result) : ['total' => 0];

        $kendaraan_result = mysqli_query($db, "SELECT COUNT(*) total FROM tb_kendaraan");
        $kendaraan = $kendaraan_result ? mysqli_fetch_assoc($kendaraan_result) : ['total' => 0];

        $transaksi_result = mysqli_query($db, "SELECT COUNT(*) total FROM tb_transaksi");
        $transaksi = $transaksi_result ? mysqli_fetch_assoc($transaksi_result) : ['total' => 0];

        // statistik tambahan untuk dashboard
        $kendaraan_aktif_result = mysqli_query($db, "SELECT COUNT(*) aktif FROM tb_transaksi WHERE status='masuk'");
        $kendaraan['aktif'] = $kendaraan_aktif_result ? mysqli_fetch_assoc($kendaraan_aktif_result)['aktif'] : 0;

        $area_result = mysqli_query($db, "SELECT COUNT(*) total FROM tb_area");
        $area = $area_result ? mysqli_fetch_assoc($area_result) : ['total' => 0];

        // statistik transaksi hari ini
        $transaksi_hari_ini_result = mysqli_query($db, "SELECT COUNT(*) hari_ini FROM tb_transaksi WHERE DATE(waktu_masuk) = CURDATE()");
        $transaksi['hari_ini'] = $transaksi_hari_ini_result ? mysqli_fetch_assoc($transaksi_hari_ini_result)['hari_ini'] : 0;

        // total & pendapatan transaksi
        // total & pendapatan transaksi (hitung yang sudah selesai saja)
        $pendapatan_result = mysqli_query($db, "SELECT SUM(biaya_total) total_pendapatan FROM tb_transaksi WHERE status='selesai'");
        $transaksi['total_pendapatan'] = $pendapatan_result ? (mysqli_fetch_assoc($pendapatan_result)['total_pendapatan'] ?? 0) : 0;

        // Pendapatan HARI INI (berdasarkan waktu keluar/bayar)
        $pendapatan_hari_ini_result = mysqli_query($db, "SELECT SUM(biaya_total) pendapatan_hari_ini FROM tb_transaksi WHERE DATE(waktu_keluar) = CURDATE() AND status = 'selesai'");
        $pendapatan_val = $pendapatan_hari_ini_result ? (mysqli_fetch_assoc($pendapatan_hari_ini_result)['pendapatan_hari_ini'] ?? 0) : 0;

        $transaksi['pendapatan_hari_ini'] = $pendapatan_val;
        $transaksi['pendapatan'] = $pendapatan_val; // Pastikan key ini ada karena dipakai di view

        // Ambil transaksi terbaru (dipakai di dashboard owner/petugas)
        $recent_sql = "SELECT t.id_parkir, t.waktu_masuk, t.waktu_keluar, t.biaya_total, t.status, k.plat_nomor, k.jenis_kendaraan, a.nama_area
                       FROM tb_transaksi t
                       LEFT JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                       LEFT JOIN tb_area a ON t.id_area = a.id_area
                       ORDER BY t.waktu_masuk DESC LIMIT 10";
        $recent_transactions = mysqli_query($db, $recent_sql);

        // Load dashboard sesuai role
        if ($role === 'admin') {
            require_once __DIR__ . '/../views/dashboard/admin.php';
        }
        elseif ($role === 'petugas') {
            require_once __DIR__ . '/../views/dashboard/petugas.php';
        }
        elseif ($role === 'owner') {
            require_once __DIR__ . '/../views/dashboard/owner.php';
        }
        else {
            require_once __DIR__ . '/../views/dashboard/admin.php';
        }
    }
}
