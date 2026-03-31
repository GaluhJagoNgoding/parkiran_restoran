<?php
require_once __DIR__ . '/../config/database.php';

class User
{
    public static function all()
    {
        $db = Database::connect();
        $query = "SELECT * FROM tb_user ORDER BY id_user DESC";
        return mysqli_query($db, $query);
    }

    public static function find($id)
    {
        $db = Database::connect();
        $id = intval($id);
        $result = mysqli_query($db, "SELECT * FROM tb_user WHERE id_user = $id");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    public static function findByCredentials($username, $password, $role = '')
    {
        $db = Database::connect();
        $username = mysqli_real_escape_string($db, $username);

        $sql = "SELECT * FROM tb_user WHERE username = '$username'";

        // Jika role ditentukan dari login page khusus role, tambahkan filter role
        if (!empty($role)) {
            $role = mysqli_real_escape_string($db, $role);
            $sql .= " AND role = '$role'";
        }

        $result = mysqli_query($db, $sql);

        if (!$result) {
            error_log("Database error in findByCredentials: " . mysqli_error($db));
            return false;
        }

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            // Verifikasi password langsung (plain text)
            if ($password === $user['password']) {
                // Pastikan user aktif
                if ($user['status_aktif'] == 1) {
                    return $user;
                }
            }
        }

        return false;
    }

    public static function create($username, $password, $role)
    {
        $db = Database::connect();
        $username = mysqli_real_escape_string($db, $username);
        $password = mysqli_real_escape_string($db, $password);
        $role = mysqli_real_escape_string($db, $role);

        $sql = "INSERT INTO tb_user (username, password, role, status_aktif) VALUES ('$username', '$password', '$role', 1)";
        return mysqli_query($db, $sql);
    }

    public static function update($id, $username, $role, $password = '')
    {
        $db = Database::connect();
        $id = intval($id);
        $username = mysqli_real_escape_string($db, $username);
        $role = mysqli_real_escape_string($db, $role);

        $sql = "UPDATE tb_user SET username='$username', role='$role'";
        if (!empty($password)) {
            $password = mysqli_real_escape_string($db, $password);
            $sql .= ", password='$password'";
        }
        $sql .= " WHERE id_user=$id";
        return mysqli_query($db, $sql);
    }

    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_user WHERE id_user=$id");
    }
}