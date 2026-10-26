<?php
namespace SmartKasir\Models;

use SmartKasir\Config\Database;
use PDO;

/**
 * Class Item
 * Merepresentasikan entitas produk/barang ritel pada sistem SmartKasir.
 */
class Item
{
    private string $itemCode;
    private string $itemName;
    private string $unit;
    private float $unitPrice;
    private int $minStock;
    private int $stockQty;
    private ?string $createdAt;

    public function __construct(
        string $itemCode = '',
        string $itemName = '',
        string $unit = 'Pcs',
        float $unitPrice = 0.0,
        int $minStock = 5,
        int $stockQty = 0,
        ?string $createdAt = null
    ) {
        $this->itemCode = $itemCode;
        $this->itemName = $itemName;
        $this->unit = $unit;
        $this->unitPrice = $unitPrice;
        $this->minStock = $minStock;
        $this->stockQty = $stockQty;
        $this->createdAt = $createdAt;
    }

    // --- Access Modifier: Public Getters & Setters ---

    public function getItemCode(): string
    {
        return $this->itemCode;
    }

    public function setItemCode(string $itemCode): void
    {
        $this->itemCode = $itemCode;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }

    public function setItemName(string $itemName): void
    {
        $this->itemName = $itemName;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    public function getMinStock(): int
    {
        return $this->minStock;
    }

    public function setMinStock(int $minStock): void
    {
        $this->minStock = $minStock;
    }

    public function getStockQty(): int
    {
        return $this->stockQty;
    }

    public function setStockQty(int $stockQty): void
    {
        $this->stockQty = $stockQty;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * Memeriksa apakah stok saat ini berada di bawah batas minimum
     */
    public function isLowStock(): bool
    {
        return $this->stockQty <= $this->minStock;
    }

    /**
     * Memeriksa apakah kuantitas yang diminta dapat dipenuhi oleh stok
     */
    public function canDeduct(int $qty): bool
    {
        return $qty > 0 && $this->stockQty >= $qty;
    }

    public function toArray(): array
    {
        return [
            'item_code' => $this->itemCode,
            'item_name' => $this->itemName,
            'unit' => $this->unit,
            'unit_price' => $this->unitPrice,
            'min_stock' => $this->minStock,
            'stock_qty' => $this->stockQty,
            'is_low_stock' => $this->isLowStock(),
            'created_at' => $this->createdAt,
        ];
    }

    // --- Static Data Access Methods ---

    public static function findByCode(string $itemCode): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM items WHERE item_code = ? LIMIT 1");
        $stmt->execute([$itemCode]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new self(
            $row['item_code'],
            $row['item_name'],
            $row['unit'],
            (float)$row['unit_price'],
            (int)$row['min_stock'],
            (int)$row['stock_qty'],
            $row['created_at'] ?? null
        );
    }

    public static function getAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM items ORDER BY item_code ASC");
        $items = [];
        while ($row = $stmt->fetch()) {
            $items[] = new self(
                $row['item_code'],
                $row['item_name'],
                $row['unit'],
                (float)$row['unit_price'],
                (int)$row['min_stock'],
                (int)$row['stock_qty'],
                $row['created_at'] ?? null
            );
        }
        return $items;
    }
}
