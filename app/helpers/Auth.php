<?php
/**
 * ==========================================================
 * Helper Auth - Otentikasi & Otorisasi Pengguna
 * ==========================================================
 * 
 * Menyediakan method statis untuk memeriksa apakah pengguna
 * sudah login dan memiliki role yang sesuai.
 * 
 * Digunakan di awal setiap method Controller untuk
 * membatasi akses berdasarkan role (admin, petugas, owner).
 */

class Auth
{
    /**
     * Memeriksa apakah pengguna sudah login.
     * 
     * Fungsi:
     * - Mengecek keberadaan $_SESSION['user']
     * - Jika belum login, redirect ke halaman login
     * 
     * @return void
     */
    public static function check()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=auth/index");
            exit;
        }
    }

    /**
     * Memastikan pengguna yang login memiliki role "admin".
     * 
     * Fungsi:
     * - Memanggil check() terlebih dahulu (wajib login)
     * - Jika bukan admin, redirect ke dashboard
     * 
     * @return void
     */
    public static function admin()
    {
        self::check();
        if ($_SESSION['user']['role'] != 'admin') {
            header("Location: index.php?url=dashboard/index");
            exit;
        }
    }

    /**
     * Memastikan pengguna adalah admin ATAU owner.
     * 
     * Fungsi:
     * - Dipakai untuk fitur yang bisa diakses admin dan owner
     *   (contoh: kelola tarif)
     * - Jika bukan keduanya, redirect ke dashboard
     * 
     * @return void
     */
    public static function adminOrOwner()
    {
        self::check();
        $role = $_SESSION['user']['role'];
        if ($role != 'admin' && $role != 'owner') {
            header("Location: index.php?url=dashboard/index");
            exit;
        }
    }

    /**
     * Memastikan pengguna yang login memiliki role "petugas".
     * 
     * Fungsi:
     * - Dipakai untuk fitur khusus petugas parkir
     * - Jika bukan petugas, redirect ke dashboard
     * 
     * @return void
     */
    public static function petugas()
    {
        self::check();
        if ($_SESSION['user']['role'] != 'petugas') {
            header("Location: index.php?url=dashboard/index");
            exit;
        }
    }

    /**
     * Memastikan pengguna yang login memiliki role "owner".
     * 
     * Fungsi:
     * - Dipakai untuk fitur khusus pemilik restoran
     * - Jika bukan owner, redirect ke dashboard
     * 
     * @return void
     */
    public static function owner()
    {
        self::check();
        if ($_SESSION['user']['role'] != 'owner') {
            header("Location: index.php?url=dashboard/index");
            exit;
        }
    }
}
