<?php
require_once __DIR__ . '/../config/database.php';

class Tarif
{
    public static function all()
    {
        $db = Database::connect();
        return mysqli_query($db, "SELECT * FROM tb_tarif ORDER BY id_tarif ASC");
    }

    public static function find($id)
    {
        $db = Database::connect();
        $id = intval($id);
        $result = mysqli_query($db, "SELECT * FROM tb_tarif WHERE id_tarif = $id");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    public static function create($jenis_kendaraan, $tarif_per_jam, $tarif_per_hari = 0)
    {
        $db = Database::connect();
        $jenis = mysqli_real_escape_string($db, $jenis_kendaraan);
        $jam = intval($tarif_per_jam);
        $hari = intval($tarif_per_hari);

        $sql = "INSERT INTO tb_tarif (jenis_kendaraan, tarif_per_jam, tarif_per_hari) VALUES ('$jenis', $jam, $hari)";
        return mysqli_query($db, $sql);
    }

    public static function update($id, $jenis_kendaraan, $tarif_per_jam, $tarif_per_hari = 0)
    {
        $db = Database::connect();
        $id = intval($id);
        $jenis = mysqli_real_escape_string($db, $jenis_kendaraan);
        $jam = intval($tarif_per_jam);
        $hari = intval($tarif_per_hari);

        $sql = "UPDATE tb_tarif SET jenis_kendaraan='$jenis', tarif_per_jam=$jam, tarif_per_hari=$hari WHERE id_tarif=$id";
        return mysqli_query($db, $sql);
    }

    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_tarif WHERE id_tarif=$id");
    }
}