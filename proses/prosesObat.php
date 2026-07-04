<?php
session_start();
require_once '../config/database.php';

// ── RBAC: semua aksi di sini butuh login ───────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php?error=Silakan login terlebih dahulu!");
    exit;
}

$pdo = Database::connect();
$aksi = $_GET['aksi'] ?? '';

// ── HAPUS (hanya ADMIN) ────────────────────────────────────────────────────
if ($aksi == 'hapus' && isset($_GET['id'])) {

    if ($_SESSION['role_nama'] !== 'ADMIN') {
        header("Location: ../views/kelolaObat.php?error=Akses ditolak! Hanya Admin yang bisa menghapus.");
        exit;
    }

    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM master_obat WHERE master_id = ?");
    $stmt->execute([$id]);

    $_SESSION['pesan_sukses'] = "Obat berhasil dihapus!";
    header("Location: ../views/kelolaObat.php");
    exit;
}

// ── TAMBAH & EDIT (hanya ADMIN) ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_SESSION['role_nama'] !== 'ADMIN') {
        header("Location: ../views/kelolaObat.php?error=Akses ditolak! Hanya Admin yang bisa mengubah data.");
        exit;
    }

    // Sanitasi & validasi input (Kriteria Keamanan)
    $nama_obat  = trim(htmlspecialchars($_POST['nama_obat']));
    $kategori   = trim(htmlspecialchars($_POST['kategori']));
    $bentuk     = trim(htmlspecialchars($_POST['bentuk']));
    $dosis      = trim(htmlspecialchars($_POST['dosis']));
    $satuan     = trim(htmlspecialchars($_POST['satuan']));
    $stok       = max(0, (int)$_POST['stok']);
    $deskripsi  = trim(htmlspecialchars($_POST['deskripsi'] ?? ''));

    // Validasi: field wajib tidak boleh kosong
    if (empty($nama_obat) || empty($kategori) || empty($bentuk) || empty($dosis) || empty($satuan)) {
        header("Location: ../views/kelolaObat.php?error=Semua field wajib harus diisi!");
        exit;
    }

    if ($aksi == 'tambah') {
        $stmt = $pdo->prepare("
            INSERT INTO master_obat (nama_obat, kategori, bentuk, dosis, satuan, stok, deskripsi)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nama_obat, $kategori, $bentuk, $dosis, $satuan, $stok, $deskripsi]);
        $_SESSION['pesan_sukses'] = "Obat '$nama_obat' berhasil ditambahkan!";

    } elseif ($aksi == 'edit') {
        $id = (int)$_POST['master_id'];
        $stmt = $pdo->prepare("
            UPDATE master_obat
            SET nama_obat=?, kategori=?, bentuk=?, dosis=?, satuan=?, stok=?, deskripsi=?
            WHERE master_id=?
        ");
        $stmt->execute([$nama_obat, $kategori, $bentuk, $dosis, $satuan, $stok, $deskripsi, $id]);
        $_SESSION['pesan_sukses'] = "Data obat '$nama_obat' berhasil diperbarui!";
    }

    header("Location: ../views/kelolaObat.php");
    exit;

} else {
    header("Location: ../views/kelolaObat.php");
    exit;
}
