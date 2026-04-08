<?php
/**
 * ==========================================================
 * Model Transaksi - Pengelolaan Data Transaksi Parkir
 * ==========================================================
 * 
 * Mengelola data tabel `tb_transaksi` yang mencatat setiap
 * kendaraan masuk dan keluar dari area parkir restoran.
 * 
 * Tabel: tb_transaksi
 * Kolom: id_parkir, id_kendaraan, id_user, id_area, id_tarif,
 *        waktu_masuk, waktu_keluar, status, biaya_total, created_at
 * 
 * Relasi:
 * - tb_kendaraan (jenis kendaraan & plat nomor)
 * - tb_user      (petugas yang mencatat)
 * - tb_area      (area parkir yang digunakan)
 * - tb_tarif     (tarif berdasarkan jenis kendaraan)
 */

require_once __DIR__ . '/../config/database.php';

class Transaksi
{
    /**
     * Mengambil semua data transaksi beserta info kendaraan dan area.
     * 
     * Fungsi:
     * - Menampilkan daftar seluruh transaksi parkir
     * - JOIN dengan tb_kendaraan untuk plat_nomor & jenis_kendaraan
     * - LEFT JOIN dengan tb_area untuk nama_area
     * - Diurutkan berdasarkan waktu_masuk terbaru
     *
     * @return mysqli_result|false Hasil query semua transaksi
     */
    public static function all()
    {
        $db = Database::connect();
        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area 
                FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                LEFT JOIN tb_area a ON t.id_area = a.id_area
                ORDER BY t.waktu_masuk DESC";
        return mysqli_query($db, $sql);
    }

    /**
     * Mengambil data transaksi dengan filter pencarian.
     * 
     * Fungsi:
     * - Mendukung pencarian berdasarkan plat nomor/jenis kendaraan
     * - Mendukung filter berdasarkan rentang tanggal
     * - Mendukung filter berdasarkan status (masuk/selesai)
     * - Semua filter bersifat opsional dan bisa dikombinasikan
     *
     * @param string $search         Kata kunci pencarian (plat/jenis)
     * @param string $tanggal_dari   Tanggal mulai filter (format: Y-m-d)
     * @param string $tanggal_sampai Tanggal akhir filter (format: Y-m-d)
     * @param string $status         Filter status: 'masuk' atau 'selesai'
     * @return mysqli_result|false Hasil query transaksi yang terfilter
     */
    public static function allFiltered($search = '', $tanggal_dari = '', $tanggal_sampai = '', $status = '')
    {
        $db = Database::connect();
        $conditions = [];

        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area 
                FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                LEFT JOIN tb_area a ON t.id_area = a.id_area";

        // Filter pencarian plat nomor atau jenis kendaraan
        if (!empty($search)) {
            $search_esc = mysqli_real_escape_string($db, $search);
            $conditions[] = "(k.plat_nomor LIKE '%$search_esc%' OR k.jenis_kendaraan LIKE '%$search_esc%')";
        }

        // Filter tanggal mulai
        if (!empty($tanggal_dari)) {
            $dari_esc = mysqli_real_escape_string($db, $tanggal_dari);
            $conditions[] = "DATE(t.waktu_masuk) >= '$dari_esc'";
        }

        // Filter tanggal akhir
        if (!empty($tanggal_sampai)) {
            $sampai_esc = mysqli_real_escape_string($db, $tanggal_sampai);
            $conditions[] = "DATE(t.waktu_masuk) <= '$sampai_esc'";
        }

        // Filter status transaksi
        if (!empty($status)) {
            $status_esc = mysqli_real_escape_string($db, $status);
            $conditions[] = "t.status = '$status_esc'";
        }

        // Gabungkan semua kondisi filter dengan AND
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY t.waktu_masuk DESC";
        return mysqli_query($db, $sql);
    }

    /**
     * Mencari satu transaksi berdasarkan ID parkir.
     * 
     * Fungsi:
     * - Mengambil detail lengkap satu transaksi
     * - Termasuk informasi kendaraan (plat, jenis) dan area
     * - Dipakai saat edit, cetak struk, atau update status
     *
     * @param int $id ID transaksi (id_parkir)
     * @return array|null Data transaksi atau null jika tidak ditemukan
     */
    public static function find($id)
    {
        $db = Database::connect();
        $id = intval($id);
        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area 
                FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                LEFT JOIN tb_area a ON t.id_area = a.id_area
                WHERE t.id_parkir = $id";
        $result = mysqli_query($db, $sql);
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    /**
     * Membuat transaksi parkir baru (kendaraan masuk).
     * 
     * Fungsi:
     * - Mencatat kendaraan masuk ke area parkir
     * - Waktu masuk otomatis diisi NOW()
     * - Status awal: 'masuk'
     * - Biaya awal: 0 (dihitung saat checkout)
     *
     * @param int $id_kendaraan ID kendaraan yang masuk
     * @param int $id_user      ID petugas yang mencatat
     * @param int $id_area      ID area parkir yang ditempati
     * @param int $id_tarif     ID tarif berdasarkan jenis kendaraan
     * @return bool True jika berhasil
     */
    public static function create($id_kendaraan, $id_user, $id_area, $id_tarif)
    {
        $db = Database::connect();
        $id_kendaraan = intval($id_kendaraan);
        $id_user      = intval($id_user);
        $id_area      = intval($id_area);
        $id_tarif     = intval($id_tarif);

        $sql = "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, waktu_masuk, status, biaya_total) 
                VALUES ($id_kendaraan, $id_user, $id_area, $id_tarif, NOW(), 'masuk', 0)";
        return mysqli_query($db, $sql);
    }

    /**
     * Memperbarui transaksi saat kendaraan keluar (checkout).
     * 
     * Fungsi:
     * - Mengisi waktu_keluar dengan NOW()
     * - Mengisi biaya_total yang sudah dihitung
     * - Mengubah status (biasanya ke 'selesai')
     * - Mencatat error ke log jika query gagal
     *
     * @param int    $id          ID transaksi (id_parkir)
     * @param int    $biaya_total Biaya parkir yang harus dibayar
     * @param string $status      Status baru: 'selesai'
     * @return bool True jika berhasil
     */
    public static function update($id, $biaya_total, $status)
    {
        $db = Database::connect();
        $id    = intval($id);
        $biaya = intval($biaya_total);
        $status = mysqli_real_escape_string($db, strtolower(trim($status)));

        $sql = "UPDATE tb_transaksi SET waktu_keluar=NOW(), biaya_total=$biaya, status='$status' WHERE id_parkir=$id";
        $result = mysqli_query($db, $sql);

        if (!$result) {
            error_log("SQL Error: " . mysqli_error($db) . " - Query: " . $sql);
        }

        return $result;
    }

    /**
     * Menghapus transaksi berdasarkan ID.
     * 
     * Fungsi:
     * - Menghapus satu record transaksi parkir
     * - Biasanya hanya admin yang bisa menghapus
     *
     * @param int $id ID transaksi (id_parkir)
     * @return bool True jika berhasil
     */
    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_transaksi WHERE id_parkir=$id");
    }

    /**
     * Mengubah status transaksi tanpa mengubah data lain.
     * 
     * Fungsi:
     * - Mengubah status saja (tanpa set waktu_keluar/biaya)
     * - Dipakai saat cetak struk untuk otomatis set status 'selesai'
     *
     * @param int    $id     ID transaksi (id_parkir)
     * @param string $status Status baru: 'masuk' atau 'selesai'
     * @return bool True jika berhasil
     */
    public static function updateStatus($id, $status)
    {
        $db = Database::connect();
        $id = intval($id);
        $status = mysqli_real_escape_string($db, strtolower(trim($status)));
        return mysqli_query($db, "UPDATE tb_transaksi SET status='$status' WHERE id_parkir=$id");
    }
}