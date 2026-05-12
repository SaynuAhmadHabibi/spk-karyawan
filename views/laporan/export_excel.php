<?php
if (!isset($data) || empty($data['ranking'])) {
    $_SESSION['error'] = 'Tidak ada data untuk diekspor.';
    header('Location: index.php?act=dashboard');
    exit;
}

$filename = 'laporan_' . ($tipe ?? 'reward') . '_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');
fputcsv($output, ['Peringkat', 'NIK', 'Nama Karyawan', 'Divisi', 'Nilai Preferensi', 'Keputusan']);

$total = count($data['ranking']);
$rank = 1;
foreach ($data['ranking'] as $r) {
    $karyawan = $karyawanMap[$r['id_karyawan']] ?? null;
    if (!$karyawan) {
        continue;
    }

    $status = '-';
    if (($tipe ?? 'reward') === 'reward' && $rank <= 3) {
        $status = 'REWARD';
    } elseif (($tipe ?? 'reward') === 'punishment' && $rank > $total - 3) {
        $status = 'PUNISHMENT';
    }

    fputcsv($output, [
        $rank,
        $karyawan['nik'] ?? '',
        $karyawan['nama'] ?? '',
        $karyawan['divisi'] ?? '',
        number_format((float)$r['nilai'], 4, '.', ''),
        $status,
    ]);
    $rank++;
}

fclose($output);
exit;
