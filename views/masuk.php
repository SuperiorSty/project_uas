<?php
$base = '..';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

if ($is_logged_in) {
    header("Location: " . ($is_admin ? "dashboardApoteker.php" : "dasboard.php"));
    exit;
}
?>

<div class="auth-layout">
    <div class="auth-brand">
        <span class="material-symbols-sharp" style="font-size:64px;margin-bottom:var(--space-3);position:relative;z-index:1">forest</span>
        <h1>ForestView Health</h1>
        <p>Pelayanan Kesehatan Restoratif. Kami menggabungkan keunggulan klinis dengan pendekatan manusiawi untuk kesehatan yang berkelanjutan.</p>
    </div>
    <div class="auth-form">
        <div class="auth-form-inner">
            <h2>Selamat Datang Kembali</h2>
            <p class="subtitle">Masuk ke akun Anda untuk melihat statistik kesehatan terbaru.</p>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error" style="margin-bottom:var(--space-4)"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" style="margin-bottom:var(--space-4)"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <form action="../proses/prosesLogin.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="form-input-icon">
                        <span class="material-symbols-sharp">mail</span>
                        <input type="text" name="username" class="form-input" placeholder="Masukkan username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Kata Sandi</label>
                    <div class="form-input-icon">
                        <span class="material-symbols-sharp">lock</span>
                        <input type="password" name="password" class="form-input" placeholder="Masukkan kata sandi" required>
                    </div>
                </div>
                <div class="form-group" style="display:flex;align-items:center;justify-content:space-between">
                    <label class="form-check">
                        <input type="checkbox"> Ingat saya
                    </label>
                    <a href="#" style="font-size:var(--fs-sm)">Lupa Kata Sandi?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="material-symbols-sharp">login</span> Masuk Ke Akun
                </button>
            </form>

            <p style="text-align:center;margin-top:var(--space-4);font-size:var(--fs-sm);color:var(--on-surface-variant)">
                Belum punya akun? <a href="daftar.php" style="font-weight:var(--fw-bold)">Daftar Sekarang</a>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
