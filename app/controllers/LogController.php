<?php
/**
 * ==========================================================
 * LogController - Pengelolaan Log Aktivitas Sistem
 * ==========================================================
 * 
 * Menampilkan dan mengelola log/riwayat aktivitas pengguna
 * di sistem (login, CRUD data, dll).
 * 
 * Routes:
 * - log/index  → Daftar semua log aktivitas
 * - log/delete → Hapus satu log (POST)
 * - log/clear  → Hapus semua log (POST)
 * 
 * Akses: Khusus Admin saja
 */

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class LogController
{
    /**
     * Menampilkan halaman daftar semua log aktivitas.
     * 
     * Fungsi:
     * - Mengambil semua log beserta username pelaku
     * - Diurutkan dari log terbaru
     * - Hanya admin yang bisa melihat
     * 
     * @return void
     */
    public function index()
    {
        Auth::admin();
        $data = Log::all();
        require_once __DIR__ . '/../views/log/index.php';
    }

    /**
     * Menghapus satu log aktivitas berdasarkan ID.
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Validasi ID log
     * 3. Hapus log via Log::delete()
     * 
     * @return void
     */
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
        } else {
            $db = Database::connect();
            Flash::set('error', 'Gagal menghapus log: ' . mysqli_error($db));
        }

        header("Location: index.php?url=log/index");
        exit;
    }

    /**
     * Menghapus semua log aktivitas dari database.
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Hapus semua log via Log::clearAll() (TRUNCATE)
     * 3. Tampilkan pesan sukses/gagal
     * 
     * Perhatian: Tindakan ini tidak bisa dibatalkan!
     * 
     * @return void
     */
    public function clear()
    {
        Auth::admin();
        Csrf::validate();

        $ok = Log::clearAll();

        if ($ok) {
            Flash::set('success', 'Semua log berhasil dibersihkan.');
        } else {
            $db = Database::connect();
            Flash::set('error', 'Gagal membersihkan log: ' . mysqli_error($db));
        }

        header("Location: index.php?url=log/index");
        exit;
    }
}