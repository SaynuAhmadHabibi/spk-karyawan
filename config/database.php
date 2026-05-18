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

    // ── Auto-create required tables ──────────────────────────────────
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(100) NOT NULL,
        `password` varchar(255) NOT NULL,
        `role` varchar(50) NOT NULL DEFAULT 'user',
        `photo` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unik_username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `karyawan` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nik` varchar(50) NOT NULL,
        `nama` varchar(100) NOT NULL,
        `jabatan` varchar(100) DEFAULT NULL,
        `divisi` varchar(100) DEFAULT NULL,
        `tanggal_masuk` date DEFAULT NULL,
        `status` enum('aktif','nonaktif') DEFAULT 'aktif',
        PRIMARY KEY (`id`),
        UNIQUE KEY `unik_nik` (`nik`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `kriteria` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `nama_kriteria` varchar(100) NOT NULL,
        `bobot` decimal(5,2) NOT NULL,
        `atribut` enum('benefit','cost') NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `penilaian` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_karyawan` int(11) NOT NULL,
        `id_kriteria` int(11) NOT NULL,
        `nilai` decimal(8,2) NOT NULL,
        `periode_bulan` date NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `unik_penilaian` (`id_karyawan`,`id_kriteria`,`periode_bulan`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `hasil_topsis` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `id_karyawan` int(11) NOT NULL,
        `nilai` decimal(10,6) NOT NULL,
        `ranking` int(11) DEFAULT NULL,
        `tipe` enum('reward','punishment') NOT NULL,
        `periode` date DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // ── Pastikan user admin ada (hanya saat DB kosong pada inisialisasi) ──
    // Jika tabel users masih kosong, buat satu akun admin default.
    // Jangan otomatis membuat/restore akun admin pada setiap request,
    // karena itu akan mengembalikan user yang sengaja dihapus.
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount == 0) {
        $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)")
            ->execute(['admin', password_hash('admin', PASSWORD_DEFAULT), 'admin']);
    }

} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#dc2626;">
        <h2>Koneksi Database Gagal</h2>
        <p>Pastikan MySQL berjalan dan konfigurasi di <code>config/database.php</code> sudah benar.</p>
        <small>' . htmlspecialchars($e->getMessage()) . '</small>
    </div>');
}