<?php 
$page_title = 'Hasil Punishment';
require_once __DIR__ . '/../layouts/header.php'; 
$data = $_SESSION['hasil_punishment'] ?? null;
if (!$data) { 
    echo '<div class="glass-panel p-6 text-center text-muted"><i class="bi bi-exclamation-triangle text-4xl mb-3 block"></i>Data tidak ditemukan. Silakan hitung ulang.</div>'; 
    require_once __DIR__ . '/../layouts/footer.php'; 
    exit; 
}
$ranking = $data['ranking'];
$karyawan = $data['karyawan'];
$periode = $data['periode'];
$debugInfo = $data['debug_info'] ?? [];
$total = count($ranking);

// Urutkan ascending (skor terendah di atas) untuk punishment
usort($ranking, function($a, $b) {
    return $a['nilai'] <=> $b['nilai'];
});

// 3 karyawan dengan skor terendah mendapat punishment
$punishmentCount = min(3, $total);

// Cek apakah ada data penilaian
$hasData = ($debugInfo['data_penilaian_ditemukan'] ?? 0) > 0;
?>

<?php if (!$hasData): ?>
<div class="glass-panel p-5 mb-6 border-l-4" style="border-left-color: #f59e0b;">
    <div class="flex items-start gap-3">
        <i class="bi bi-exclamation-triangle-fill text-warning text-xl mt-0.5"></i>
        <div>
            <h3 class="text-gray-800 font-semibold mb-1">Data Penilaian Tidak Ditemukan</h3>
            <p class="text-sm text-muted mb-3">Tidak ada data penilaian untuk periode <strong class="text-gray-800"><?= date('F Y', strtotime($periode)) ?></strong>. Semua skor akan bernilai 0.</p>
            <div class="flex gap-2">
                <a href="index.php?act=hitung_punishment_form" class="btn-glass px-4 py-2 text-sm hover:text-warning">
                    <i class="bi bi-arrow-left mr-1"></i> Pilih Periode Lain
                </a>
                <a href="index.php?act=penilaian_input" class="btn-glass px-4 py-2 text-sm hover:text-primary">
                    <i class="bi bi-pencil-square mr-1"></i> Input Penilaian
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="glass-panel overflow-hidden">
    <div class="px-6 py-5 flex justify-between items-center flex-wrap gap-4 border-b" style="border-color: var(--card-border);">
        <h2 class="text-xl font-bold text-gray-800"><i class="bi bi-exclamation-triangle-fill text-danger mr-2"></i> Hasil Punishment <span class="text-sm font-normal text-muted ml-2">(Periode <?= date('F Y', strtotime($periode)) ?>)</span></h2>
        <div class="flex gap-2">
            <a href="index.php?act=export_excel&tipe=punishment" class="btn-glass px-4 py-2 text-sm flex items-center gap-2 hover:text-success"><i class="bi bi-file-earmark-excel"></i> Excel</a>
            <a href="index.php?act=export_pdf&tipe=punishment" class="btn-glass px-4 py-2 text-sm flex items-center gap-2 hover:text-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
            <a href="index.php?act=detail_perhitungan&tipe=punishment" class="btn-primary-glow px-4 py-2 text-sm flex items-center gap-2"><i class="bi bi-calculator"></i> Detail Perhitungan</a>
        </div>
    </div>
    
    <div class="px-6 py-4 flex gap-4 text-sm" style="border-bottom: 1px solid var(--card-border); background: rgba(0,0,0,0.1);">
        <span class="badge-glass badge-danger"><i class="bi bi-person-exclamation"></i> Total <?= $total ?> Karyawan — <?= $punishmentCount ?> Terendah Mendapat Punishment</span>
    </div>

    <div class="overflow-x-auto">
        <table class="glass-table">
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Divisi</th>
                    <th>Skor</th>
                    <th class="text-center">Keputusan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ranking)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-muted">
                        <i class="bi bi-inbox text-4xl opacity-50 block mb-3"></i>
                        <p>Tidak ada data punishment.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php 
                $no = 1;
                foreach ($ranking as $r): 
                    $k = null;
                    foreach ($karyawan as $kary) {
                        if ($kary['id'] == $r['id_karyawan']) { $k = $kary; break; }
                    }
                    if (!$k) continue;
                    
                    // 3 skor terendah mendapat punishment (sudah diurutkan ascending)
                    $isPunishment = ($no <= $punishmentCount);
                    $rowStyle = $isPunishment ? 'background: rgba(239, 68, 68, 0.08);' : '';
                ?>
                <tr style="<?= $rowStyle ?>">
                    <td class="text-center font-bold <?= $isPunishment ? 'text-danger text-lg' : 'text-muted' ?>"><?= $no ?></td>
                    <td class="font-medium <?= $isPunishment ? 'text-danger' : 'text-gray-800' ?>"><?= htmlspecialchars($k['nama'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($k['divisi'] ?? '-') ?></td>
                    <td class="font-mono <?= $isPunishment ? 'text-danger font-bold' : 'text-primary' ?>"><?= number_format((float)$r['nilai'], 4) ?></td>
                    <td class="text-center">
                        <?php if ($isPunishment): ?>
                        <span class="badge-glass badge-danger px-3 py-1 text-xs">⚠️ PUNISHMENT</span>
                        <?php else: ?>
                        <span class="badge-glass px-3 py-1 text-xs" style="color: var(--success); border-color: rgba(34,197,94,0.3);">✓ AMAN</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php $no++; endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 flex justify-between items-center flex-wrap gap-4" style="background: rgba(0,0,0,0.2); border-top: 1px solid var(--card-border);">
        <div class="text-sm text-muted">
            <i class="bi bi-info-circle text-danger mr-1"></i> Dasar keputusan: 3 karyawan dengan skor preferensi terendah direkomendasikan mendapat punishment.
        </div>
        <div class="flex gap-2">
            <a href="index.php?act=hitung_punishment_form" class="btn-glass px-4 py-2 text-sm hover:text-gray-800">Hitung Ulang</a>
            <a href="index.php?act=hasil_reward" class="btn-glass px-4 py-2 text-sm hover:text-primary" style="color: var(--primary) !important;">Lihat Hasil Reward</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
