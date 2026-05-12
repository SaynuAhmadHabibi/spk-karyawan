<?php
require_once __DIR__ . '/../config/database.php';
$count = $pdo->query('SELECT COUNT(*) FROM karyawan')->fetchColumn();
echo "$count karyawan ditemukan\n";
$countP = $pdo->query('SELECT COUNT(*) FROM penilaian')->fetchColumn();
echo "$countP penilaian ditemukan\n";
