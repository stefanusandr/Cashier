<?php
namespace SmartKasir\Controllers;

use SmartKasir\Services\ProductService;
use SmartKasir\Utils\SessionManager;
use SmartKasir\Models\Item;

/**
 * Class ItemController
 * Mengelola antarmuka dan manipulasi data barang/produk ritel (Skeleton Sprint 3).
 * Sesuai kebutuhan SKPL-F08.
 */
class ItemController
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    /**
     * Menampilkan daftar master barang
     */
    public function index(): void
    {
        if (!SessionManager::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $items = $this->productService->getAllProducts();
        $user = SessionManager::getUser();
        $pageTitle = 'Manajemen Produk - SmartKasir POS';

        // Skeleton view render (akan dilengkapi pada Sprint 3)
        require_once __DIR__ . '/../views/items/index.php';
    }

    /**
     * Menyimpan barang baru atau pembaruan barang
     */
    public function store(): void
    {
        // Logika simpan produk Sprint 3
    }

    /**
     * Menyesuaikan stok barang
     */
    public function adjustStock(): void
    {
        // Logika penyesuaian stok Sprint 3
    }
}
