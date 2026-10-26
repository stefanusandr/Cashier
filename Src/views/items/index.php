<?php
require_once __DIR__ . '/../layouts/header.php';
?>
<main class="main-container">
    <div style="background: #ffffff; border-radius: var(--radius-md); border: 1px solid var(--border); padding: 2.5rem; text-align: center;">
        <div style="width: 70px; height: 70px; background: #eff6ff; color: #2563eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 1.5rem;">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <h1 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;">Manajemen Produk &amp; Stok</h1>
        <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 1.5rem;">
            Modul master data barang (tambah, edit, penyesuaian stok, riwayat mutasi barang) merupakan target implementasi <strong>Sprint 3 (Minggu XIII)</strong>.
        </p>
        <a href="/dashboard" class="action-btn" style="display: inline-flex; width: auto; padding: 0.75rem 1.5rem;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</main>
<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
