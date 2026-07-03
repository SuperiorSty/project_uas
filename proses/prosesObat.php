<?php
session_start();
// Memanggil file konfigurasi database
require_once '../config/database.php';

// Memanggil fungsi connect()
$pdo = Database::connect();

$aksi = $_GET['aksi'] ?? '';

// --- LOGIKA HAPUS DATA ---
if ($aksi == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Menggunakan Prepared Statement untuk mencegah SQL Injection
    $stmt = $pdo->prepare("DELETE FROM master_obat WHERE master_id = ?");
    $stmt->execute([$id]);
    
    // Redirect kembali ke halaman kelola obat
    header("Location: ../views/kelolaObat.php");
    exit;
}

// --- LOGIKA TAMBAH & UBAH DATA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_obat = trim($_POST['nama_obat']);
    $kategori = trim($_POST['kategori']);
    $bentuk = trim($_POST['bentuk']);
    $dosis = trim($_POST['dosis']);
    $satuan = trim($_POST['satuan']);
    $stok = (int)$_POST['stok'];

    if ($aksi == 'tambah') {
        $stmt = $pdo->prepare("INSERT INTO master_obat (nama_obat, kategori, bentuk, dosis, satuan, stok) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nama_obat, $kategori, $bentuk, $dosis, $satuan, $stok]);
    } 
    elseif ($aksi == 'edit') {
        $id = $_POST['master_id'];
        $stmt = $pdo->prepare("UPDATE master_obat SET nama_obat=?, kategori=?, bentuk=?, dosis=?, satuan=?, stok=? WHERE master_id=?");
        $stmt->execute([$nama_obat, $kategori, $bentuk, $dosis, $satuan, $stok, $id]);
    }

    // Redirect kembali ke halaman kelola obat setelah proses selesai
    header("Location: ../views/kelolaObat.php");
    exit;
} else {
    // Jika ada yang mencoba mengakses file ini langsung tanpa lewat form/tombol hapus
    header("Location: ../views/kelolaObat.php");
    exit;
}
?>