<?php
require_once __DIR__ . '/../models/Tarif.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class TarifController
{
    public function index()
    {
        Auth::adminOrOwner();
        $data = Tarif::all();
        require_once __DIR__ . '/../views/tarif/index.php';
    }

    public function create()
    {
        Auth::adminOrOwner();
        require_once __DIR__ . '/../views/tarif/create.php';
    }

    public function store()
    {
        Auth::adminOrOwner();
        Csrf::validate();
        $jenis_kendaraan = trim($_POST['jenis_kendaraan'] ?? '');
        $tarif_per_jam = floatval($_POST['tarif_per_jam'] ?? 0);
        $tarif_per_hari = floatval($_POST['tarif_per_hari'] ?? 0);

        if (empty($jenis_kendaraan) || $tarif_per_jam <= 0 || $tarif_per_hari <= 0) {
            Flash::set('error', 'Data tidak lengkap. Semua tarif harus bernilai lebih dari 0.');
            header("Location: index.php?url=tarif/create");
            exit;
        }

        $ok = Tarif::create($jenis_kendaraan, $tarif_per_jam, $tarif_per_hari);
        if ($ok) {
            Flash::set('success', 'Tarif berhasil ditambahkan.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'create_tarif', json_encode(['jenis' => $jenis_kendaraan, 'jam' => $tarif_per_jam, 'hari' => $tarif_per_hari]));
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal menambahkan tarif: ' . mysqli_error($db));
        }
        header("Location: index.php?url=tarif/index");
        exit;
    }

    public function edit()
    {
        Auth::adminOrOwner();
        $id = $_GET['id'] ?? 0;
        $tarif = Tarif::find($id);
        if (!$tarif) {
            Flash::set('error', 'Tarif tidak ditemukan.');
            header("Location: index.php?url=tarif/index");
            exit;
        }
        require_once __DIR__ . '/../views/tarif/edit.php';
    }

    public function update()
    {
        Auth::adminOrOwner();
        Csrf::validate();
        $id = $_POST['id'] ?? 0;
        $jenis_kendaraan = trim($_POST['jenis_kendaraan'] ?? '');
        $tarif_per_jam = floatval($_POST['tarif_per_jam'] ?? 0);
        $tarif_per_hari = floatval($_POST['tarif_per_hari'] ?? 0);

        if (empty($jenis_kendaraan) || $tarif_per_jam <= 0 || $tarif_per_hari <= 0) {
            Flash::set('error', 'Data tidak lengkap. Semua tarif harus bernilai lebih dari 0.');
            header("Location: index.php?url=tarif/edit&id={$id}");
            exit;
        }

        $ok = Tarif::update($id, $jenis_kendaraan, $tarif_per_jam, $tarif_per_hari);
        if ($ok) {
            Flash::set('success', 'Tarif berhasil diperbarui.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'update_tarif', json_encode(['id' => $id, 'jenis' => $jenis_kendaraan, 'jam' => $tarif_per_jam, 'hari' => $tarif_per_hari]));
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal memperbarui tarif: ' . mysqli_error($db));
        }
        header("Location: index.php?url=tarif/index");
        exit;
    }

    public function delete()
    {
        Auth::adminOrOwner();
        Csrf::validate();
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            Flash::set('error', 'ID tarif tidak valid.');
            header("Location: index.php?url=tarif/index");
            exit;
        }
        $ok = Tarif::delete($id);
        if ($ok) {
            Flash::set('success', 'Tarif berhasil dihapus.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'delete_tarif', json_encode(['id' => $id]));
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal menghapus tarif: ' . mysqli_error($db));
        }
        header("Location: index.php?url=tarif/index");
        exit;
    }
}