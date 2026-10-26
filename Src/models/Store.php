<?php
namespace SmartKasir\Models;

use SmartKasir\Config\Database;
use PDO;

/**
 * Class Store
 * Merepresentasikan entitas toko / cabang ritel SmartKasir.
 */
class Store
{
    private string $storeId;
    private string $storeName;
    private ?string $address;
    private ?string $phone;

    public function __construct(
        string $storeId = '',
        string $storeName = '',
        ?string $address = null,
        ?string $phone = null
    ) {
        $this->storeId = $storeId;
        $this->storeName = $storeName;
        $this->address = $address;
        $this->phone = $phone;
    }

    // --- Access Modifier: Public Getters & Setters ---

    public function getStoreId(): string
    {
        return $this->storeId;
    }

    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }

    public function getStoreName(): string
    {
        return $this->storeName;
    }

    public function setStoreName(string $storeName): void
    {
        $this->storeName = $storeName;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function toArray(): array
    {
        return [
            'store_id' => $this->storeId,
            'store_name' => $this->storeName,
            'address' => $this->address,
            'phone' => $this->phone,
        ];
    }

    // --- Static Data Access Methods ---

    public static function findById(string $storeId): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM stores WHERE store_id = ?");
        $stmt->execute([$storeId]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new self($row['store_id'], $row['store_name'], $row['address'], $row['phone']);
    }

    public static function getAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM stores ORDER BY store_id ASC");
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new self($row['store_id'], $row['store_name'], $row['address'], $row['phone']);
        }
        return $result;
    }
}
