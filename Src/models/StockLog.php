<?php
namespace SmartKasir\Models;

use SmartKasir\Config\Database;
use PDO;

/**
 * Class StockLog
 * Merepresentasikan pencatatan audit log mutasi perubahan stok barang (Sprint 2 & 3).
 */
class StockLog
{
    private ?int $logId;
    private string $itemCode;
    private int $changeQty;
    private int $previousStock;
    private int $currentStock;
    private string $type; // 'SALE' | 'RESTOCK' | 'ADJUSTMENT'
    private ?string $referenceId;
    private ?string $createdAt;

    public function __construct(
        ?int $logId = null,
        string $itemCode = '',
        int $changeQty = 0,
        int $previousStock = 0,
        int $currentStock = 0,
        string $type = 'SALE',
        ?string $referenceId = null,
        ?string $createdAt = null
    ) {
        $this->logId = $logId;
        $this->itemCode = $itemCode;
        $this->changeQty = $changeQty;
        $this->previousStock = $previousStock;
        $this->currentStock = $currentStock;
        $this->type = strtoupper($type);
        $this->referenceId = $referenceId;
        $this->createdAt = $createdAt;
    }

    // --- Access Modifier: Public Getters & Setters ---

    public function getLogId(): ?int
    {
        return $this->logId;
    }

    public function setLogId(?int $logId): void
    {
        $this->logId = $logId;
    }

    public function getItemCode(): string
    {
        return $this->itemCode;
    }

    public function setItemCode(string $itemCode): void
    {
        $this->itemCode = $itemCode;
    }

    public function getChangeQty(): int
    {
        return $this->changeQty;
    }

    public function setChangeQty(int $changeQty): void
    {
        $this->changeQty = $changeQty;
    }

    public function getPreviousStock(): int
    {
        return $this->previousStock;
    }

    public function setPreviousStock(int $previousStock): void
    {
        $this->previousStock = $previousStock;
    }

    public function getCurrentStock(): int
    {
        return $this->currentStock;
    }

    public function setCurrentStock(int $currentStock): void
    {
        $this->currentStock = $currentStock;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = strtoupper($type);
    }

    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }

    public function setReferenceId(?string $referenceId): void
    {
        $this->referenceId = $referenceId;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'log_id' => $this->logId,
            'item_code' => $this->itemCode,
            'change_qty' => $this->changeQty,
            'previous_stock' => $this->previousStock,
            'current_stock' => $this->currentStock,
            'type' => $this->type,
            'reference_id' => $this->referenceId,
            'created_at' => $this->createdAt,
        ];
    }

    // --- Static Data Access Methods ---

    public static function create(
        string $itemCode,
        int $changeQty,
        int $previousStock,
        int $currentStock,
        string $type,
        ?string $referenceId = null
    ): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO stock_logs (item_code, change_qty, previous_stock, current_stock, type, reference_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$itemCode, $changeQty, $previousStock, $currentStock, $type, $referenceId]);
    }

    public static function getByItemCode(string $itemCode, int $limit = 10): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM stock_logs WHERE item_code = ? ORDER BY created_at DESC LIMIT ?");
        $stmt->bindValue(1, $itemCode);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        $logs = [];
        while ($row = $stmt->fetch()) {
            $logs[] = new self(
                (int)$row['log_id'],
                $row['item_code'],
                (int)$row['change_qty'],
                (int)$row['previous_stock'],
                (int)$row['current_stock'],
                $row['type'],
                $row['reference_id'] ?? null,
                $row['created_at'] ?? null
            );
        }
        return $logs;
    }
}
