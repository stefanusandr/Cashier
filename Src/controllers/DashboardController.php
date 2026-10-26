<?php
namespace SmartKasir\Controllers;

use SmartKasir\Utils\SessionManager;
use SmartKasir\Models\Item;
use SmartKasir\Models\Order;
use SmartKasir\Config\Database;

/**
 * Class DashboardController
 * Menampilkan ringkasan informasi dan beranda utama setelah autentikasi berhasil.
 */
class DashboardController
{
    /**
     * Menampilkan halaman dashboard role-based (Admin / Kasir).
     */
    public function index(): void
    {
        // Proteksi rute: Pengguna harus login terlebih dahulu
        if (!SessionManager::isLoggedIn()) {
            SessionManager::setFlash('warning', 'Silakan masuk terlebih dahulu untuk mengakses sistem.');
            header('Location: /login');
            exit;
        }

        $user = SessionManager::getUser();
        $flash = SessionManager::getFlash();
        $pageTitle = 'Dashboard - SmartKasir POS';

        // Hitung metrik sederhana
        $items = Item::getAll();
        $itemCount = count($items);

        $lowStockCount = 0;
        foreach ($items as $it) {
            if ($it->isLowStock()) {
                $lowStockCount++;
            }
        }

        $orders = Order::getAll(5);
        $orderCount = count($orders);

        $activeDriver = Database::getActiveDriver();

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
