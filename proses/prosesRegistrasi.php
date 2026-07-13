<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username']);
    $password     = trim($_POST['password']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email        = trim($_POST['email']);
    $role_pasien  = 2;

    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($email)) {
        header("Location: ../views/daftar.php?error=Semua data wajib diisi!");
        exit;
    }

    // Mengamankan password asli menggunakan Hash (PASSWORD_BCRYPT / DEFAULT)
    $password_aman = password_hash($password, PASSWORD_DEFAULT);

    try {
        $db = new Database();
        $conn = $db->getConn();

        // Query INSERT aman menggunakan Prepared Statements
        $query = "INSERT INTO users (role_id, username, password, nama_lengkap, email) 
                  VALUES (?, ?, ?, ?, ?)";
                  
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issss", $role_pasien, $username, $password_aman, $nama_lengkap, $email);
        $exec = $stmt->execute();

        if ($exec) {
            header("Location: ../views/masuk.php?success=Registrasi berhasil! Silahkan login.");
            exit;
        }

    } catch (Exception $e) {
        // Cek jika username atau email duplikat (karena kita set UNIQUE di database)
        if ($conn->errno == 1062) {
            header("Location: ../views/daftar.php?error=Username atau Email sudah terdaftar!");
        } else {
            header("Location: ../views/daftar.php?error=Gagal mendaftar, coba lagi.");
        }
        exit;
    }
}