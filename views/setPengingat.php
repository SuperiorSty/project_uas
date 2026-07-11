<?php
$base = '..';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

if (!$is_logged_in) {
    header("Location: masuk.php");
    exit;
}

$id_obat = (int)($_GET['id_obat'] ?? 0);
if ($id_obat <= 0) {
    header("Location: katalogObat.php?error=Obat tidak valid.");
    exit;
}

$db = new Database();
$conn = $db->getConn();

$obat = null;
try {
    $stmt = $conn->prepare("SELECT * FROM master_obat WHERE id_obat = ?");
    $stmt->bind_param("i", $id_obat);
    $stmt->execute();
    $obat = $stmt->get_result()->fetch_assoc();
} catch (Exception $e) {}

if (!$obat) {
    header("Location: katalogObat.php?error=Obat tidak ditemukan.");
    exit;
}
?>

<section class="section-padding">
    <div class="container" style="max-width:640px">
        <a href="katalogObat.php" class="btn btn-sm" style="background:var(--surface-container);color:var(--on-surface-variant);margin-bottom:var(--space-4)">
            <span class="material-symbols-sharp" style="font-size:16px">arrow_back</span> Kembali ke Katalog
        </a>

        <div class="card" style="overflow:hidden">
            <div style="padding:var(--space-4);border-bottom:1px solid var(--outline-variant)">
                <div style="display:flex;align-items:center;gap:12px">
                    <span class="material-symbols-sharp" style="font-size:36px;color:var(--secondary)">medication</span>
                    <div>
                        <h3 style="font-weight:var(--fw-bold);margin:0"><?= htmlspecialchars($obat['nama_obat']) ?></h3>
                        <small style="color:var(--on-surface-variant)"><?= htmlspecialchars($obat['kategori']) ?></small>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error" style="margin:var(--space-4);margin-bottom:0"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="../proses/prosesSetPengingat.php" method="POST" style="padding:var(--space-4)">
                <input type="hidden" name="id_obat" value="<?= $id_obat ?>">

                <div class="form-group">
                    <label class="form-label">Jumlah Stok</label>
                    <p style="font-size:var(--fs-xs);color:var(--on-surface-variant);margin-top:-8px;margin-bottom:var(--space-2)">Masukkan jumlah stok obat yang Anda peroleh dari apotek.</p>
                    <input type="number" name="jumlah_stok" class="form-input" min="0" value="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Frekuensi Minum</label>
                    <p style="font-size:var(--fs-xs);color:var(--on-surface-variant);margin-top:-8px;margin-bottom:var(--space-2)">Berapa kali sehari obat ini harus diminum?</p>
                    <div style="display:flex;gap:var(--space-2)">
                        <label class="radio-card" onclick="selectFrekuensi(1)">
                            <input type="radio" name="frekuensi" value="1" checked onchange="updateJamInputs()">
                            <span>1x Sehari</span>
                        </label>
                        <label class="radio-card" onclick="selectFrekuensi(2)">
                            <input type="radio" name="frekuensi" value="2" onchange="updateJamInputs()">
                            <span>2x Sehari</span>
                        </label>
                        <label class="radio-card" onclick="selectFrekuensi(3)">
                            <input type="radio" name="frekuensi" value="3" onchange="updateJamInputs()">
                            <span>3x Sehari</span>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipe Jadwal</label>
                    <p style="font-size:var(--fs-xs);color:var(--on-surface-variant);margin-top:-8px;margin-bottom:var(--space-2)">Pilih cara menentukan jam minum obat.</p>
                    <div style="display:flex;gap:var(--space-2)">
                        <label class="radio-card" onclick="selectTipe('spesifik')">
                            <input type="radio" name="tipe_jadwal" value="spesifik" checked onchange="toggleTipeJadwal()">
                            <span>Jam Spesifik</span>
                        </label>
                        <label class="radio-card" onclick="selectTipe('interval')">
                            <input type="radio" name="tipe_jadwal" value="interval" onchange="toggleTipeJadwal()">
                            <span>Interval</span>
                        </label>
                    </div>
                </div>

                <div id="spesifik_container">
                    <div class="form-group">
                        <label class="form-label">Jam Minum</label>
                        <p style="font-size:var(--fs-xs);color:var(--on-surface-variant);margin-top:-8px;margin-bottom:var(--space-2)">Atur jam spesifik kapan obat harus diminum.</p>
                        <div id="jam_inputs">
                            <div class="form-group" style="margin-bottom:var(--space-2)">
                                <input type="time" name="jam_minum[]" class="form-input" value="08:00" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="interval_container" style="display:none">
                    <div class="form-group">
                        <label class="form-label">Jam Minum Pertama</label>
                        <p style="font-size:var(--fs-xs);color:var(--on-surface-variant);margin-top:-8px;margin-bottom:var(--space-2)">Atur jam dosis pertama, sisanya akan dihitung otomatis.</p>
                        <input type="time" name="jam_pertama" class="form-input" value="08:00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gap Antar Jam (jam)</label>
                        <input type="number" name="gap_jam" class="form-input" step="0.5" min="1" value="8">
                        <p style="font-size:var(--fs-xs);color:var(--on-surface-variant);margin-top:4px">Contoh: setiap 12 jam, setiap 8 jam.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Notifikasi Stok Menipis</label>
                    <p style="font-size:var(--fs-xs);color:var(--on-surface-variant);margin-top:-8px;margin-bottom:var(--space-2)">Kirim notifikasi saat stok tersisa jumlah berikut:</p>
                    <input type="number" name="minimal_notif_stok" class="form-input" min="1" value="3" required>
                </div>

                <div style="display:flex;gap:var(--space-2);justify-content:flex-end;margin-top:var(--space-4)">
                    <a href="katalogObat.php" class="btn" style="background:var(--surface-container);color:var(--on-surface-variant)">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Pengingat</button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
.radio-card {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    border: 1.5px solid var(--outline-variant);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all 0.15s ease;
    flex: 1;
    text-align: center;
    justify-content: center;
    font-weight: var(--fw-semibold);
    font-size: var(--fs-sm);
}
.radio-card:has(input:checked) {
    border-color: var(--secondary);
    background: color-mix(in srgb, var(--secondary) 10%, transparent);
}
.radio-card input { display: none; }
</style>

<script>
function selectFrekuensi(val) {
    document.querySelectorAll('[name="frekuensi"]').forEach(r => r.checked = r.value == val);
    updateJamInputs();
}

function selectTipe(val) {
    document.querySelectorAll('[name="tipe_jadwal"]').forEach(r => r.checked = r.value == val);
    toggleTipeJadwal();
}

function toggleTipeJadwal() {
    const isSpesifik = document.querySelector('[name="tipe_jadwal"]:checked').value === 'spesifik';
    document.getElementById('spesifik_container').style.display = isSpesifik ? 'block' : 'none';
    document.getElementById('interval_container').style.display = isSpesifik ? 'none' : 'block';
}

function updateJamInputs() {
    const frek = parseInt(document.querySelector('[name="frekuensi"]:checked').value) || 1;
    const container = document.getElementById('jam_inputs');
    const current = container.querySelectorAll('.form-group').length;

    if (current < frek) {
        const baseTime = container.querySelector('input')?.value || '08:00';
        for (let i = current; i < frek; i++) {
            const div = document.createElement('div');
            div.className = 'form-group';
            div.style.marginBottom = 'var(--space-2)';
            const input = document.createElement('input');
            input.type = 'time';
            input.name = 'jam_minum[]';
            input.className = 'form-input';
            // Auto-set subsequent times 8 hours apart
            const [h, m] = baseTime.split(':').map(Number);
            const nh = (h + i * 8) % 24;
            input.value = String(nh).padStart(2, '0') + ':' + String(m).padStart(2, '0');
            input.required = true;
            div.appendChild(input);
            container.appendChild(div);
        }
    } else if (current > frek) {
        for (let i = current - 1; i >= frek; i--) {
            container.removeChild(container.children[i]);
        }
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
