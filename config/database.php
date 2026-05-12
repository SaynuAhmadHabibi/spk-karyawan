<?php
// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_NAME', 'spk_topsis');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

try {
    $pdo_tmp = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo_tmp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo_tmp->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
    if ($stmt->rowCount() == 0) {
        $pdo_tmp->exec("CREATE DATABASE `" . DB_NAME . "` CHARACTER SET " . DB_CHARSET);
    }
    $pdo_tmp = null;
    
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#dc2626;">
        <h2>Koneksi Database Gagal</h2>
        <p>Pastikan MySQL berjalan dan konfigurasi di <code>config/database.php</code> sudah benar.</p>
        <small>' . htmlspecialchars($e->getMessage()) . '</small>
    </div>');
}