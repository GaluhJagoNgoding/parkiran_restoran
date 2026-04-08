<?php
/**
 * ==========================================================
 * Model Area - Pengelolaan Area Parkir
 * ==========================================================
 * 
 * Mengelola data tabel `tb_area` yang berisi informasi
 * area/zona parkir di restoran beserta kapasitasnya.
 * 
 * Tabel: tb_area
 * Kolom: id_area, nama_area, kapasitas, lokasi, created_at
 */

require_once __DIR__ . '/../config/database.php';

class Area
{
    /**
     * Mengambil semua data area parkir, diurutkan berdasarkan nama.
     * 
     * Fungsi:
     * - Menampilkan daftar semua area parkir (Area A, B, C, ...)
     * - Diurutkan berdasarkan nama_area ASC (alfabet)
     *
     * @return mysqli_result|false Hasil query semua area
     */
    public static function all()
    {
        $db = Database::connect();
        return mysqli_query($db, "SELECT * FROM tb_area ORDER BY nama_area ASC");
    }

    /**
     * Mencari satu area berdasarkan ID.
     * 
     * Fungsi:
     * - Dipakai saat edit area atau cek kapasitas sebelum transaksi
     *
     * @param int $id ID area
     * @return array|null Data area atau null jika tidak ditemukan
     */
    public static function find($id)
    {
        $db = Database::connect();
        $id = intval($id);
        $result = mysqli_query($db, "SELECT * FROM tb_area WHERE id_area = $id");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    /**
     * Menambahkan area parkir baru ke database.
     * 
     * Fungsi:
     * - Membuat area parkir baru dengan nama, kapasitas, dan lokasi
     * - Contoh: Area A, kapasitas 30, Depan Restoran
     *
     * @param string $nama_area  Nama area parkir
     * @param int    $kapasitas  Jumlah maksimal kendaraan yang bisa ditampung
     * @param string $lokasi     Deskripsi lokasi area parkir
     * @return bool True jika berhasil
     */
    public static function create($nama_area, $kapasitas, $lokasi)
    {
        $db = Database::connect();
        $nama = mysqli_real_escape_string($db, $nama_area);
        $kap  = intval($kapasitas);
        $lok  = mysqli_real_escape_string($db, $lokasi);

        $sql = "INSERT INTO tb_area (nama_area, kapasitas, lokasi) 
                VALUES ('$nama', $kap, '$lok')";
        return mysqli_query($db, $sql);
    }

    /**
     * Memperbarui data area parkir berdasarkan ID.
     * 
     * Fungsi:
     * - Mengubah nama, kapasitas, dan lokasi area
     *
     * @param int    $id        ID area
     * @param string $nama_area Nama area baru
     * @param int    $kapasitas Kapasitas baru
     * @param string $lokasi    Lokasi baru
     * @return bool True jika berhasil
     */
    public static function update($id, $nama_area, $kapasitas, $lokasi)
    {
        $db = Database::connect();
        $id   = intval($id);
        $nama = mysqli_real_escape_string($db, $nama_area);
        $kap  = intval($kapasitas);
        $lok  = mysqli_real_escape_string($db, $lokasi);

        $sql = "UPDATE tb_area SET nama_area='$nama', kapasitas=$kap, lokasi='$lok' 
                WHERE id_area=$id";
        return mysqli_query($db, $sql);
    }

    /**
     * Menghapus area parkir berdasarkan ID.
     * 
     * Fungsi:
     * - Menghapus satu record area dari tb_area
     * - Perhatian: Akan gagal jika ada transaksi yang masih menggunakan area ini
     *
     * @param int $id ID area yang dihapus
     * @return bool True jika berhasil
     */
    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_area WHERE id_area=$id");
    }
}