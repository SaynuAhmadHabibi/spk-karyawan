<?php
/**
 * CLI Script: Seed All Data (Karyawan, Kriteria, Penilaian)
 * Script ini digunakan untuk populate dummy data ke database via terminal.
 */
require_once __DIR__ . '/../config/database.php';

try {
    echo "1. Mengosongkan data lama...\n";
    $pdo->exec("DELETE FROM hasil_topsis");
    $pdo->exec("DELETE FROM penilaian");
    $pdo->exec("DELETE FROM kriteria");
    $pdo->exec("ALTER TABLE kriteria AUTO_INCREMENT = 1");
    $pdo->exec("DELETE FROM karyawan");
    $pdo->exec("ALTER TABLE karyawan AUTO_INCREMENT = 1");

    echo "2. Menyisipkan 5 Kriteria (Total Bobot 1.00)...\n";
    $kriterias = [
        ['Kedisiplinan & Kehadiran', 0.25, 'benefit'],
        ['Target Pencapaian Kerja', 0.30, 'benefit'],
        ['Kerjasama Tim', 0.15, 'benefit'],
        ['Teguran / Pelanggaran', 0.15, 'cost'],
        ['Inisiatif & Tanggung Jawab', 0.15, 'benefit']
    ];
    $stmtKriteria = $pdo->prepare("INSERT INTO kriteria (nama_kriteria, bobot, atribut) VALUES (?, ?, ?)");
    foreach ($kriterias as $k) {
        $stmtKriteria->execute($k);
    }

    echo "3. Menyisipkan 45 Karyawan Dummy...\n";
    $namas = [
        "Budi Santoso", "Andi Pratama", "Siti Aminah", "Dewi Lestari", "Agus Setiawan", 
        "Joko Susanto", "Rina Marlina", "Hendra Wijaya", "Rudi Hartono", "Sri Wahyuni",
        "Ahmad Fauzi", "Dwi Kusuma", "Maya Sari", "Rizky Ramadhan", "Dian Novita",
        "Fajar Nugroho", "Eko Saputro", "Anita Wulandari", "Yudi Pratama", "Indah Permata",
        "Aris Munandar", "Nurul Hidayah", "Bayu Setiawan", "Rini Astuti", "Teguh Purnomo",
        "Wahyu Hidayat", "Ratna Kartika", "Heri Gunawan", "Lina Marlina", "Sigit Prasetyo",
        "Wawan Setiawan", "Fitri Rahayu", "Rahmat Hidayat", "Deni Saputra", "Ayu Lestari",
        "Haryanto Suryo", "Sari Indah", "Kiki Amalia", "Bambang Pamungkas", "Endang Susanti",
        "Iwan Kurniawan", "Nadia Safitri", "Ricky Yulianto", "Dimas Anggara", "Adipati Seta"
    ];
    
    $jabatans = ['Staff', 'Operator', 'Supervisor', 'Admin', 'Teknisi'];
    $divisis = ['Produksi', 'Gudang', 'Pemasaran', 'Keuangan', 'HRD', 'IT', 'Operasional'];

    $stmtKaryawan = $pdo->prepare("INSERT INTO karyawan (nama, jabatan, divisi, tanggal_masuk, status) VALUES (?, ?, ?, ?, 'aktif')");
    
    $pdo->beginTransaction(); // Mulai transaksi agar cepat

    $karyawanIds = [];
    for ($i = 0; $i < 45; $i++) {
        $nama = $namas[$i];
        $jabatan = $jabatans[array_rand($jabatans)];
        $divisi = $divisis[array_rand($divisis)];
        $days_ago = rand(365, 1800);
        $tanggal_masuk = date('Y-m-d', strtotime("-$days_ago days"));

        $stmtKaryawan->execute([$nama, $jabatan, $divisi, $tanggal_masuk]);
        $karyawanIds[] = $pdo->lastInsertId();
    }

    echo "4. Menyisipkan Data Penilaian (6 Bulan Terakhir)...\n";
    $kriteriaIds = [1, 2, 3, 4, 5];
    $stmtPenilaian = $pdo->prepare("INSERT INTO penilaian (id_karyawan, id_kriteria, nilai, periode_bulan) VALUES (?, ?, ?, ?)");
    
    // Generate untuk 6 bulan ke belakang
    for ($m = 5; $m >= 0; $m--) {
        $periode = date('Y-m-01', strtotime("-$m months"));
        foreach ($karyawanIds as $k_id) {
            foreach ($kriteriaIds as $idx => $kr_id) {
                // Buat nilai yang realistis
                $nilai = 0;
                if ($idx == 0) $nilai = rand(70, 100);       // Kedisiplinan
                elseif ($idx == 1) $nilai = rand(60, 100);   // Target
                elseif ($idx == 2) $nilai = rand(75, 100);   // Kerjasama
                elseif ($idx == 3) $nilai = rand(0, 30);     // Teguran (Cost - semakin kecil semakin baik)
                elseif ($idx == 4) $nilai = rand(65, 100);   // Inisiatif

                $stmtPenilaian->execute([$k_id, $kr_id, $nilai, $periode]);
            }
        }
    }
    
    $pdo->commit(); // Selesaikan transaksi

    echo "=================================================\n";
    echo "✅ SUCCESS! Data dummy berhasil digenerate!\n";
    echo "- 5 Kriteria\n";
    echo "- 45 Karyawan\n";
    echo "- 1350 Data Penilaian (45 orang x 5 kriteria x 6 bulan)\n";
    
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
