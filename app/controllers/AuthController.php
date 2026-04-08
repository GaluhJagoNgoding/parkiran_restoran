<?php
/**
 * ==========================================================
 * AuthController - Mengelola Proses Autentikasi Pengguna
 * ==========================================================
 * 
 * Menangani login, logout, dan tampilan halaman login.
 * Mendukung login umum (semua role) dan login per-role 
 * (admin, petugas, owner memiliki halaman login tersendiri).
 * 
 * Routes:
 * - auth/index          → Halaman login umum
 * - auth/login_admin    → Halaman login khusus admin
 * - auth/login_petugas  → Halaman login khusus petugas
 * - auth/login_owner    → Halaman login khusus owner
 * - auth/proses         → Proses verifikasi login (POST)
 * - auth/logout         → Proses logout
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../helpers/Csrf.php';

class AuthController
{
    /**
     * Menampilkan halaman login umum (semua role).
     * 
     * @return void
     */
    public function index()
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Menampilkan halaman login khusus Admin.
     * 
     * @return void
     */
    public function login_admin()
    {
        require_once __DIR__ . '/../views/auth/login_admin.php';
    }

    /**
     * Menampilkan halaman login khusus Petugas.
     * 
     * @return void
     */
    public function login_petugas()
    {
        require_once __DIR__ . '/../views/auth/login_petugas.php';
    }

    /**
     * Menampilkan halaman login khusus Owner.
     * 
     * @return void
     */
    public function login_owner()
    {
        require_once __DIR__ . '/../views/auth/login_owner.php';
    }

    /**
     * Memproses form login (POST request).
     * 
     * Fungsi:
     * 1. Validasi CSRF token
     * 2. Ambil username, password, dan role dari form
     * 3. Cek credential via User::findByCredentials()
     * 4. Jika berhasil → simpan user ke session → redirect ke dashboard
     * 5. Jika gagal → tampilkan pesan error dengan kredensial default
     * 6. Catat setiap percobaan login ke log aktivitas
     * 
     * @return void
     */
    public function proses()
    {
        Csrf::validate();

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? '';

        // Validasi input tidak boleh kosong
        if (empty($username) || empty($password)) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin: 20px;'>
                    ❌ Username dan password tidak boleh kosong<br>
                    <a href='index.php?url=auth/index' style='color: #721c24; text-decoration: underline;'>Kembali ke Login</a>
                  </div>";
            return;
        }

        // Cari user berdasarkan kredensial
        $user = User::findByCredentials($username, $password, $role);

        if ($user) {
            // Login berhasil: simpan data user ke session
            $_SESSION['user'] = $user;

            // Catat log login berhasil
            try {
                Log::create($user['id_user'], 'login', json_encode([
                    'role'      => $user['role'],
                    'timestamp' => date('Y-m-d H:i:s')
                ]));
            } catch (Exception $e) {
                // Error log tidak boleh menghentikan proses login
            }

            header("Location: index.php?url=dashboard/index");
            exit;
        } else {
            // Login gagal: catat ke log
            try {
                Log::create(null, 'login_failed', json_encode([
                    'username'  => $username,
                    'role'      => $role,
                    'timestamp' => date('Y-m-d H:i:s')
                ]));
            } catch (Exception $e) {
                // Error log tidak boleh menghentikan proses
            }

            // Tampilkan pesan error beserta kredensial default
            echo "<div style='background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin: 20px;'>
                    ❌ Login gagal! Username atau password salah.<br><br>
                    <strong>Kredensial default:</strong><br>
                    Admin: admin / admin123<br>
                    Petugas: petugas / petugas123<br>
                    Owner: owner / owner123<br><br>
                    <a href='index.php?url=auth/index' style='color: #721c24; text-decoration: underline;'>Kembali ke Login</a>
                  </div>";
        }
    }

    /**
     * Memproses logout pengguna.
     * 
     * Fungsi:
     * 1. Catat log logout
     * 2. Hancurkan session (session_destroy)
     * 3. Redirect ke halaman login
     * 
     * @return void
     */
    public function logout()
    {
        $userId = $_SESSION['user']['id_user'] ?? 0;

        // Catat log logout sebelum session dihancurkan
        try {
            Log::create($userId, 'logout', json_encode([
                'timestamp' => date('Y-m-d H:i:s')
            ]));
        } catch (Exception $e) {
            // Error log tidak boleh menghentikan proses logout
        }

        session_destroy();
        header("Location: index.php?url=auth/index");
        exit;
    }
}
