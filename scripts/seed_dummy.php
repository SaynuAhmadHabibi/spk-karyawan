<?php
/**
 * Seed 50 data karyawan + penilaian untuk 3 periode
 * Jalankan via browser: http://localhost/spk-topsis/scripts/seed_dummy.php
 */

require_once __DIR__ . '/../config/database.php';

// ============================================================
// DATA KARYAWAN (50 orang)
// ============================================================
$jabatan_list = ['Staff', 'Senior Staff', 'Supervisor', 'Manager', 'Junior Staff', 'Coordinator', 'Analyst', 'Executive'];
$divisi_list  = ['HRD', 'IT', 'Finance', 'Marketing', 'Produksi', 'Operasional', 'Logistik', 'Legal', 'R&D', 'Customer Service'];
$status_list  = ['aktif', 'aktif', 'aktif', 'aktif', 'aktif', 'aktif', 'aktif', 'aktif', 'aktif', 'nonaktif']; // 90% aktif

$nama_depan = ['Ahmad', 'Budi', 'Citra', 'Dewi', 'Eko', 'Faisal', 'Gina', 'Hendra', 'Indah', 'Joko',
               'Kartika', 'Lukman', 'Maya', 'Nanda', 'Oscar', 'Putri', 'Qori', 'Rizky', 'Sari', 'Taufik',
               'Umi', 'Vina', 'Wahyu', 'Xena', 'Yanto', 'Zahra', 'Arif', 'Bayu', 'Cahya', 'Dian',
               'Elsa', 'Fajar', 'Galih', 'Hani', 'Irfan', 'Jasmine', 'Kevin', 'Lina', 'Mulyadi', 'Nina',
               'Oki', 'Puspita', 'Raffi', 'Sinta', 'Teguh', 'Umar', 'Vera', 'Wulan', 'Yoga', 'Zulfa'];

$nama_belakang = ['Pratama', 'Saputra', 'Lestari', 'Rahayu', 'Nugroho', 'Hidayat', 'Permata', 'Susanto',
                  'Wibowo', 'Kurniawan', 'Salsabila', 'Putra', 'Handayani', 'Suryani', 'Firmansyah',
                  'Anggraini', 'Ramadhan', 'Hartono', 'Setiawan', 'Yuniar'];

echo "<pre style='font-family:monospace; background:#0f172a; color:#22c55e; padding:2rem; border-radius:12px;'>\n";
echo "╔══════════════════════════════════════════╗\n";
echo "║   🌱 SPK TOPSIS - DATA SEEDER           ║\n";
echo "╚══════════════════════════════════════════╝\n\n";

try {

    // Hapus data lama
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE penilaian");
    $pdo->exec("TRUNCATE TABLE karyawan");
    $pdo->exec("TRUNCATE TABLE hasil_topsis");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "✓ Data lama dibersihkan\n";

    // Pastikan kriteria ada
    $kriteriaCount = $pdo->query("SELECT COUNT(*) FROM kriteria")->fetchColumn();
    if ($kriteriaCount == 0) {
        $pdo->exec("INSERT INTO kriteria (id, nama_kriteria, bobot, atribut) VALUES
            (1, 'Absensi', 0.25, 'benefit'),
            (2, 'Jumlah Telat', 0.20, 'cost'),
            (3, 'Jumlah tidak hadir', 0.20, 'cost'),
            (4, 'Kecepatan kinerja', 0.15, 'benefit'),
            (5, 'Kualitas hasil kerja', 0.12, 'benefit')");
        echo "✓ Kriteria default ditambahkan\n";
    }

    // Ambil daftar kriteria
    $kriteria = $pdo->query("SELECT id, atribut FROM kriteria ORDER BY id")->fetchAll();
    echo "✓ " . count($kriteria) . " kriteria ditemukan\n\n";

    // ============================================================
    // INSERT 50 KARYAWAN
    // ============================================================
    $stmtKaryawan = $pdo->prepare("INSERT INTO karyawan (nama, jabatan, divisi, tanggal_masuk, status) VALUES (?, ?, ?, ?, ?)");

    $insertedIds = [];
    echo "━━━ Menambahkan 50 Karyawan ━━━\n";

    for ($i = 0; $i < 50; $i++) {
        $nama = $nama_depan[$i] . ' ' . $nama_belakang[array_rand($nama_belakang)];
        $jabatan = $jabatan_list[array_rand($jabatan_list)];
        $divisi = $divisi_list[array_rand($divisi_list)];
        // Tanggal masuk antara 2020-01-01 s.d. 2025-12-31
        $tglMasuk = date('Y-m-d', mt_rand(strtotime('2020-01-01'), strtotime('2025-12-31')));
        $status = $status_list[array_rand($status_list)];

        $stmtKaryawan->execute([$nama, $jabatan, $divisi, $tglMasuk, $status]);
        $insertedIds[] = $pdo->lastInsertId();

        $statusIcon = $status === 'aktif' ? '🟢' : '🔴';
        echo "  $statusIcon #" . str_pad($i + 1, 2, '0', STR_PAD_LEFT) . " | $nama | $jabatan | $divisi\n";
    }

    echo "\n✓ 50 karyawan berhasil ditambahkan\n\n";

    // ============================================================
    // INSERT PENILAIAN untuk 3 periode
    // ============================================================
    $periodes = ['2026-01-01', '2026-02-01', '2026-03-01'];
    $periodeLabels = ['Januari 2026', 'Februari 2026', 'Maret 2026'];

    $stmtNilai = $pdo->prepare("INSERT INTO penilaian (id_karyawan, id_kriteria, nilai, periode_bulan) VALUES (?, ?, ?, ?)");

    $totalNilai = 0;
    echo "━━━ Menambahkan Penilaian (3 Periode) ━━━\n";

    foreach ($periodes as $pi => $periode) {
        echo "\n  📅 Periode: {$periodeLabels[$pi]}\n";
        $countPeriode = 0;

        foreach ($insertedIds as $karyawanId) {
            foreach ($kriteria as $krit) {
                // Generate nilai realistis
                if ($krit['atribut'] === 'benefit') {
                    // Benefit: nilai tinggi lebih baik (60-100)
                    $nilai = mt_rand(55, 100);
                } else {
                    // Cost: nilai rendah lebih baik (0-40, kadang lebih tinggi)
                    $nilai = mt_rand(0, 45);
                    // 20% chance nilai tinggi (karyawan bermasalah)
                    if (mt_rand(1, 100) <= 20) {
                        $nilai = mt_rand(40, 80);
                    }
                }

                $stmtNilai->execute([$karyawanId, $krit['id'], $nilai, $periode]);
                $totalNilai++;
                $countPeriode++;
            }
        }
        echo "     ✓ $countPeriode penilaian ditambahkan\n";
    }



    echo "\n╔══════════════════════════════════════════╗\n";
    echo "║   ✅ SEEDER BERHASIL!                    ║\n";
    echo "╠══════════════════════════════════════════╣\n";
    echo "║   Karyawan  : 50 data                   ║\n";
    echo "║   Penilaian : $totalNilai data" . str_repeat(' ', 20 - strlen((string)$totalNilai)) . "║\n";
    echo "║   Periode   : 3 bulan                   ║\n";
    echo "╚══════════════════════════════════════════╝\n";
    echo "\n🔗 <a href='../index.php?act=dashboard' style='color:#3b82f6'>Kembali ke Dashboard →</a>\n";

} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
