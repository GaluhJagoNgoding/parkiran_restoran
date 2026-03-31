<?php
class Csrf
{
    /**
     * Generate CSRF token dan simpan di session
     */
    public static function generate()
    {
        if (!session_id())
            session_start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Output hidden input field untuk form
     */
    public static function field()
    {
        $token = self::generate();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Validasi CSRF token dari POST request
     * Return true jika valid, redirect/die jika tidak
     */
    public static function validate()
    {
        if (!session_id())
            session_start();
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die("<div style='background:#f8d7da;color:#721c24;padding:20px;margin:20px;border-radius:6px;font-family:Arial;'>
                ❌ <strong>CSRF token tidak valid.</strong> Akses ditolak.<br><br>
                <a href='javascript:history.back()' style='color:#721c24;'>← Kembali</a>
            </div>");
        }
        // Token valid
        return true;
    }
}
