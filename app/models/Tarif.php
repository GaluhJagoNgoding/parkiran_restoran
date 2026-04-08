<?php
/**
 * ==========================================================
 * Model Tarif - Pengelolaan Tarif Parkir
 * ==========================================================
 * 
 * Mengelola data tabel `tb_tarif` yang berisi tarif parkir
 * berdasarkan jenis kendaraan (per jam dan per hari).
 * 
 * Tabel: tb_tarif
 * Kolom: id_tarif, jenis_kendaraan, tarif_per_jam, tarif_per_hari,
 *        created_at
 */

require_once __DIR__ . '/../config/database.php';

class Tarif
{
    /**
     * Mengambil semua data tarif, diurutkan berdasarkan ID.
     * 
     * Fungsi:
     * - Menampilkan daftar semua tarif parkir
     * - Diurutkan ASC agar Motor, Mobil, Truk berurutan
     *
     * @return mysqli_result|false Hasil query semua tarif
     */
    public static function all()
    {
        $db = Database::connect();
        return mysqli_query($db, "SELECT * FROM tb_tarif ORDER BY id_tarif ASC");
    }

    /**
     * Mencari satu tarif berdasarkan ID.
     * 
     * Fungsi:
     * - Dipakai saat edit tarif atau menghitung biaya transaksi
     *
     * @param int $id ID tarif
     * @return array|null Data tarif atau null jika tidak ditemukan
     */
    public static function find($id)
    {
        $db = Database::connect();
        $id = intval($id);
        $result = mysqli_query($db, "SELECT * FROM tb_tarif WHERE id_tarif = $id");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    /**
     * Menambahkan tarif baru ke database.
     * 
     * Fungsi:
     * - Membuat tarif baru untuk jenis kendaraan tertentu
     * - Menyimpan tarif per jam dan per hari (dalam Rupiah)
     *
     * @param string $jenis_kendaraan Jenis kendaraan (Motor/Mobil/Truk)
     * @param int    $tarif_per_jam   Tarif per jam dalam Rupiah
     * @param int    $tarif_per_hari  Tarif per hari dalam Rupiah (default: 0)
     * @return bool True jika berhasil
     */
    public static function create($jenis_kendaraan, $tarif_per_jam, $tarif_per_hari = 0)
    {
        $db = Database::connect();
        $jenis = mysqli_real_escape_string($db, $jenis_kendaraan);
        $jam   = intval($tarif_per_jam);
        $hari  = intval($tarif_per_hari);

        $sql = "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam, tarif_per_hari) 
                VALUES ('$jenis', $jam, $hari)";
        return mysqli_query($db, $sql);
    }

    /**
     * Memperbarui data tarif berdasarkan ID.
     * 
     * Fungsi:
     * - Mengubah jenis kendaraan, tarif per jam, dan tarif per hari
     *
     * @param int    $id              ID tarif
     * @param string $jenis_kendaraan Jenis kendaraan baru
     * @param int    $tarif_per_jam   Tarif per jam baru
     * @param int    $tarif_per_hari  Tarif per hari baru (default: 0)
     * @return bool True jika berhasil
     */
    public static function update($id, $jenis_kendaraan, $tarif_per_jam, $tarif_per_hari = 0)
    {
        $db = Database::connect();
        $id    = intval($id);
        $jenis = mysqli_real_escape_string($db, $jenis_kendaraan);
        $jam   = intval($tarif_per_jam);
        $hari  = intval($tarif_per_hari);

        $sql = "UPDATE tb_tarif SET jenis_kendaraan='$jenis', tarif_per_jam=$jam, tarif_per_hari=$hari 
                WHERE id_tarif=$id";
        return mysqli_query($db, $sql);
    }

    /**
     * Menghapus tarif berdasarkan ID.
     * 
     * Fungsi:
     * - Menghapus satu record tarif
     * - Perhatian: Akan gagal jika ada transaksi yang merujuk tarif ini
     *
     * @param int $id ID tarif yang dihapus
     * @return bool True jika berhasil
     */
    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_tarif WHERE id_tarif=$id");
    }
}