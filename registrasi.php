<?php 
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: views/dashboard.php");
    exit;
}
?>

<h2>Pendaftaran Akun Pasien Baru</h2>

<?php if (isset($_GET['error'])): ?>
    <div style="color: red; margin-bottom: 15px;">
        <?= htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<form action="proses/prosesRegistrasi.php" method="POST">
    <input type="text" name="nama_lengkap" placeholder="Masukkan Nama Lengkap" required>
    <br><br>

    <input type="email" name="email" placeholder="Masukkan Email Aktif" required>
    <br><br>

    <input type="text" name="username" placeholder="Buat Username Baru" required>
    <br><br>
    
    <input type="password" name="password" placeholder="Buat Password" required>
    <br><br>
    
    <input type="submit" value="Daftar Sekarang">
</form>

<p>Sudah punya akun? <a href="index.php">Login di sini</a></p>