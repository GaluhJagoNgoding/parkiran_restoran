<?php
class Flash {
    public static function set($type, $message) {
        if (!session_id()) session_start();
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    public static function get() {
        if (!session_id()) session_start();
        if (isset($_SESSION['flash'])) {
            $f = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $f;
        }
        return null;
    }
}
