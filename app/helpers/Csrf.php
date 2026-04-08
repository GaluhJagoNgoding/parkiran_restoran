<?php
/**
 * ==========================================================
 * Helper CSRF - Perlindungan Cross-Site Request Forgery
 * ==========================================================
 * 
 * Menyediakan mekanisme token CSRF untuk melindungi form
 * dari serangan CSRF (permintaan palsu lintas situs).
 * 
 * Alur kerja:
 * 1. generate() → buat token acak, simpan di session
 * 2. field()    → output <input hidden> berisi token untuk form
 * 3. validate() → verifikasi token dari POST request
 */

class Csrf
{
    /**
     * Membuat token CSRF dan menyimpannya di session.
     * 
     * Fungsi:
     * - Menghasilkan token acak 64 karakter hex (32 bytes)
     * - Hanya membuat token baru jika session belum memilikinya
     * - Token disimpan di $_SESSION['csrf_token']
     *
     * @return string Token CSRF yang aktif
     */
    public static function generate()
    {
        if (!session_id()) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Menghasilkan input hidden HTML berisi token CSRF.
     * 
     * Fungsi:
     * - Dipanggil di dalam form HTML
     * - Otomatis memanggil generate() untuk mendapat token
     * - Token di-escape dengan htmlspecialchars agar aman
     *
     * Contoh penggunaan di view:
     *   <?= Csrf::field() ?>
     *
     * @return string Tag <input type="hidden"> berisi token
     */
    public static function field()
    {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Memvalidasi token CSRF dari POST request.
     * 
     * Fungsi:
     * - Mengambil csrf_token dari $_POST
     * - Membandingkan dengan token di session (timing-safe)
     * - Jika tidak valid: tampilkan error 403 dan hentikan eksekusi
     * - Jika valid: return true, eksekusi controller dilanjutkan
     *
     * @return bool True jika token valid
     */
    public static function validate()
    {
        if (!session_id()) {
            session_start();
        }

        $token = $_POST['csrf_token'] ?? '';

        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die("<div style='background:#f8d7da;color:#721c24;padding:20px;margin:20px;border-radius:6px;font-family:Arial;'>
                ❌ <strong>CSRF token tidak valid.</strong> Akses ditolak.<br><br>
                <a href='javascript:history.back()' style='color:#721c24;'>← Kembali</a>
            </div>");
        }

        return true;
    }
}
