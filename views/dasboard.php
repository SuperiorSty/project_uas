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
    $stmt = $conn->prepare("SELECT r.*, o.nama_obat, o.dosis, o.aturan_pakai FROM riwayat_obat r JOIN obat o ON r.obat_id = o.id WHERE r.user_id = ? AND DATE(r.waktu_jadwal) = CURDATE() ORDER BY r.waktu_jadwal ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute(); $jadwal_today = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt = $conn->prepare("SELECT COUNT(*) FROM riwayat_obat WHERE user_id = ? AND status = 'Sudah Diminum'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute(); $jumlah_diminum = (int)$stmt->get_result()->fetch_row()[0];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM riwayat_obat WHERE user_id = ? AND status = 'Terlewat'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute(); $jumlah_terlewat = (int)$stmt->get_result()->fetch_row()[0];

    $total_jadwal = $jumlah_diminum + $jumlah_terlewat;
    $persentase_kepatuhan = $total_jadwal > 0 ? round(($jumlah_diminum / $total_jadwal) * 100) : 0;

    $stmt = $conn->prepare("SELECT DISTINCT o.id, o.nama_obat, o.dosis, o.aturan_pakai FROM riwayat_obat r JOIN obat o ON r.obat_id = o.id WHERE r.user_id = ? ORDER BY o.nama_obat ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute(); $obat_aktif = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {}
?>

<section class="hero hero-compact">
    <div class="hero-inner">
        <div class="hero-text">
            <h1>Selamat Pagi, <?= htmlspecialchars($nama_user) ?>!</h1>
            <p>Berdayakan Hidup Melalui Kesehatan. Navigasi kesehatan bersama ForestView.</p>
            <a href="daftar.php" class="btn btn-ghost">
                <span class="material-symbols-sharp">notifications_active</span> Lihat Pengingat Obat
            </a>
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
        <span class="stat-label">Kepatuhan</span>
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
        <h3 style="color:var(--secondary)"><?= $total_jadwal ?></h3>
        <span class="material-symbols-sharp" style="color:var(--secondary);font-size:20px">list_alt</span>
        <span class="stat-label">Total Jadwal</span>
    </div>
</div>

<div class="grid-2" style="margin-bottom:var(--space-4)">
    <div class="card" style="padding:var(--space-4)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-3)">
            <h5 style="font-weight:var(--fw-bold);display:flex;align-items:center;gap:8px;margin:0">
                <span class="material-symbols-sharp" style="color:var(--secondary)">schedule</span> Jadwal Obat Hari Ini
            </h5>
            <span class="badge badge-primary"><?= $jumlah_diminum ?>/<?= $total_jadwal ?: 0 ?> Selesai</span>
        </div>
        <?php if (count($jadwal_today) > 0): ?>
            <?php foreach ($jadwal_today as $item):
                $waktu = date('H:i', strtotime($item['waktu_jadwal']));
                $is_completed = $item['status'] === 'Sudah Diminum';
                $is_missed = $item['status'] === 'Terlewat';
                $dot = $is_completed ? 'timeline-dot-completed' : ($is_missed ? 'timeline-dot-missed' : 'timeline-dot-pending');
            ?>
            <div class="timeline-item">
                <div class="timeline-time"><?= $waktu ?></div>
                <div class="timeline-dot <?= $dot ?>"></div>
                <div class="timeline-content">
                    <h6><?= htmlspecialchars($item['nama_obat']) ?> <small style="color:var(--on-surface-variant);font-weight:var(--fw-regular)"><?= htmlspecialchars($item['dosis']) ?></small></h6>
                    <small style="color:var(--on-surface-variant)"><?= htmlspecialchars($item['aturan_pakai'] ?? '-') ?></small>
                </div>
                <?php if (!$is_completed && !$is_missed): ?>
                    <form action="../proses/prosesKonsumsi.php?aksi=catat" method="POST" style="margin:0">
                        <input type="hidden" name="riwayat_id" value="<?= $item['id'] ?>">
                        <input type="hidden" name="status" value="Sudah Diminum">
                        <button type="submit" class="btn btn-sm" style="background:var(--primary-container);color:var(--on-primary-fixed);border-radius:var(--radius-full)">
                            <span class="material-symbols-sharp" style="font-size:14px">check</span> Minum
                        </button>
                    </form>
                <?php elseif ($is_missed): ?>
                    <form action="../proses/prosesKonsumsi.php?aksi=catat" method="POST" style="margin:0">
                        <input type="hidden" name="riwayat_id" value="<?= $item['id'] ?>">
                        <input type="hidden" name="status" value="Sudah Diminum">
                        <button type="submit" class="btn btn-sm" style="background:var(--secondary);color:var(--on-secondary);border-radius:var(--radius-full)">
                            <span class="material-symbols-sharp" style="font-size:14px">refresh</span> Catat
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-sm" style="background:var(--surface-container);color:var(--on-surface-variant);border-radius:var(--radius-full)" disabled>
                        <span class="material-symbols-sharp" style="font-size:14px">check</span> Selesai
                    </button>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <span class="material-symbols-sharp">calendar_month</span>
                <h6>Belum Ada Jadwal untuk Hari Ini</h6>
                <p style="font-size:var(--fs-sm)">Cari obat dari katalog dan tambahkan ke obatmu untuk mulai memantau jadwal.</p>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <div class="card" style="padding:var(--space-4);margin-bottom:var(--space-4)">
            <h5 style="font-weight:var(--fw-bold);display:flex;align-items:center;gap:8px;margin-bottom:var(--space-3)">
                <span class="material-symbols-sharp" style="color:var(--secondary)">monitoring</span> Statistik Vital
            </h5>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
                <div class="card" style="padding:var(--space-3);text-align:center;box-shadow:none">
                    <span class="material-symbols-sharp" style="font-size:28px;color:var(--secondary)">directions_walk</span>
                    <h3 style="font-weight:var(--fw-bold);margin:var(--space-1) 0">8.420</h3>
                    <small style="color:var(--on-surface-variant)">/10rb Langkah</small>
                </div>
                <div class="card" style="padding:var(--space-3);text-align:center;box-shadow:none">
                    <span class="material-symbols-sharp" style="font-size:28px;color:var(--error)">favorite</span>
                    <h3 style="font-weight:var(--fw-bold);margin:var(--space-1) 0">101</h3>
                    <small style="color:var(--on-surface-variant)">bpm</small>
                </div>
            </div>
        </div>

        <div class="card" style="padding:var(--space-4)">
            <h5 style="font-weight:var(--fw-bold);display:flex;align-items:center;gap:8px;margin-bottom:var(--space-3)">
                <span class="material-symbols-sharp" style="color:var(--secondary)">pills</span> Obat Aktif
            </h5>
            <?php if (count($obat_aktif) > 0): ?>
                <?php foreach ($obat_aktif as $o): ?>
                <div class="obat-aktif-item">
                    <span class="material-symbols-sharp">medication</span>
                    <div>
                        <span style="font-weight:var(--fw-semibold);font-size:var(--fs-sm)"><?= htmlspecialchars($o['nama_obat']) ?></span>
                        <small style="color:var(--on-surface-variant);display:block"><?= htmlspecialchars($o['dosis']) ?> &mdash; <?= htmlspecialchars($o['aturan_pakai'] ?? '-') ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="padding:var(--space-3)">
                    <span class="material-symbols-sharp">medication</span>
                    <p style="font-size:var(--fs-sm);margin:0">Belum ada obat aktif. Cari dari katalog untuk memulai.</p>
                </div>
            <?php endif; ?>
            <a href="../index.php" class="btn btn-outline btn-sm btn-block" style="margin-top:var(--space-3)">
                <span class="material-symbols-sharp">search</span> Cari Obat dari Katalog
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
