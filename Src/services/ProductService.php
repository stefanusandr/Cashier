<?php
namespace SmartKasir\Services;

use SmartKasir\Models\Item;
use SmartKasir\Models\StockLog;
use SmartKasir\Config\Database;
use PDO;

/**
 * Class ProductService
 * Menangani logika bisnis master data barang dan pembaruan inventaris (Sprint 2 & 3).
 * Sesuai spesifikasi DPPL 3.2.
 */
class ProductService
{
    /**
     * Mengambil data produk berdasarkan kode barang (SKPL-F03, NFR-PERF-01).
     *
     * @param string $itemCode
     * @return Item|null
     */
    public function getItem(string $itemCode): ?Item
    {
        return Item::findByCode($itemCode);
    }

    /**
     * Memperbarui kuantitas stok produk dan mencatat riwayat perubahan (SKPL-F08).
     *
     * @param string $itemCode
     * @param int $deltaQty Perubahan kuantitas (positif untuk restock, negatif untuk pengurangan)
     * @param string $type Jenis mutasi ('SALE', 'RESTOCK', 'ADJUSTMENT')
     * @param string|null $referenceId Referensi nomor transaksi atau DO
     * @return bool
     */
    public function updateStock(string $itemCode, int $deltaQty, string $type = 'ADJUSTMENT', ?string $referenceId = null): bool
    {
        $item = $this->getItem($itemCode);
        if (!$item) {
            return false;
        }

        $prevStock = $item->getStockQty();
        $newStock = $prevStock + $deltaQty;

        if ($newStock < 0) {
            return false; // Stok tidak boleh negatif
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE items SET stock_qty = ? WHERE item_code = ?");
        $success = $stmt->execute([$newStock, $itemCode]);

        if ($success) {
            StockLog::create($itemCode, $deltaQty, $prevStock, $newStock, $type, $referenceId);
        }

        return $success;
    }

    /**
     * Menyimpan produk baru atau memperbarui data produk yang ada (SKPL-F08).
     *
     * @param Item $item
     * @return bool
     */
    public function saveProduct(Item $item): bool
    {
        $pdo = Database::getConnection();
        $existing = $this->getItem($item->getItemCode());

        if ($existing) {
            $stmt = $pdo->prepare("
                UPDATE items 
                SET item_name = ?, unit = ?, unit_price = ?, min_stock = ?, stock_qty = ? 
                WHERE item_code = ?
            ");
            return $stmt->execute([
                $item->getItemName(),
                $item->getUnit(),
                $item->getUnitPrice(),
                $item->getMinStock(),
                $item->getStockQty(),
                $item->getItemCode()
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO items (item_code, item_name, unit, unit_price, min_stock, stock_qty)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([
                $item->getItemCode(),
                $item->getItemName(),
                $item->getUnit(),
                $item->getUnitPrice(),
                $item->getMinStock(),
                $item->getStockQty()
            ]);
        }
    }

    /**
     * Mengambil seluruh daftar barang
     *
     * @return Item[]
     */
    public function getAllProducts(): array
    {
        return Item::getAll();
    }
}
