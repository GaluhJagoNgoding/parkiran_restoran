<?php
require_once __DIR__ . '/../config/database.php';

class Log
{
    public static function all()
    {
        $db = Database::connect();
        $q = mysqli_query($db, "SELECT l.*, u.username FROM tb_log_aktivitas l LEFT JOIN tb_user u ON l.id_user=u.id_user ORDER BY l.id_log DESC");
        return $q ? $q : false;
    }

    public static function create($user_id, $action, $meta = '')
    {
        $db = Database::connect();
        $user_id_val = ($user_id === 0 || $user_id === null) ? "NULL" : intval($user_id);
        $action = mysqli_real_escape_string($db, $action);
        $aktivitas = $action;
        if (!empty($meta)) {
            $aktivitas .= " ($meta)";
        }
        $sql = "INSERT INTO tb_log_aktivitas (id_user, aktivitas, waktu_aktivitas) VALUES ($user_id_val, '$aktivitas', NOW())";
        return mysqli_query($db, $sql);
    }

    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        $sql = "DELETE FROM tb_log_aktivitas WHERE id_log=$id";
        return mysqli_query($db, $sql);
    }

    public static function clearAll()
    {
        $db = Database::connect();
        $sql = "TRUNCATE TABLE tb_log_aktivitas";
        return mysqli_query($db, $sql);
    }
}