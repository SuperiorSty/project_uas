<?php
session_start();
// Proteksi Sesi dihapus dari sini agar halaman index.php bersikap publik/terbuka untuk semua orang.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Pengingat Obat - Beranda Utama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        /* Menggunakan Warna Utama #3688C9 */
        .navbar-brand h2 { color: #3688C9; font-weight: 700; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        
        /* Gradasi menggunakan Warna Utama #3688C9 and Warna Kedua #36B6C9 */
        .search-box { 
            background: linear-gradient(135deg, #3688C9, #36B6C9); 
            padding: 50px 40px; 
            border-radius: 20px; 
            color: white; 
        }
        
        /* Tombol menggunakan Warna Utama #3688C9 dan Hover menggunakan Warna Ketiga #3659C9 */
        .btn-blue { background-color: #3688C9; color: white; border-radius: 20px; transition: all 0.3s ease; }
        .btn-blue:hover { background-color: #3659C9; color: white; transform: translateY(-1px); }
        
        .btn-search { background-color: #3659C9; color: white; }
        .btn-search:hover { background-color: #2b46a0; color: white; }
        
        .badge-primary-custom { background-color: #3688C9; color: white; }
        .badge-secondary-custom { background-color: #36B6C9; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <h2 class="m-0"><i class="fa-solid fa-capsules me-2"></i>PengingatObat</h2>
            </a>
            <div class="ms-auto d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- JIKA SUDAH LOGIN: TAMPILKAN DROPDOWN PROFIL INTERAKTIF -->
                    <div class="dropdown">
                        <button class="btn btn-light bg-white border d-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user text-secondary fs-4"></i>
                            <span class="fw-semibold text-dark text-capitalize">
                                <?= htmlspecialchars($_SESSION['nama_user']); ?>
                            </span>
                            <i class="fa-solid fa-chevron-down text-muted" style="font-size: 12px;"></i>
                        </button>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2 rounded-3" aria-labelledby="userMenuButton" style="min-width: 220px;">
                            <li>
                                <a class="dropdown-item py-2 text-muted rounded-2" href="views/dashboard.php">
                                    <i class="fa-solid fa-gauge me-2" style="color: #3688C9;"></i> Dashboard Sesi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 text-muted rounded-2" href="views/riwayat.php">
                                    <i class="fa-solid fa-clock-history me-2" style="color: #3688C9;"></i> Riwayat Minum Obat
                                </a>
                            </li>
                            <li><hr class="dropdown-divider text-muted"></li>
                            <li>
                                <a class="dropdown-item py-2 text-danger fw-semibold rounded-2" href="proses/prosesLogout.php">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Keluar Akun
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- JIKA BELUM LOGIN: TOMBOL DIPISAH MENJADI MASUK & DAFTAR ALAPROFESIONAL -->
                    <a href="views/login.php" class="btn btn-blue btn-sm rounded-pill px-4 fw-bold shadow-sm me-2">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk
                    </a>
                    <a href="registrasi.php" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-bold" style="border-color: #3688C9; color: #3688C9;">
                        <i class="fa-solid fa-user-plus me-1"></i> Daftar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <div class="search-box mb-5 shadow-sm text-center text-md-start">
            <div class="row align-items-center">
                <div class="col-md-7 mb-4 mb-md-0">
                    <h1 class="fw-bold mb-3">Tebus Resep Praktis,<br>Sembuh Lebih Cepat</h1>
                    <p class="lead mb-0">Cari info obat di katalog kami dan kelola waktu minum obat secara otomatis melalui pengingat sistem email.</p>
                </div>
                <div class="col-md-5">
                    <div class="bg-white p-3 rounded-4 shadow-sm text-dark">
                        <h6 class="fw-bold text-muted mb-3"><i class="fa-solid fa-magnifying-glass me-2 text-info"></i>Cari Obat Terdaftar</h6>
                        <form action="index.php" method="GET">
                            <div class="input-group mb-2">
                                <input type="text" name="keyword" class="form-control" placeholder="Masukkan nama obat... (cth: Paracetamol)">
                                <button class="btn btn-search" type="submit">Cari</button>
                            </div>
                            <small class="text-muted text-start d-block">*Menampilkan data resmi katalog master obat.</small>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom p-4">
                    <h4 class="fw-bold mb-4"><i class="fa-solid fa-book-medical text-primary me-2"></i>Katalog Master Obat</h4>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card p-3 border rounded-3 bg-light">
                                <span class="badge badge-primary-custom align-self-start mb-2">Analgesik</span>
                                <h5 class="fw-bold mb-1">Paracetamol 500mg</h5>
                                <p class="small text-muted mb-0">Meredakan demam dan gejala nyeri ringan sampai sedang pada tubuh.</p>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card p-3 border rounded-3 bg-light">
                                <span class="badge badge-secondary-custom align-self-start mb-2">Antibiotik</span>
                                <h5 class="fw-bold mb-1">Amoxicillin 500mg</h5>
                                <p class="small text-muted mb-0">Mengobati berbagai macam infeksi akibat pertumbuhan bakteri berbahaya.</p>
                            </div>
                        </div>
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