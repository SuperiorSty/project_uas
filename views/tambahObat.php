<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Obat</title>
</head>
<body>
    <h2>Tambah Data Obat Baru</h2>
    
    <form action="../proses/prosesObat.php?aksi=tambah" method="POST">
        <label>Nama Obat:</label><br>
        <input type="text" name="nama_obat" required><br><br>

        <label>Kategori (contoh: Analgesik, Vitamin):</label><br>
        <input type="text" name="kategori" required><br><br>

        <label>Bentuk:</label><br>
        <select name="bentuk" required>
            <option value="">-- Pilih Bentuk --</option>
            <option value="Tablet">Tablet</option>
            <option value="Kapsul">Kapsul</option>
            <option value="Sirup">Sirup</option>
        </select><br><br>

        <label>Dosis (contoh: 500mg, 10ml):</label><br>
        <input type="text" name="dosis" required><br><br>

        <label>Satuan (contoh: Strip, Botol, Box):</label><br>
        <input type="text" name="satuan" required><br><br>

        <label>Jumlah Stok:</label><br>
        <input type="number" name="stok" min="0" required><br><br>

        <button type="submit">Simpan Obat</button>
        <a href="kelolaObat.php"><button type="button">Batal</button></a>
    </form>
</body>
</html>