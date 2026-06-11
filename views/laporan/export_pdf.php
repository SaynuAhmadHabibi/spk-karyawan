<?php
if (!isset($data) || empty($data['ranking'])) {
  $_SESSION['error'] = 'Tidak ada data untuk diekspor.';
  header('Location: index.php?act=dashboard');
  exit;
}

$title = strtoupper($tipe ?? 'REWARD');
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan <?= htmlspecialchars($title) ?></title>
  <style>
    body { font-family: Arial, sans-serif; color: #0f172a; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f3f4f6; }
    .center { text-align: center; }
  </style>
</head>
<body>
  <h2>LAPORAN <?= htmlspecialchars($title) ?></h2>
  <div>Periode: <?= htmlspecialchars($data['periode_mulai'] ?? ($data['periode'] ?? '-')) ?> <?php if(!empty($data['periode_akhir'])): ?>s/d <?= htmlspecialchars($data['periode_akhir']) ?><?php endif; ?></div>

  <table>
    <thead>
      <tr>
        <th class="center">Peringkat</th>
        <th>Nama Karyawan</th>
        <th>Divisi</th>
        <th class="center">Nilai Preferensi</th>
        <th class="center">Keputusan</th>
      </tr>
    </thead>
    <tbody>
      <?php $rank = 1; $total = count($data['ranking']); foreach($data['ranking'] as $r):
        $k = $karyawanMap[$r['id_karyawan']] ?? null;
        if (!$k) continue;
        $status = '-';
        if (($tipe ?? 'reward') === 'reward' && $rank <= 3) $status = 'REWARD';
        if (($tipe ?? 'reward') === 'punishment' && $rank > $total - 3) $status = 'PUNISHMENT';
      ?>
      <tr>
        <td class="center"><?= $rank++ ?></td>
        <td><?= htmlspecialchars($k['nama'] ?? '') ?></td>
        <td><?= htmlspecialchars($k['divisi'] ?? '') ?></td>
        <td class="center"><?= number_format((float)$r['nilai'], 4) ?></td>
        <td class="center"><?= $status ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if (!class_exists('Dompdf\\Dompdf')): ?>
    <script>
      // If Dompdf not installed, show print hint so user can Save as PDF
      window.onload = function(){ window.print(); };
    </script>
  <?php endif; ?>
</body>
</html>
