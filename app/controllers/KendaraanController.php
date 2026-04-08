<?php
/**
 * ==========================================================
 * KendaraanController - CRUD Pengelolaan Data Kendaraan
 * ==========================================================
 * 
 * Menangani pengelolaan data kendaraan yang terdaftar
 * di sistem parkir restoran.
 * 
 * Routes:
 * - kendaraan/index        → Daftar semua kendaraan
 * - kendaraan/create       → Form tambah kendaraan baru
 * - kendaraan/store        → Proses simpan kendaraan (POST)
 * - kendaraan/edit         → Form edit kendaraan
 * - kendaraan/update       → Proses update kendaraan (POST)
 * - kendaraan/delete       → Proses hapus kendaraan (POST)
 * - kendaraan/toggleStatus → Toggle aktif/non-aktif kendaraan (GET)
 * 
 * Akses: Semua user yang sudah login
 */

require_once __DIR__ . '/../models/Kendaraan.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class KendaraanController
{
    /**
     * Menampilkan daftar semua kendaraan terdaftar.
     * 
     * @return void
     */
    public function index()
    {
        Auth::admin();
        $data = Kendaraan::all();
        require_once __DIR__ . '/../views/kendaraan/index.php';
    }

    /**
     * Menampilkan form tambah kendaraan baru.
     * 
     * @return void
     */
    public function create()
    {
        Auth::admin();
        require_once __DIR__ . '/../views/kendaraan/create.php';
    }

    /**
     * Menyimpan kendaraan baru ke database.
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Ambil data dari form (plat, jenis, warna, pemilik)
     * 3. Validasi: plat nomor dan jenis wajib diisi
     * 4. Simpan via Kendaraan::create()
     * 5. Catat log aktivitas
     * 
     * @return void
     */
    public function store()
    {
        Auth::admin();
        Csrf::validate();

        $plat_nomor       = trim($_POST['plat_nomor'] ?? '');
        $jenis_kendaraan  = trim($_POST['jenis_kendaraan'] ?? '');
        $warna            = trim($_POST['warna'] ?? '');
        $pemilik          = trim($_POST['pemilik'] ?? '');

        // Validasi: plat nomor dan jenis wajib diisi
        if (empty($plat_nomor) || empty($jenis_kendaraan)) {
            Flash::set('error', 'Plat nomor dan jenis kendaraan tidak boleh kosong.');
            header("Location: index.php?url=kendaraan/create");
            exit;
        }

        $ok = Kendaraan::create($plat_nomor, $jenis_kendaraan, $warna, $pemilik);

        if ($ok) {
            Flash::set('success', 'Kendaraan berhasil ditambahkan.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'create_kendaraan', json_encode([
                'plat'  => $plat_nomor,
                'jenis' => $jenis_kendaraan
            ]));
        } else {
            Flash::set('error', 'Gagal menambahkan kendaraan.');
        }

        header("Location: index.php?url=kendaraan/index");
        exit;
    }

    /**
     * Menampilkan form edit kendaraan.
     * 
     * Fungsi:
     * - Mengambil data kendaraan berdasarkan ID dari parameter GET
     * - Jika kendaraan tidak ditemukan, redirect dengan pesan error
     * 
     * @return void
     */
    public function edit()
    {
        Auth::admin();

        $id = $_GET['id'] ?? 0;
        $kendaraan = Kendaraan::find($id);

        if (!$kendaraan) {
            Flash::set('error', 'Kendaraan tidak ditemukan.');
            header("Location: index.php?url=kendaraan/index");
            exit;
        }

        require_once __DIR__ . '/../views/kendaraan/edit.php';
    }

    /**
     * Memproses update data kendaraan.
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Validasi input (plat & jenis wajib)
     * 3. Update data kendaraan
     * 4. Catat log aktivitas
     * 
     * @return void
     */
    public function update()
    {
        Auth::admin();
        Csrf::validate();

        $id               = $_POST['id'] ?? 0;
        $plat_nomor       = trim($_POST['plat_nomor'] ?? '');
        $jenis_kendaraan  = trim($_POST['jenis_kendaraan'] ?? '');
        $warna            = trim($_POST['warna'] ?? '');
        $pemilik          = trim($_POST['pemilik'] ?? '');

        // Validasi: plat nomor dan jenis wajib diisi
        if (empty($plat_nomor) || empty($jenis_kendaraan)) {
            Flash::set('error', 'Plat nomor dan jenis kendaraan tidak boleh kosong.');
            header("Location: index.php?url=kendaraan/edit&id={$id}");
            exit;
        }

        $ok = Kendaraan::update($id, $plat_nomor, $jenis_kendaraan, $warna, $pemilik);

        if ($ok) {
            Flash::set('success', 'Kendaraan berhasil diperbarui.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'update_kendaraan', json_encode([
                'id'   => $id,
                'plat' => $plat_nomor
            ]));
        } else {
            Flash::set('error', 'Gagal memperbarui kendaraan.');
        }

        header("Location: index.php?url=kendaraan/index");
        exit;
    }

    /**
     * Menghapus kendaraan dari database.
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Validasi ID kendaraan
     * 3. Hapus kendaraan via Kendaraan::delete()
     * 4. Catat log aktivitas
     * 
     * @return void
     */
    public function delete()
    {
        Auth::admin();
        Csrf::validate();

        $id = $_POST['id'] ?? 0;
        if (!$id) {
            Flash::set('error', 'ID kendaraan tidak valid.');
            header("Location: index.php?url=kendaraan/index");
            exit;
        }

        $ok = Kendaraan::delete($id);

        if ($ok) {
            Flash::set('success', 'Kendaraan berhasil dihapus.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'delete_kendaraan', json_encode(['id' => $id]));
        } else {
            Flash::set('error', 'Gagal menghapus kendaraan.');
        }

        header("Location: index.php?url=kendaraan/index");
        exit;
    }

    /**
     * Toggle status aktif/non-aktif kendaraan.
     * 
     * Fungsi:
     * - Jika saat ini aktif (1) → ubah jadi non-aktif (0)
     * - Jika saat ini non-aktif (0) → ubah jadi aktif (1)
     * - Kendaraan non-aktif tidak tampil di dropdown transaksi
     * - Menggunakan method GET (klik tombol toggle di tabel)
     * 
     * @return void
     */
    public function toggleStatus()
    {
        Auth::admin();

        $id = $_GET['id'] ?? 0;
        if (!$id) {
            Flash::set('error', 'ID kendaraan tidak valid.');
            header("Location: index.php?url=kendaraan/index");
            exit;
        }

        $kendaraan = Kendaraan::find($id);
        if (!$kendaraan) {
            Flash::set('error', 'Kendaraan tidak ditemukan.');
            header("Location: index.php?url=kendaraan/index");
            exit;
        }

        // Toggle: 1 → 0, 0 → 1
        $newStatus = $kendaraan['status'] == 1 ? 0 : 1;
        $ok = Kendaraan::updateStatus($id, $newStatus);

        if ($ok) {
            $statusText = $newStatus == 1 ? 'Aktif' : 'Non-Aktif';
            Flash::set('success', 'Status kendaraan berhasil diubah menjadi ' . $statusText . '.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'toggle_status_kendaraan', json_encode([
                'id'         => $id,
                'new_status' => $newStatus
            ]));
        } else {
            Flash::set('error', 'Gagal mengubah status kendaraan.');
        }

        header("Location: index.php?url=kendaraan/index");
        exit;
    }
}
