<?php
class Auth {
    public static function check() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?url=auth/index");
            exit;
        }
    }

    public static function admin() {
        self::check();
        if ($_SESSION['user']['role'] != 'admin') {
            header("Location: index.php?url=dashboard/index");
            exit;
        }
    }

    public static function adminOrOwner() {
        self::check();
        $role = $_SESSION['user']['role'];
        if ($role != 'admin' && $role != 'owner') {
            header("Location: index.php?url=dashboard/index");
            exit;
        }
    }

    public static function petugas() {
        self::check();
        if ($_SESSION['user']['role'] != 'petugas') {
            header("Location: index.php?url=dashboard/index");
            exit;
        }
    }

    public static function owner() {
        self::check();
        if ($_SESSION['user']['role'] != 'owner') {
            header("Location: index.php?url=dashboard/index");
            exit;
        }
    }
}
