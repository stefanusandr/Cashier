<?php
namespace SmartKasir\Services;

use SmartKasir\Models\Order;
use SmartKasir\Models\OrderDetail;
use SmartKasir\Config\Database;
use Exception;
use PDO;

/**
 * Class TransactionService
 * Mengelola kalkulasi subtotal, total tagihan, validasi pembayaran tunai,
 * serta eksekusi transaksi basis data ACID untuk POS (Sprint 2 - SKPL-F04, F05, F06, NFR-REL-01).
 */
class TransactionService
{
    private ProductService $productService;

    public function __construct(?ProductService $productService = null)
    {
        $this->productService = $productService ?? new ProductService();
    }

    /**
     * Menghitung subtotal dari harga satuan dikalikan kuantitas (SKPL-F04).
     */
    public function calculateSubtotal(float $unitPrice, int $qty): float
    {
        if ($qty <= 0) {
            return 0.0;
        }
        return round($unitPrice * $qty, 2);
    }

    /**
     * Menjumlahkan seluruh subtotal belanjaan menjadi total belanja akhir (SKPL-F05).
     *
     * @param OrderDetail[]|array $cartItems
     * @return float
     */
    public function calculateGrandTotal(array $cartItems): float
    {
        $total = 0.0;
        foreach ($cartItems as $item) {
            if ($item instanceof OrderDetail) {
                $total += $item->getSubtotal();
            } elseif (is_array($item) && isset($item['subtotal'])) {
                $total += (float)$item['subtotal'];
            }
        }
        return round($total, 2);
    }

    /**
     * Memvalidasi apakah uang bayar cukup untuk melunasi total belanja (SKPL-F06).
     */
    public function validatePayment(float $grandTotal, float $paidAmount): bool
    {
        return $paidAmount >= $grandTotal && $grandTotal >= 0;
    }

    /**
     * Menghitung nominal kembalian yang harus diserahkan ke pelanggan (SKPL-F06).
     */
    public function calculateChange(float $grandTotal, float $paidAmount): float
    {
        if (!$this->validatePayment($grandTotal, $paidAmount)) {
            return 0.0;
        }
        return round($paidAmount - $grandTotal, 2);
    }

    /**
     * Menjalankan penyimpanan transaksi menggunakan mekanisme ACID Database Transaction (NFR-REL-01).
     * Memasukkan data ke tabel orders, order_details, dan memotong stock_qty.
     *
     * @param Order $order
     * @return bool
     * @throws Exception
     */
    public function commitTransaction(Order $order): bool
    {
        $pdo = Database::getConnection();

        try {
            $pdo->beginTransaction();

            // 1. Simpan Header Order
            $stmtOrder = $pdo->prepare("
                INSERT INTO orders (order_id, order_date, user_id, total_amount, paid_amount, change_amount)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmtOrder->execute([
                $order->getOrderId(),
                $order->getOrderDate(),
                $order->getUserId(),
                $order->getTotalAmount(),
                $order->getPaidAmount(),
                $order->getChangeAmount()
            ]);

            // 2. Simpan setiap detail dan potong stok
            $stmtDetail = $pdo->prepare("
                INSERT INTO order_details (order_id, item_code, quantity, subtotal)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($order->getItems() as $detail) {
                $stmtDetail->execute([
                    $order->getOrderId(),
                    $detail->getItemCode(),
                    $detail->getQuantity(),
                    $detail->getSubtotal()
                ]);

                // Potong stok produk
                $item = $this->productService->getItem($detail->getItemCode());
                if (!$item || !$item->canDeduct($detail->getQuantity())) {
                    throw new Exception("Stok untuk produk '{$detail->getItemCode()}' tidak mencukupi.");
                }

                $this->productService->updateStock(
                    $detail->getItemCode(),
                    -$detail->getQuantity(),
                    'SALE',
                    $order->getOrderId()
                );
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
