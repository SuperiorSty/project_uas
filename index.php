<?php
$base = '.';
require_once __DIR__ . '/config/database.php';
include __DIR__ . '/includes/header.php';

$db = new Database();
$conn = $db->getConn();

$master_obat = [];
try {
    $result = $conn->query("SELECT * FROM master_obat ORDER BY nama_obat ASC");
    $master_obat = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {}
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
            <p>Catat, ingat, dan pantau kepatuhan minum obat secara otomatis. Navigasi kesehatan bersama ForestView &mdash; rekanan medis terpercaya untuk perjalanan pemulihan Anda.</p>
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
        <h2 class="section-title">Layanan Kami</h2>
        <p class="section-subtitle">Solusi Medis Komprehensif untuk Kesehatan Anda</p>

        <div class="grid-3" style="margin-top:var(--space-4)">
            <div class="service-card">
                <div class="icon-circle"><span class="material-symbols-sharp">favorite</span></div>
                <h3>Kardiologi</h3>
                <p>Navigasi kesehatan jantung dengan teknologi medis terdepan dan perawatan terpercaya.</p>
                <a href="views/tentang.php" class="btn btn-outline btn-sm">Pelajari Selengkapnya</a>
            </div>
            <div class="service-card">
                <div class="icon-circle"><span class="material-symbols-sharp">dentistry</span></div>
                <h3>Kedokteran Gigi</h3>
                <p>Senyum sehat adalah prioritas kami dengan dukungan teknologi Online Health Hub.</p>
                <a href="views/tentang.php" class="btn btn-outline btn-sm">Pelajari Selengkapnya</a>
            </div>
            <div class="service-card">
                <div class="icon-circle"><span class="material-symbols-sharp">neurology</span></div>
                <h3>Neurologi</h3>
                <p>Penanganan saraf dan otak yang presisi menggunakan standar medis internasional.</p>
                <a href="views/tentang.php" class="btn btn-outline btn-sm">Pelajari Selengkapnya</a>
            </div>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <h2 class="section-title">4 Langkah Menuju Kesehatan Optimal</h2>
        <p class="section-subtitle">Navigasi Kesehatan Bersama: Rekanan Medis Terpercaya Anda</p>

        <div class="grid-4" style="margin-top:var(--space-4)">
            <div class="step-card">
                <div class="step-number">1</div>
                <h4>Profil Dokter</h4>
                <p>Temukan spesialis yang tepat untuk kebutuhan kesehatan Anda.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h4>Konsultasi</h4>
                <p>Diskusikan keluhan Anda dengan dokter secara langsung atau virtual.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h4>Jadwal Temu</h4>
                <p>Atur jadwal konsultasi yang sesuai dengan waktu luang Anda.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h4>Dapatkan Solusi</h4>
                <p>Terima resep dan rencana perawatan yang dipersonalisasi untuk Anda.</p>
            </div>
        </div>
    </div>
</section>

<section class="section-padding" style="background:var(--surface-container-lowest)">
    <div class="container">
        <div style="background:linear-gradient(135deg, #006e1c 0%, #4caf50 50%);border-radius:var(--radius-xl);padding:var(--space-6);text-align:center;color:#fff;box-shadow:var(--shadow-md);position:relative;overflow:hidden">
            <div style="position:relative;z-index:1">
                <h2 style="font-size:var(--fs-3xl);font-weight:var(--fw-extrabold);margin-bottom:var(--space-3)">Mulai Perjalanan Kesehatan Anda</h2>
                <p style="opacity:0.85;margin-bottom:var(--space-4);max-width:500px;margin-left:auto;margin-right:auto">Daftar sekarang dan dapatkan akses ke fitur pengingat obat, riwayat konsumsi, dan konsultasi dengan apoteker.</p>
                <a href="views/daftar.php" class="btn btn-ghost btn-lg">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
