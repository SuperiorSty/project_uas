
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= $base ?? '.' ?>/index.php" class="navbar-brand" style="margin-bottom:var(--space-2)">
                    <span class="brand-icon material-symbols-sharp">forest</span>
                    <span class="brand-text">ForestView Health</span>
                </a>
                <p>Menggabungkan ketenangan alam dengan presisi pengobatan modern untuk memberikan perawatan yang memulihkan bagi setiap pasien.</p>
            </div>
            <div>
                <h4>Perusahaan</h4>
                <a href="<?= $base ?? '.' ?>/views/tentang.php">Tentang Kami</a>
            </div>
            <div>
                <h4>Pasien</h4>
                <a href="<?= $base ?? '.' ?>/views/daftar.php">Pemesanan</a>
                <a href="<?= $base ?? '.' ?>/views/masuk.php">Portal Pasien</a>
            </div>
            <div>
                <h4>Dukungan</h4>
                <a href="<?= $base ?? '.' ?>/views/kontak.php">Pusat Bantuan</a>
                <a href="#">Kebijakan Privasi</a>
                <a href="#">Syarat & Ketentuan</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; 2026 ForestView Health. Hak cipta dilindungi undang-undang.</span>
            <span>Dibangun dengan <span class="material-symbols-sharp" style="color:var(--error);font-size:14px">favorite</span> untuk kesehatan Anda</span>
        </div>
    </div>
</footer>

</div>

<script>
// Hapus ?success= / ?error= dari URL setelah alert ditampilkan
if (location.search.includes('success=') || location.search.includes('error=')) {
    const clean = location.pathname + location.hash;
    history.replaceState(null, '', clean);
}

function toggleTheme() {
    const html = document.documentElement;
    const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    document.getElementById('navThemeIcon').textContent = next === 'dark' ? 'light_mode' : 'dark_mode';
}

document.addEventListener('click', function(e) {
    const dd = document.getElementById('userDropdown');
    if (dd && !dd.contains(e.target)) {
        dd.classList.remove('open');
    }
});

function toggleDropdown() {
    document.getElementById('userDropdown')?.classList.toggle('open');
}

(function() {
    const saved = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    const icon = document.getElementById('navThemeIcon');
    if (icon) icon.textContent = saved === 'dark' ? 'light_mode' : 'dark_mode';
})();
</script>
</body>
</html>
