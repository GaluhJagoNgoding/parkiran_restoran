<?php
/**
 * ==========================================================
 * Router Utama - Entry Point Aplikasi Sistem Parkir Restoran
 * ==========================================================
 * 
 * File ini adalah satu-satunya pintu masuk (front controller)
 * untuk seluruh aplikasi. Semua request melewati file ini.
 * 
 * Format URL: index.php?url=NamaController/namaMethod
 * Contoh    : index.php?url=auth/index
 *              → AuthController → method index()
 * 
 * Fitur Keamanan:
 * - Whitelist controller yang diperbolehkan
 * - Validasi keberadaan file controller
 * - Validasi keberadaan method di controller
 * - Halaman 404 custom jika tidak ditemukan
 */

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configure session for hosting compatibility (BEFORE session_start)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

session_start();

// ─── Parse URL ──────────────────────────────────────────────
// Extract controller dan method dari parameter URL
$url = isset($_GET['url']) ? $_GET['url'] : 'auth/index';
$url = trim($url, '/');
$parts = explode('/', $url);

// Extract controller name (convert to PascalCase + Controller)
$controller_name = isset($parts[0]) ? $parts[0] : 'auth';
$controller = ucfirst($controller_name) . 'Controller';

// Extract method name (default: index)
$method = isset($parts[1]) ? $parts[1] : 'index';

// ─── Whitelist Controller ───────────────────────────────────
// Hanya controller dalam daftar ini yang bisa diakses
// Ini mencegah akses ke file/class sembarangan
$allowed_controllers = [
    'AuthController',
    'DashboardController',
    'TransaksiController',
    'UserController',
    'KendaraanController',
    'TarifController',
    'AreaController',
    'LogController'
];

if (!in_array($controller, $allowed_controllers)) {
    http_response_code(404);
    echo "<!DOCTYPE html><html><head><title>404 - Halaman Tidak Ditemukan</title>
    <style>body{font-family:Arial,sans-serif;background:#f3f6fb;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0}
    .box{background:#fff;padding:40px 50px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);text-align:center;max-width:450px}
    h1{font-size:72px;color:#2d6cdf;margin:0}h2{color:#333;margin:10px 0 20px}p{color:#666;line-height:1.6}
    a{display:inline-block;margin-top:20px;padding:10px 24px;background:#2d6cdf;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;transition:all .3s}
    a:hover{background:#1a5cc4;transform:translateY(-2px)}</style></head>
    <body><div class='box'><h1>404</h1><h2>Halaman Tidak Ditemukan</h2><p>Halaman yang Anda cari tidak tersedia.</p>
    <a href='index.php?url=auth/index'>← Kembali ke Login</a></div></body></html>";
    exit;
}

// ─── Load Controller ────────────────────────────────────────
$controllerFile = __DIR__ . "/../app/controllers/$controller.php";

if (!file_exists($controllerFile)) {
    http_response_code(404);
    echo "Controller tidak ditemukan.";
    exit;
}

require_once $controllerFile;
$obj = new $controller;

// ─── Validasi Method ────────────────────────────────────────
if (!method_exists($obj, $method)) {
    http_response_code(404);
    echo "<!DOCTYPE html><html><head><title>404 - Halaman Tidak Ditemukan</title>
    <style>body{font-family:Arial,sans-serif;background:#f3f6fb;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0}
    .box{background:#fff;padding:40px 50px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.08);text-align:center;max-width:450px}
    h1{font-size:72px;color:#2d6cdf;margin:0}h2{color:#333;margin:10px 0 20px}p{color:#666;line-height:1.6}
    a{display:inline-block;margin-top:20px;padding:10px 24px;background:#2d6cdf;color:#fff;border-radius:8px;text-decoration:none;font-weight:600;transition:all .3s}
    a:hover{background:#1a5cc4;transform:translateY(-2px)}</style></head>
    <body><div class='box'><h1>404</h1><h2>Halaman Tidak Ditemukan</h2><p>Halaman yang Anda cari tidak tersedia.</p>
    <a href='index.php?url=auth/index'>← Kembali ke Login</a></div></body></html>";
    exit;
}

// ─── Jalankan Controller & Method ───────────────────────────
$obj->$method();
