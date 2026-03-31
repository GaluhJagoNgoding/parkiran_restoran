<?php
require_once __DIR__ . '/../config/database.php';

class Transaksi
{
    public static function all()
    {
        $db = Database::connect();
        // Fix #10: JOIN dengan tb_area untuk mendapatkan nama area
        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area 
                FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                LEFT JOIN tb_area a ON t.id_area = a.id_area
                ORDER BY t.waktu_masuk DESC";
        return mysqli_query($db, $sql);
    }

    /**
     * Fix #9: Search & Filter support
     */
    public static function allFiltered($search = '', $tanggal_dari = '', $tanggal_sampai = '', $status = '')
    {
        $db = Database::connect();
        $conditions = [];

        // Fix #10: JOIN dengan tb_area untuk nama area
        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area 
                FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                LEFT JOIN tb_area a ON t.id_area = a.id_area";

        // Filter pencarian plat nomor
        if (!empty($search)) {
            $search_esc = mysqli_real_escape_string($db, $search);
            $conditions[] = "(k.plat_nomor LIKE '%$search_esc%' OR k.jenis_kendaraan LIKE '%$search_esc%')";
        }

        // Filter tanggal
        if (!empty($tanggal_dari)) {
            $dari_esc = mysqli_real_escape_string($db, $tanggal_dari);
            $conditions[] = "DATE(t.waktu_masuk) >= '$dari_esc'";
        }
        if (!empty($tanggal_sampai)) {
            $sampai_esc = mysqli_real_escape_string($db, $tanggal_sampai);
            $conditions[] = "DATE(t.waktu_masuk) <= '$sampai_esc'";
        }

        // Filter status
        if (!empty($status)) {
            $status_esc = mysqli_real_escape_string($db, $status);
            $conditions[] = "t.status = '$status_esc'";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY t.waktu_masuk DESC";
        return mysqli_query($db, $sql);
    }

    public static function find($id)
    {
        $db = Database::connect();
        $id = intval($id);
        // Fix #10: JOIN dengan tb_area untuk nama area
        $sql = "SELECT t.*, k.plat_nomor, k.jenis_kendaraan, a.nama_area 
                FROM tb_transaksi t 
                JOIN tb_kendaraan k ON t.id_kendaraan = k.id_kendaraan 
                LEFT JOIN tb_area a ON t.id_area = a.id_area
                WHERE t.id_parkir = $id";
        $result = mysqli_query($db, $sql);
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    public static function create($id_kendaraan, $id_user, $id_area, $id_tarif)
    {
        $db = Database::connect();
        $id_kendaraan = intval($id_kendaraan);
        $id_user = intval($id_user);
        $id_area = intval($id_area);
        $id_tarif = intval($id_tarif);

        $sql = "INSERT INTO tb_transaksi (id_kendaraan, id_user, id_area, id_tarif, waktu_masuk, status, biaya_total) 
                VALUES ($id_kendaraan, $id_user, $id_area, $id_tarif, NOW(), 'masuk', 0)";
        return mysqli_query($db, $sql);
    }

    public static function update($id, $biaya_total, $status)
    {
        $db = Database::connect();
        $id = intval($id);
        $biaya = intval($biaya_total);
        $status = strtolower(trim($status));
        $status = mysqli_real_escape_string($db, $status);

        // Set waktu_keluar ke NOW() saat update (checkout)
        $sql = "UPDATE tb_transaksi SET waktu_keluar=NOW(), biaya_total=$biaya, status='$status' WHERE id_parkir=$id";
        $result = mysqli_query($db, $sql);

        if (!$result) {
            error_log("SQL Error: " . mysqli_error($db) . " - Query: " . $sql);
        }

        return $result;
    }

    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_transaksi WHERE id_parkir=$id");
    }

    public static function updateStatus($id, $status)
    {
        $db = Database::connect();
        $id = intval($id);
        $status = strtolower(trim($status));
        $status = mysqli_real_escape_string($db, $status);

        $sql = "UPDATE tb_transaksi SET status='$status' WHERE id_parkir=$id";
        return mysqli_query($db, $sql);
    }
}