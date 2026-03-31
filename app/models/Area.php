<?php
require_once __DIR__ . '/../config/database.php';

class Area {
    public static function all() {
        $db = Database::connect();
        return mysqli_query($db, "SELECT * FROM tb_area ORDER BY nama_area ASC");
    }

    public static function find($id) {
        $db = Database::connect();
        $id = intval($id);
        $result = mysqli_query($db, "SELECT * FROM tb_area WHERE id_area = $id");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    public static function create($nama_area, $kapasitas, $lokasi) {
        $db = Database::connect();
        $nama = mysqli_real_escape_string($db, $nama_area);
        $kap = intval($kapasitas);
        $lok = mysqli_real_escape_string($db, $lokasi);

        $sql = "INSERT INTO tb_area (nama_area, kapasitas, lokasi) VALUES ('$nama', $kap, '$lok')";
        return mysqli_query($db, $sql);
    }

    public static function update($id, $nama_area, $kapasitas, $lokasi) {
        $db = Database::connect();
        $id = intval($id);
        $nama = mysqli_real_escape_string($db, $nama_area);
        $kap = intval($kapasitas);
        $lok = mysqli_real_escape_string($db, $lokasi);

        $sql = "UPDATE tb_area SET nama_area='$nama', kapasitas=$kap, lokasi='$lok' WHERE id_area=$id";
        return mysqli_query($db, $sql);
    }

    public static function delete($id) {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_area WHERE id_area=$id");
    }
}