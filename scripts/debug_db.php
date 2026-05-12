<?php
$pdo = new PDO('mysql:host=localhost;dbname=spk_topsis;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== SAMPLE PERIODE_BULAN VALUES ===" . PHP_EOL;
$stmt = $pdo->query("SELECT DISTINCT periode_bulan FROM penilaian ORDER BY periode_bulan DESC LIMIT 10");
foreach ($stmt as $r) {
    echo "  [" . $r['periode_bulan'] . "]" . PHP_EOL;
}

echo PHP_EOL . "=== COLUMN TYPE ===" . PHP_EOL;
$stmt = $pdo->query("SHOW COLUMNS FROM penilaian WHERE Field='periode_bulan'");
$col = $stmt->fetch(PDO::FETCH_ASSOC);
echo "  Type: " . $col['Type'] . PHP_EOL;

echo PHP_EOL . "=== TOTAL ROWS ===" . PHP_EOL;
$stmt = $pdo->query("SELECT COUNT(*) as c FROM penilaian");
echo "  " . $stmt->fetch(PDO::FETCH_ASSOC)['c'] . " rows" . PHP_EOL;

echo PHP_EOL . "=== SAMPLE DATA (5 rows) ===" . PHP_EOL;
$stmt = $pdo->query("SELECT id_karyawan, id_kriteria, nilai, periode_bulan FROM penilaian LIMIT 5");
foreach ($stmt as $r) {
    echo "  karyawan=" . $r['id_karyawan'] . " kriteria=" . $r['id_kriteria'] 
         . " nilai=" . $r['nilai'] . " periode=[" . $r['periode_bulan'] . "]" . PHP_EOL;
}

echo PHP_EOL . "=== KARYAWAN STATUS ===" . PHP_EOL;
$stmt = $pdo->query("SELECT COUNT(*) as c FROM karyawan WHERE status='aktif'");
$aktif = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
$stmt = $pdo->query("SELECT COUNT(*) as c FROM karyawan");
$total = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
echo "  Aktif: $aktif / Total: $total" . PHP_EOL;

echo PHP_EOL . "=== KRITERIA ===" . PHP_EOL;
$stmt = $pdo->query("SELECT id, nama_kriteria, bobot, atribut FROM kriteria ORDER BY id");
foreach ($stmt as $r) {
    echo "  ID=" . $r['id'] . " nama=" . $r['nama_kriteria'] 
         . " bobot=" . $r['bobot'] . " atribut=" . $r['atribut'] . PHP_EOL;
}
