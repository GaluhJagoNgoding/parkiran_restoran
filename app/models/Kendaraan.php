<?php
/**
 * ==========================================================
 * Model Kendaraan - Pengelolaan Data Kendaraan
 * ==========================================================
 * 
 * Mengelola data tabel `tb_kendaraan` yang berisi informasi
 * kendaraan yang tercatat di sistem parkir.
 * 
 * Tabel: tb_kendaraan
 * Kolom: id_kendaraan, plat_nomor, jenis_kendaraan, warna,
 *        pemilik, status, created_at
 */

require_once __DIR__ . '/../config/database.php';

class Kendaraan
{
    /**
     * Mengambil semua data kendaraan, diurutkan dari yang terbaru.
     * 
     * Fungsi:
     * - Menampilkan daftar seluruh kendaraan terdaftar
     * - Diurutkan berdasarkan id_kendaraan DESC
     *
     * @return mysqli_result|false Hasil query berisi semua kendaraan
     */
    public static function all()
    {
        $db = Database::connect();
        return mysqli_query($db, "SELECT * FROM tb_kendaraan ORDER BY id_kendaraan DESC");
    }

    /**
     * Mencari satu kendaraan berdasarkan ID.
     * 
     * Fungsi:
     * - Dipakai saat edit, detail, atau membuat transaksi parkir
     * - Mengembalikan data lengkap satu kendaraan
     *
     * @param int $id ID kendaraan
     * @return array|null Data kendaraan atau null jika tidak ditemukan
     */
    public static function find($id)
    {
        $db = Database::connect();
        $id = intval($id);
        $result = mysqli_query($db, "SELECT * FROM tb_kendaraan WHERE id_kendaraan = $id");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    /**
     * Menambahkan kendaraan baru ke database.
     * 
     * Fungsi:
     * - Menyimpan data kendaraan baru (plat, jenis, warna, pemilik)
     * - Semua input di-escape untuk keamanan SQL
     *
     * @param string $plat_nomor       Nomor plat kendaraan (unik)
     * @param string $jenis_kendaraan  Jenis: Motor, Mobil, Truk, dll.
     * @param string $warna            Warna kendaraan
     * @param string $pemilik          Nama pemilik kendaraan
     * @return bool True jika berhasil
     */
    public static function create($plat_nomor, $jenis_kendaraan, $warna, $pemilik)
    {
        $db = Database::connect();
        $plat    = mysqli_real_escape_string($db, $plat_nomor);
        $jenis   = mysqli_real_escape_string($db, $jenis_kendaraan);
        $warna   = mysqli_real_escape_string($db, $warna);
        $pemilik = mysqli_real_escape_string($db, $pemilik);

        $sql = "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik) 
                VALUES ('$plat', '$jenis', '$warna', '$pemilik')";
        return mysqli_query($db, $sql);
    }

    /**
     * Memperbarui data kendaraan berdasarkan ID.
     * 
     * Fungsi:
     * - Mengubah informasi kendaraan yang sudah terdaftar
     * - Memperbarui plat_nomor, jenis, warna, dan pemilik
     *
     * @param int    $id               ID kendaraan
     * @param string $plat_nomor       Plat nomor baru
     * @param string $jenis_kendaraan  Jenis kendaraan baru
     * @param string $warna            Warna baru
     * @param string $pemilik          Pemilik baru
     * @return bool True jika berhasil
     */
    public static function update($id, $plat_nomor, $jenis_kendaraan, $warna, $pemilik)
    {
        $db = Database::connect();
        $id      = intval($id);
        $plat    = mysqli_real_escape_string($db, $plat_nomor);
        $jenis   = mysqli_real_escape_string($db, $jenis_kendaraan);
        $warna   = mysqli_real_escape_string($db, $warna);
        $pemilik = mysqli_real_escape_string($db, $pemilik);

        $sql = "UPDATE tb_kendaraan SET 
                plat_nomor='$plat', jenis_kendaraan='$jenis', 
                warna='$warna', pemilik='$pemilik' 
                WHERE id_kendaraan=$id";
        return mysqli_query($db, $sql);
    }

    /**
     * Menghapus kendaraan berdasarkan ID.
     * 
     * Fungsi:
     * - Menghapus record kendaraan dari tb_kendaraan
     * - Perhatian: Akan gagal jika ada transaksi terkait (FK constraint)
     *
     * @param int $id ID kendaraan yang dihapus
     * @return bool True jika berhasil
     */
    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_kendaraan WHERE id_kendaraan=$id");
    }

    /**
     * Mengubah status aktif/non-aktif kendaraan.
     * 
     * Fungsi:
     * - Toggle status kendaraan (1 = aktif, 0 = non-aktif)
     * - Kendaraan non-aktif tidak bisa dipakai transaksi baru
     *
     * @param int $id     ID kendaraan
     * @param int $status Status baru: 1 (aktif) atau 0 (non-aktif)
     * @return bool True jika berhasil
     */
    public static function updateStatus($id, $status)
    {
        $db = Database::connect();
        $id = intval($id);
        $status = intval($status);
        return mysqli_query($db, "UPDATE tb_kendaraan SET status=$status WHERE id_kendaraan=$id");
    }
}