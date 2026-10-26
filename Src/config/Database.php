<?php
namespace SmartKasir\Config;

use PDO;
use PDOException;

/**
 * Class Database
 * Menangani koneksi basis data menggunakan pola Singleton (PDO).
 * Mendukung MySQL dan fallback SQLite untuk kemudahan pengujian lokal.
 */
class Database
{
    private static ?PDO $instance = null;
    private static string $driver = 'mysql'; // 'mysql' atau 'sqlite'

    // Parameter koneksi MySQL
    private static string $host = '127.0.0.1';
    private static string $port = '3306';
    private static string $dbName = 'smartkasir_db';
    private static string $username = 'root';
    private static string $password = '';

    // File path untuk SQLite
    private static string $sqlitePath = __DIR__ . '/../../Database/smartkasir.sqlite';

    // Private constructor untuk mencegah instansiasi langsung
    private function __construct()
    {
    }

    /**
     * Mendapatkan instance PDO Singleton.
     * Mencoba MySQL terlebih dahulu, jika gagal otomatis beralih ke SQLite lokal.
     *
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                // Coba koneksi MySQL terlebih dahulu
                $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbName . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 2, // Timeout cepat jika MySQL mati
                ];

                self::$instance = new PDO($dsn, self::$username, self::$password, $options);
                self::$driver = 'mysql';
            } catch (PDOException $e) {
                // Fallback ke SQLite jika MySQL tidak aktif atau belum dibuat
                try {
                    $sqliteDir = dirname(self::$sqlitePath);
                    if (!is_dir($sqliteDir)) {
                        mkdir($sqliteDir, 0777, true);
                    }

                    $dsn = "sqlite:" . self::$sqlitePath;
                    self::$instance = new PDO($dsn, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]);
                    self::$instance->exec("PRAGMA foreign_keys = ON;");
                    self::$driver = 'sqlite';

                    // Auto-init tabel jika database baru dibuat
                    self::initializeSqliteSchema(self::$instance);
                } catch (PDOException $ex) {
                    die("Database Connection Error: " . $ex->getMessage());
                }
            }
        }

        return self::$instance;
    }

    /**
     * Inisialisasi skema dan seed data untuk SQLite secara otomatis
     */
    private static function initializeSqliteSchema(PDO $pdo): void
    {
        // Cek apakah tabel users sudah ada
        $check = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->fetch();
        if ($check) {
            return;
        }

        $schema = <<<SQL
        CREATE TABLE IF NOT EXISTS stores (
            store_id VARCHAR(10) PRIMARY KEY,
            store_name VARCHAR(100) NOT NULL,
            address TEXT,
            phone VARCHAR(20)
        );

        CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY AUTOINCREMENT,
            store_id VARCHAR(10) NOT NULL,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role VARCHAR(20) NOT NULL CHECK(role IN ('ADMIN', 'CASHIER')),
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (store_id) REFERENCES stores(store_id) ON DELETE RESTRICT
        );

        CREATE TABLE IF NOT EXISTS items (
            item_code VARCHAR(30) PRIMARY KEY,
            item_name VARCHAR(150) NOT NULL,
            unit VARCHAR(30) NOT NULL DEFAULT 'Pcs',
            unit_price REAL NOT NULL,
            min_stock INTEGER NOT NULL DEFAULT 5,
            stock_qty INTEGER NOT NULL DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS orders (
            order_id VARCHAR(35) PRIMARY KEY,
            order_date DATETIME NOT NULL,
            user_id INTEGER NOT NULL,
            total_amount REAL NOT NULL,
            paid_amount REAL NOT NULL,
            change_amount REAL NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        );

        CREATE TABLE IF NOT EXISTS order_details (
            detail_id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id VARCHAR(35) NOT NULL,
            item_code VARCHAR(30) NOT NULL,
            quantity INTEGER NOT NULL,
            subtotal REAL NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
            FOREIGN KEY (item_code) REFERENCES items(item_code)
        );

        CREATE TABLE IF NOT EXISTS stock_logs (
            log_id INTEGER PRIMARY KEY AUTOINCREMENT,
            item_code VARCHAR(30) NOT NULL,
            change_qty INTEGER NOT NULL,
            previous_stock INTEGER NOT NULL,
            current_stock INTEGER NOT NULL,
            type VARCHAR(20) NOT NULL CHECK(type IN ('SALE', 'RESTOCK', 'ADJUSTMENT')),
            reference_id VARCHAR(50),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (item_code) REFERENCES items(item_code)
        );
SQL;
        $pdo->exec($schema);

        // Seed data toko
        $stmtStore = $pdo->prepare("INSERT OR IGNORE INTO stores (store_id, store_name, address, phone) VALUES (?, ?, ?, ?)");
        $stores = [
            ['TK001', 'SmartKasir Cabang Ketintang', 'Jl. Ketintang No. 156, Surabaya', '031-8280001'],
            ['TK002', 'SmartKasir Cabang Gubeng', 'Jl. Raya Gubeng No. 45, Surabaya', '031-5030002'],
            ['TK003', 'SmartKasir Cabang Rungkut', 'Jl. Raya Rungkut Madya No. 88, Surabaya', '031-8700003'],
            ['TK004', 'SmartKasir Cabang Darmo', 'Jl. Raya Darmo No. 12, Surabaya', '031-5670004'],
            ['TK005', 'SmartKasir Cabang Wiyung', 'Jl. Raya Menganti Wiyung No. 21, Surabaya', '031-7520005'],
        ];
        foreach ($stores as $s) {
            $stmtStore->execute($s);
        }

        // Seed users dengan hash bcrypt yang valid
        $adminHash = password_hash('admin123', PASSWORD_BCRYPT);
        $kasirHash = password_hash('kasir123', PASSWORD_BCRYPT);

        $stmtUser = $pdo->prepare("INSERT OR IGNORE INTO users (user_id, store_id, username, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?, ?)");
        $users = [
            [1, 'TK001', 'stefanus_admin', $adminHash, 'Stefanus Andri Hendrawan', 'ADMIN'],
            [2, 'TK001', 'fatih_kasir', $kasirHash, 'Fatih Khairu Alfifajri', 'CASHIER'],
            [3, 'TK002', 'irham_kasir', $kasirHash, 'Irham Maulana A.', 'CASHIER'],
            [4, 'TK003', 'alif_kasir', $kasirHash, 'Alif Lutfan A.', 'CASHIER'],
            [5, 'TK004', 'jevico_kasir', $kasirHash, 'Jevico Royvaldo F', 'CASHIER'],
        ];
        foreach ($users as $u) {
            $stmtUser->execute($u);
        }

        // Seed items
        $stmtItem = $pdo->prepare("INSERT OR IGNORE INTO items (item_code, item_name, unit, unit_price, min_stock, stock_qty) VALUES (?, ?, ?, ?, ?, ?)");
        $items = [
            ['BRG001', 'Kopi Susu Gula Aren', 'Cup', 15000.00, 10, 50],
            ['BRG002', 'Teh Tarik Dingin', 'Cup', 12000.00, 10, 45],
            ['BRG003', 'Air Mineral 600ml', 'Botol', 5000.00, 20, 100],
            ['BRG004', 'Roti Bakar Cokelat', 'Porsi', 10000.00, 5, 30],
            ['BRG005', 'Kentang Goreng Crispy', 'Porsi', 18000.00, 8, 25],
        ];
        foreach ($items as $item) {
            $stmtItem->execute($item);
        }
    }

    public static function getActiveDriver(): string
    {
        return self::$driver;
    }
}
