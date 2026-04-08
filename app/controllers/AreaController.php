<?php
/**
 * ==========================================================
 * AreaController - CRUD Pengelolaan Area Parkir
 * ==========================================================
 * 
 * Menangani pengelolaan area/zona parkir di restoran
 * (contoh: Area A - Depan Restoran, Area B - Samping, dll).
 * 
 * Routes:
 * - area/index  → Daftar semua area parkir
 * - area/create → Form tambah area baru
 * - area/store  → Proses simpan area (POST)
 * - area/edit   → Form edit area
 * - area/update → Proses update area (POST)
 * - area/delete → Proses hapus area (POST)
 * 
 * Akses: Khusus Admin saja
 */

require_once __DIR__ . '/../models/Area.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class AreaController
{
    /**
     * Menampilkan daftar semua area parkir.
     * 
     * @return void
     */
    public function index()
    {
        Auth::admin();
        $data = Area::all();
        require_once __DIR__ . '/../views/area/index.php';
    }

    /**
     * Menampilkan form tambah area baru.
     * 
     * @return void
     */
    public function create()
    {
        Auth::admin();
        require_once __DIR__ . '/../views/area/create.php';
    }

    /**
     * Menyimpan area parkir baru ke database.
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Ambil data dari form (nama_area, kapasitas, lokasi)
     * 3. Validasi: nama wajib diisi dan kapasitas harus > 0
     * 4. Simpan via Area::create()
     * 5. Catat log aktivitas
     * 
     * @return void
     */
    public function store()
    {
        Auth::admin();
        Csrf::validate();

        $nama_area = trim($_POST['nama_area'] ?? '');
        $kapasitas = intval($_POST['kapasitas'] ?? 0);
        $lokasi    = trim($_POST['lokasi'] ?? '');

        // Validasi: nama wajib dan kapasitas harus positif
        if (empty($nama_area) || $kapasitas <= 0) {
            Flash::set('error', 'Data tidak lengkap atau kapasitas harus lebih dari 0.');
            header("Location: index.php?url=area/create");
            exit;
        }

        $ok = Area::create($nama_area, $kapasitas, $lokasi);

        if ($ok) {
            Flash::set('success', 'Area berhasil ditambahkan.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'create_area', json_encode([
                'nama'      => $nama_area,
                'kapasitas' => $kapasitas
            ]));
        } else {
            $db = Database::connect();
            Flash::set('error', 'Gagal menambahkan area: ' . mysqli_error($db));
        }

        header("Location: index.php?url=area/index");
        exit;
    }

    /**
     * Menampilkan form edit area parkir.
     * 
     * Fungsi:
     * - Mengambil data area berdasarkan ID dari parameter GET
     * - Jika area tidak ditemukan, redirect dengan pesan error
     * 
     * @return void
     */
    public function edit()
    {
        Auth::admin();

        $id = $_GET['id'] ?? 0;
        $area = Area::find($id);

        if (!$area) {
            Flash::set('error', 'Area tidak ditemukan.');
            header("Location: index.php?url=area/index");
            exit;
        }

        require_once __DIR__ . '/../views/area/edit.php';
    }

    /**
     * Memproses update data area parkir.
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Validasi input (nama wajib dan kapasitas > 0)
     * 3. Update area via Area::update()
     * 4. Catat log aktivitas
     * 
     * @return void
     */
    public function update()
    {
        Auth::admin();
        Csrf::validate();

        $id        = $_POST['id'] ?? 0;
        $nama_area = trim($_POST['nama_area'] ?? '');
        $kapasitas = intval($_POST['kapasitas'] ?? 0);
        $lokasi    = trim($_POST['lokasi'] ?? '');

        // Validasi: nama wajib dan kapasitas harus positif
        if (empty($nama_area) || $kapasitas <= 0) {
            Flash::set('error', 'Data tidak lengkap atau kapasitas harus lebih dari 0.');
            header("Location: index.php?url=area/edit&id={$id}");
            exit;
        }

        $ok = Area::update($id, $nama_area, $kapasitas, $lokasi);

        if ($ok) {
            Flash::set('success', 'Area berhasil diperbarui.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'update_area', json_encode([
                'id'        => $id,
                'nama'      => $nama_area,
                'kapasitas' => $kapasitas
            ]));
        } else {
            $db = Database::connect();
            Flash::set('error', 'Gagal memperbarui area: ' . mysqli_error($db));
        }

        header("Location: index.php?url=area/index");
        exit;
    }

    /**
     * Menghapus area parkir dari database.
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Validasi ID area
     * 3. Hapus area via Area::delete()
     * 4. Catat log aktivitas
     * 
     * Perhatian: Akan gagal jika masih ada transaksi aktif di area ini.
     * 
     * @return void
     */
    public function delete()
    {
        Auth::admin();
        Csrf::validate();

        $id = $_POST['id'] ?? 0;
        if (!$id) {
            Flash::set('error', 'ID area tidak valid.');
            header("Location: index.php?url=area/index");
            exit;
        }

        $ok = Area::delete($id);

        if ($ok) {
            Flash::set('success', 'Area berhasil dihapus.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'delete_area', json_encode(['id' => $id]));
        } else {
            $db = Database::connect();
            Flash::set('error', 'Gagal menghapus area: ' . mysqli_error($db));
        }

        header("Location: index.php?url=area/index");
        exit;
    }
}