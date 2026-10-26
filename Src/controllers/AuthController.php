<?php
namespace SmartKasir\Controllers;

use SmartKasir\Services\AuthService;
use SmartKasir\Utils\SessionManager;

/**
 * Class AuthController
 * Menangani alur navigasi dan HTTP request autentikasi (Login & Logout).
 * Memenuhi kriteria Sprint 1 (UC-01).
 */
class AuthController
{
    private AuthService $authService;

    public function __construct(?AuthService $authService = null)
    {
        $this->authService = $authService ?? new AuthService();
    }

    /**
     * Menampilkan tampilan Form Login (IMK).
     * Jika pengguna sudah aktif login, langsung diarahkan ke Dashboard.
     */
    public function showLoginForm(): void
    {
        if (SessionManager::isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }

        $flash = SessionManager::getFlash();
        $pageTitle = 'Login - SmartKasir POS';

        // Render view
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Memproses permintaan POST login dari formulir.
     */
    public function processLogin(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $result = $this->authService->login($username, $password);

        if ($result['success']) {
            SessionManager::setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($result['user']->getFullName()) . '!');
            header('Location: /dashboard');
            exit;
        } else {
            SessionManager::setFlash('danger', $result['message']);
            header('Location: /login');
            exit;
        }
    }

    /**
     * Memproses permintaan logout pengguna dan membersihkan sesi.
     */
    public function logout(): void
    {
        $this->authService->logout();
        SessionManager::setFlash('info', 'Anda berhasil keluar dari sistem.');
        header('Location: /login');
        exit;
    }
}
