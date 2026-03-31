<?php
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class LogController
{
    // Menampilkan halaman daftar log aktivitas (Hanya Admin)
    public function index()
    {
        Auth::admin();
        $data = Log::all();
        require_once __DIR__ . '/../views/log/index.php';
    }

    // Menghapus satu log berdasarkan ID
    public function delete()
    {
        Auth::admin();
        Csrf::validate();
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            Flash::set('error', 'ID log tidak valid.');
            header("Location: index.php?url=log/index");
            exit;
        }
        $ok = Log::delete($id);
        if ($ok) {
            Flash::set('success', 'Log berhasil dihapus.');
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal menghapus log: ' . mysqli_error($db));
        }
        header("Location: index.php?url=log/index");
        exit;
    }

    // Membersihkan semua data log dari database
    public function clear()
    {
        Auth::admin();
        Csrf::validate();
        $ok = Log::clearAll();
        if ($ok)
            Flash::set('success', 'Semua log berhasil dibersihkan.');
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal membersihkan log: ' . mysqli_error($db));
        }
        header("Location: index.php?url=log/index");
        exit;
    }
}