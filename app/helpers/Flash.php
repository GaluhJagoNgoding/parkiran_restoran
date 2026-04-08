<?php
/**
 * ==========================================================
 * Helper Flash - Pesan Notifikasi Satu-Kali (Flash Message)
 * ==========================================================
 * 
 * Menyimpan pesan notifikasi di session yang hanya tampil
 * sekali setelah redirect (contoh: "Data berhasil disimpan").
 * 
 * Alur kerja:
 * 1. Controller memanggil Flash::set('success', 'Pesan...')
 * 2. Controller melakukan redirect
 * 3. View memanggil Flash::get() untuk menampilkan pesan
 * 4. Pesan otomatis dihapus dari session setelah dibaca
 */

class Flash
{
    /**
     * Menyimpan pesan flash ke session.
     * 
     * Fungsi:
     * - Menyimpan type (success/error) dan message ke $_SESSION['flash']
     * - Dipanggil di Controller sebelum redirect
     *
     * @param string $type    Jenis pesan: 'success' atau 'error'
     * @param string $message Isi pesan yang ditampilkan ke pengguna
     * @return void
     */
    public static function set($type, $message)
    {
        if (!session_id()) {
            session_start();
        }
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Mengambil dan menghapus pesan flash dari session.
     * 
     * Fungsi:
     * - Membaca pesan dari $_SESSION['flash']
     * - Langsung menghapus pesan agar tidak tampil ulang
     * - Mengembalikan null jika tidak ada pesan
     *
     * @return array|null Array berisi 'type' dan 'message', atau null
     */
    public static function get()
    {
        if (!session_id()) {
            session_start();
        }

        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }

        return null;
    }
}
