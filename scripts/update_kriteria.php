<?php
/**
 * Update Kriteria & Generate Ulang Nilai
 */
require_once __DIR__ . '/../config/database.php';

try {
    echo "1. Menghapus data perhitungan dan kriteria lama...\n";
    $pdo->exec("DELETE FROM hasil_topsis");
    $pdo->exec("DELETE FROM penilaian");
    $pdo->exec("DELETE FROM kriteria");
    $pdo->exec("ALTER TABLE kriteria AUTO_INCREMENT = 1");

    $pdo->beginTransaction(); // Mulai transaksi setelah ALTER TABLE

    echo "2. Menyisipkan Kriteria Baru...\n";
    $kriterias = [
        ['ABSENSI', 0.28, 'benefit'],
        ['JUMLAH TELAT', 0.20, 'cost'],
        ['JUMLAH TIDAK HADIR', 0.20, 'cost'],
        ['KECEPATAN KINERJA', 0.17, 'benefit'],
        ['KUALITAS HASIL KERJA', 0.15, 'benefit']
    ];
    
    $stmtKriteria = $pdo->prepare("INSERT INTO kriteria (nama_kriteria, bobot, atribut) VALUES (?, ?, ?)");
    foreach ($kriterias as $k) {
        $stmtKriteria->execute($k);
    }

    echo "3. Men-generate ulang nilai karyawan sesuai kriteria baru...\n";
    $karyawanIds = $pdo->query("SELECT id FROM karyawan")->fetchAll(PDO::FETCH_COLUMN);
    $kriteriaIds = [1, 2, 3, 4, 5];
    
    $stmtPenilaian = $pdo->prepare("INSERT INTO penilaian (id_karyawan, id_kriteria, nilai, periode_bulan) VALUES (?, ?, ?, ?)");
    
    for ($m = 5; $m >= 0; $m--) {
        $periode = date('Y-m-01', strtotime("-$m months"));
        foreach ($karyawanIds as $k_id) {
            foreach ($kriteriaIds as $idx => $kr_id) {
                $nilai = 0;
                if ($idx == 0) $nilai = rand(80, 100);       // ABSENSI (Benefit, skala 0-100)
                elseif ($idx == 1) $nilai = rand(0, 10);     // JUMLAH TELAT (Cost, hari/kali, makin kecil makin baik)
                elseif ($idx == 2) $nilai = rand(0, 5);      // JUMLAH TIDAK HADIR (Cost, hari, makin kecil makin baik)
                elseif ($idx == 3) $nilai = rand(70, 100);   // KECEPATAN KINERJA (Benefit, skala 0-100)
                elseif ($idx == 4) $nilai = rand(75, 100);   // KUALITAS HASIL KERJA (Benefit, skala 0-100)

                $stmtPenilaian->execute([$k_id, $kr_id, $nilai, $periode]);
            }
        }
    }

    $pdo->commit();
    echo "=================================================\n";
    echo "✅ SUCCESS! Kriteria berhasil diubah dan nilai telah disesuaikan!\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
