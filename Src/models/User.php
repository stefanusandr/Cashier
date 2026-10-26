<?php
namespace SmartKasir\Models;

use SmartKasir\Config\Database;
use PDO;

/**
 * Class User
 * Merepresentasikan pengguna aplikasi POS (Admin & Kasir) dengan otentikasi berbasis BCrypt.
 */
class User
{
    private int $userId;
    private string $storeId;
    private string $username;
    private string $passwordHash;
    private string $fullName;
    private string $role; // 'ADMIN' | 'CASHIER'
    private bool $isActive;
    private ?string $createdAt;

    public function __construct(
        int $userId = 0,
        string $storeId = '',
        string $username = '',
        string $passwordHash = '',
        string $fullName = '',
        string $role = 'CASHIER',
        bool $isActive = true,
        ?string $createdAt = null
    ) {
        $this->userId = $userId;
        $this->storeId = $storeId;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->fullName = $fullName;
        $this->role = strtoupper($role);
        $this->isActive = $isActive;
        $this->createdAt = $createdAt;
    }

    // --- Access Modifier: Public Getters & Setters ---

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getStoreId(): string
    {
        return $this->storeId;
    }

    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = strtoupper($role);
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * Memvalidasi apakah password mentah cocok dengan hash di basis data
     */
    public function verifyPassword(string $rawPassword): bool
    {
        return password_verify($rawPassword, $this->passwordHash);
    }

    /**
     * Mendapatkan entitas toko tempat user bertugas
     */
    public function getStore(): ?Store
    {
        if (empty($this->storeId)) {
            return null;
        }
        return Store::findById($this->storeId);
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'store_id' => $this->storeId,
            'username' => $this->username,
            'full_name' => $this->fullName,
            'role' => $this->role,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
        ];
    }

    // --- Static Data Access Methods ---

    public static function findByUsername(string $username): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new self(
            (int)$row['user_id'],
            $row['store_id'],
            $row['username'],
            $row['password_hash'],
            $row['full_name'],
            $row['role'],
            (bool)$row['is_active'],
            $row['created_at'] ?? null
        );
    }

    public static function findById(int $userId): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new self(
            (int)$row['user_id'],
            $row['store_id'],
            $row['username'],
            $row['password_hash'],
            $row['full_name'],
            $row['role'],
            (bool)$row['is_active'],
            $row['created_at'] ?? null
        );
    }

    public static function getAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT * FROM users ORDER BY user_id ASC");
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[] = new self(
                (int)$row['user_id'],
                $row['store_id'],
                $row['username'],
                $row['password_hash'],
                $row['full_name'],
                $row['role'],
                (bool)$row['is_active'],
                $row['created_at'] ?? null
            );
        }
        return $result;
    }
}
