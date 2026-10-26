<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="main-container">
    <?php if (!empty($flash)): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
            <i class="fa-solid <?= $flash['type'] === 'danger' ? 'fa-circle-exclamation' : ($flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-info') ?>"></i>
            <span><?= htmlspecialchars($flash['message']) ?></span>
        </div>
    <?php endif; ?>

    <!-- Hero Banner -->
    <div class="dashboard-hero">
        <div>
            <h1 class="hero-greeting">Halo, <?= htmlspecialchars($user['full_name']) ?>!</h1>
            <p class="hero-subtitle">
                Anda masuk sebagai <strong><?= htmlspecialchars($user['role']) ?></strong> di <strong><?= htmlspecialchars($user['store_name']) ?></strong>.
            </p>
        </div>
        <div>
            <span class="hero-badge-driver">
                <i class="fa-solid fa-database"></i> DB: <?= strtoupper($activeDriver) ?>
            </span>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon-box icon-blue">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div>
                <div class="metric-val"><?= $itemCount ?></div>
                <div class="metric-label">Total Produk Terdaftar</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon-box icon-amber">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="metric-val"><?= $lowStockCount ?></div>
                <div class="metric-label">Stok Menipis / Perlu Restock</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon-box icon-green">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div class="metric-val"><?= $orderCount ?></div>
                <div class="metric-label">Riwayat Transaksi</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon-box icon-purple">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <div class="metric-val">Aktif</div>
                <div class="metric-label">Sesi Autentikasi (Sprint 1)</div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content Grid -->
    <div class="dashboard-grid">
        <!-- Riwayat Transaksi Terbaru -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Transaksi Terkini</h2>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Data Sampel Sistem</span>
            </div>
            <div class="table-responsive">
                <table class="pos-table">
                    <thead>
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Waktu</th>
                            <th>Total Tagihan</th>
                            <th>Uang Bayar</th>
                            <th>Kembalian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    Belum ada transaksi tercatat.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $ord): ?>
                                <tr>
                                    <td><strong style="color: var(--primary);"><?= htmlspecialchars($ord->getOrderId()) ?></strong></td>
                                    <td><?= htmlspecialchars($ord->getOrderDate()) ?></td>
                                    <td>Rp <?= number_format($ord->getTotalAmount(), 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($ord->getPaidAmount(), 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($ord->getChangeAmount(), 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Panel Aksi Cepat / Modul Sprint -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-compass"></i> Menu & Sprint Navigasi</h2>
            </div>
            <div class="card-body">
                <div class="action-grid">
                    <a href="/pos" class="action-btn">
                        <div class="action-icon" style="color: #059669;">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div>
                            <div>Kasir / Layar POS</div>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Fokus Sprint 2 (Order & Payment)</small>
                        </div>
                    </a>

                    <a href="/items" class="action-btn">
                        <div class="action-icon" style="color: #2563eb;">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <div>Katalog Produk & Stok</div>
                            <small style="color: var(--text-muted); font-size: 0.75rem;">Fokus Sprint 3 (Master Data)</small>
                        </div>
                    </a>

                    <div style="background: #f1f5f9; padding: 1rem; border-radius: var(--radius-sm); font-size: 0.8rem; color: #475569; margin-top: 0.5rem;">
                        <strong><i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Status Sprint 1:</strong>
                        <p style="margin-top: 0.25rem;">Autentikasi (IMK - Code - Database) telah berstatus <strong>Done</strong>. Kredensial dienkripsi dengan BCrypt dan sesi login berhasil diverifikasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
