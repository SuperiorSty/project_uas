<?php
$base = '..';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

if (!$is_logged_in) {
    header("Location: masuk.php");
    exit;
}

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

<section class="section-padding">
    <div class="container">
        <h2 class="section-title">Katalog Obat</h2>
        <p class="section-subtitle">Temukan obat yang Anda butuhkan dan tambahkan ke daftar pribadi Anda.</p>

        <?php if (count($master_obat) > 0): ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:var(--space-4);margin-top:var(--space-4)">
            <?php foreach ($master_obat as $obat): ?>
            <div class="card" style="padding:var(--space-4);display:flex;flex-direction:column;justify-content:space-between">
                <div>
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:var(--space-3)">
                        <span class="material-symbols-sharp" style="font-size:36px;color:var(--secondary)">medication</span>
                        <div>
                            <h4 style="margin:0;font-weight:var(--fw-bold);font-size:var(--fs-lg)"><?= htmlspecialchars($obat['nama_obat']) ?></h4>
                            <small style="color:var(--on-surface-variant)"><?= htmlspecialchars($obat['kategori'] ?? '-') ?></small>
                        </div>
                    </div>
                    <?php if (!empty($obat['deskripsi'])): ?>
                    <p style="font-size:var(--fs-sm);color:var(--on-surface-variant);margin-bottom:var(--space-3)"><?= htmlspecialchars($obat['deskripsi']) ?></p>
                    <?php endif; ?>
                </div>
                <?php if (!$is_admin): ?>
                <a href="setPengingat.php?id_obat=<?= $obat['id_obat'] ?>" class="btn btn-primary btn-sm btn-block" style="margin-top:var(--space-3)">
                    <span class="material-symbols-sharp" style="font-size:16px">add</span> Tambahkan ke Obat Saya
                </a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state" style="padding:var(--space-6)">
            <span class="material-symbols-sharp" style="font-size:48px">medication</span>
            <p style="margin:0">Belum ada obat tersedia di katalog.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
