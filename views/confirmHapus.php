<?php
$base = '..';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

if (!$is_logged_in) {
    header("Location: masuk.php");
    exit;
}

$id_obat_user = (int)($_GET['id_obat_user'] ?? 0);
$nama_obat = $_GET['nama'] ?? '';
$from = $_GET['from'] ?? 'dasboard.php';

if ($id_obat_user <= 0 || empty($nama_obat)) {
    header("Location: $from");
    exit;
}
?>
<div class="container" style="padding-top:var(--space-6);padding-bottom:var(--space-6)">
    <div class="confirm-overlay" style="display:flex;position:static;padding:0;background:none">
        <div class="confirm-modal">
            <div class="confirm-header">
                <span class="material-symbols-sharp icon">warning</span>
                <h3>Hapus Obat?</h3>
            </div>
            <div class="confirm-body">
                Yakin ingin menghapus <strong><?= htmlspecialchars($nama_obat) ?></strong> dari daftar?
                <br>Jadwal akan dihapus, tapi riwayat minum tetap tersimpan.
            </div>
            <div class="confirm-footer">
                <a href="<?= htmlspecialchars($from) ?>" class="btn" style="background:var(--surface-container);color:var(--on-surface-variant);text-decoration:none;display:inline-flex;align-items:center;justify-content:center;flex:1">Batal</a>
                <form action="../proses/prosesObat.php?aksi=hapus_obat_user" method="POST" style="flex:1;display:flex">
                    <input type="hidden" name="id_obat_user" value="<?= $id_obat_user ?>">
                    <button type="submit" class="btn" style="background:var(--error);color:var(--on-error);border:none;width:100%;justify-content:center">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
