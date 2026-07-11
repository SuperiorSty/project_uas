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
$search = $_GET['search'] ?? '';

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
    $sql .= " ORDER BY nama_obat ASC";
    if (!empty($params)) {
        $stmt = $db->prepareBind($sql, $params, $types);
        $all_obat = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $result = $conn->query($sql);
        $all_obat = $result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {}
?>

<div class="container" style="padding-top:var(--space-4);padding-bottom:var(--space-6)">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-4);flex-wrap:wrap;gap:var(--space-3)">
        <div>
            <h2 style="font-size:var(--fs-3xl);font-weight:var(--fw-bold);margin-bottom:var(--space-1)">Dashboard Apoteker</h2>
            <p style="color:var(--on-surface-variant);font-size:var(--fs-sm);margin:0">Kelola katalog obat di sistem.</p>
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
            <span class="stat-label">Total Obat</span>
        </div>
    </div>

    <div class="card" style="overflow:hidden">
        <div style="padding:var(--space-4);border-bottom:1px solid var(--outline-variant)">
            <h4 style="font-weight:var(--fw-bold);margin:0;display:flex;align-items:center;gap:8px">
                <span class="material-symbols-sharp" style="color:var(--secondary)">inventory_2</span> Katalog Obat
            </h4>
        </div>
        <form method="GET" action="" style="padding:var(--space-3) var(--space-4);display:flex;gap:var(--space-2);flex-wrap:wrap;align-items:center;border-bottom:1px solid var(--outline-variant)">
            <div style="position:relative;flex:1;min-width:200px">
                <span class="material-symbols-sharp" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--outline);font-size:18px">search</span>
                <input type="text" name="search" class="form-input" placeholder="Cari nama atau kategori..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="padding-left:38px">
            </div>
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
                        <th>Deskripsi</th>
                        <th style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($all_obat as $row): ?>
                    <tr>
                        <td style="text-align:center;color:var(--on-surface-variant)"><?= $no++ ?></td>
                        <td>
                            <span style="font-weight:var(--fw-semibold)"><?= htmlspecialchars($row['nama_obat']) ?></span>
                        </td>
                        <td><span class="badge badge-primary"><?= htmlspecialchars($row['kategori']) ?></span></td>
                        <td style="color:var(--on-surface-variant);font-size:var(--fs-sm);max-width:300px">
                            <?= htmlspecialchars($row['deskripsi'] ?? '-') ?>
                        </td>
                        <td>
                            <button class="btn btn-sm" style="background:rgba(255,193,7,0.2);color:#8a6d00;border:none;border-radius:var(--radius-sm)" onclick="editObat(<?= $row['id_obat'] ?>)">
                                <span class="material-symbols-sharp" style="font-size:16px">edit_square</span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (count($all_obat) === 0): ?>
                    <tr><td colspan="5" style="text-align:center;padding:var(--space-5);color:var(--on-surface-variant)">Data obat tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Obat -->
<div class="modal" id="addModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:300;align-items:center;justify-content:center" onclick="closeModal(event)">
    <div class="card" style="width:480px;max-width:90vw;padding:0;overflow:hidden;box-shadow:var(--shadow-lg)" onclick="event.stopPropagation()">
        <div style="padding:var(--space-4);display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--outline-variant)">
            <h4 style="font-weight:var(--fw-bold);margin:0;display:flex;align-items:center;gap:8px">
                <span class="material-symbols-sharp" style="color:var(--secondary)">add_circle</span> Tambah Obat
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
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-input" rows="3" placeholder="Opsional"></textarea>
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
    <div class="card" style="width:480px;max-width:90vw;padding:0;overflow:hidden;box-shadow:var(--shadow-lg)" onclick="event.stopPropagation()">
        <div style="padding:var(--space-4);display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--outline-variant)">
            <h4 style="font-weight:var(--fw-bold);margin:0;display:flex;align-items:center;gap:8px">
                <span class="material-symbols-sharp" style="color:var(--secondary)">edit</span> Edit Data Obat
            </h4>
            <button class="theme-toggle" onclick="closeEditModal()" style="width:32px;height:32px"><span class="material-symbols-sharp">close</span></button>
        </div>
        <form action="../proses/prosesObat.php?aksi=edit" method="POST" style="padding:var(--space-4)">
            <input type="hidden" name="id_obat" id="editIdObat">
            <div class="form-group">
                <label class="form-label">Nama Obat</label>
                <input type="text" name="nama_obat" id="editNama" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <input type="text" name="kategori" id="editKategori" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="editDeskripsi" class="form-input" rows="3"></textarea>
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
    const obat = allObat.find(o => o.id_obat == id);
    if (!obat) return;
    document.getElementById('editIdObat').value = obat.id_obat;
    document.getElementById('editNama').value = obat.nama_obat;
    document.getElementById('editKategori').value = obat.kategori;
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
