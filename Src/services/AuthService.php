<?php
namespace SmartKasir\Services;

use SmartKasir\Models\User;
use SmartKasir\Utils\SessionManager;

/**
 * Class AuthService
 * Bertanggung jawab atas logika bisnis autentikasi, enkripsi password, dan verifikasi sesi.
 * Mengimplementasikan kebutuhan SKPL-F01, SKPL-F02, dan NFR-SEC-01 (Sprint 1).
 */
class AuthService
{
    /**
     * Memproses autentikasi pengguna berdasarkan username dan kata sandi mentah.
     *
     * @param string $username
     * @param string $rawPassword
     * @return array [success => bool, message => string, user => ?User]
     */
    public function login(string $username, string $rawPassword): array
    {
        $username = trim($username);
        $rawPassword = trim($rawPassword);

        // Validasi input awal
        if (empty($username) || empty($rawPassword)) {
            return [
                'success' => false,
                'message' => 'Username dan kata sandi wajib diisi.',
                'user' => null,
            ];
        }

        // Cari user di database melalui model User
        $user = User::findByUsername($username);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Username atau kata sandi tidak valid.',
                'user' => null,
            ];
        }

        // Periksa status aktif akun
        if (!$user->isActive()) {
            return [
                'success' => false,
                'message' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.',
                'user' => null,
            ];
        }

        // Verifikasi kecocokan password menggunakan BCrypt (NFR-SEC-01)
        if (!$user->verifyPassword($rawPassword)) {
            return [
                'success' => false,
                'message' => 'Username atau kata sandi tidak valid.',
                'user' => null,
            ];
        }

        // Ambil informasi toko terkait
        $store = $user->getStore();
        $storeName = $store ? $store->getStoreName() : 'Cabang Utama';

        // Daftarkan sesi pengguna melalui SessionManager
        SessionManager::setUser([
            'user_id' => $user->getUserId(),
            'username' => $user->getUsername(),
            'full_name' => $user->getFullName(),
            'role' => $user->getRole(),
            'store_id' => $user->getStoreId(),
            'store_name' => $storeName,
        ]);

        return [
            'success' => true,
            'message' => 'Autentikasi berhasil.',
            'user' => $user,
        ];
    }

    /**
     * Mengakhiri sesi pengguna aktif dan membersihkan cookie otentikasi.
     */
    public function logout(): void
    {
        SessionManager::destroy();
    }

    /**
     * Menghasilkan hash BCrypt aman dari password mentah (NFR-SEC-01).
     *
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }
}
