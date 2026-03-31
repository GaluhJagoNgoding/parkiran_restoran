<?php
require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class TransaksiController
{
    public function index()
    {
        Auth::check();

        // Support search & filter (#9)
        $search = trim($_GET['search'] ?? '');
        $tanggal_dari = $_GET['tanggal_dari'] ?? '';
        $tanggal_sampai = $_GET['tanggal_sampai'] ?? '';
        $filter_status = $_GET['status'] ?? '';

        $data = Transaksi::allFiltered($search, $tanggal_dari, $tanggal_sampai, $filter_status);
        require_once __DIR__ . '/../views/transaksi/index.php';
    }

    public function create()
    {
        Auth::check();
        // Ambil data kendaraan untuk dropdown
        require_once __DIR__ . '/../models/Kendaraan.php';
        $kendaraan = Kendaraan::all();

        require_once __DIR__ . '/../models/Area.php';
        $areas = Area::all();

        // Ambil info kapasitas real-time (#7)
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

    public function store()
    {
        Auth::check();
        Csrf::validate(); // Fix #3: CSRF protection

        $id_kendaraan = $_POST['id_kendaraan'] ?? 0;
        $id_area = $_POST['id_area'] ?? 0;

        if (!$id_kendaraan || !$id_area) {
            Flash::set('error', 'Data tidak lengkap. Pilih kendaraan dan area.');
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        // Cari id_tarif berdasarkan jenis_kendaraan di tb_kendaraan
        require_once __DIR__ . '/../models/Kendaraan.php';
        $k = Kendaraan::find($id_kendaraan);

        if (!$k) {
            Flash::set('error', 'Kendaraan tidak ditemukan.');
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        // Fix #7: Cek kapasitas area sebelum transaksi baru
        require_once __DIR__ . '/../models/Area.php';
        $area = Area::find($id_area);
        if (!$area) {
            Flash::set('error', 'Area parkir tidak ditemukan.');
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        $db = Database::connect();
        $id_area_int = intval($id_area);
        $count_result = mysqli_query($db, "SELECT COUNT(*) as terpakai FROM tb_transaksi WHERE id_area=$id_area_int AND status='masuk'");
        $count_row = $count_result ? mysqli_fetch_assoc($count_result) : ['terpakai' => 0];

        if ($count_row['terpakai'] >= $area['kapasitas']) {
            Flash::set('error', 'Area "' . htmlspecialchars($area['nama_area']) . '" sudah penuh! (Kapasitas: ' . $area['kapasitas'] . ', Terisi: ' . $count_row['terpakai'] . ')');
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        $jenis = $k['jenis_kendaraan'] ?? 'lainnya';

        require_once __DIR__ . '/../models/Tarif.php';
        // Fix #2: SQL Injection — pakai escape string
        $jenis_escaped = mysqli_real_escape_string($db, $jenis);
        $q_t = mysqli_query($db, "SELECT id_tarif FROM tb_tarif WHERE jenis_kendaraan = '$jenis_escaped'");

        if ($q_t && mysqli_num_rows($q_t) > 0) {
            $t = mysqli_fetch_assoc($q_t);
            $id_tarif = $t['id_tarif'];
        }
        else {
            Flash::set('error', "Tarif untuk jenis kendaraan '$jenis' belum disetting admin.");
            header("Location: index.php?url=transaksi/create");
            exit;
        }

        $id_user = $_SESSION['user']['id_user'] ?? 0;

        $ok = Transaksi::create($id_kendaraan, $id_user, $id_area, $id_tarif);
        if ($ok) {
            Flash::set('success', 'Transaksi parkir berhasil dibuat.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'create_transaksi', json_encode(['id_kendaraan' => $id_kendaraan]));
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal membuat transaksi: ' . mysqli_error($db));
        }
        header("Location: index.php?url=transaksi/index");
        exit;
    }

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

        // Ambil informasi tarif berdasarkan id_tarif
        require_once __DIR__ . '/../models/Tarif.php';
        $tarif = Tarif::find($transaksi['id_tarif']);

        require_once __DIR__ . '/../views/transaksi/edit.php';
    }

    public function update()
    {
        Auth::check();
        Csrf::validate(); // Fix #3: CSRF protection

        $id = $_POST['id'] ?? 0;
        $biaya_total = intval($_POST['biaya_total'] ?? 0);
        $status = trim($_POST['status'] ?? 'selesai');
        $status = strtolower($status);

        if (!$id) {
            Flash::set('error', 'ID transaksi tidak valid.');
            header("Location: index.php?url=transaksi/index");
            exit;
        }

        $ok = Transaksi::update($id, $biaya_total, $status);
        if ($ok) {
            Flash::set('success', 'Transaksi berhasil diperbarui. Status: ' . ucfirst($status));
            Log::create($_SESSION['user']['id_user'] ?? null, 'update_transaksi', json_encode(['id' => $id, 'status' => $status, 'biaya' => $biaya_total]));
        }
        else {
            require_once __DIR__ . '/../config/database.php';
            $db = Database::connect();
            Flash::set('error', 'Gagal memperbarui transaksi: ' . mysqli_error($db));
        }
        header("Location: index.php?url=transaksi/index");
        exit;
    }

    public function delete()
    {
        Auth::check();
        Csrf::validate(); // Fix #3: CSRF protection

        $id = $_POST['id'] ?? 0;
        $ok = Transaksi::delete($id);
        if ($ok)
            Flash::set('success', 'Transaksi berhasil dihapus.');
        else
            Flash::set('error', 'Gagal menghapus transaksi.');
        header("Location: index.php?url=transaksi/index");
        exit;
    }

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

        // Auto-update status to selesai when receipt is viewed
        if (strtolower($transaksi['status']) !== 'selesai') {
            Transaksi::updateStatus($id, 'selesai');
            // Refresh transaksi data to get updated status
            $transaksi = Transaksi::find($id);
        }

        require_once __DIR__ . '/../views/transaksi/struk.php';
    }
}
