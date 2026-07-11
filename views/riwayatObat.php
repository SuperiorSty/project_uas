<?php
$base = '..';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

if (!$is_logged_in) {
    header("Location: masuk.php");
    exit;
}

include __DIR__ . '/../proses/prosesRiwayat.php';
?>

<section class="section-padding">
    <div class="container">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-4);flex-wrap:wrap;gap:var(--space-3)">
            <div>
                <h2 class="section-title" style="margin-bottom:0">Riwayat Minum Obat</h2>
                <p style="color:var(--on-surface-variant);font-size:var(--fs-sm);margin:var(--space-1) 0 0">Catatan konsumsi obat Anda, termasuk obat yang sudah tidak aktif.</p>
            </div>
            <a href="dasboard.php" class="btn btn-outline btn-sm">
                <span class="material-symbols-sharp" style="font-size:16px">arrow_back</span> Kembali ke Dashboard
            </a>
        </div>

        <form method="GET" action="" style="display:flex;gap:var(--space-2);flex-wrap:wrap;align-items:center;margin-bottom:var(--space-4)">
            <div style="position:relative;flex:1;min-width:180px">
                <span class="material-symbols-sharp" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--outline);font-size:18px">search</span>
                <input type="text" name="search" class="form-input" placeholder="Cari nama obat..." value="<?= htmlspecialchars($search) ?>" style="padding-left:38px">
            </div>
            <select name="status" class="form-select" style="width:auto;min-width:140px">
                <option value="">Semua Status</option>
                <option value="Sudah Diminum" <?= $filter_status === 'Sudah Diminum' ? 'selected' : '' ?>>Sudah Diminum</option>
                <option value="Terlewat" <?= $filter_status === 'Terlewat' ? 'selected' : '' ?>>Terlewat</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="riwayatObat.php" class="btn btn-sm" style="background:var(--surface-container);color:var(--on-surface-variant)">Reset</a>
        </form>

        <?php if (count($riwayat) > 0): ?>
        <div class="card" style="overflow:hidden">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayat as $r): 
                            $waktu = new DateTime($r['waktu_jadwal']);
                            $tgl = $waktu->format('d M Y');
                            $jam = $waktu->format('H:i');
                        ?>
                        <tr>
                            <td style="white-space:nowrap"><?= $tgl ?></td>
                            <td style="white-space:nowrap"><?= $jam ?> WITA</td>
                            <td style="font-weight:var(--fw-semibold)"><?= htmlspecialchars($r['nama_obat']) ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($r['kategori']) ?></span></td>
                            <td>
                                <?php if ($r['status'] === 'Sudah Diminum'): ?>
                                    <span class="badge badge-success">Sudah Diminum</span>
                                <?php elseif ($r['status'] === 'Terlewat'): ?>
                                    <span class="badge badge-danger">Terlewat</span>
                                <?php else: ?>
                                    <span class="badge badge-muted">Ditunda</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="empty-state" style="padding:var(--space-6)">
            <span class="material-symbols-sharp" style="font-size:48px">history</span>
            <h6>Belum Ada Riwayat</h6>
            <p style="font-size:var(--fs-sm);margin:0">Riwayat minum obat akan muncul di sini setelah Anda mulai mencatat konsumsi.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
