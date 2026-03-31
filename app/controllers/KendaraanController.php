<?php
require_once __DIR__ . '/../models/Kendaraan.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class KendaraanController
{
    public function index()
    {
        Auth::check();
        $data = Kendaraan::all();
        require_once __DIR__ . '/../views/kendaraan/index.php';
    }

    public function create()
    {
        Auth::check();
        require_once __DIR__ . '/../views/kendaraan/create.php';
    }

    public function store()
    {
        Auth::check();
        Csrf::validate();
        $plat_nomor = trim($_POST['plat_nomor'] ?? '');
        $jenis_kendaraan = trim($_POST['jenis_kendaraan'] ?? '');
        $warna = trim($_POST['warna'] ?? '');
        $pemilik = trim($_POST['pemilik'] ?? '');

        if (empty($plat_nomor) || empty($jenis_kendaraan)) {
            Flash::set('error', 'Plat nomor dan jenis kendaraan tidak boleh kosong.');
            header("Location: index.php?url=kendaraan/create");
            exit;
        }

        $ok = Kendaraan::create($plat_nomor, $jenis_kendaraan, $warna, $pemilik);
        if ($ok) {
            Flash::set('success', 'Kendaraan berhasil ditambahkan.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'create_kendaraan', json_encode(['plat' => $plat_nomor, 'jenis' => $jenis_kendaraan]));
        }
        else
            Flash::set('error', 'Gagal menambahkan kendaraan.');
        header("Location: index.php?url=kendaraan/index");
        exit;
    }

    public function edit()
    {
        Auth::check();
        $id = $_GET['id'] ?? 0;
        $kendaraan = Kendaraan::find($id);
        if (!$kendaraan) {
            Flash::set('error', 'Kendaraan tidak ditemukan.');
            header("Location: index.php?url=kendaraan/index");
            exit;
        }
        require_once __DIR__ . '/../views/kendaraan/edit.php';
    }

    public function update()
    {
        Auth::check();
        Csrf::validate();
        $id = $_POST['id'] ?? 0;
        $plat_nomor = trim($_POST['plat_nomor'] ?? '');
        $jenis_kendaraan = trim($_POST['jenis_kendaraan'] ?? '');
        $warna = trim($_POST['warna'] ?? '');
        $pemilik = trim($_POST['pemilik'] ?? '');

        if (empty($plat_nomor) || empty($jenis_kendaraan)) {
            Flash::set('error', 'Plat nomor dan jenis kendaraan tidak boleh kosong.');
            header("Location: index.php?url=kendaraan/edit&id={$id}");
            exit;
        }

        $ok = Kendaraan::update($id, $plat_nomor, $jenis_kendaraan, $warna, $pemilik);
        if ($ok) {
            Flash::set('success', 'Kendaraan berhasil diperbarui.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'update_kendaraan', json_encode(['id' => $id, 'plat' => $plat_nomor]));
        }
        else
            Flash::set('error', 'Gagal memperbarui kendaraan.');
        header("Location: index.php?url=kendaraan/index");
        exit;
    }

    public function delete()
    {
        Auth::check();
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
        }
        else
            Flash::set('error', 'Gagal menghapus kendaraan.');
        header("Location: index.php?url=kendaraan/index");
        exit;
    }

    public function toggleStatus()
    {
        Auth::check();
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

        $newStatus = $kendaraan['status'] == 1 ? 0 : 1;
        $ok = Kendaraan::updateStatus($id, $newStatus);
        if ($ok) {
            $statusText = $newStatus == 1 ? 'Aktif' : 'Non-Aktif';
            Flash::set('success', 'Status kendaraan berhasil diubah menjadi ' . $statusText . '.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'toggle_status_kendaraan', json_encode(['id' => $id, 'new_status' => $newStatus]));
        }
        else
            Flash::set('error', 'Gagal mengubah status kendaraan.');
        header("Location: index.php?url=kendaraan/index");
        exit;
    }
}
