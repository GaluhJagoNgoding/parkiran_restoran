<?php
/**
 * ==========================================================
 * DashboardController - Halaman Utama Setelah Login
 * ==========================================================
 * 
 * Menampilkan dashboard yang berbeda sesuai role pengguna:
 * - Admin   → Statistik lengkap + kelola semua data
 * - Petugas → Statistik operasional + transaksi terbaru
 * - Owner   → Statistik pendapatan + laporan
 * 
 * Routes:
 * - dashboard/index → Halaman dashboard (otomatis sesuai role)
 */

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../config/database.php';

class DashboardController
{
    /**
     * Menampilkan halaman dashboard sesuai role pengguna.
     * 
     * Fungsi:
     * 1. Memastikan user sudah login (Auth::check)
     * 2. Menghitung statistik dari database:
     *    - Total user, kendaraan, transaksi, area
     *    - Kendaraan yang sedang parkir (status 'masuk')
     *    - Transaksi hari ini
     *    - Total pendapatan (seluruh waktu)
     *    - Pendapatan hari ini
     * 3. Mengambil 10 transaksi terbaru
     * 4. Memuat view dashboard sesuai role (admin/petugas/owner)
     * 
     * @return void
     */
    public function index()
    {
        Auth::check();

        $db   = Database::connect();
        $role = $_SESSION['user']['role'] ?? 'admin';

        // ─── Statistik Umum ──────────────────────────────────

        // Total user terdaftar
        $user_result = mysqli_query($db, "SELECT COUNT(*) total FROM tb_user");
        $user = $user_result ? mysqli_fetch_assoc($user_result) : ['total' => 0];

        // Total kendaraan terdaftar
        $kendaraan_result = mysqli_query($db, "SELECT COUNT(*) total FROM tb_kendaraan");
        $kendaraan = $kendaraan_result ? mysqli_fetch_assoc($kendaraan_result) : ['total' => 0];

        // Total semua transaksi
        $transaksi_result = mysqli_query($db, "SELECT COUNT(*) total FROM tb_transaksi");
        $transaksi = $transaksi_result ? mysqli_fetch_assoc($transaksi_result) : ['total' => 0];

        // Total area parkir
        $area_result = mysqli_query($db, "SELECT COUNT(*) total FROM tb_area");
        $area = $area_result ? mysqli_fetch_assoc($area_result) : ['total' => 0];

        // ─── Statistik Real-time ────────────────────────────

        // Jumlah kendaraan yang sedang parkir saat ini
        $kendaraan_aktif_result = mysqli_query($db, "SELECT COUNT(*) aktif FROM tb_transaksi WHERE status='masuk'");
        $kendaraan['aktif'] = $kendaraan_aktif_result ? mysqli_fetch_assoc($kendaraan_aktif_result)['aktif'] : 0;

        // Jumlah transaksi masuk hari ini
        $transaksi_hari_ini_result = mysqli_query($db, "SELECT COUNT(*) hari_ini FROM tb_transaksi WHERE DATE(waktu_masuk) = CURDATE()");
        $transaksi['hari_ini'] = $transaksi_hari_ini_result ? mysqli_fetch_assoc($transaksi_hari_ini_result)['hari_ini'] : 0;

        // ─── Statistik Pendapatan ───────────────────────────

        // Total pendapatan keseluruhan (hanya transaksi 'selesai')
        $pendapatan_result = mysqli_query($db, "SELECT SUM(biaya_total) total_pendapatan FROM tb_transaksi WHERE status='selesai'");
        $transaksi['total_pendapatan'] = $pendapatan_result ? (mysqli_fetch_assoc($pendapatan_result)['total_pendapatan'] ?? 0) : 0;

        // Pendapatan hari ini (berdasarkan waktu keluar/bayar)
        $pendapatan_hari_ini_result = mysqli_query($db, "SELECT SUM(biaya_total) pendapatan_hari_ini FROM tb_transaksi WHERE DATE(waktu_keluar) = CURDATE() AND status = 'selesai'");
        $pendapatan_val = $pendapatan_hari_ini_result ? (mysqli_fetch_assoc($pendapatan_hari_ini_result)['pendapatan_hari_ini'] ?? 0) : 0;

        $transaksi['pendapatan_hari_ini'] = $pendapatan_val;
        $transaksi['pendapatan']          = $pendapatan_val;

        // ─── Transaksi Terbaru ──────────────────────────────

        // Ambil 10 transaksi terbaru untuk tabel di dashboard
        $recent_sql = "SELECT t.id_parkir, t.waktu_masuk, t.waktu_keluar, t.biaya_total, t.status, 
                              k.plat_nomor, k.jenis_kendaraan, a.nama_area
                       FROM tb_transaksi t
                       LEFT JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan
                       LEFT JOIN tb_area a ON t.id_area = a.id_area
                       ORDER BY t.waktu_masuk DESC LIMIT 10";
        $recent_transactions = mysqli_query($db, $recent_sql);

        // ─── Load View Sesuai Role ──────────────────────────

        if ($role === 'admin') {
            require_once __DIR__ . '/../views/dashboard/admin.php';
        } elseif ($role === 'petugas') {
            require_once __DIR__ . '/../views/dashboard/petugas.php';
        } elseif ($role === 'owner') {
            require_once __DIR__ . '/../views/dashboard/owner.php';
        } else {
            require_once __DIR__ . '/../views/dashboard/admin.php';
        }
    }
}
