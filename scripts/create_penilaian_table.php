<?php
require_once __DIR__ . '/../config/database.php';

// protect script to localhost only
$remote = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
if ($remote !== '127.0.0.1' && $remote !== '::1' && php_sapi_name() !== 'cli') {
  echo "Access denied: can only be run from localhost.";
  exit;
}

try {
  $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
  $sql = "CREATE TABLE IF NOT EXISTS `penilaian` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_karyawan` int(11) NOT NULL,
    `id_kriteria` int(11) NOT NULL,
    `nilai` decimal(8,2) NOT NULL,
    `periode_bulan` date NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `unik_penilaian` (`id_karyawan`,`id_kriteria`,`periode_bulan`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

  $pdo->exec($sql);

  // add foreign keys if referenced tables exist
  try {
    $pdo->exec("ALTER TABLE penilaian ADD CONSTRAINT fk_penilaian_karyawan FOREIGN KEY (id_karyawan) REFERENCES karyawan(id) ON DELETE CASCADE");
  } catch (Exception $e) { /* ignore if already exists or karyawan missing */ }
  try {
    $pdo->exec("ALTER TABLE penilaian ADD CONSTRAINT fk_penilaian_kriteria FOREIGN KEY (id_kriteria) REFERENCES kriteria(id) ON DELETE CASCADE");
  } catch (Exception $e) { /* ignore if already exists or kriteria missing */ }

  $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
  echo "Table `penilaian` created (or already exists).";
} catch (PDOException $e) {
  try { $pdo->exec('SET FOREIGN_KEY_CHECKS=1'); } catch (Exception $ex) {}
  echo "Error creating table: " . htmlspecialchars($e->getMessage());
}