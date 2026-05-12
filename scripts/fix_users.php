<?php
/**
 * Reset Users — Hapus semua user, buat hanya 1 admin
 */
require_once __DIR__ . '/../config/database.php';

try {
    // Hapus semua user lama
    $pdo->exec("DELETE FROM users");
    
    // Reset auto increment
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
    
    // Buat 1 user admin
    $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)")
        ->execute(['admin', password_hash('admin', PASSWORD_DEFAULT), 'admin']);

    // Verify
    $users = $pdo->query("SELECT id, username, role FROM users ORDER BY id")->fetchAll();

    echo '<html><body style="font-family:Inter,sans-serif;background:#0d2524;color:#f8fafc;padding:2rem;">';
    echo '<h2 style="color:#22c55e;">✅ User Reset Berhasil!</h2>';
    echo '<p>Hanya 1 user admin yang dibuat:</p>';
    echo '<table style="border-collapse:collapse;margin:1rem 0;">';
    echo '<tr style="background:rgba(34,197,94,0.2);"><th style="padding:10px 20px;border:1px solid rgba(255,255,255,0.2);">ID</th><th style="padding:10px 20px;border:1px solid rgba(255,255,255,0.2);">Username</th><th style="padding:10px 20px;border:1px solid rgba(255,255,255,0.2);">Password</th><th style="padding:10px 20px;border:1px solid rgba(255,255,255,0.2);">Role</th></tr>';
    foreach ($users as $u) {
        echo '<tr>';
        echo '<td style="padding:10px 20px;border:1px solid rgba(255,255,255,0.1);text-align:center;">' . $u['id'] . '</td>';
        echo '<td style="padding:10px 20px;border:1px solid rgba(255,255,255,0.1);font-weight:bold;">' . htmlspecialchars($u['username']) . '</td>';
        echo '<td style="padding:10px 20px;border:1px solid rgba(255,255,255,0.1);">admin</td>';
        echo '<td style="padding:10px 20px;border:1px solid rgba(255,255,255,0.1);">' . htmlspecialchars($u['role']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '<br><p style="font-size:1.2em;">🔑 Login dengan: <strong>Username:</strong> admin &nbsp;|&nbsp; <strong>Password:</strong> admin</p>';
    echo '<br><a href="/spk-topsis/index.php" style="color:#3b82f6;font-weight:bold;font-size:1.1em;">→ Klik untuk Login Sekarang</a>';
    echo '</body></html>';

} catch (PDOException $e) {
    echo '<h2 style="color:#ef4444;">Error: ' . htmlspecialchars($e->getMessage()) . '</h2>';
}
