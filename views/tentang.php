<?php
$base = '..';
include __DIR__ . '/../includes/header.php';
?>

<section class="hero hero-compact">
    <div class="hero-inner">
        <div class="hero-text">
            <h1>Perawatan Pemulihan</h1>
            <p>Mendefinisikan Ulang Kesehatan Melalui Alam & Teknologi. Di ForestView Health, kami menggabungkan ketelitian klinis dengan lingkungan yang memulihkan untuk memberikan pengalaman perawatan kesehatan yang memelihara tubuh dan pikiran.</p>
            <div class="hero-actions">
                <a href="#layanan" class="btn btn-ghost btn-lg">
                    <span class="material-symbols-sharp">list_alt</span> Layanan Kami
                </a>
            </div>
        </div>
        <div class="hero-image">
            <span class="material-symbols-sharp" style="font-size:56px">favorite</span>
        </div>
    </div>
</section>

<section class="section-padding" style="background:var(--surface-container-lowest)">
    <div class="container">
        <div style="text-align:center;margin-bottom:var(--space-6)">
            <div class="about-hero-stats">
                <div class="about-hero-stat">
                    <h2>98%</h2>
                    <p>Tingkat kepuasan pasien di seluruh departemen klinis kami.</p>
                </div>
            </div>
        </div>

        <div style="max-width:800px;margin:0 auto var(--space-6);text-align:center">
            <h2 class="section-title">Misi Kami: Kasih Sayang Berbasis Sains</h2>
            <p style="font-size:var(--fs-base);color:var(--on-surface-variant);line-height:1.8">
                ForestView didirikan berdasarkan prinsip bahwa lingkungan adalah komponen kritis dari penyembuhan. Dengan mengintegrasikan teknologi diagnostik mutakhir dengan ruang yang terinspirasi oleh dunia alami, kami menciptakan tempat perlindungan untuk pemulihan dan perawatan pencegahan.
            </p>
        </div>

        <div class="grid-2" style="gap:var(--space-5)">
            <div class="feature-card">
                <div class="icon-circle"><span class="material-symbols-sharp">biotech</span></div>
                <h3>Teknologi Generasi Berikutnya</h3>
                <p>Diagnostik presisi yang didukung oleh AI dan pemantauan waktu nyata untuk hasil perawatan yang optimal.</p>
            </div>
            <div class="feature-card">
                <div class="icon-circle"><span class="material-symbols-sharp">nature_people</span></div>
                <h3>Desain Holistik</h3>
                <p>Fasilitas yang dibangun untuk mengurangi kortisol dan meningkatkan kesejahteraan mental pasien.</p>
            </div>
        </div>
    </div>
</section>

<section id="layanan" class="section-padding">
    <div class="container">
        <h2 class="section-title">Mengapa Memilih ForestView?</h2>
        <p class="section-subtitle">Kami melampaui batas pengobatan tradisional untuk menawarkan pengalaman yang mulus dan personal.</p>

        <div class="grid-3" style="margin-top:var(--space-4)">
            <div class="feature-card">
                <div class="icon-circle"><span class="material-symbols-sharp">neurology</span></div>
                <h3>Perawatan Spesialis Tingkat Lanjut</h3>
                <p>Pusat keunggulan dalam Kardiologi, Neurologi, dan Ortopedi menggunakan bedah berbantuan robotik dan pengujian genomik.</p>
                <a href="tentang.php" class="btn btn-outline btn-sm">
                    Jelajahi Spesialisasi <span class="material-symbols-sharp" style="font-size:14px">arrow_forward</span>
                </a>
            </div>
            <div class="feature-card">
                <div class="icon-circle"><span class="material-symbols-sharp">videocam</span></div>
                <h3>Akses Virtual 24/7</h3>
                <p>Bicaralah dengan dokter bersertifikat kapan saja melalui portal pasien kami yang aman dan terenkripsi.</p>
            </div>
            <div class="feature-card">
                <div class="icon-circle"><span class="material-symbols-sharp">shield_with_heart</span></div>
                <h3>Privasi Terjamin</h3>
                <p>Enkripsi kelas militer untuk catatan kesehatan dan data pribadi Anda.</p>
            </div>
        </div>
    </div>
</section>


<?php include __DIR__ . '/../includes/footer.php'; ?>
