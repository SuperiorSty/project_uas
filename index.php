<?php 
session_start();
// Jika user sudah login, langsung tendang ke dashboard (Biar gak bisa akses halaman login lagi)
if (isset($_SESSION['user_id'])) {
    header("Location: views/dashboard.php");
    exit;
}
?>

<?php if (isset($_GET['error'])): ?>
    <div style="color: red; margin-bottom: 15px;">
        <?= htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div style="color: green; margin-bottom: 15px;">
        <?= htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

<form action="proses/prosesLogin.php" method="POST">
    <input type="text" name="username" placeholder="Masukkan Username Anda" required>
    <br><br>
    
    <input type="password" name="password" placeholder="Masukkan Password Anda" required>
    <br><br>
    
    <input type="submit" value="Masuk (Login)">
</form>

<p>Belum punya akun? <a href="registrasi.php">Daftar di sini</a></p>