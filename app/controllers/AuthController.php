<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../helpers/Csrf.php';

class AuthController
{
    public function index()
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Role-specific login pages
    public function login_admin()
    {
        require_once __DIR__ . '/../views/auth/login_admin.php';
    }

    public function login_petugas()
    {
        require_once __DIR__ . '/../views/auth/login_petugas.php';
    }

    public function login_owner()
    {
        require_once __DIR__ . '/../views/auth/login_owner.php';
    }

    public function proses()
    {
        Csrf::validate();
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? ''; // optional role from role-specific login pages

        if (empty($username) || empty($password)) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin: 20px;'>
                    ❌ Username dan password tidak boleh kosong<br>
                    <a href='index.php?url=auth/index' style='color: #721c24; text-decoration: underline;'>Kembali ke Login</a>
                  </div>";
            return;
        }

        $user = User::findByCredentials($username, $password, $role);

        if ($user) {
            $_SESSION['user'] = $user;
            try {
                Log::create($user['id_user'], 'login', json_encode(['role' => $user['role'], 'timestamp' => date('Y-m-d H:i:s')]));
            }
            catch (Exception $e) {
            // Log error tidak perlu menghentikan proses login
            }
            header("Location: index.php?url=dashboard/index");
            exit;
        }
        else {
            try {
                Log::create(null, 'login_failed', json_encode(['username' => $username, 'role' => $role, 'timestamp' => date('Y-m-d H:i:s')]));
            }
            catch (Exception $e) {
            // Log error tidak perlu menghentikan proses
            }
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

    public function logout()
    {
        $userId = $_SESSION['user']['id_user'] ?? 0;
        try {
            Log::create($userId, 'logout', json_encode(['timestamp' => date('Y-m-d H:i:s')]));
        }
        catch (Exception $e) {
        // Log error tidak perlu menghentikan proses logout
        }
        session_destroy();
        header("Location: index.php?url=auth/index");
        exit;
    }
}
