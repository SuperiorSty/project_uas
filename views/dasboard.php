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

$jadwal_today = []; $jumlah_diminum = 0; $jumlah_terlewat = 0;
$total_jadwal = 0; $persentase_kepatuhan = 0; $obat_aktif = [];

try {
    // Jadwal hari ini dari jadwal_reminder + obat + master_obat 
    $stmt = $conn->prepare("
        SELECT jr.*, o.jumlah_stok, m.nama_obat, m.kategori
        FROM jadwal_reminder jr
        JOIN obat o ON jr.id_obat_user = o.id_obat_user
        JOIN master_obat m ON o.id_obat = m.id_obat
        WHERE o.user_id = ?
        ORDER BY jr.jam_minum ASC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $jadwal_today = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Total jadwal aktif hari ini (dari jadwal_reminder) 
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total_hari_ini
        FROM jadwal_reminder jr
        JOIN obat o ON jr.id_obat_user = o.id_obat_user
        WHERE o.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res_total = $stmt->get_result()->fetch_assoc();
    $total_jadwal = (int)$res_total['total_hari_ini'];

    // Obat aktif 
    $stmt = $conn->prepare("
        SELECT o.*, m.nama_obat, m.kategori
        FROM obat o
        JOIN master_obat m ON o.id_obat = m.id_obat
        WHERE o.user_id = ?
        ORDER BY m.nama_obat ASC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $obat_aktif = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {}


// ── [BERUBAH] 1. TANDAI JADWAL TERLEWAT & SINKRON KE RIWAYAT (DINAIKKAN KE ATAS) ──
try {
    $today_date = date('Y-m-d');
    
    // Ambil semua jadwal hari ini yang sudah lewat jamnya tapi status_hari_ini masih pending (0)
    $stmt_check = $conn->prepare("
        SELECT jr.id_jadwal, jr.id_obat_user, jr.jam_minum 
        FROM jadwal_reminder jr
        JOIN obat o ON jr.id_obat_user = o.id_obat_user
        WHERE o.user_id = ? AND jr.status_hari_ini = 0 AND jr.jam_minum < CURTIME()
    ");
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $overdue_items = $stmt_check->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!empty($overdue_items)) {
        foreach ($overdue_items as $item) {
            $waktu_jadwal_lengkap = $today_date . ' ' . $item['jam_minum'];

            // Cek biar tidak duplikat insert ke riwayat_obat
            $stmt_history_check = $conn->prepare("
                SELECT COUNT(*) as ada 
                FROM riwayat_obat 
                WHERE user_id = ? AND id_obat_user = ? AND DATE(waktu_jadwal) = ?
            ");
            $stmt_history_check->bind_param("iis", $user_id, $item['id_obat_user'], $today_date);
            $stmt_history_check->execute();
            $cek_riwayat = $stmt_history_check->get_result()->fetch_assoc();

            if ((int)$cek_riwayat['ada'] == 0) {
                // Tembak INSERT ke riwayat_obat dengan status 'Terlewat'
                $stmt_ins = $conn->prepare("
                    INSERT INTO riwayat_obat (user_id, id_obat_user, waktu_jadwal, status, is_notified, waktu_diminum) 
                    VALUES (?, ?, ?, 'Terlewat', 0, NULL)
                ");
                $stmt_ins->bind_param("iis", $user_id, $item['id_obat_user'], $waktu_jadwal_lengkap);
                $stmt_ins->execute();
            }
        }

        // Update status_hari_ini di jadwal_reminder menjadi 99 (Terlewat)
        $stmt_up = $conn->prepare("
            UPDATE jadwal_reminder jr
            JOIN obat o ON jr.id_obat_user = o.id_obat_user
            SET jr.status_hari_ini = 99
            WHERE o.user_id = ? AND jr.status_hari_ini = 0 AND jr.jam_minum < CURTIME()
        ");
        $stmt_up->bind_param("i", $user_id);
        $stmt_up->execute();
    }
} catch (Exception $e) {}


// ── [BERUBAH] 2. HITUNG STATS DARI RIWAYAT_OBAT HARI INI (DITURUNKAN KE BAWAH AGAR AKURAT) ──
try {
    $today_date = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT 
            SUM(CASE WHEN status = 'Sudah Diminum' THEN 1 ELSE 0 END) as diminum,
            SUM(CASE WHEN status = 'Terlewat' THEN 1 ELSE 0 END) as terlewat
        FROM riwayat_obat 
        WHERE user_id = ? AND DATE(waktu_jadwal) = ?
    ");
    $stmt->bind_param("is", $user_id, $today_date);
    $stmt->execute();
    $stats_riwayat = $stmt->get_result()->fetch_assoc();

    $jumlah_diminum = (int)($stats_riwayat['diminum'] ?? 0);
    $jumlah_terlewat = (int)($stats_riwayat['terlewat'] ?? 0);
    $persentase_kepatuhan = $total_jadwal > 0 ? round(($jumlah_diminum / $total_jadwal) * 100) : 0;
} catch (Exception $e) {}


// ── Jadwal 5-10 menit mendatang  ──
$next_warning = null;
try {
    $stmt = $conn->prepare("
        SELECT jr.jam_minum, m.nama_obat,
               TIMESTAMPDIFF(MINUTE, CURTIME(), jr.jam_minum) AS menit_menuju
        FROM jadwal_reminder jr
        JOIN obat o ON jr.id_obat_user = o.id_obat_user
        JOIN master_obat m ON o.id_obat = m.id_obat
        WHERE o.user_id = ? AND jr.status_hari_ini = 0
          AND TIME_TO_SEC(TIMEDIFF(jr.jam_minum, CURTIME())) BETWEEN 300 AND 600
        LIMIT 1
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $next_warning = $stmt->get_result()->fetch_assoc();
} catch (Exception $e) {}
?>

<?php if ($next_warning): ?>
<div class="container" style="padding-top:var(--space-4);padding-bottom:0">
    <div class="warning-banner">
        <div class="warning-banner-inner">
            <span class="material-symbols-sharp" style="color:var(--secondary);font-size:28px;">alarm</span>
            <div class="warning-banner-text">
                <strong><?= htmlspecialchars($next_warning['nama_obat']) ?></strong> akan diminum dalam 
                <strong><?= $next_warning['menit_menuju'] ?> menit</strong> lagi &mdash; 
                jadwal pukul <?= substr($next_warning['jam_minum'], 0, 5) ?> WITA
            </div>
            <a href="#jadwal-section" class="btn btn-sm btn-primary" style="flex-shrink:0;">
                <span class="material-symbols-sharp" style="font-size:14px">check</span> Lihat Jadwal
            </a>
        </div>
    </div>
</div>
<style>
.warning-banner {
    background: color-mix(in srgb, var(--secondary) 12%, transparent);
    border: 1px solid var(--secondary);
    border-radius: var(--radius-sm);
    padding: var(--space-3) var(--space-4);
    margin-bottom: var(--space-4);
}
.warning-banner-inner {
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
}
.warning-banner-text {
    flex: 1; font-size: var(--fs-sm);
    color: var(--on-surface-variant);
}
.warning-banner-text strong { color: var(--on-surface); }
</style>
</div>
<?php endif; ?>

<section class="hero hero-compact">
    <div class="hero-inner">
        <div class="hero-text">
            <h1>Selamat Datang, <?= htmlspecialchars($nama_user) ?>!</h1>
            <p>Berdayakan Hidup Melalui Kesehatan. Navigasi kesehatan bersama ForestView.</p>
        </div>
        <div class="hero-image">
            <span class="material-symbols-sharp" style="font-size:56px">heart_plus</span>
        </div>
    </div>
</section>

<div class="container" style="padding-top:var(--space-4);padding-bottom:var(--space-6)">

<div class="stat-grid" style="margin-bottom:var(--space-4)">
    <div class="stat-card">
        <?php
        $sc = '#dc3545';
        if ($persentase_kepatuhan >= 80) $sc = '#4caf50';
        elseif ($persentase_kepatuhan >= 50) $sc = '#ffc107';
        ?>
        <div class="score-circle" style="background:<?= $sc ?>"><?= $persentase_kepatuhan ?>%</div>
        <span class="stat-label">Kepatuhan Hari Ini</span>
    </div>
    <div class="stat-card success">
        <h3 style="color:var(--primary-container)"><?= $jumlah_diminum ?></h3>
        <span class="material-symbols-sharp" style="color:var(--primary-container);font-size:20px">check_circle</span>
        <span class="stat-label">Diminum</span>
    </div>
    <div class="stat-card danger">
        <h3 style="color:var(--error)"><?= $jumlah_terlewat ?></h3>
        <span class="material-symbols-sharp" style="color:var(--error);font-size:20px">cancel</span>
        <span class="stat-label">Terlewat</span>
    </div>
    <div class="stat-card info">
        <h3 style="color:var(--secondary)"><?= count($jadwal_today) ?></h3>
        <span class="material-symbols-sharp" style="color:var(--secondary);font-size:20px">list_alt</span>
        <span class="stat-label">Total Jadwal</span>
    </div>
</div>

<div class="grid-2" style="margin-bottom:var(--space-4)">
    <div class="card" style="padding:var(--space-4)" id="jadwal-section">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-3)">
            <h5 style="font-weight:var(--fw-bold);display:flex;align-items:center;gap:8px;margin:0">
                <span class="material-symbols-sharp" style="color:var(--secondary)">schedule</span> Jadwal Obat Hari Ini
            </h5>
            <span class="badge badge-primary"><?= $jumlah_diminum ?>/<?= count($jadwal_today) ?: 0 ?> Selesai</span>
        </div>
        <?php if (count($jadwal_today) > 0): ?>
            <?php 
            foreach ($jadwal_today as $item):
                $waktu = substr($item['jam_minum'], 0, 5);
                $status = (int)$item['status_hari_ini'];
                $is_completed = $status === 1 || $status === 2;
                $is_late = $status === 2;
                $is_missed = $status === 99;
                $dot = $is_missed || $is_late ? 'timeline-dot-missed' : ($is_completed ? 'timeline-dot-completed' : 'timeline-dot-pending');
            ?>
            <div class="timeline-item">
                <div class="timeline-time"><?= $waktu ?></div>
                <div class="timeline-dot <?= $dot ?>"></div>
                <div class="timeline-content">
                    <h6><?= htmlspecialchars($item['nama_obat']) ?></h6>
                    <small style="color:var(--on-surface-variant)"><?= htmlspecialchars($item['kategori']) ?> &mdash; Stok: <?= (int)$item['jumlah_stok'] ?></small>
                </div>
                <?php if ($is_completed): ?>
                    <button class="btn btn-sm" style="background:var(--surface-container);color:var(--on-surface-variant);border-radius:var(--radius-full)" disabled>
                        <span class="material-symbols-sharp" style="font-size:14px">check</span> Selesai
                    </button>
                <?php else: ?>
                    <form action="../proses/prosesKonsumsi.php?aksi=catat" method="POST" style="margin:0">
                        <input type="hidden" name="id_jadwal" value="<?= $item['id_jadwal'] ?>">
                        <input type="hidden" name="id_obat_user" value="<?= $item['id_obat_user'] ?>">
                        <input type="hidden" name="jam_minum" value="<?= $item['jam_minum'] ?>">
                        <button type="submit" class="btn btn-sm" style="background:<?= $is_missed ? 'var(--secondary)' : 'var(--primary-container)' ?>;color:<?= $is_missed ? 'var(--on-secondary)' : 'var(--on-primary-fixed)' ?>;border-radius:var(--radius-full)">
                            <span class="material-symbols-sharp" style="font-size:14px">check</span> Minum
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <span class="material-symbols-sharp">calendar_month</span>
                <h6>Belum Ada Jadwal untuk Hari Ini</h6>
                <p style="font-size:var(--fs-sm)">Cari obat dari katalog dan tambahkan jadwal untuk mulai memantau.</p>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <div class="card" style="padding:var(--space-4)">
            <h5 style="font-weight:var(--fw-bold);display:flex;align-items:center;gap:8px;margin-bottom:var(--space-3)">
                <span class="material-symbols-sharp" style="color:var(--secondary)">pills</span> Obat Aktif
            </h5>
            <?php if (count($obat_aktif) > 0): ?>
                <?php foreach ($obat_aktif as $o): ?>
                <div class="obat-aktif-item">
                    <span class="material-symbols-sharp">medication</span>
                    <div style="flex:1">
                        <span style="font-weight:var(--fw-semibold);font-size:var(--fs-sm)"><?= htmlspecialchars($o['nama_obat']) ?></span>
                        <small style="color:var(--on-surface-variant);display:block">
                            <?= htmlspecialchars($o['kategori']) ?> &mdash; Stok: <?= (int)$o['jumlah_stok'] ?>
                            <?php if ((int)$o['jumlah_stok'] <= (int)$o['minimal_notif_stok']): ?>
                                <span style="color:var(--error);font-weight:var(--fw-semibold)">(Stok Menipis!)</span>
                            <?php endif; ?>
                        </small>
                    </div>
                    <form action="../proses/prosesObat.php?aksi=hapus_obat_user" method="POST" style="margin:0">
                        <input type="checkbox" id="hapus_<?= $o['id_obat_user'] ?>" class="hapus-checkbox" style="display:none">
                        <input type="hidden" name="id_obat_user" value="<?= $o['id_obat_user'] ?>">
                        <label for="hapus_<?= $o['id_obat_user'] ?>" class="btn btn-sm" style="background:rgba(186,26,26,0.1);color:var(--error);border:none;border-radius:var(--radius-full);padding:4px 10px;cursor:pointer" title="Hapus obat">
                            <span class="material-symbols-sharp" style="font-size:14px">delete</span>
                        </label>
                        <div class="confirm-overlay">
                            <div class="confirm-modal">
                                <div class="confirm-header">
                                    <span class="material-symbols-sharp icon">warning</span>
                                    <h3>Hapus Obat?</h3>
                                </div>
                                <div class="confirm-body">
                                    Yakin ingin menghapus <strong><?= htmlspecialchars($o['nama_obat']) ?></strong> dari daftar?
                                    <br>Jadwal akan dihapus.
                                </div>
                                <div class="confirm-footer">
                                    <label for="hapus_<?= $o['id_obat_user'] ?>" class="btn" style="background:var(--surface-container);color:var(--on-surface-variant);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;flex:1">Batal</label>
                                    <button type="submit" class="btn" style="background:var(--error);color:var(--on-error);flex:1;justify-content:center;border:none">Hapus</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="padding:var(--space-3)">
                    <span class="material-symbols-sharp">medication</span>
                    <p style="font-size:var(--fs-sm);margin:0">Belum ada obat aktif. Cari dari katalog untuk memulai.</p>
                </div>
            <?php endif; ?>
            <a href="katalogObat.php" class="btn btn-outline btn-sm btn-block" style="margin-top:var(--space-3)">
                <span class="material-symbols-sharp">search</span> Cari Obat dari Katalog
            </a>
            <a href="riwayatObat.php" class="btn btn-outline btn-sm btn-block" style="margin-top:var(--space-2)">
                <span class="material-symbols-sharp">history</span> Lihat Riwayat Minum
            </a>
        </div>
    </div>
</div>

<div class="card" style="padding:var(--space-4);background:var(--surface-container);border-color:transparent">
    <div style="display:flex;gap:var(--space-3);align-items:flex-start">
        <span class="material-symbols-sharp" style="font-size:32px;color:var(--secondary);flex-shrink:0">psychiatry</span>
        <div>
            <h5 style="font-weight:var(--fw-bold);margin-bottom:var(--space-1)">Saran Dokter</h5>
            <p style="font-size:var(--fs-sm);color:var(--on-surface-variant);margin:0">&ldquo;Pastikan Anda minum minimal 2 liter air hari ini dan hindari kafein setelah jam 4 sore untuk tidur yang lebih nyenyak.&rdquo;</p>
        </div>
    </div>
</div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
