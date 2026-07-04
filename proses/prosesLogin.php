<?php
session_start();
// Menghubungkan ke konfigurasi database PDO + .env yang baru
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi & Sanitasi Input Sederhana (Kriteria Keamanan Dosen)
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: ../index.php?error=Semua kolom wajib diisi!");
        exit;
    }

    try {
        // 1. Ambil koneksi database berbasis PDO Static
        $db = Database::connect();

        /* 2. Query menggunakan PDO Prepared Statements (Mitigasi SQL Injection)
             Kita sekalian JOIN ke tabel roles agar langsung tahu dia Admin atau Pasien
        */
        $query = "SELECT u.*, r.nama_role 
                  FROM users u 
                  JOIN roles r ON u.roles_id = r.roles_id 
                  WHERE u.username = :username";
                  
        $stmt = $db->prepare($query);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(); // Otomatis mengembalikan Array Asosiatif karena konfigurasi PDO kita

        // 3. Verifikasi Password Hash dari Database
        if ($user && password_verify($password, $user['password'])) {
            
            // Login Sukses! Set data ke Session (Kriteria Akses Kontrol/RBAC)
            $_SESSION['user_id']   = $user['users_id'];
            $_SESSION['role_id']   = $user['roles_id'];
            $_SESSION['role_nama'] = $user['nama_role'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['nama_user'] = $user['nama_lengkap'];

            // Mengarahkan halaman berdasarkan Role (RBAC)
            if ($user['nama_role'] === 'ADMIN') {
                // header("Location: ../views/dashboard.php?role=admin");
                header("Location: ../index.php?role=admin");
            } else {
                // header("Location: ../views/dashboard.php");
                header("Location: ../index.php?role=pasien");
            }
            exit;
        } else {
            // Login Gagal
            header("Location: ../index.php?error=Username atau Password salah!");
            exit;
        }

    } catch (PDOException $e) {
        // Log error jika database bermasalah
        header("Location: ../index.php?error=Terjadi kesalahan sistem.");
        exit;
    }
} else {
    // Jika diakses ilegal tanpa POST
    header("Location: ../index.php");
    exit;
}