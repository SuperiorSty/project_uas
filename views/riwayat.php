<?php
session_start();
require_once '../config/database.php';
$pdo = Database::connect();

// Jika belum login, tendang balik ke halaman login (index.php)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$current_user_id = $_SESSION['user_id']; 

// --- AMBIL DAFTAR RIWAYAT KONSUMSI OBAT (HANYA UNTUK USER YANG SEDANG LOGIN) ---
$sql_riwayat = "SELECT r.*, m.nama_obat, m.dosis, m.bentuk 
                FROM riwayat_obat r
                INNER JOIN master_obat m ON r.obat_id = m.master_id
                WHERE r.user_id = ? 
                ORDER BY r.waktu_jadwal DESC LIMIT 20";

$stmt_riwayat = $pdo->prepare($sql_riwayat);
$stmt_riwayat->execute([$current_user_id]);
$riwayat_list = $stmt_riwayat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Konsumsi Obat - PengingatObat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand h2 { color: #3688C9; font-weight: 700; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        
        /* Header Gradasi Senada dengan Search Box Beranda */
        .page-header-box { 
            background: linear-gradient(135deg, #3688C9, #36B6C9); 
            padding: 40px; 
            border-radius: 20px; 
            color: white; 
        }

        /* Navigasi Dropdown Menu */
        .btn-profile { background-color: white; border: 1px solid #dee2e6; border-radius: 10px; transition: all 0.3s ease; }
        .btn-profile:hover { background-color: #f8f9fa; }

        /* Custom badge status */
        .badge-diminum { background-color: #28a745; color: white; font-weight: 600; }
        .badge-terlewat { background-color: #dc3545; color: white; font-weight: 600; }
        .badge-ditunda { background-color: #6c757d; color: white; font-weight: 600; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <h2 class="m-0"><i class="fa-solid fa-capsules me-2"></i>PengingatObat</h2>
            </a>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-profile d-flex align-items-center gap-2 px-3 py-2 shadow-sm" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-circle-user text-secondary fs-4"></i>
                        <span class="fw-semibold text-dark text-capitalize">
                            <?= htmlspecialchars($_SESSION['nama_user']); ?>
                        </span>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2 rounded-3" aria-labelledby="userMenuButton" style="min-width: 220px;">
                        <li>
                            <a class="dropdown-item py-2 text-muted rounded-2" href="dashboard.php">
                                <i class="fa-solid fa-gauge me-2" style="color: #3688C9;"></i> Dashboard Sesi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 text-muted rounded-2 active" href="riwayat.php" style="background-color: #3688C9; color: white !important;">
                                <i class="fa-solid fa-clock-history me-2"></i> Riwayat Minum Obat
                            </a>
                        </li>
                        <li><hr class="dropdown-divider text-muted"></li>
                        <li>
                            <a class="dropdown-item py-2 text-danger fw-semibold rounded-2" href="../proses/prosesLogout.php">
                                <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar Akun
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <div class="page-header-box mb-5 shadow-sm">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="fw-bold mb-2"><i class="fa-solid fa-notes-medical me-2"></i>Log Riwayat Konsumsi</h1>
                    <p class="lead mb-0">Semua data kepatuhan minum obat Anda tercatat secara otomatis demi memantau proses kesembuhan.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="dashboard.php" class="btn btn-light fw-bold rounded-pill px-4 py-2 text-dark shadow-sm">
                        <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="text-center" style="width: 70px;">No</th>
                            <th>Nama Obat</th>
                            <th>Dosis / Bentuk</th>
                            <th>Waktu Jadwal</th>
                            <th>Waktu Realisasi Minum</th>
                            <th class="text-center" style="width: 150px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($riwayat_list as $row): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++ ?></td>
                                <td>
                                    <span class="text-dark fw-bold"><?= htmlspecialchars($row['nama_obat']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1.5"><?= htmlspecialchars($row['dosis']) ?> (<?= htmlspecialchars($row['bentuk']) ?>)</span>
                                </td>
                                <td class="text-muted small">
                                    <i class="fa-regular fa-calendar me-1"></i> <?= htmlspecialchars($row['waktu_jadwal']) ?>
                                </td>
                                <td class="small">
                                    <?php if ($row['waktu_diminum']): ?>
                                        <span class="text-success fw-semibold">
                                            <i class="fa-solid fa-check-double me-1"></i> <?= htmlspecialchars($row['waktu_diminum']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted text-opacity-50 italic">
                                            <i class="fa-solid fa-minus me-1"></i> <em class="text-secondary opacity-75">Tidak tercatat (Terlewat/Ditunda)</em>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $badge_class = 'badge-ditunda'; 
                                        if ($row['status'] == 'Sudah Diminum') $badge_class = 'badge-diminum'; 
                                        if ($row['status'] == 'Terlewat') $badge_class = 'badge-terlewat'; 
                                    ?>
                                    <span class="badge <?= $badge_class ?> rounded-pill px-3 py-2 shadow-sm text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($riwayat_list) == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-2 mb-3 text-secondary opacity-50 d-block"></i>
                                    Belum ada catatan riwayat konsumsi obat yang tersimpan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <footer class="bg-white text-center py-4 border-top mt-5">
        <p class="text-muted m-0">&copy; 2026 Kelompok UAS ITB STIKOM Bali. Desain terinspirasi oleh Halodoc.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>