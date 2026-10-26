<?php
namespace SmartKasir\Models;

use SmartKasir\Config\Database;
use PDO;

/**
 * Class Order
 * Merepresentasikan transaksi penjualan kasir (Header Pesanan) yang menampung relasi detail belanja.
 */
class Order
{
    private string $orderId;
    private string $orderDate;
    private int $userId;
    private float $totalAmount;
    private float $paidAmount;
    private float $changeAmount;

    /** @var OrderDetail[] */
    private array $items = [];

    public function __construct(
        string $orderId = '',
        string $orderDate = '',
        int $userId = 0,
        float $totalAmount = 0.0,
        float $paidAmount = 0.0,
        float $changeAmount = 0.0
    ) {
        $this->orderId = $orderId;
        $this->orderDate = $orderDate;
        $this->userId = $userId;
        $this->totalAmount = $totalAmount;
        $this->paidAmount = $paidAmount;
        $this->changeAmount = $changeAmount;
    }

    // --- Access Modifier: Public Getters & Setters ---

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getOrderDate(): string
    {
        return $this->orderDate;
    }

    public function setOrderDate(string $orderDate): void
    {
        $this->orderDate = $orderDate;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getPaidAmount(): float
    {
        return $this->paidAmount;
    }

    public function setPaidAmount(float $paidAmount): void
    {
        $this->paidAmount = $paidAmount;
    }

    public function getChangeAmount(): float
    {
        return $this->changeAmount;
    }

    public function setChangeAmount(float $changeAmount): void
    {
        $this->changeAmount = $changeAmount;
    }

    /**
     * Menambahkan baris detail transaksi ke dalam order
     */
    public function addItem(OrderDetail $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return OrderDetail[]
     */
    public function getItems(): array
    {
        if (empty($this->items) && !empty($this->orderId)) {
            $this->items = OrderDetail::findByOrderId($this->orderId);
        }
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function getUser(): ?User
    {
        return User::findById($this->userId);
    }

    public function toArray(): array
    {
        $itemsArr = [];
        foreach ($this->getItems() as $item) {
            $itemsArr[] = $item->toArray();
        }

        return [
            'order_id' => $this->orderId,
            'order_date' => $this->orderDate,
            'user_id' => $this->userId,
            'user' => $this->getUser() ? $this->getUser()->toArray() : null,
            'total_amount' => $this->totalAmount,
            'paid_amount' => $this->paidAmount,
            'change_amount' => $this->changeAmount,
            'items' => $itemsArr,
        ];
    }

    // --- Static Data Access Methods ---

    public static function findById(string $orderId): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? LIMIT 1");
        $stmt->execute([$orderId]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $order = new self(
            $row['order_id'],
            $row['order_date'],
            (int)$row['user_id'],
            (float)$row['total_amount'],
            (float)$row['paid_amount'],
            (float)$row['change_amount']
        );
        $order->setItems(OrderDetail::findByOrderId($row['order_id']));
        return $order;
    }

    public static function getAll(int $limit = 20): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM orders ORDER BY order_date DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        $results = [];
        while ($row = $stmt->fetch()) {
            $order = new self(
                $row['order_id'],
                $row['order_date'],
                (int)$row['user_id'],
                (float)$row['total_amount'],
                (float)$row['paid_amount'],
                (float)$row['change_amount']
            );
            $results[] = $order;
        }
        return $results;
    }
}
