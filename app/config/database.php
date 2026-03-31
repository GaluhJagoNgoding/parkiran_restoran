<?php
class Database
{
    public static function connect()
    {
        $conn = mysqli_connect("localhost", "root", "", "parkir_restoran");
        if (!$conn) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }
        mysqli_set_charset($conn, "utf8");
        return $conn;
    }
}
