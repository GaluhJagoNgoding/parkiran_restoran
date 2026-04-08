<?php
/**
 * ==========================================================
 * TransaksiController - CRUD Transaksi Parkir
 * ==========================================================
 * 
 * Menangani operasi transaksi parkir mulai dari kendaraan
 * masuk hingga keluar dan cetak struk.
 * 
 * Routes:
 * - transaksi/index  → Daftar semua transaksi (dengan filter & search)
 * - transaksi/create → Form input kendaraan masuk
 * - transaksi/store  → Proses simpan transaksi baru (POST)
 * - transaksi/edit   → Form edit transaksi/checkout
 * - transaksi/update → Proses update transaksi (POST)
 * - transaksi/delete → Proses hapus transaksi (POST)
 * - transaksi/struk  → Cetak struk/kwitansi parkir
 * 
 * Akses: Semua user yang sudah login
 */

require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class TransaksiController
{
    /**
     * Menampilkan daftar semua transaksi dengan fitur search & filter.
     * 
     * Fungsi:
     * - Mendukung pencarian berdasarkan plat nomor/jenis kendaraan
     * - Mendukung filter tanggal (dari - sampai)
     * - Mendukung filter status (masuk/selesai)
     * 
     * @return void
     */
    public function index()
    {
        Auth::check();

        // Ambil parameter search & filter dari URL
        $search         = trim($_GET['search'] ?? '');
        $tanggal_dari   = $_GET['tanggal_dari'] ?? '';
        $tanggal_sampai = $_GET['tanggal_sampai'] ?? '';
        $filter_status  = $_GET['status'] ?? '';

        $data = Transaksi::allFiltered($search, $tanggal_dari, $tanggal_sampai, $filter_status);
        require_once __DIR__ . '/../views/transaksi/index.php';
    }

    /**
     * Menampilkan form untuk mencatat kendaraan masuk parkir.
     * 
     * Fungsi:
     * - Mengambil daftar kendaraan terdaftar untuk dropdown
     * - Mengambil daftar area parkir beserta info kapasitas
     * - Menghitung penggunaan area saat ini (real-time)
     *   untuk menampilkan sisa kapasitas di form
     * 
     * @return void
     */
    public function create()
    {
        Auth::check();

        // Ambil data kendaraan untuk dropdown
        require_once __DIR__ . '/../models/Kendaraan.php';
        $kendaraan = Kendaraan::all();

        // Ambil data area parkir untuk dropdown
        require_once __DIR__ . '/../models/Area.php';
        $areas = Area::all();

        // Hitung penggunaan area saat ini (berapa slot terpakai)
        $db = Database::connect();
        $area_usage = [];
        $usage_query = mysqli_query($db, "SELECT id_area, COUNT(*) as terpakai FROM tb_transaksi WHERE status='masuk' GROUP BY id_area");
        if ($usage_query) {
            while ($row = mysqli_fetch_assoc($usage_query)) {
                $area_usage[$row['id_area']] = $row['terpakai'];
            }
        }

        require_once __DIR__ . '/../views/transaksi/create.php';
    }

    /**
     * Menyimpan transaksi parkir baru (kendaraan masuk).
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Validasi input (kendaraan & area harus dipilih)
     * 3. Cari data kendaraan → ambil jenis_kendaraan
     * 4. Cek kapasitas area (apakah masih ada slot kosong)
     * 5. Cari tarif berdasarkan jenis kendaraan
     * 6. Simpan transaksi baru ke database
     * 7. Catat log aktivitas
     * 
     * @return void
     */
    public function store()
    {
        Auth::check();
        Csrf::validate();

        $id_kendaraan = $_POST['id_kendaraan'] ?? 0;
        $id_area      = $_POST['id_area'] ?? 0;

        // Validasi: kendaraan dan area harus dipilih
        if (!$id_kendaraan || !$id_area) {
            Flash::set('error', 'Data tidak lengkap. Pilih kendaraan dan area.');
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        // Cari data kendaraan untuk mendapat jenis_kendaraan
        require_once __DIR__ . '/../models/Kendaraan.php';
        $k = Kendaraan::find($id_kendaraan);
        if (!$k) {
            Flash::set('error', 'Kendaraan tidak ditemukan.');
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        // Cek kapasitas area parkir
        require_once __DIR__ . '/../models/Area.php';
        $area = Area::find($id_area);
        if (!$area) {
            Flash::set('error', 'Area parkir tidak ditemukan.');
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        // Hitung jumlah kendaraan yang sedang parkir di area ini
        $db = Database::connect();
        $id_area_int = intval($id_area);
        $count_result = mysqli_query($db, "SELECT COUNT(*) as terpakai FROM tb_transaksi WHERE id_area=$id_area_int AND status='masuk'");
        $count_row = $count_result ? mysqli_fetch_assoc($count_result) : ['terpakai' => 0];

        // Tolak jika area sudah penuh
        if ($count_row['terpakai'] >= $area['kapasitas']) {
            Flash::set('error', 'Area "' . htmlspecialchars($area['nama_area']) . '" sudah penuh! (Kapasitas: ' . $area['kapasitas'] . ', Terisi: ' . $count_row['terpakai'] . ')');
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        // Cari tarif berdasarkan jenis kendaraan
        $jenis = $k['jenis_kendaraan'] ?? 'lainnya';
        require_once __DIR__ . '/../models/Tarif.php';
        $jenis_escaped = mysqli_real_escape_string($db, $jenis);
        $q_t = mysqli_query($db, "SELECT id_tarif FROM tb_tarif WHERE jenis_kendaraan = '$jenis_escaped'");

        if ($q_t && mysqli_num_rows($q_t) > 0) {
            $t = mysqli_fetch_assoc($q_t);
            $id_tarif = $t['id_tarif'];
        } else {
            Flash::set('error', "Tarif untuk jenis kendaraan '$jenis' belum disetting admin.");
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        // Simpan transaksi baru
        $id_user = $_SESSION['user']['id_user'] ?? 0;
        $ok = Transaksi::create($id_kendaraan, $id_user, $id_area, $id_tarif);

        if ($ok) {
            Flash::set('success', 'Transaksi parkir berhasil dibuat.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'create_transaksi', json_encode(['id_kendaraan' => $id_kendaraan]));
        } else {
            $db = Database::connect();
            Flash::set('error', 'Gagal membuat transaksi: ' . mysqli_error($db));
        }

        header("Location: index.php?url=transaksi/index");
        exit;
    }

    /**
     * Menampilkan form edit/checkout transaksi.
     * 
     * Fungsi:
     * - Mengambil detail transaksi berdasarkan ID
     * - Mengambil info tarif untuk menghitung biaya
     * - Menampilkan form untuk input biaya dan ubah status
     * 
     * @return void
     */
    public function edit()
    {
        Auth::check();

        $id = $_GET['id'] ?? 0;
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            Flash::set('error', 'Transaksi tidak ditemukan.');
            header("Location: index.php?url=transaksi/index");
            exit;
        }

        // Ambil informasi tarif untuk perhitungan biaya
        require_once __DIR__ . '/../models/Tarif.php';
        $tarif = Tarif::find($transaksi['id_tarif']);

        require_once __DIR__ . '/../views/transaksi/edit.php';
    }

    /**
     * Memproses update transaksi (checkout/pembayaran).
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Ambil ID, biaya_total, dan status dari form
     * 3. Update transaksi (set waktu_keluar, biaya, status)
     * 4. Catat log aktivitas
     * 
     * @return void
     */
    public function update()
    {
        Auth::check();
        Csrf::validate();

        $id          = $_POST['id'] ?? 0;
        $biaya_total = intval($_POST['biaya_total'] ?? 0);
        $status      = strtolower(trim($_POST['status'] ?? 'selesai'));

        if (!$id) {
            Flash::set('error', 'ID transaksi tidak valid.');
            header("Location: index.php?url=transaksi/index");
            exit;
        }

        $ok = Transaksi::update($id, $biaya_total, $status);

        if ($ok) {
            Flash::set('success', 'Transaksi berhasil diperbarui. Status: ' . ucfirst($status));
            Log::create($_SESSION['user']['id_user'] ?? null, 'update_transaksi', json_encode([
                'id'    => $id,
                'status' => $status,
                'biaya' => $biaya_total
            ]));
        } else {
            $db = Database::connect();
            Flash::set('error', 'Gagal memperbarui transaksi: ' . mysqli_error($db));
        }

        header("Location: index.php?url=transaksi/index");
        exit;
    }

    /**
     * Menghapus transaksi parkir.
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Hapus transaksi berdasarkan ID dari POST
     * 3. Tampilkan pesan sukses/gagal
     * 
     * @return void
     */
    public function delete()
    {
        Auth::check();
        Csrf::validate();

        $id = $_POST['id'] ?? 0;
        $ok = Transaksi::delete($id);

        if ($ok) {
            Flash::set('success', 'Transaksi berhasil dihapus.');
        } else {
            Flash::set('error', 'Gagal menghapus transaksi.');
        }

        header("Location: index.php?url=transaksi/index");
        exit;
    }

    /**
     * Menampilkan struk/kwitansi transaksi parkir.
     * 
     * Fungsi:
     * - Mengambil detail transaksi lengkap
     * - Otomatis mengubah status menjadi 'selesai' saat struk dilihat
     * - Menampilkan halaman struk yang bisa di-print
     * 
     * @return void
     */
    public function struk()
    {
        Auth::check();

        $id = $_GET['id'] ?? 0;
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            Flash::set('error', 'Transaksi tidak ditemukan.');
            header("Location: index.php?url=transaksi/index");
            exit;
        }

        // Otomatis set status 'selesai' saat struk dibuka
        if (strtolower($transaksi['status']) !== 'selesai') {
            Transaksi::updateStatus($id, 'selesai');
            $transaksi = Transaksi::find($id);
        }

        require_once __DIR__ . '/../views/transaksi/struk.php';
    }
}
