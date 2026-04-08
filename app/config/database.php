<?php
/**
 * ==========================================================
 * Konfigurasi Database - Sistem Parkir Restoran
 * ==========================================================
 * 
 * File ini berisi class Database yang menyediakan koneksi
 * ke MySQL/MariaDB menggunakan driver mysqli.
 * 
 * Digunakan oleh seluruh Model untuk mengakses database.
 */

class Database
{
    /**
     * Membuat dan mengembalikan koneksi database mysqli.
     * 
     * Fungsi:
     * - Menghubungkan aplikasi ke server MySQL di localhost
     * - Menggunakan database "parkir_restoran"
     * - Mengatur charset ke UTF-8 agar mendukung karakter Indonesia
     * - Menghentikan aplikasi jika koneksi gagal (die)
     *
     * @return mysqli Objek koneksi database yang siap dipakai
     */
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
