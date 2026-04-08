<?php
/**
 * ==========================================================
 * Model User - Pengelolaan Data Pengguna Sistem
 * ==========================================================
 * 
 * Mengelola data tabel `tb_user` (admin, petugas, owner).
 * Menyediakan operasi CRUD dan autentikasi login.
 * 
 * Tabel: tb_user
 * Kolom: id_user, username, password, role, status_aktif, created_at
 */

require_once __DIR__ . '/../config/database.php';

class User
{
    /**
     * Mengambil semua data user, diurutkan dari yang terbaru.
     * 
     * Fungsi:
     * - Menampilkan daftar seluruh user di halaman kelola user
     * - Diurutkan berdasarkan id_user DESC (terbaru di atas)
     *
     * @return mysqli_result|false Hasil query berisi semua user
     */
    public static function all()
    {
        $db = Database::connect();
        return mysqli_query($db, "SELECT * FROM tb_user ORDER BY id_user DESC");
    }

    /**
     * Mencari satu user berdasarkan ID.
     * 
     * Fungsi:
     * - Dipakai saat edit user atau menampilkan detail
     * - ID di-cast ke integer untuk keamanan
     *
     * @param int $id ID user yang dicari
     * @return array|null Data user sebagai array asosiatif, atau null
     */
    public static function find($id)
    {
        $db = Database::connect();
        $id = intval($id);
        $result = mysqli_query($db, "SELECT * FROM tb_user WHERE id_user = $id");
        return $result ? mysqli_fetch_assoc($result) : null;
    }

    /**
     * Mencari user berdasarkan username dan password untuk login.
     * 
     * Fungsi:
     * - Mencocokkan kredensial login pengguna
     * - Opsional: filter berdasarkan role (untuk halaman login per-role)
     * - Memverifikasi password secara plain text
     * - Mengecek status_aktif (hanya user aktif yang bisa login)
     * 
     * Catatan Keamanan:
     * - Password disimpan plain text (sebaiknya gunakan password_hash
     *   untuk produksi)
     *
     * @param string $username Username yang diinput
     * @param string $password Password yang diinput
     * @param string $role     (Opsional) Filter role tertentu
     * @return array|false Data user jika cocok, atau false jika gagal
     */
    public static function findByCredentials($username, $password, $role = '')
    {
        $db = Database::connect();
        $username = mysqli_real_escape_string($db, $username);

        $sql = "SELECT * FROM tb_user WHERE username = '$username'";

        // Jika role ditentukan, tambahkan filter (login per-role)
        if (!empty($role)) {
            $role = mysqli_real_escape_string($db, $role);
            $sql .= " AND role = '$role'";
        }

        $result = mysqli_query($db, $sql);

        if (!$result) {
            error_log("Database error in findByCredentials: " . mysqli_error($db));
            return false;
        }

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password (plain text)
            if ($password === $user['password']) {
                // Pastikan user dalam status aktif
                if ($user['status_aktif'] == 1) {
                    return $user;
                }
            }
        }

        return false;
    }

    /**
     * Membuat user baru di database.
     * 
     * Fungsi:
     * - Menyisipkan data user baru ke tb_user
     * - Semua input di-escape untuk mencegah SQL injection
     * - Status default: aktif (1)
     *
     * @param string $username Username baru
     * @param string $password Password baru
     * @param string $role     Role: 'admin', 'petugas', atau 'owner'
     * @return bool True jika berhasil
     */
    public static function create($username, $password, $role)
    {
        $db = Database::connect();
        $username = mysqli_real_escape_string($db, $username);
        $password = mysqli_real_escape_string($db, $password);
        $role = mysqli_real_escape_string($db, $role);

        $sql = "INSERT INTO tb_user (username, password, role, status_aktif) 
                VALUES ('$username', '$password', '$role', 1)";
        return mysqli_query($db, $sql);
    }

    /**
     * Memperbarui data user berdasarkan ID.
     * 
     * Fungsi:
     * - Mengubah username dan role user
     * - Password hanya diubah jika diisi (tidak kosong)
     * - Jika password kosong, password lama dipertahankan
     *
     * @param int    $id       ID user yang diubah
     * @param string $username Username baru
     * @param string $role     Role baru
     * @param string $password (Opsional) Password baru, kosong = tidak diubah
     * @return bool True jika berhasil
     */
    public static function update($id, $username, $role, $password = '')
    {
        $db = Database::connect();
        $id = intval($id);
        $username = mysqli_real_escape_string($db, $username);
        $role = mysqli_real_escape_string($db, $role);

        $sql = "UPDATE tb_user SET username='$username', role='$role'";

        if (!empty($password)) {
            $password = mysqli_real_escape_string($db, $password);
            $sql .= ", password='$password'";
        }

        $sql .= " WHERE id_user=$id";
        return mysqli_query($db, $sql);
    }

    /**
     * Menghapus user berdasarkan ID.
     * 
     * Fungsi:
     * - Menghapus satu record user dari tb_user
     * - ID di-cast ke integer untuk keamanan
     *
     * @param int $id ID user yang dihapus
     * @return bool True jika berhasil
     */
    public static function delete($id)
    {
        $db = Database::connect();
        $id = intval($id);
        return mysqli_query($db, "DELETE FROM tb_user WHERE id_user=$id");
    }
}