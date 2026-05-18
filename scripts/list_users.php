<?php
require_once __DIR__ . '/../config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$user = $_SESSION['user'] ?? null;
if (!$user || ($user['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo '<h2>Akses ditolak</h2><p>Hanya pengguna dengan peran <strong>admin</strong> yang dapat melihat halaman ini.</p>';
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY id");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die('<pre>Query error: ' . htmlspecialchars($e->getMessage()) . '</pre>');
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Daftar Pengguna — SPK TOPSIS</title>
    <style>table{border-collapse:collapse;width:100%}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#f4f4f4}</style>
</head>
<body>
    <h1>Daftar Pengguna</h1>
    <p>Menampilkan kolom: <strong>id</strong>, <strong>username</strong>, <strong>role</strong>.</p>
    <table>
        <thead>
            <tr><th>id</th><th>username</th><th>role</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['id']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="/spk-topsis/index.php">Kembali ke aplikasi</a></p>
</body>
</html>
