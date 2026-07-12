<?php
$base = '.';
require_once __DIR__ . '/config/database.php';
include __DIR__ . '/includes/header.php';

$db = new Database();
$conn = $db->getConn();
?>

<?php if (isset($_GET['error'])): ?>
<div class="container" style="padding-top:var(--space-4)">
    <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?><button class="alert-dismiss" onclick="this.parentElement.remove()">&times;</button></div>
</div>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
<div class="container" style="padding-top:var(--space-4)">
    <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?><button class="alert-dismiss" onclick="this.parentElement.remove()">&times;</button></div>
</div>
<?php endif; ?>

<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <h1>Pantau Jadwal Minum Obatmu</h1>
            <p>Catat, ingat, dan pantau kepatuhan minum obat secara otomatis. Catat, ingat, dan pantau kepatuhan minum obat secara otomatis &mdash; rekanan kesehatan terpercaya untuk perjalanan pemulihan Anda.</p>
            <div class="hero-actions">
                <?php if ($is_logged_in): ?>
                    <a href="<?= $is_admin ? 'views/dashboardApoteker.php' : 'views/dasboard.php' ?>" class="btn btn-ghost btn-lg">
                        <span class="material-symbols-sharp">dashboard</span> Dashboard Saya
                    </a>
                <?php else: ?>
                    <a href="views/daftar.php" class="btn btn-ghost btn-lg">
                        <span class="material-symbols-sharp">person_add</span> Daftar Gratis
                    </a>
                    <a href="views/masuk.php" class="btn btn-ghost btn-lg">
                        <span class="material-symbols-sharp">login</span> Masuk
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="hero-image">
            <span class="material-symbols-sharp" style="font-size:100px">pill</span>
        </div>
    </div>
</section>

<section class="section-padding" style="background:var(--surface-container-lowest)">
    <div class="container">
        <h2 class="section-title">Manfaat untuk Pasien</h2>
        <p class="section-subtitle">Kemudahan mengelola konsumsi obat harian dalam satu platform.</p>

        <div class="grid-3" style="margin-top:var(--space-4)">
            <div class="service-card">
                <div class="icon-circle"><span class="material-symbols-sharp">schedule</span></div>
                <h3>Tepat Waktu</h3>
                <p>Dapatkan pengingat otomatis sesuai jadwal minum obat yang sudah diatur, tidak perlu khawatir lupa.</p>
            </div>
            <div class="service-card">
                <div class="icon-circle"><span class="material-symbols-sharp">monitoring</span></div>
                <h3>Pantau Kepatuhan</h3>
                <p>Lihat persentase kepatuhan minum obat harian dan riwayat konsumsi secara real-time.</p>
            </div>
            <div class="service-card">
                <div class="icon-circle"><span class="material-symbols-sharp">notifications_active</span></div>
                <h3>Notifikasi Otomatis</h3>
                <p>Peringatan minum obat akan dikirimkan tepat waktu agar pengobatan berjalan optimal.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <h2 class="section-title">4 Langkah Mudah Minum Obat Tepat Waktu</h2>
        <p class="section-subtitle">Kelola jadwal konsumsi obat harian Anda dengan mudah dan disiplin.</p>

        <div class="grid-4" style="margin-top:var(--space-4)">
            <div class="step-card">
                <div class="step-number">1</div>
                <h4>Cari & Tambahkan Obat</h4>
                <p>Temukan obat Anda dari katalog dan tambahkan ke daftar pribadi dengan jadwal minum.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h4>Atur Jadwal Harian</h4>
                <p>Tentukan waktu minum obat — pagi, siang, atau malam sesuai resep dokter.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h4>Dapatkan Pengingat</h4>
                <p>Notifikasi tepat waktu membantu Anda tidak pernah terlewat satu dosis pun.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h4>Pantau Kepatuhan</h4>
                <p>Lihat riwayat konsumsi dan persentase kepatuhan minum obat harian Anda.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
