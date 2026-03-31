<?php
require_once __DIR__ . '/../models/Area.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class AreaController
{
    public function index()
    {
        Auth::admin();
        $data = Area::all();
        require_once __DIR__ . '/../views/area/index.php';
    }

    public function create()
    {
        Auth::admin();
        require_once __DIR__ . '/../views/area/create.php';
    }

    public function store()
    {
        Auth::admin();
        Csrf::validate();
        $nama_area = trim($_POST['nama_area'] ?? '');
        $kapasitas = intval($_POST['kapasitas'] ?? 0);
        $lokasi = trim($_POST['lokasi'] ?? '');

        if (empty($nama_area) || $kapasitas <= 0) {
            Flash::set('error', 'Data tidak lengkap atau kapasitas harus lebih dari 0.');
            header("Location: index.php?url=area/create");
            exit;
        }

        $ok = Area::create($nama_area, $kapasitas, $lokasi);
        if ($ok) {
            Flash::set('success', 'Area berhasil ditambahkan.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'create_area', json_encode(['nama' => $nama_area, 'kapasitas' => $kapasitas]));
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal menambahkan area: ' . mysqli_error($db));
        }

        header("Location: index.php?url=area/index");
        exit;
    }

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

    public function update()
    {
        Auth::admin();
        Csrf::validate();
        $id = $_POST['id'] ?? 0;
        $nama_area = trim($_POST['nama_area'] ?? '');
        $kapasitas = intval($_POST['kapasitas'] ?? 0);
        $lokasi = trim($_POST['lokasi'] ?? '');

        if (empty($nama_area) || $kapasitas <= 0) {
            Flash::set('error', 'Data tidak lengkap atau kapasitas harus lebih dari 0.');
            header("Location: index.php?url=area/edit&id={$id}");
            exit;
        }

        $ok = Area::update($id, $nama_area, $kapasitas, $lokasi);
        if ($ok) {
            Flash::set('success', 'Area berhasil diperbarui.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'update_area', json_encode(['id' => $id, 'nama' => $nama_area, 'kapasitas' => $kapasitas]));
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal memperbarui area: ' . mysqli_error($db));
        }

        header("Location: index.php?url=area/index");
        exit;
    }

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
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal menghapus area: ' . mysqli_error($db));
        }
        header("Location: index.php?url=area/index");
        exit;
    }
}