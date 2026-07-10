<?php
$base = '..';
include __DIR__ . '/../includes/header.php';
?>

<section class="hero hero-compact">
    <div class="hero-inner">
        <div class="hero-text">
            <h1>Hubungi Kami</h1>
            <p>Kami di ForestView Health berkomitmen untuk memberikan layanan kesehatan terbaik. Hubungi tim kami sekarang untuk konsultasi cepat melalui WhatsApp.</p>
        </div>
        <div class="hero-image">
            <span class="material-symbols-sharp" style="font-size:56px">chat</span>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="grid-3" style="margin-bottom:var(--space-6)">
            <div class="contact-card">
                <div class="contact-icon"><span class="material-symbols-sharp">chat</span></div>
                <div>
                    <h4>Respon Cepat</h4>
                    <p>Hubungi Kami via WhatsApp<br><strong>Jam operasional: 08:00 - 21:00 WIB</strong></p>
                    <a href="#" class="btn btn-primary btn-sm" style="margin-top:var(--space-2)">
                        <span class="material-symbols-sharp" style="font-size:18px">chat</span> Hubungi WhatsApp
                    </a>
                </div>
            </div>
            <div class="contact-card">
                <div class="contact-icon"><span class="material-symbols-sharp">location_on</span></div>
                <div>
                    <h4>Alamat Klinik</h4>
                    <p>Jl. Pinus Hijau No. 12, Area Kesehatan Utama, Jakarta Selatan, 12345</p>
                </div>
            </div>
            <div class="contact-card">
                <div class="contact-icon"><span class="material-symbols-sharp">schedule</span></div>
                <div>
                    <h4>Jam Buka</h4>
                    <p>
                        Senin - Jumat: 08:00 - 20:00<br>
                        Sabtu: 08:00 - 15:00<br>
                        Minggu: Tutup
                    </p>
                </div>
            </div>
        </div>

        <div class="card" style="padding:var(--space-5);text-align:center;margin-bottom:var(--space-5)">
            <div style="background:var(--surface-container);border-radius:var(--radius-md);padding:var(--space-6);margin-bottom:var(--space-4);display:flex;flex-direction:column;align-items:center;gap:var(--space-3)">
                <span class="material-symbols-sharp" style="font-size:48px;color:var(--outline)">map</span>
                <p style="color:var(--on-surface-variant)">Peta sedang dimuat...</p>
            </div>
            <a href="#" class="btn btn-primary-green">
                <span class="material-symbols-sharp">directions</span> Petunjuk Arah via Google Maps
            </a>
        </div>

        <div class="card" style="padding:var(--space-5);text-align:center;background:var(--surface-container);border-color:transparent;max-width:700px;margin:0 auto">
            <span class="material-symbols-sharp" style="font-size:40px;color:var(--secondary);margin-bottom:var(--space-2)">format_quote</span>
            <p style="font-size:var(--fs-lg);font-style:italic;color:var(--on-surface);margin-bottom:var(--space-2)">&ldquo;Butuh bantuan cepat? Admin kami siap menjawab pertanyaan Anda via WhatsApp.&rdquo;</p>
            <p style="font-size:var(--fs-sm);color:var(--on-surface-variant)">&mdash; Sarah, Layanan Pelanggan</p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
