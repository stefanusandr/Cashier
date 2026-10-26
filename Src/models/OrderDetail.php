<?php
namespace SmartKasir\Models;

use SmartKasir\Config\Database;
use PDO;

/**
 * Class OrderDetail
 * Merepresentasikan item baris rinci dalam sebuah pesanan/transaksi.
 */
class OrderDetail
{
    private ?int $detailId;
    private string $orderId;
    private string $itemCode;
    private int $quantity;
    private float $subtotal;

    // Relasi opsional ke objek Item
    private ?Item $item = null;

    public function __construct(
        ?int $detailId = null,
        string $orderId = '',
        string $itemCode = '',
        int $quantity = 1,
        float $subtotal = 0.0
    ) {
        $this->detailId = $detailId;
        $this->orderId = $orderId;
        $this->itemCode = $itemCode;
        $this->quantity = $quantity;
        $this->subtotal = $subtotal;
    }

    // --- Access Modifier: Public Getters & Setters ---

    public function getDetailId(): ?int
    {
        return $this->detailId;
    }

    public function setDetailId(?int $detailId): void
    {
        $this->detailId = $detailId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getItemCode(): string
    {
        return $this->itemCode;
    }

    public function setItemCode(string $itemCode): void
    {
        $this->itemCode = $itemCode;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    public function getItem(): ?Item
    {
        if ($this->item === null && !empty($this->itemCode)) {
            $this->item = Item::findByCode($this->itemCode);
        }
        return $this->item;
    }

    public function setItem(?Item $item): void
    {
        $this->item = $item;
        if ($item !== null) {
            $this->itemCode = $item->getItemCode();
        }
    }

    public function toArray(): array
    {
        return [
            'detail_id' => $this->detailId,
            'order_id' => $this->orderId,
            'item_code' => $this->itemCode,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'item' => $this->getItem() ? $this->getItem()->toArray() : null,
        ];
    }

    // --- Static Data Access Methods ---

    public static function findByOrderId(string $orderId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM order_details WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = new self(
                (int)$row['detail_id'],
                $row['order_id'],
                $row['item_code'],
                (int)$row['quantity'],
                (float)$row['subtotal']
            );
        }
        return $details;
    }
}
