<?php
class Database
{
    // Menggunakan variabel static agar koneksi bisa dipakai berulang kali tanpa membuat objek baru
    private static $pdo = null;

    // Fungsi helper untuk membaca file .env secara manual (Native PHP)
    private static function loadEnv()
    {
        $path = dirname(__DIR__) . '/.env';
        if (!file_exists($path)) {
            die("Error: File .env tidak ditemukan di root folder!");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Abaikan baris komentar jika ada
            if (strpos(trim($line), '#') === 0) continue;

            // Memisahkan key dan value berdasarkan tanda '='
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Masukkan ke dalam environment variable PHP
            if (!array_key_exists($key, $_ENV)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }
    }

    // Fungsi utama untuk mendapatkan koneksi PDO
    public static function connect()
    {
        // Jika koneksi sudah ada, langsung kembalikan koneksi tersebut (Singleton Pattern)
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // Panggil fungsi pembaca .env
        self::loadEnv();

        // Mengambil data kredensial dari file .env yang sudah di-load
        $host = getenv('DB_HOST');
        $db   = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        try {
            // Membuat koneksi database baru menggunakan PDO
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            
            // Pengaturan opsi ekstra PDO demi keamanan dan penanganan error
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mengubah error MySQL jadi Exception PHP
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Hasil query otomatis berbentuk Array Asosiatif
                PDO::ATTR_EMULATE_PREPARES   => false,                  // Memaksa true prepared statements (Anti SQL Injection)
            ];

            self::$pdo = new PDO($dsn, $user, $pass, $options);
            return self::$pdo;

        } catch (PDOException $e) {
            die("Koneksi ke database gagal: " . $e->getMessage());
        }
    }
}