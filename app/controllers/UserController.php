<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../helpers/Flash.php';
require_once __DIR__ . '/../helpers/Csrf.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../config/database.php';

class UserController
{
    public function index()
    {
        Auth::admin();
        $data = User::all();
        require_once __DIR__ . '/../views/user/index.php';
    }

    public function create()
    {
        Auth::admin();
        require_once __DIR__ . '/../views/user/create.php';
    }

    public function store()
    {
        Auth::admin();
        Csrf::validate();
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = $_POST['role'] ?? 'petugas';

        if (empty($username) || empty($password)) {
            Flash::set('error', 'Username dan password tidak boleh kosong.');
            header("Location: index.php?url=user/create");
            exit;
        }

        // cek duplikasi username
        $db = Database::connect();
        $u = mysqli_real_escape_string($db, $username);
        $check = mysqli_query($db, "SELECT id_user FROM tb_user WHERE username='$u'");
        if ($check && mysqli_num_rows($check) > 0) {
            Flash::set('error', 'Username sudah digunakan.');
            header("Location: index.php?url=user/create");
            exit;
        }

        $ok = User::create($username, $password, $role);
        if ($ok) {
            Flash::set('success', 'User berhasil dibuat.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'create_user', json_encode(['username' => $username, 'role' => $role]));
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal membuat user: ' . mysqli_error($db));
        }
        header("Location: index.php?url=user/index");
        exit;
    }

    public function edit()
    {
        Auth::admin();
        $id = $_GET['id'] ?? 0;
        $user = User::find($id);
        if (!$user) {
            Flash::set('error', 'User tidak ditemukan.');
            header("Location: index.php?url=user/index");
            exit;
        }
        require_once __DIR__ . '/../views/user/edit.php';
    }

    public function update()
    {
        Auth::admin();
        Csrf::validate();
        $id = $_POST['id'] ?? 0;
        $username = trim($_POST['username'] ?? '');
        $role = $_POST['role'] ?? 'petugas';
        $password = trim($_POST['password'] ?? '');

        if (empty($username)) {
            Flash::set('error', 'Username tidak boleh kosong.');
            header("Location: index.php?url=user/edit&id={$id}");
            exit;
        }

        // Cek duplikasi username (kecuali milik sendiri)
        $db = Database::connect();
        $u = mysqli_real_escape_string($db, $username);
        $check = mysqli_query($db, "SELECT id_user FROM tb_user WHERE username='$u' AND id_user != $id");
        if ($check && mysqli_num_rows($check) > 0) {
            Flash::set('error', 'Username sudah digunakan user lain.');
            header("Location: index.php?url=user/edit&id={$id}");
            exit;
        }

        $ok = User::update($id, $username, $role, $password);
        if ($ok) {
            Flash::set('success', 'User berhasil diperbarui.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'update_user', json_encode(['id' => $id, 'username' => $username]));
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal memperbarui user: ' . mysqli_error($db));
        }
        header("Location: index.php?url=user/index");
        exit;
    }

    public function delete()
    {
        Auth::admin();
        Csrf::validate();
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            Flash::set('error', 'ID user tidak valid.');
            header("Location: index.php?url=user/index");
            exit;
        }
        $ok = User::delete($id);
        if ($ok) {
            Flash::set('success', 'User berhasil dihapus.');
            Log::create($_SESSION['user']['id_user'] ?? null, 'delete_user', json_encode(['id' => $id]));
        }
        else {
            $db = Database::connect();
            Flash::set('error', 'Gagal menghapus user: ' . mysqli_error($db));
        }
        header("Location: index.php?url=user/index");
        exit;
    }
}