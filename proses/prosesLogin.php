<?php
session_start();
// Menghubungkan ke konfigurasi database MySQLi + .env
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi & Sanitasi Input Sederhana (Kriteria Keamanan Dosen)
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: ../views/masuk.php?error=Semua kolom wajib diisi!");
        exit;
    }

    try {
        // 1. Ambil koneksi database berbasis MySQLi
        $db = new Database();
        $conn = $db->getConn();

        /* 2. Query menggunakan Prepared Statements (Mitigasi SQL Injection)
             Kita sekalian JOIN ke tabel roles agar langsung tahu dia Admin atau Pasien
        */
        $query = "SELECT u.*, r.nama_role 
                  FROM users u 
                  JOIN roles r ON u.roles_id = r.roles_id 
                  WHERE u.username = ?";
                  
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        // 3. Verifikasi Password Hash dari Database
        if ($user && password_verify($password, $user['password'])) {
            
            // Login Sukses! Set data ke Session (Kriteria Akses Kontrol/RBAC)
            $_SESSION['user_id']   = $user['users_id'];
            $_SESSION['role_id']   = $user['roles_id'];
            $_SESSION['role_nama'] = $user['nama_role'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['nama_user'] = $user['nama_lengkap'];

            $redirect = $user['nama_role'] === 'ADMIN' ? '../views/dashboardApoteker.php' : '../views/dasboard.php';
            header("Location: $redirect");
            exit;
        } else {
            // Login Gagal
            header("Location: ../views/masuk.php?error=Username atau Password salah!");
            exit;
        }

    } catch (Exception $e) {
        // Log error jika database bermasalah
        header("Location: ../views/masuk.php?error=Terjadi kesalahan sistem.");
        exit;
    }
} else {
    // Jika diakses ilegal tanpa POST
    header("Location: ../views/masuk.php");
    exit;
}