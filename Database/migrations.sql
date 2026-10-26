-- ========================================================
-- SISTEM POINT OF SALE (POS) - SMARTKASIR
-- DDL & DML MIGRATION SCRIPT (MySQL Compatible)
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tabel: stores (Master Toko / Cabang)
CREATE TABLE IF NOT EXISTS stores (
    store_id VARCHAR(10) PRIMARY KEY,
    store_name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel: users (Pengguna Sistem Kasir / Admin)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    store_id VARCHAR(10) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('ADMIN', 'CASHIER') NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_store FOREIGN KEY (store_id) REFERENCES stores(store_id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel: items (Master Data Produk/Barang)
CREATE TABLE IF NOT EXISTS items (
    item_code VARCHAR(30) PRIMARY KEY,
    item_name VARCHAR(150) NOT NULL,
    unit VARCHAR(30) NOT NULL DEFAULT 'Pcs',
    unit_price DECIMAL(12, 2) NOT NULL,
    min_stock INT NOT NULL DEFAULT 5,
    stock_qty INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel: orders (Header Transaksi Penjualan)
CREATE TABLE IF NOT EXISTS orders (
    order_id VARCHAR(35) PRIMARY KEY, -- Format: TRX-YYYYMMDD-XXXX
    order_date DATETIME NOT NULL,
    user_id INT NOT NULL,
    total_amount DECIMAL(12, 2) NOT NULL,
    paid_amount DECIMAL(12, 2) NOT NULL,
    change_amount DECIMAL(12, 2) NOT NULL,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabel: order_details (Detail Item dalam Transaksi)
CREATE TABLE IF NOT EXISTS order_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(35) NOT NULL,
    item_code VARCHAR(30) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    CONSTRAINT fk_details_order FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    CONSTRAINT fk_details_item FOREIGN KEY (item_code) REFERENCES items(item_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabel: stock_logs (Pencatatan Audit Mutasi Stok)
CREATE TABLE IF NOT EXISTS stock_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(30) NOT NULL,
    change_qty INT NOT NULL,
    previous_stock INT NOT NULL,
    current_stock INT NOT NULL,
    type ENUM('SALE', 'RESTOCK', 'ADJUSTMENT') NOT NULL,
    reference_id VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_stocklogs_item FOREIGN KEY (item_code) REFERENCES items(item_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- DATA SEED (Sampel 5 Data per Tabel)
-- Password default:
-- stefanus_admin: admin123
-- kasir (fatih, irham, alif, jevico): kasir123
-- ========================================================

INSERT INTO stores (store_id, store_name, address, phone) VALUES
('TK001', 'SmartKasir Cabang Ketintang', 'Jl. Ketintang No. 156, Surabaya', '031-8280001'),
('TK002', 'SmartKasir Cabang Gubeng', 'Jl. Raya Gubeng No. 45, Surabaya', '031-5030002'),
('TK003', 'SmartKasir Cabang Rungkut', 'Jl. Raya Rungkut Madya No. 88, Surabaya', '031-8700003'),
('TK004', 'SmartKasir Cabang Darmo', 'Jl. Raya Darmo No. 12, Surabaya', '031-5670004'),
('TK005', 'SmartKasir Cabang Wiyung', 'Jl. Raya Menganti Wiyung No. 21, Surabaya', '031-7520005')
ON DUPLICATE KEY UPDATE store_name=VALUES(store_name);

INSERT INTO users (user_id, store_id, username, password_hash, full_name, role) VALUES
(1, 'TK001', 'stefanus_admin', '$2y$10$wTqKjUvgWd862fU25uTqB.R3X9z7aM0Yq0N7bHh4f9.p2Q8u9K5K2', 'Stefanus Andri Hendrawan', 'ADMIN'),
(2, 'TK001', 'fatih_kasir', '$2y$10$f6fH8P2o7I1uQv1p6C0Cg.e8eL3L1x2m4B9z6r2U5c8a0Q9w4L7jG', 'Fatih Khairu Alfifajri', 'CASHIER'),
(3, 'TK002', 'irham_kasir', '$2y$10$f6fH8P2o7I1uQv1p6C0Cg.e8eL3L1x2m4B9z6r2U5c8a0Q9w4L7jG', 'Irham Maulana A.', 'CASHIER'),
(4, 'TK003', 'alif_kasir', '$2y$10$f6fH8P2o7I1uQv1p6C0Cg.e8eL3L1x2m4B9z6r2U5c8a0Q9w4L7jG', 'Alif Lutfan A.', 'CASHIER'),
(5, 'TK004', 'jevico_kasir', '$2y$10$f6fH8P2o7I1uQv1p6C0Cg.e8eL3L1x2m4B9z6r2U5c8a0Q9w4L7jG', 'Jevico Royvaldo F', 'CASHIER')
ON DUPLICATE KEY UPDATE username=VALUES(username);

INSERT INTO items (item_code, item_name, unit, unit_price, min_stock, stock_qty) VALUES
('BRG001', 'Kopi Susu Gula Aren', 'Cup', 15000.00, 10, 50),
('BRG002', 'Teh Tarik Dingin', 'Cup', 12000.00, 10, 45),
('BRG003', 'Air Mineral 600ml', 'Botol', 5000.00, 20, 100),
('BRG004', 'Roti Bakar Cokelat', 'Porsi', 10000.00, 5, 30),
('BRG005', 'Kentang Goreng Crispy', 'Porsi', 18000.00, 8, 25)
ON DUPLICATE KEY UPDATE item_name=VALUES(item_name);

INSERT INTO orders (order_id, order_date, user_id, total_amount, paid_amount, change_amount) VALUES
('TRX-20261019-0001', '2026-10-19 09:15:00', 2, 30000.00, 50000.00, 20000.00),
('TRX-20261019-0002', '2026-10-19 10:30:00', 2, 24000.00, 30000.00, 6000.00),
('TRX-20261019-0003', '2026-10-19 11:45:00', 3, 15000.00, 20000.00, 5000.00),
('TRX-20261019-0004', '2026-10-19 13:20:00', 4, 20000.00, 20000.00, 0.00),
('TRX-20261019-0005', '2026-10-19 15:10:00', 5, 36000.00, 50000.00, 14000.00)
ON DUPLICATE KEY UPDATE total_amount=VALUES(total_amount);

INSERT INTO order_details (detail_id, order_id, item_code, quantity, subtotal) VALUES
(1, 'TRX-20261019-0001', 'BRG001', 2, 30000.00),
(2, 'TRX-20261019-0002', 'BRG002', 2, 24000.00),
(3, 'TRX-20261019-0003', 'BRG003', 3, 15000.00),
(4, 'TRX-20261019-0004', 'BRG004', 2, 20000.00),
(5, 'TRX-20261019-0005', 'BRG005', 2, 36000.00)
ON DUPLICATE KEY UPDATE quantity=VALUES(quantity);

SET FOREIGN_KEY_CHECKS = 1;