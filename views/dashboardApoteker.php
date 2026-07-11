<?php
$base = '..';
require_once __DIR__ . '/../config/database.php';
include __DIR__ . '/../includes/header.php';

if (!$is_logged_in || !$is_admin) {
    header("Location: masuk.php");
    exit;
}

$db = new Database();
$conn = $db->getConn();

$all_obat = [];
$stok_menipis = 0;
$search = $_GET['search'] ?? '';
$bentuk = $_GET['bentuk'] ?? '';

try {
    $sql = "SELECT * FROM master_obat WHERE 1=1";
    $params = [];
    $types = '';
    if (!empty($search)) {
        $sql .= " AND (nama_obat LIKE ? OR kategori LIKE ?)";
        $types .= 'ss';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    if (!empty($bentuk)) {
        $sql .= " AND bentuk = ?";
        $types .= 's';
        $params[] = $bentuk;
    }
    $sql .= " ORDER BY nama_obat ASC";
    if (!empty($params)) {
        $stmt = $db->prepareBind($sql, $params, $types);
        $all_obat = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $result = $conn->query($sql);
        $all_obat = $result->fetch_all(MYSQLI_ASSOC);
    }

    $result = $conn->query("SELECT COUNT(*) FROM master_obat WHERE stok < 5");
    $stok_menipis = (int)$result->fetch_row()[0];
} catch (Exception $e) {}
?>

<div class="dashboard-layout">
    <div class="sidebar">
        <a class="nav-link active" href="dashboardApoteker.php">
            <span class="material-symbols-sharp">dashboard</span> Dashboard
        </a>
        <a class="nav-link" href="dashboardApoteker.php">
            <span class="material-symbols-sharp">medical_services</span> Obat-obatan
        </a>
        <a class="nav-link" href="dasboard.php">
            <span class="material-symbols-sharp">monitoring</span> Statistik Kesehatan
        </a>
        <a class="nav-link" href="#">
            <span class="material-symbols-sharp">calendar_today</span> Janji Temu
        </a>
        <hr style="border-color:var(--outline-variant);margin:var(--space-3) 0">
        <a class="nav-link" href="../index.php">
            <span class="material-symbols-sharp">arrow_back</span> Kembali ke Beranda
        </a>
    </div>

    <div class="dashboard-content">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-4);flex-wrap:wrap;gap:var(--space-3)">
            <div>
                <h2 style="font-size:var(--fs-3xl);font-weight:var(--fw-bold);margin-bottom:var(--space-1)">Dashboard Apoteker</h2>
                <p style="color:var(--on-surface-variant);font-size:var(--fs-sm);margin:0">Kelola stok obat, pantau pengeluaran resep, dan awasi perawatan pasien.</p>
            </div>
            <button class="btn btn-primary-green" onclick="openAddModal()">
                <span class="material-symbols-sharp">add</span> Input Obat Baru
            </button>
        </div>

        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error" style="margin-bottom:var(--space-4)"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" style="margin-bottom:var(--space-4)"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
        <div class="alert alert-success" style="margin-bottom:var(--space-4)"><?= $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?></div>
        <?php endif; ?>

        <div class="stat-grid" style="margin-bottom:var(--space-5)">
            <div class="stat-card success">
                <span class="material-symbols-sharp" style="font-size:28px;color:var(--primary-container)">inventory_2</span>
                <h3 style="color:var(--primary-container)"><?= count($all_obat) ?></h3>
                <span class="stat-label">Total SKU</span>
            </div>
            <div class="stat-card danger">
                <span class="material-symbols-sharp" style="font-size:28px;color:var(--error)">warning</span>
                <h3 style="color:var(--error)"><?= $stok_menipis ?></h3>
                <span class="stat-label">Stok Rendah</span>
            </div>
            <div class="stat-card info">
                <span class="material-symbols-sharp" style="font-size:28px;color:var(--secondary)">done_all</span>
                <h3 style="color:var(--secondary)">84</h3>
                <span class="stat-label">Resep Terlayani</span>
            </div>
            <div class="stat-card">
                <span class="material-symbols-sharp" style="font-size:28px;color:var(--on-surface-variant)">hourglass_empty</span>
                <h3 style="color:var(--on-surface-variant)">06</h3>
                <span class="stat-label">Pesanan Menunggu</span>
            </div>
        </div>

        <?php if ($stok_menipis > 0): ?>
        <div class="card" style="padding:var(--space-3);margin-bottom:var(--space-4);border-left:4px solid var(--error);background:rgba(186,26,26,0.05)">
            <strong style="color:var(--error);display:flex;align-items:center;gap:8px">
                <span class="material-symbols-sharp" style="font-size:18px">warning</span> Peringatan!
            </strong>
            <span style="color:var(--on-surface-variant);font-size:var(--fs-sm)">Ada <?= $stok_menipis ?> jenis obat yang stoknya di bawah 5.</span>
        </div>
        <?php endif; ?>

        <div class="card" style="overflow:hidden">
            <div style="padding:var(--space-4);border-bottom:1px solid var(--outline-variant)">
                <h4 style="font-weight:var(--fw-bold);margin:0;display:flex;align-items:center;gap:8px">
                    <span class="material-symbols-sharp" style="color:var(--secondary)">inventory_2</span> Stok Obat
                </h4>
            </div>
            <form method="GET" action="" style="padding:var(--space-3) var(--space-4);display:flex;gap:var(--space-2);flex-wrap:wrap;align-items:center;border-bottom:1px solid var(--outline-variant)">
                <div style="position:relative;flex:1;min-width:200px">
                    <span class="material-symbols-sharp" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--outline);font-size:18px">search</span>
                    <input type="text" name="search" class="form-input" placeholder="Cari nama atau kategori..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="padding-left:38px">
                </div>
                <select name="bentuk" class="form-select" style="width:auto;min-width:120px">
                    <option value="">Semua Bentuk</option>
                    <option value="Tablet" <?= ($_GET['bentuk'] ?? '') == 'Tablet' ? 'selected' : '' ?>>Tablet</option>
                    <option value="Kapsul" <?= ($_GET['bentuk'] ?? '') == 'Kapsul' ? 'selected' : '' ?>>Kapsul</option>
                    <option value="Sirup" <?= ($_GET['bentuk'] ?? '') == 'Sirup' ? 'selected' : '' ?>>Sirup</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                <a href="dashboardApoteker.php" class="btn btn-sm" style="background:var(--surface-container);color:var(--on-surface-variant)">Reset</a>
            </form>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:50px">No</th>
                            <th>Nama Obat</th>
                            <th>Kategori</th>
                            <th>Tingkat Stok</th>
                            <th>Kedaluwarsa</th>
                            <th style="width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($all_obat as $row): ?>
                        <tr>
                            <td style="text-align:center;color:var(--on-surface-variant)"><?= $no++ ?></td>
                            <td>
                                <span style="font-weight:var(--fw-semibold)"><?= htmlspecialchars($row['nama_obat']) ?> <?= htmlspecialchars($row['dosis'] ?? '') ?></span>
                                <br><small style="color:var(--on-surface-variant)"><?= htmlspecialchars($row['bentuk']) ?></small>
                            </td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($row['kategori']) ?></span></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span class="badge <?= ($row['stok'] ?? 0) < 5 ? 'badge-danger' : 'badge-success' ?>">
                                        <?= (int)($row['stok'] ?? 0) ?> unit
                                    </span>
                                </div>
                            </td>
                            <td style="color:var(--on-surface-variant);font-size:var(--fs-sm)">Des 2025</td>
                            <td>
                                <button class="btn btn-sm" style="background:rgba(255,193,7,0.2);color:#8a6d00;border:none;border-radius:var(--radius-sm)" onclick="editObat(<?= $row['master_id'] ?>)">
                                    <span class="material-symbols-sharp" style="font-size:16px">edit_square</span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($all_obat) === 0): ?>
                        <tr><td colspan="6" style="text-align:center;padding:var(--space-5);color:var(--on-surface-variant)">Data obat tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:var(--space-3) var(--space-4);border-top:1px solid var(--outline-variant);text-align:center">
                <a href="#" style="font-size:var(--fs-sm);font-weight:var(--fw-semibold)">Lihat Semua Inventaris</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Obat -->
<div class="modal" id="addModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:300;align-items:center;justify-content:center" onclick="closeModal(event)">
    <div class="card" style="width:480px;max-width:90vw;padding:0;overflow:hidden;box-shadow:var(--shadow-lg);max-height:90vh;overflow-y:auto" onclick="event.stopPropagation()">
        <div style="padding:var(--space-4);display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--outline-variant)">
            <h4 style="font-weight:var(--fw-bold);margin:0;display:flex;align-items:center;gap:8px">
                <span class="material-symbols-sharp" style="color:var(--secondary)">add_circle</span> Tambah Stok Obat
            </h4>
            <button class="theme-toggle" onclick="closeAddModal()" style="width:32px;height:32px"><span class="material-symbols-sharp">close</span></button>
        </div>
        <form action="../proses/prosesObat.php?aksi=tambah" method="POST" style="padding:var(--space-4)">
            <div class="form-group">
                <label class="form-label">Nama Obat</label>
                <input type="text" name="nama_obat" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <input type="text" name="kategori" class="form-input" placeholder="Contoh: Antibiotik, Analgesik" required>
            </div>
            <div class="form-group">
                <label class="form-label">Bentuk</label>
                <select name="bentuk" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="Tablet">Tablet</option>
                    <option value="Kapsul">Kapsul</option>
                    <option value="Sirup">Sirup</option>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
                <div class="form-group">
                    <label class="form-label">Dosis</label>
                    <input type="text" name="dosis" class="form-input" placeholder="500mg" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="satuan" class="form-input" placeholder="Strip" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah Stok</label>
                <input type="number" name="stok" class="form-input" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-input" rows="2" placeholder="Opsional"></textarea>
            </div>
            <div style="display:flex;gap:var(--space-2);justify-content:flex-end;margin-top:var(--space-4)">
                <button type="button" class="btn" style="background:var(--surface-container);color:var(--on-surface-variant)" onclick="closeAddModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Obat -->
<div class="modal" id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:300;align-items:center;justify-content:center" onclick="closeModal(event)">
    <div class="card" style="width:480px;max-width:90vw;padding:0;overflow:hidden;box-shadow:var(--shadow-lg);max-height:90vh;overflow-y:auto" onclick="event.stopPropagation()">
        <div style="padding:var(--space-4);display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--outline-variant)">
            <h4 style="font-weight:var(--fw-bold);margin:0;display:flex;align-items:center;gap:8px">
                <span class="material-symbols-sharp" style="color:var(--secondary)">edit</span> Edit Data Obat
            </h4>
            <button class="theme-toggle" onclick="closeEditModal()" style="width:32px;height:32px"><span class="material-symbols-sharp">close</span></button>
        </div>
        <form action="../proses/prosesObat.php?aksi=edit" method="POST" style="padding:var(--space-4)">
            <input type="hidden" name="master_id" id="editMasterId">
            <div class="form-group">
                <label class="form-label">Nama Obat</label>
                <input type="text" name="nama_obat" id="editNama" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <input type="text" name="kategori" id="editKategori" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Bentuk</label>
                <select name="bentuk" id="editBentuk" class="form-select" required>
                    <option value="Tablet">Tablet</option>
                    <option value="Kapsul">Kapsul</option>
                    <option value="Sirup">Sirup</option>
                </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
                <div class="form-group">
                    <label class="form-label">Dosis</label>
                    <input type="text" name="dosis" id="editDosis" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan</label>
                    <input type="text" name="satuan" id="editSatuan" class="form-input" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" id="editStok" class="form-input" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="editDeskripsi" class="form-input" rows="2"></textarea>
            </div>
            <div style="display:flex;gap:var(--space-2);justify-content:flex-end;margin-top:var(--space-4)">
                <button type="button" class="btn" style="background:var(--surface-container);color:var(--on-surface-variant)" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
const allObat = <?= json_encode($all_obat) ?>;

function openAddModal() {
    document.getElementById('addModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
    document.body.style.overflow = '';
}
function closeModal(e) {
    if (e && e.target === e.currentTarget) {
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
        document.body.style.overflow = '';
    }
}

function editObat(id) {
    const obat = allObat.find(o => o.master_id == id);
    if (!obat) return;
    document.getElementById('editMasterId').value = obat.master_id;
    document.getElementById('editNama').value = obat.nama_obat;
    document.getElementById('editKategori').value = obat.kategori;
    document.getElementById('editBentuk').value = obat.bentuk;
    document.getElementById('editDosis').value = obat.dosis;
    document.getElementById('editSatuan').value = obat.satuan;
    document.getElementById('editStok').value = obat.stok;
    document.getElementById('editDeskripsi').value = obat.deskripsi || '';
    document.getElementById('editModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
        document.body.style.overflow = '';
    }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
