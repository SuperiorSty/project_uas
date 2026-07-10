<?php
class Database
{
    protected $conn;

    // Fungsi helper untuk membaca file .env secara manual (Native PHP)
    private function loadEnv()
    {
        $path = dirname(__DIR__) . '/.env';
        if (!file_exists($path)) {
            die("Error: File .env tidak ditemukan di root folder!");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (!array_key_exists($key, $_ENV)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
            }
        }
    }

    public function __construct()
    {
        $this->connect();
    }

    protected function connect()
    {
        $this->loadEnv();

        $host = getenv('DB_HOST');
        $db   = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');

        $this->conn = new mysqli($host, $user, $pass, $db);

        if ($this->conn->connect_error) {
            die("Koneksi ke database gagal: " . $this->conn->connect_error);
        }
    }

    public function getConn()
    {
        return $this->conn;
    }

    public function prepareBind($sql, $params = [], $types = '')
    {
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) {
            $refs = [$types];
            for ($i = 0; $i < count($params); $i++) {
                $refs[] = &$params[$i];
            }
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }
        $stmt->execute();
        return $stmt;
    }
}
