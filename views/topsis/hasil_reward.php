<?php 
$page_title = 'Hasil Reward';
require_once __DIR__ . '/../layouts/header.php'; 
$data = $_SESSION['hasil_reward'] ?? null;
if (!$data) { 
    echo '<div class="glass-panel p-6 text-center text-muted"><i class="bi bi-exclamation-triangle text-4xl mb-3 block"></i>Data tidak ditemukan. Silakan hitung ulang.</div>'; 
    require_once __DIR__ . '/../layouts/footer.php'; 
    exit; 
}
$ranking = $data['ranking'];
$karyawan = $data['karyawan'];
$periodeMulai = $data['periode_mulai'];
$periodeAkhir = $data['periode_akhir'];
$total = count($ranking);
$top3 = array_slice($ranking, 0, 3);

// Cek apakah ada data penilaian
$hasData = true;
if (!empty($ranking)) {
    $maxScore = max(array_column($ranking, 'nilai'));
    if ($maxScore == 0) {
        $hasData = false;
    }
}
?>

<?php if (!$hasData): ?>
<div class="glass-panel p-5 mb-6 border-l-4" style="border-left-color: #f59e0b;">
    <div class="flex items-start gap-3">
        <i class="bi bi-exclamation-triangle-fill text-warning text-xl mt-0.5"></i>
        <div>
            <h3 class="text-white font-semibold mb-1">Data Penilaian Tidak Ditemukan</h3>
            <p class="text-sm text-muted mb-3">Tidak ada data penilaian untuk rentang periode <strong class="text-white"><?= date('M Y', strtotime($periodeMulai)) ?> s/d <?= date('M Y', strtotime($periodeAkhir)) ?></strong>. Semua skor akan bernilai 0.</p>
            <div class="flex gap-2">
                <a href="index.php?act=hitung_reward_form" class="btn-glass px-4 py-2 text-sm hover:text-warning">
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
        <h2 class="text-xl font-bold text-white"><i class="bi bi-trophy-fill text-warning mr-2"></i> Hasil Reward <span class="text-sm font-normal text-muted ml-2">(<?= date('M Y', strtotime($periodeMulai)) ?> s/d <?= date('M Y', strtotime($periodeAkhir)) ?>)</span></h2>
        <div class="flex gap-2">
            <a href="index.php?act=export_excel&tipe=reward" class="btn-glass px-4 py-2 text-sm flex items-center gap-2 hover:text-success"><i class="bi bi-file-earmark-excel"></i> Excel</a>
            <a href="index.php?act=export_pdf&tipe=reward" class="btn-glass px-4 py-2 text-sm flex items-center gap-2 hover:text-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
            <a href="index.php?act=detail_perhitungan&tipe=reward" class="btn-primary-glow px-4 py-2 text-sm flex items-center gap-2"><i class="bi bi-calculator"></i> Detail Perhitungan</a>
        </div>
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
                        <p>Tidak ada data reward.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php 
                $rank=1; 
                foreach($ranking as $r): 
                    $k = current(array_filter($karyawan, fn($x)=>$x['id']==$r['id_karyawan'])); 
                    if(!$k) continue; 
                    
                    $isReward = ($rank <= 3); 
                    $rowStyle = $isReward ? 'background: rgba(34, 197, 94, 0.05);' : '';
                    $medals = ['rank-1 🥇', 'rank-2 🥈', 'rank-3 🥉'];
                ?>
                <tr style="<?= $rowStyle ?>">
                    <td class="text-center font-bold text-lg <?= $isReward ? explode(' ', $medals[$rank-1])[0] : 'text-muted' ?>">
                        <?= $isReward ? explode(' ', $medals[$rank-1])[1] : $rank ?>
                    </td>
                    <td class="font-medium <?= $rank == 1 ? 'text-warning' : 'text-white' ?>"><?= htmlspecialchars($k['nama']??'-') ?></td>
                    <td><?= htmlspecialchars($k['divisi']??'-') ?></td>
                    <td class="font-mono text-primary font-bold"><?= number_format((float)$r['nilai'], 4) ?></td>
                    <td class="text-center">
                        <?php if ($isReward): ?>
                        <span class="badge-glass badge-success px-3 py-1 text-xs">LAYAK REWARD</span>
                        <?php else: ?>
                        <span class="badge-glass px-3 py-1 text-xs" style="color: rgba(255,255,255,0.5); border-color: rgba(255,255,255,0.1);">TIDAK</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php $rank++; endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 flex justify-between items-center flex-wrap gap-4" style="background: rgba(0,0,0,0.2); border-top: 1px solid var(--card-border);">
        <div class="text-sm text-muted">
            <i class="bi bi-info-circle text-primary mr-1"></i> 3 karyawan dengan nilai preferensi tertinggi mendapatkan REWARD.
        </div>
        <div class="flex gap-2">
            <a href="index.php?act=hitung_reward_form" class="btn-glass px-4 py-2 text-sm hover:text-white">Hitung Ulang</a>
            <a href="index.php?act=hasil_punishment" class="btn-glass px-4 py-2 text-sm hover:text-danger">Lihat Punishment</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>