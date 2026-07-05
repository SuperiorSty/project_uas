<?php
session_start();
require_once '../config/database.php';
$db = Database::connect();

// Proteksi: Jika belum login, tendang ke index.php
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama_user = $_SESSION['nama_user'];

// --- LOGIKA HITUNG KEPATUHAN (TUGAS REKONSILIASI DATA) ---

// 1. Hitung total obat yang "Sudah Diminum" oleh user ini
$stmt_diminum = $db->prepare("SELECT COUNT(*) FROM riwayat_obat WHERE user_id = ? AND status = 'Sudah Diminum'");
$stmt_diminum->execute([$user_id]);
$jumlah_diminum = $stmt_diminum->fetchColumn();

// 2. Hitung total obat yang "Terlewat" oleh user ini
$stmt_terlewat = $db->prepare("SELECT COUNT(*) FROM riwayat_obat WHERE user_id = ? AND status = 'Terlewat'");
$stmt_terlewat->execute([$user_id]);
$jumlah_terlewat = $stmt_terlewat->fetchColumn();

// 3. Hitung persentase total (Abaikan status 'Ditunda' karena belum ada konfirmasi akhir)
$total_jadwal = $jumlah_diminum + $jumlah_terlewat;
$persentase_kepatuhan = 0;

if ($total_jadwal > 0) {
    $persentase_kepatuhan = round(($jumlah_diminum / $total_jadwal) * 100);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien - PengingatObat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-brand h2 { color: #3688C9; font-weight: 700; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        
        /* Gradient Box Khas Halodoc */
        .dashboard-header-box { 
            background: linear-gradient(135deg, #3688C9, #36B6C9); 
            padding: 40px; 
            border-radius: 20px; 
            color: white; 
        }

        .btn-profile { background-color: white; border: 1px solid #dee2e6; border-radius: 10px; transition: all 0.3s ease; }
        .btn-profile:hover { background-color: #f8f9fa; }

        /* Lingkaran Persentase Skor */
        .score-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            margin: 0 auto 15px auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
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
                            <?= htmlspecialchars($nama_user); ?>
                        </span>
                        <i class="fa-solid fa-chevron-down text-muted" style="font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2 rounded-3" aria-labelledby="userMenuButton" style="min-width: 220px;">
                        <li>
                            <a class="dropdown-item py-2 text-muted rounded-2 active" href="dashboard.php" style="background-color: #3688C9; color: white !important;">
                                <i class="fa-solid fa-gauge me-2"></i> Dashboard Sesi
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 text-muted rounded-2" href="riwayat.php">
                                <i class="fa-solid fa-clock-history me-2" style="color: #3688C9;"></i> Riwayat Minum Obat
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
        
        <div class="dashboard-header-box mb-5 shadow-sm text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="fw-bold mb-2">Selamat Datang, <?= htmlspecialchars($nama_user) ?>! 👋</h1>
                    <p class="lead mb-0">Pantau statistik kepatuhan dan jadwal minum obat harianmu agar terapi penyembuhan berjalan optimal.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="riwayat.php" class="btn btn-light fw-bold rounded-pill px-4 py-2 text-dark shadow-sm">
                        <i class="fa-solid fa-clock-history me-2" style="color: #3688C9;"></i>Lihat Riwayat Log
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-md-4">
                <div class="card card-custom p-4 text-center h-100 bg-white">
                    <h5 class="fw-bold text-muted mb-4">Skor Kepatuhan Terapi</h5>
                    
                    <?php 
                        // Atur warna lingkaran skor berdasarkan tingkat kepatuhan
                        $score_color = '#dc3545'; // Merah jika < 50%
                        if ($persentase_kepatuhan >= 80) {
                            $score_color = '#28a745'; // Hijau jika bagus (>=80%)
                        } elseif ($persentase_kepatuhan >= 50) {
                            $score_color = '#ffc107'; // Kuning/Oranye jika sedang
                        }
                    ?>
                    
                    <div class="score-circle text-white" style="background-color: <?= $score_color ?>;">
                        <?= $persentase_kepatuhan ?>%
                    </div>
                    
                    <p class="small text-muted px-2">
                        <?= $persentase_kepatuhan >= 80 ? 'Pertahankan! Kepatuhan Anda sangat baik untuk mendukung proses kesembuhan.' : 'Tingkatkan! Usahakan minum obat tepat waktu sesuai alarm jadwal.' ?>
                    </p>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card card-custom p-4 h-100 bg-white">
                    <h5 class="fw-bold text-muted mb-4"><i class="fa-solid fa-chart-pie me-2 text-primary"></i>Rincian Evaluasi Minum Obat</h5>
                    
                    <div class="row text-center my-auto">
                        <div class="col-6 col-sm-4 mb-3">
                            <div class="p-3 border rounded-3 bg-light">
                                <h2 class="fw-bold text-success m-0"><?= $jumlah_diminum ?></h2>
                                <small class="text-muted fw-semibold">Sukses Diminum</small>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 mb-3">
                            <div class="p-3 border rounded-3 bg-light">
                                <h2 class="fw-bold text-danger m-0"><?= $jumlah_terlewat ?></h2>
                                <small class="text-muted fw-semibold">Terlewat / Gagal</small>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 mb-3">
                            <div class="p-3 border rounded-3 bg-light" style="border-left: 4px solid #3688C9 !important;">
                                <h2 class="fw-bold text-dark m-0"><?= $total_jadwal ?></h2>
                                <small class="text-muted fw-semibold">Total Evaluasi</small>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 mt-3 mb-0 d-flex align-items-center gap-2" style="background-color: #e8f4fd; color: #1d527e;">
                        <i class="fa-solid fa-circle-info fs-5"></i>
                        <small class="m-0">Status obat berlabel <strong>"Ditunda"</strong> belum dimasukkan ke dalam persentase kalkulasi evaluasi medis harian.</small>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <footer class="bg-white text-center py-4 border-top mt-5">
        <p class="text-muted m-0">&copy; 2026 Kelompok UAS ITB STIKOM Bali. Desain terinspirasi oleh Halodoc.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>