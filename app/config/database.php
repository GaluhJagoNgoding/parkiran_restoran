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
     * Load environment variables from .env file
     */
    private static function loadEnv()
    {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                if (!getenv($name)) {
                    putenv("$name=$value");
                }
            }
        }
    }

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
        // Load environment variables
        self::loadEnv();

        // Auto-detect environment: localhost = XAMPP, otherwise = Production hosting
        $server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';

        if ($server_name == 'localhost' || $server_name == '127.0.0.1' || strpos($server_name, '.local') !== false) {
            // Konfigurasi Lokal (XAMPP)
            $host = "localhost";
            $user = "root";
            $pass = "";
            $db   = "parkiran_restoran";
        } else {
            // Konfigurasi Production (InfinityFree atau hosting lain)
            // Ganti dengan kredensial hosting Anda
            $host = getenv('DB_HOST') ?: "sql100.infinityfree.com";
            $user = getenv('DB_USER') ?: "if0_41607120";
            $pass = getenv('DB_PASS') ?: "GALUHGANTENG15";
            $db   = getenv('DB_NAME') ?: "if0_41607120_parkiran_restoran";
        }

        $conn = mysqli_connect($host, $user, $pass, $db);

        if (!$conn) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }

        mysqli_set_charset($conn, "utf8");
        return $conn;
    }
}
