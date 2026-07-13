<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/masuk.php?error=Silakan login terlebih dahulu!");
    exit;
}

$db = new Database();
$conn = $db->getConn();
$aksi = $_GET['aksi'] ?? '';

// ── HAPUS OBAT USER (Pasien) ──────────────────────────────────────────────
if ($aksi == 'hapus_obat_user' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_obat_user = (int)($_POST['id_obat_user'] ?? 0);
    if ($id_obat_user <= 0) {
        header("Location: ../views/dasboard.php?error=Data tidak valid.");
        exit;
    }

    $stmt = $conn->prepare("SELECT id_obat_user FROM obat WHERE id_obat_user = ? AND user_id = ?");
    $stmt->bind_param("ii", $id_obat_user, $_SESSION['user_id']);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) {
        header("Location: ../views/dasboard.php?error=Obat tidak ditemukan.");
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM obat WHERE id_obat_user = ?");
    $stmt->bind_param("i", $id_obat_user);
    if ($stmt->execute()) {
        header("Location: ../views/dasboard.php?success=Obat berhasil dihapus. Riwayat tetap tersimpan.");
    } else {
        header("Location: ../views/dasboard.php?error=Gagal menghapus obat.");
    }
    exit;
}

// ── HAPUS (hanya ADMIN) ────────────────────────────────────────────────────
if ($aksi == 'hapus' && isset($_GET['id'])) {

    if ($_SESSION['role_nama'] !== 'ADMIN') {
        header("Location: ../views/dashboardApoteker.php?error=Akses ditolak! Hanya Admin yang bisa menghapus.");
        exit;
    }

    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM master_obat WHERE id_obat = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $_SESSION['pesan_sukses'] = "Obat berhasil dihapus!";
    header("Location: ../views/dashboardApoteker.php");
    exit;
}

// ── TAMBAH & EDIT (hanya ADMIN) ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_SESSION['role_nama'] !== 'ADMIN') {
        header("Location: ../views/dashboardApoteker.php?error=Akses ditolak! Hanya Admin yang bisa mengubah data.");
        exit;
    }

    $nama_obat  = trim(htmlspecialchars($_POST['nama_obat']));
    $kategori   = trim(htmlspecialchars($_POST['kategori']));
    $deskripsi  = trim(htmlspecialchars($_POST['deskripsi'] ?? ''));

    if (empty($nama_obat) || empty($kategori)) {
        header("Location: ../views/dashboardApoteker.php?error=Nama dan kategori wajib diisi!");
        exit;
    }

    if ($aksi == 'tambah') {
        $stmt = $conn->prepare("INSERT INTO master_obat (nama_obat, kategori, deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama_obat, $kategori, $deskripsi);
        $stmt->execute();
        $_SESSION['pesan_sukses'] = "Obat '$nama_obat' berhasil ditambahkan!";

    } elseif ($aksi == 'edit') {
        $id = (int)$_POST['id_obat'];
        $stmt = $conn->prepare("UPDATE master_obat SET nama_obat=?, kategori=?, deskripsi=? WHERE id_obat=?");
        $stmt->bind_param("sssi", $nama_obat, $kategori, $deskripsi, $id);
        $stmt->execute();
        $_SESSION['pesan_sukses'] = "Data obat '$nama_obat' berhasil diperbarui!";
    }

    header("Location: ../views/dashboardApoteker.php");
    exit;

} else {
    header("Location: ../views/dashboardApoteker.php");
    exit;
}
