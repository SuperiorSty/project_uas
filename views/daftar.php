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
        <span class="material-symbols-sharp" style="font-size:64px;margin-bottom:var(--space-3);position:relative;z-index:1">medication</span>
        <h1>Pengingat Obat</h1>
        <p>Pantau, ingat, dan kelola jadwal minum obat harian Anda dengan mudah dan tepat waktu.</p>

        <div style="display:flex;gap:var(--space-4);margin-top:var(--space-4);position:relative;z-index:1;flex-wrap:wrap;justify-content:center">
            <div style="text-align:center;max-width:140px">
                <span class="material-symbols-sharp" style="font-size:36px;color:var(--primary-container)">verified_user</span>
                <p style="font-size:var(--fs-xs);margin-top:var(--space-1);opacity:0.8">Aman & Terpercaya</p>
            </div>
            <div style="text-align:center;max-width:140px">
                <span class="material-symbols-sharp" style="font-size:36px;color:var(--primary-container)">medical_services</span>
                <p style="font-size:var(--fs-xs);margin-top:var(--space-1);opacity:0.8">Terintegrasi</p>
            </div>
            <div style="text-align:center;max-width:140px">
                <span class="material-symbols-sharp" style="font-size:36px;color:var(--primary-container)">schedule</span>
                <p style="font-size:var(--fs-xs);margin-top:var(--space-1);opacity:0.8">24/7 Akses</p>
            </div>
        </div>
    </div>
    <div class="auth-form">
        <div class="auth-form-inner">
            <h2>Buat Akun Baru</h2>
            <p class="subtitle">Lengkapi data diri Anda untuk memulai.</p>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error" style="margin-bottom:var(--space-4)"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success" style="margin-bottom:var(--space-4)"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <form action="../proses/prosesRegistrasi.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="form-input-icon">
                        <span class="material-symbols-sharp">badge</span>
                        <input type="text" name="nama_lengkap" class="form-input" placeholder="Masukkan nama lengkap" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="form-input-icon">
                        <span class="material-symbols-sharp">mail</span>
                        <input type="email" name="email" class="form-input" placeholder="Masukkan email aktif" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="form-input-icon">
                        <span class="material-symbols-sharp">person</span>
                        <input type="text" name="username" class="form-input" placeholder="Buat username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Kata Sandi</label>
                    <div class="form-input-icon">
                        <span class="material-symbols-sharp">lock</span>
                        <input type="password" name="password" class="form-input" placeholder="Buat kata sandi" required>
                    </div>
                </div>
                <div class="form-group" style="margin:var(--space-3) 0">
                    <label class="form-check">
                        <input type="checkbox" required>
                        <span>Saya setuju dengan <a href="#">Syarat & Ketentuan</a> serta <a href="#">Kebijakan Privasi</a> Pengingat Obat.</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <span class="material-symbols-sharp">person_add</span> Daftar Sekarang
                </button>
            </form>

            <p style="text-align:center;margin-top:var(--space-4);font-size:var(--fs-sm);color:var(--on-surface-variant)">
                Sudah punya akun? <a href="masuk.php" style="font-weight:var(--fw-bold)">Masuk</a>
            </p>
        </div>
    </div>
</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>
