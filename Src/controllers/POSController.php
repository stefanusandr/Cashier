<?php
namespace SmartKasir\Controllers;

use SmartKasir\Services\TransactionService;
use SmartKasir\Services\ProductService;
use SmartKasir\Services\PdfReceiptService;
use SmartKasir\Utils\SessionManager;

/**
 * Class POSController
 * Controller inti transaksi kasir dan checkout penjualan (Skeleton Sprint 2).
 * Sesuai DPPL Layout 1: Layar Kasir / POS.
 */
class POSController
{
    private TransactionService $transactionService;
    private ProductService $productService;
    private PdfReceiptService $pdfService;

    public function __construct()
    {
        $this->transactionService = new TransactionService();
        $this->productService = new ProductService();
        $this->pdfService = new PdfReceiptService();
    }

    /**
     * Menampilkan antarmuka POS kasir
     */
    public function index(): void
    {
        if (!SessionManager::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $user = SessionManager::getUser();
        $pageTitle = 'Transaksi Penjualan - SmartKasir POS';

        // Skeleton view render (akan dilengkapi pada Sprint 2)
        require_once __DIR__ . '/../views/pos/index.php';
    }

    /**
     * Endpoint API pencarian barang berdasarkan barcode/kode barang
     */
    public function searchItem(): void
    {
        $code = $_GET['code'] ?? '';
        $item = $this->productService->getItem($code);

        header('Content-Type: application/json');
        echo json_encode([
            'found' => $item !== null,
            'item' => $item ? $item->toArray() : null,
        ]);
        exit;
    }

    /**
     * Memproses checkout transaksi penjualan dan cetak struk PDF
     */
    public function checkout(): void
    {
        // Logika checkout Sprint 2
    }
}
