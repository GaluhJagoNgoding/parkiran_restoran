<?php
require_once __DIR__ . '/../config/database.php';

class Kendaraan
{
    public static function all()
    {
        $db = Database::connect();
        return mysqli_query($db, "SELECT * FROM tb_kendaraan ORDER BY id_kendaraan DESC");
    }

    public static function find($id)
    {
        $db = Database::connect();
        $id = intval($id);
        $result = mysqli_query($db, "SELECT * FROM tb_kendaraan WHERE id_kendaraan = $id");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    public static function create($plat_nomor, $jenis_kendaraan, $warna, $pemilik)
    {
        $db = Database::connect();
        $plat = mysqli_real_escape_string($db, $plat_nomor);
        $jenis = mysqli_real_escape_string($db, $jenis_kendaraan);
        $warna = mysqli_real_escape_string($db, $warna);
        $pemilik = mysqli_real_escape_string($db, $pemilik);

        $sql = "INSERT INTO tb_kendaraan (plat_nomor, jenis_kendaraan, warna, pemilik) 
                VALUES ('$plat', '$jenis', '$warna', '$pemilik')";
        return mysqli_query($db, $sql);
    }

    public static function update($id, $plat_nomor, $jenis_kendaraan, $warna, $pemilik)
    {
        $db = Database::connect();
        $id = intval($id);
        $plat = mysqli_real_escape_string($db, $plat_nomor);
        $jenis = mysqli_real_escape_string($db, $jenis_kendaraan);
        $warna = mysqli_real_escape_string($db, $warna);
        $pemilik = mysqli_real_escape_string($db, $pemilik);

        $sql = "UPDATE tb_kendaraan SET 
                plat_nomor='$plat', jenis_kendaraan='$jenis', warna='$warna', pemilik='$pemilik' 
                WHERE id_kendaraan=$id";
        return mysqli_query($db, $sql);
    }

    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        // Hapus transaksi terkait dulu jika perlu, atau biarkan constraint database menangani
        return mysqli_query($db, "DELETE FROM tb_kendaraan WHERE id_kendaraan=$id");
    }

    public static function updateStatus($id, $status)
    {
        $db = Database::connect();
        $id = intval($id);
        $status = intval($status);
        $sql = "UPDATE tb_kendaraan SET status=$status WHERE id_kendaraan=$id";
        return mysqli_query($db, $sql);
    }
}