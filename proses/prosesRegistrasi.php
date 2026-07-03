<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username']);
    $password     = trim($_POST['password']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email        = trim($_POST['email']);
    $role_pasien  = 2; // Secara default, yang mendaftar mandiri adalah Pasien (role_id = 2)

    if (empty($username) || empty($password) || empty($nama_lengkap) || empty($email)) {
        header("Location: ../registrasi.php?error=Semua data wajib diisi!");
        exit;
    }

    // Mengamankan password asli menggunakan Hash (PASSWORD_BCRYPT / DEFAULT)
    $password_aman = password_hash($password, PASSWORD_DEFAULT);

    try {
        $db = Database::connect();

        // Query INSERT aman menggunakan PDO
        $query = "INSERT INTO users (roles_id, username, password, nama_lengkap, email) 
                  VALUES (:role_id, :username, :password, :nama, :email)";
                  
        $stmt = $db->prepare($query);
        $exec = $stmt->execute([
            'role_id'  => $role_pasien,
            'username' => $username,
            'password' => $password_aman, // Password yang sudah di-hash dimasukkan ke DB
            'nama'     => $nama_lengkap,
            'email'    => $email
        ]);

        if ($exec) {
            header("Location: ../index.php?success=Registrasi berhasil! Silahkan login.");
            exit;
        }

    } catch (PDOException $e) {
        // Cek jika username atau email duplikat (karena kita set UNIQUE di database)
        if ($e->getCode() == 23000) {
            header("Location: ../registrasi.php?error=Username atau Email sudah terdaftar!");
        } else {
            header("Location: ../registrasi.php?error=Gagal mendaftar, coba lagi.");
        }
        exit;
    }
}