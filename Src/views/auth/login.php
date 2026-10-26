<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-bg-blob-1"></div>
    <div class="auth-bg-blob-2"></div>

    <div class="auth-card">
        <div class="auth-brand">
            <div class="brand-icon">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <h1 class="brand-title">Smart<span>Kasir</span></h1>
            <p class="brand-subtitle">Point of Sale System &mdash; Silakan masuk ke akun Anda</p>
        </div>

        <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>" role="alert">
                <i class="fa-solid <?= $flash['type'] === 'danger' ? 'fa-circle-exclamation' : ($flash['type'] === 'success' ? 'fa-circle-check' : 'fa-circle-info') ?>"></i>
                <span><?= htmlspecialchars($flash['message']) ?></span>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" id="form-login" autocomplete="off">
            <div class="form-group">
                <label for="username" class="form-label">Username Pengguna</label>
                <div class="input-container">
                    <i class="fa-regular fa-user input-icon"></i>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        class="form-input" 
                        placeholder="Masukkan username Anda" 
                        required 
                        autofocus
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="input-container">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-input" 
                        placeholder="Masukkan kata sandi" 
                        required
                    >
                    <button 
                        type="button" 
                        class="toggle-password" 
                        onclick="togglePasswordVisibility('password', 'eye-icon')" 
                        title="Tampilkan / Sembunyikan Kata Sandi"
                        aria-label="Toggle password visibility"
                    >
                        <i class="fa-regular fa-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary" id="btn-submit-login" style="margin-top: 1.5rem;">
                <span>Masuk ke Sistem</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <div class="demo-accounts">
            <div class="demo-title"><i class="fa-solid fa-bolt" style="color: #f59e0b;"></i> Klik Akun Demo Pengujian</div>
            <div class="demo-pills">
                <button type="button" class="demo-pill" onclick="fillDemo('stefanus_admin', 'admin123')">
                    <span class="demo-role"><i class="fa-solid fa-user-shield"></i> Admin</span>
                    <span class="demo-user">stefanus_admin</span>
                </button>
                <button type="button" class="demo-pill" onclick="fillDemo('fatih_kasir', 'kasir123')">
                    <span class="demo-role"><i class="fa-solid fa-user-tag"></i> Kasir</span>
                    <span class="demo-user">fatih_kasir</span>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>
