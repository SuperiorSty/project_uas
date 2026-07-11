<?php
require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->getConn();

$filter_status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$riwayat = [];

try {
    $sql = "
        SELECT r.*,
            COALESCE(m.nama_obat, r.nama_obat) as nama_obat,
            COALESCE(m.kategori, '') as kategori
        FROM riwayat_obat r
        LEFT JOIN obat o ON r.id_obat_user = o.id_obat_user
        LEFT JOIN master_obat m ON o.id_obat = m.id_obat
        WHERE r.user_id = ?
    ";
    $params = [$user_id];
    $types = 'i';

    if (!empty($filter_status)) {
        $sql .= " AND r.status = ?";
        $params[] = $filter_status;
        $types .= 's';
    }
    if (!empty($search)) {
        $sql .= " AND (COALESCE(m.nama_obat, r.nama_obat) LIKE ?)";
        $params[] = "%$search%";
        $types .= 's';
    }

    $sql .= " ORDER BY r.waktu_jadwal DESC LIMIT 100";

    $stmt = $db->prepareBind($sql, $params, $types);
    $riwayat = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {}
