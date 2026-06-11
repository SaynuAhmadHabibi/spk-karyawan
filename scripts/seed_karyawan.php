<?php
/**
 * Seed Karyawan — Generate 45 data dummy
 * Jalankan via browser: http://localhost/spk-topsis/scripts/seed_karyawan.php
 */
require_once __DIR__ . '/../config/database.php';

// Restrict to localhost
$remote = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
if (php_sapi_name() !== 'cli' && !in_array($remote, ['127.0.0.1', '::1'])) {
    echo "Access denied: script may only be run from localhost.";
    exit;
}

if (!isset($_GET['confirm']) || $_GET['confirm'] !== '1') {
    echo '<html><body style="font-family:Inter,sans-serif;background:#0d2524;color:#f8fafc;padding:2rem;">';
    echo '<h2 style="color:#22c55e;">🌱 Seed Data Karyawan</h2>';
    echo '<p>Script ini akan <strong>menghapus semua data karyawan lama (termasuk nilai)</strong> dan membuat <strong>45 data karyawan dummy baru</strong>.</p>';
    echo '<p style="color:#f59e0b;"><strong>⚠️ Peringatan:</strong> Semua data perhitungan terkait karyawan sebelumnya akan hilang!</p>';
    echo '<p><a href="?confirm=1" style="color:#22c55e;font-weight:bold;font-size:1.2em;">✅ Klik untuk Generate 45 Data Dummy</a></p>';
    echo '</body></html>';
    exit;
}

try {
    // 1. Kosongkan nilai perhitungan/topsis dan penilaian yang terhubung dengan karyawan
    $pdo->exec("DELETE FROM hasil_topsis");
    $pdo->exec("DELETE FROM penilaian");
    
    // 2. Kosongkan tabel karyawan
    $pdo->exec("DELETE FROM karyawan");
    $pdo->exec("ALTER TABLE karyawan AUTO_INCREMENT = 1");

    // Daftar nama acak untuk data dummy
    $namas = [
        "Budi Santoso", "Andi Pratama", "Siti Aminah", "Dewi Lestari", "Agus Setiawan", 
        "Joko Susanto", "Rina Marlina", "Hendra Wijaya", "Rudi Hartono", "Sri Wahyuni",
        "Ahmad Fauzi", "Dwi Kusuma", "Maya Sari", "Rizky Ramadhan", "Dian Novita",
        "Fajar Nugroho", "Eko Saputro", "Anita Wulandari", "Yudi Pratama", "Indah Permata",
        "Aris Munandar", "Nurul Hidayah", "Bayu Setiawan", "Rini Astuti", "Teguh Purnomo",
        "Wahyu Hidayat", "Ratna Kartika", "Heri Gunawan", "Lina Marlina", "Sigit Prasetyo",
        "Wawan Setiawan", "Fitri Rahayu", "Rahmat Hidayat", "Deni Saputra", "Ayu Lestari",
        "Haryanto Suryo", "Sari Indah", "Kiki Amalia", "Bambang Pamungkas", "Endang Susanti",
        "Iwan Kurniawan", "Nadia Safitri", "Ricky Yulianto", "Dimas Anggara", "Adipati Seta",
        "Luna Maheswari", "Tora Sudiro", "Reza Rahadian", "Vino Bastian", "Ariel Noah"
    ];

    $jabatans = ['Staff', 'Operator', 'Supervisor', 'Admin', 'Teknisi'];
    $divisis = ['Produksi', 'Gudang', 'Pemasaran', 'Keuangan', 'HRD', 'IT', 'Operasional'];

    $stmt = $pdo->prepare("INSERT INTO karyawan (nama, jabatan, divisi, tanggal_masuk, status) VALUES (?, ?, ?, ?, 'aktif')");
    
    $insertedCount = 0;
    for ($i = 0; $i < 45; $i++) {
        $nama = $namas[$i];
        $jabatan = $jabatans[array_rand($jabatans)];
        $divisi = $divisis[array_rand($divisis)];
        
        // Random tanggal masuk antara 1 hingga 5 tahun lalu (365 sampai 1800 hari yang lalu)
        $days_ago = rand(365, 1800);
        $tanggal_masuk = date('Y-m-d', strtotime("-$days_ago days"));

        $stmt->execute([$nama, $jabatan, $divisi, $tanggal_masuk]);
        $insertedCount++;
    }

    echo '<html><body style="font-family:Inter,sans-serif;background:#0d2524;color:#f8fafc;padding:2rem;">';
    echo '<h2 style="color:#22c55e;">✅ Generate Data Berhasil!</h2>';
    echo "<p><strong>$insertedCount</strong> data karyawan dummy berhasil ditambahkan.</p>";
    echo '<p><a href="/spk-topsis/index.php?act=karyawan" style="color:#3b82f6;font-weight:bold;font-size:1.1em;">🔗 Kembali ke Halaman Data Karyawan →</a></p>';
    echo '</body></html>';

} catch (PDOException $e) {
    echo '<h2 style="color:#ef4444;">❌ Error</h2>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
