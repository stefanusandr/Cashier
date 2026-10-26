<?php
namespace SmartKasir\Utils;

/**
 * Class SessionManager
 * Mengelola siklus sesi pengguna, autentikasi state, role checking, dan flash alert.
 */
class SessionManager
{
    /**
     * Memulai sesi secara aman jika belum aktif
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (!headers_sent()) {
                ini_set('session.cookie_httponly', '1');
                ini_set('session.use_only_cookies', '1');
                session_start();
            } else {
                @session_start();
            }
        }
    }

    /**
     * Menyimpan data login user ke dalam session
     */
    public static function setUser(array $userData): void
    {
        self::start();
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id(true);
        }

        $_SESSION['user_id'] = $userData['user_id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['full_name'] = $userData['full_name'];
        $_SESSION['role'] = $userData['role'];
        $_SESSION['store_id'] = $userData['store_id'];
        $_SESSION['store_name'] = $userData['store_name'] ?? 'Cabang Utama';
        $_SESSION['is_logged_in'] = true;
        $_SESSION['login_time'] = time();
    }

    /**
     * Mengambil seluruh data user yang sedang login
     */
    public static function getUser(): ?array
    {
        self::start();
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'user_id' => $_SESSION['user_id'] ?? 0,
            'username' => $_SESSION['username'] ?? '',
            'full_name' => $_SESSION['full_name'] ?? '',
            'role' => $_SESSION['role'] ?? '',
            'store_id' => $_SESSION['store_id'] ?? '',
            'store_name' => $_SESSION['store_name'] ?? '',
            'login_time' => $_SESSION['login_time'] ?? null,
        ];
    }

    /**
     * Memeriksa apakah ada user yang aktif login
     */
    public static function isLoggedIn(): bool
    {
        self::start();
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }

    /**
     * Memeriksa apakah user memiliki role tertentu (ADMIN atau CASHIER)
     */
    public static function hasRole(string $role): bool
    {
        self::start();
        return self::isLoggedIn() && isset($_SESSION['role']) && strtoupper($_SESSION['role']) === strtoupper($role);
    }

    /**
     * Mengakhiri sesi (Logout) dan membersihkan data pengguna dengan tetap mempertahankan flash alert
     */
    public static function destroy(): void
    {
        self::start();
        $flash = $_SESSION['flash'] ?? null;
        $_SESSION = [];
        if ($flash !== null) {
            $_SESSION['flash'] = $flash;
        }
    }

    /**
     * Menyimpan pesan flash (notifikasi sekali pakai)
     */
    public static function setFlash(string $type, string $message): void
    {
        self::start();
        $_SESSION['flash'] = [
            'type' => $type, // 'success', 'danger', 'warning', 'info'
            'message' => $message
        ];
    }

    /**
     * Mengambil dan menghapus pesan flash
     */
    public static function getFlash(): ?array
    {
        self::start();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
