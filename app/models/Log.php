<?php
/**
 * ==========================================================
 * Model Log - Pencatatan Log Aktivitas Sistem
 * ==========================================================
 * 
 * Mengelola data tabel `tb_log_aktivitas` yang mencatat
 * setiap aktivitas penting (login, CRUD data, dll).
 * 
 * Tabel: tb_log_aktivitas
 * Kolom: id_log, id_user, aktivitas, waktu_aktivitas
 * 
 * Relasi:
 * - tb_user (siapa yang melakukan aktivitas)
 */

require_once __DIR__ . '/../config/database.php';

class Log
{
    /**
     * Mengambil semua log aktivitas beserta nama user-nya.
     * 
     * Fungsi:
     * - Menampilkan riwayat seluruh aktivitas di sistem
     * - LEFT JOIN dengan tb_user untuk mendapat username pelaku
     * - Diurutkan dari log terbaru (id_log DESC)
     *
     * @return mysqli_result|false Hasil query semua log
     */
    public static function all()
    {
        $db = Database::connect();
        $sql = "SELECT l.*, u.username 
                FROM tb_log_aktivitas l 
                LEFT JOIN tb_user u ON l.id_user = u.id_user 
                ORDER BY l.id_log DESC";
        $result = mysqli_query($db, $sql);
        return $result ? $result : false;
    }

    /**
     * Mencatat aktivitas baru ke dalam log.
     * 
     * Fungsi:
     * - Menyimpan siapa yang melakukan apa dan kapan
     * - id_user bisa NULL (contoh: login gagal tanpa user valid)
     * - Meta data (JSON) ditambahkan ke deskripsi aktivitas
     * - Waktu otomatis diisi NOW()
     *
     * @param int|null $user_id ID user yang melakukan aktivitas (null jika anonim)
     * @param string   $action  Jenis aksi: 'login', 'create_user', 'delete_transaksi', dst.
     * @param string   $meta    (Opsional) Data tambahan dalam format JSON
     * @return bool True jika berhasil
     */
    public static function create($user_id, $action, $meta = '')
    {
        $db = Database::connect();
        $user_id_val = ($user_id === 0 || $user_id === null) ? "NULL" : intval($user_id);
        $action = mysqli_real_escape_string($db, $action);

        // Gabungkan action dengan meta data sebagai deskripsi lengkap
        $aktivitas = $action;
        if (!empty($meta)) {
            $aktivitas .= " ($meta)";
        }

        $sql = "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) 
                VALUES ($user_id_val, '$aktivitas', NOW())";
        return mysqli_query($db, $sql);
    }

    /**
     * Menghapus satu log berdasarkan ID.
     * 
     * Fungsi:
     * - Menghapus satu record log aktivitas
     * - Hanya admin yang bisa menghapus (dikontrol di Controller)
     *
     * @param int $id ID log yang dihapus
     * @return bool True jika berhasil
     */
    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_log_aktivitas WHERE id_log=$id");
    }

    /**
     * Menghapus SEMUA log dari database (TRUNCATE).
     * 
     * Fungsi:
     * - Membersihkan seluruh tabel log aktivitas
     * - Menggunakan TRUNCATE (lebih cepat dari DELETE, reset auto_increment)
     * - Hanya admin yang bisa melakukan ini (dikontrol di Controller)
     *
     * @return bool True jika berhasil
     */
    public static function clearAll()
    {
        $db = Database::connect();
        return mysqli_query($db, "TRUNCATE TABLE tb_log_aktivitas");
    }
}