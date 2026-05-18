<?php 
$page_title = 'Detail Perhitungan TOPSIS';
include __DIR__ . '/../layouts/header.php'; 

$data = $data ?? [];
$detail = $data['detail'] ?? [];
$karyawan = $data['karyawan'] ?? [];
$kriteria = $data['kriteria'] ?? [];
$tipe = $data['tipe'] ?? 'reward';

if ($tipe === 'punishment') {
    $periodeText = 'Periode: ' . date('F Y', strtotime($data['periode']));
} else {
    $periodeText = 'Periode: ' . date('M Y', strtotime($data['periode_mulai'])) . ' s/d ' . date('M Y', strtotime($data['periode_akhir']));
}
?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-white">Detail Perhitungan TOPSIS</h1>
    <p class="text-muted mt-1"><?= htmlspecialchars($periodeText) ?></p>
    <div class="mt-3">
        <span class="badge-glass <?= $tipe === 'reward' ? 'badge-success' : 'badge-danger' ?> px-3 py-1">
            <i class="bi <?= $tipe === 'reward' ? 'bi-trophy' : 'bi-exclamation-triangle' ?> mr-1"></i>
            <?= ucfirst($tipe) ?> Mode
        </span>
    </div>
</div>

<?php if (empty($detail)): ?>
    <div class="glass-panel p-6 border-l-4 border-warning">
        <p class="text-warning"><i class="bi bi-info-circle mr-2"></i>Data perhitungan tidak tersedia. Silakan hitung terlebih dahulu.</p>
    </div>
<?php else: ?>
    <!-- STEP 1: Matriks Keputusan -->
    <div class="glass-panel overflow-hidden mb-6">
        <div class="px-5 py-3 border-b flex items-center gap-3" style="border-color: var(--card-border); background: rgba(255,255,255,0.02)">
            <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0" style="background: var(--primary); color: white; box-shadow: 0 0 8px var(--primary-glow)">1</div>
            <div>
                <h2 class="text-white font-semibold text-sm">Matriks Keputusan (X)</h2>
                <p class="text-muted" style="font-size:11px">Matriks nilai awal dari setiap alternatif terhadap setiap kriteria.</p>
            </div>
        </div>
        <div class="overflow-auto" style="max-height:320px">
            <table style="width:100%;border-collapse:separate;border-spacing:0;font-size:12px">
                <thead style="position:sticky;top:0;z-index:2">
                    <tr>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:left;border-bottom:1px solid var(--card-border);white-space:nowrap">Alternatif</th>
                        <?php foreach ($kriteria as $k): ?>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:right;border-bottom:1px solid var(--card-border);white-space:nowrap"><?= htmlspecialchars($k['nama_kriteria']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($detail['matriks_awal'] ?? []) as $i => $row): ?>
                    <tr style="transition:.15s">
                        <td style="padding:7px 14px;color:#fff;font-weight:500;border-bottom:1px solid rgba(255,255,255,.03);white-space:nowrap"><?= htmlspecialchars($karyawan[$i]['nama'] ?? '-') ?></td>
                        <?php foreach ($row as $val): ?>
                        <td style="padding:7px 14px;color:#c0c8d2;font-family:monospace;text-align:right;border-bottom:1px solid rgba(255,255,255,.03)"><?= number_format((float)$val, 2) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- STEP 2: Normalisasi Matriks -->
    <div class="glass-panel overflow-hidden mb-6">
        <div class="px-5 py-3 border-b flex items-center gap-3" style="border-color: var(--card-border); background: rgba(255,255,255,0.02)">
            <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0" style="background: var(--success); color: white; box-shadow: 0 0 8px var(--success-glow)">2</div>
            <div>
                <h2 class="text-white font-semibold text-sm">Normalisasi Matriks (R)</h2>
                <p class="text-muted" style="font-size:11px">Rumus: r<sub>ij</sub> = x<sub>ij</sub> / √(∑ x<sub>ij</sub>²)</p>
            </div>
        </div>
        <div class="overflow-auto" style="max-height:320px">
            <table style="width:100%;border-collapse:separate;border-spacing:0;font-size:12px">
                <thead style="position:sticky;top:0;z-index:2">
                    <tr>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:left;border-bottom:1px solid var(--card-border);white-space:nowrap">Alternatif</th>
                        <?php foreach ($kriteria as $k): ?>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:right;border-bottom:1px solid var(--card-border);white-space:nowrap"><?= htmlspecialchars($k['nama_kriteria']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($detail['normalisasi'] ?? []) as $i => $row): ?>
                    <tr style="transition:.15s">
                        <td style="padding:7px 14px;color:#fff;font-weight:500;border-bottom:1px solid rgba(255,255,255,.03);white-space:nowrap"><?= htmlspecialchars($karyawan[$i]['nama'] ?? '-') ?></td>
                        <?php foreach ($row as $val): ?>
                        <td style="padding:7px 14px;color:#c0c8d2;font-family:monospace;text-align:right;border-bottom:1px solid rgba(255,255,255,.03)"><?= number_format((float)$val, 4) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- STEP 3: Normalisasi Terbobot -->
    <div class="glass-panel overflow-hidden mb-6">
        <div class="px-5 py-3 border-b flex items-center gap-3" style="border-color: var(--card-border); background: rgba(255,255,255,0.02)">
            <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0" style="background: #a855f7; color: white; box-shadow: 0 0 8px rgba(168,85,247,.4)">3</div>
            <div>
                <h2 class="text-white font-semibold text-sm">Matriks Ternormalisasi Terbobot (Y)</h2>
                <p class="text-muted" style="font-size:11px">Rumus: y<sub>ij</sub> = w<sub>j</sub> × r<sub>ij</sub></p>
            </div>
        </div>
        <div class="overflow-auto" style="max-height:320px">
            <table style="width:100%;border-collapse:separate;border-spacing:0;font-size:12px">
                <thead style="position:sticky;top:0;z-index:2">
                    <tr>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:left;border-bottom:1px solid var(--card-border);white-space:nowrap">Alternatif</th>
                        <?php foreach ($kriteria as $k): ?>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:right;border-bottom:1px solid var(--card-border);white-space:nowrap"><?= htmlspecialchars($k['nama_kriteria']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($detail['terbobot'] ?? []) as $i => $row): ?>
                    <tr style="transition:.15s">
                        <td style="padding:7px 14px;color:#fff;font-weight:500;border-bottom:1px solid rgba(255,255,255,.03);white-space:nowrap"><?= htmlspecialchars($karyawan[$i]['nama'] ?? '-') ?></td>
                        <?php foreach ($row as $val): ?>
                        <td style="padding:7px 14px;color:#c0c8d2;font-family:monospace;text-align:right;border-bottom:1px solid rgba(255,255,255,.03)"><?= number_format((float)$val, 4) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- STEP 4: Solusi Ideal -->
    <div class="grid md:grid-cols-2 gap-4 mb-6">
        <div class="glass-panel overflow-hidden">
            <div class="px-5 py-3 border-b flex items-center gap-3" style="border-color:var(--card-border);background:rgba(34,197,94,0.05)">
                <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0" style="background:#eab308;color:white;box-shadow:0 0 8px rgba(234,179,8,.4)">4</div>
                <h3 class="text-white font-semibold text-sm"><i class="bi bi-plus-circle text-success mr-1"></i> Solusi Ideal Positif (A⁺)</h3>
            </div>
            <div class="px-5 py-3 space-y-1">
                <?php foreach (($detail['ideal_positif'] ?? []) as $j => $val): ?>
                <div class="flex justify-between items-center py-1.5 border-b" style="border-color:var(--card-border)">
                    <span style="font-size:12px;color:#c0c8d2"><?= htmlspecialchars($kriteria[$j]['nama_kriteria'] ?? '-') ?></span>
                    <span style="font-size:12px;font-family:monospace;font-weight:700;color:var(--success)"><?= number_format((float)$val, 4) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="glass-panel overflow-hidden">
            <div class="px-5 py-3 border-b flex items-center gap-3" style="border-color:var(--card-border);background:rgba(239,68,68,0.1)">
                <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0" style="background:var(--danger);color:white;box-shadow:0 0 8px var(--danger-glow)">4</div>
                <h3 class="text-white font-semibold text-sm"><i class="bi bi-dash-circle text-danger mr-1"></i> Solusi Ideal Negatif (A⁻)</h3>
            </div>
            <div class="px-5 py-3 space-y-1">
                <?php foreach (($detail['ideal_negatif'] ?? []) as $j => $val): ?>
                <div class="flex justify-between items-center py-1.5 border-b" style="border-color:var(--card-border)">
                    <span style="font-size:12px;color:#c0c8d2"><?= htmlspecialchars($kriteria[$j]['nama_kriteria'] ?? '-') ?></span>
                    <span style="font-size:12px;font-family:monospace;font-weight:700;color:var(--danger)"><?= number_format((float)$val, 4) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- STEP 5: Jarak ke Solusi Ideal -->
    <div class="glass-panel overflow-hidden mb-6">
        <div class="px-5 py-3 border-b flex items-center gap-3" style="border-color:var(--card-border);background:rgba(255,255,255,0.02)">
            <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0" style="background:#f97316;color:white;box-shadow:0 0 8px rgba(249,115,22,.4)">5</div>
            <div>
                <h2 class="text-white font-semibold text-sm">Jarak ke Solusi Ideal (D⁺ dan D⁻)</h2>
                <p style="font-size:11px;color:#c0c8d2">D⁺ = √(∑ (y<sub>ij</sub> - A⁺<sub>j</sub>)²) &nbsp;|&nbsp; D⁻ = √(∑ (y<sub>ij</sub> - A⁻<sub>j</sub>)²)</p>
            </div>
        </div>
        <div class="overflow-auto" style="max-height:320px">
            <table style="width:100%;border-collapse:separate;border-spacing:0;font-size:12px">
                <thead style="position:sticky;top:0;z-index:2">
                    <tr>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:left;border-bottom:1px solid var(--card-border)">Alternatif</th>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:right;border-bottom:1px solid var(--card-border)">D⁺ ke Positif</th>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:right;border-bottom:1px solid var(--card-border)">D⁻ ke Negatif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($karyawan as $i => $k): ?>
                    <tr>
                        <td style="padding:7px 14px;color:#fff;font-weight:500;border-bottom:1px solid rgba(255,255,255,.03)"><?= htmlspecialchars($k['nama'] ?? '-') ?></td>
                        <td style="padding:7px 14px;font-family:monospace;text-align:right;color:var(--success);border-bottom:1px solid rgba(255,255,255,.03)"><?= number_format((float)($detail['jarak_positif'][$i] ?? 0), 4) ?></td>
                        <td style="padding:7px 14px;font-family:monospace;text-align:right;color:var(--danger);border-bottom:1px solid rgba(255,255,255,.03)"><?= number_format((float)($detail['jarak_negatif'][$i] ?? 0), 4) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- STEP 6: Nilai Preferensi -->
    <div class="glass-panel overflow-hidden mb-6">
        <div class="px-5 py-3 border-b flex items-center gap-3" style="border-color:var(--card-border);background:rgba(255,255,255,0.02)">
            <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0" style="background:var(--primary);color:white;box-shadow:0 0 8px var(--primary-glow)">6</div>
            <div>
                <h2 class="text-white font-semibold text-sm">Nilai Preferensi (V)</h2>
                <p style="font-size:11px;color:#c0c8d2">Rumus: V<sub>i</sub> = D⁻ / (D⁺ + D⁻)</p>
            </div>
        </div>
        <div class="overflow-auto" style="max-height:320px">
            <table style="width:100%;border-collapse:separate;border-spacing:0;font-size:12px">
                <thead style="position:sticky;top:0;z-index:2">
                    <tr>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:left;border-bottom:1px solid var(--card-border)">Alternatif</th>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:right;border-bottom:1px solid var(--card-border)">Nilai Preferensi (V)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($karyawan as $i => $k): ?>
                    <tr>
                        <td style="padding:7px 14px;color:#fff;font-weight:500;border-bottom:1px solid rgba(255,255,255,.03)"><?= htmlspecialchars($k['nama'] ?? '-') ?></td>
                        <?php
                        $valV = (float)($detail['preferensi'][$i] ?? 0);
                        $vc = ($valV >= 0.7) ? 'var(--success)' : (($valV <= 0.3) ? 'var(--danger)' : 'var(--primary)');
                        ?>
                        <td style="padding:7px 14px;font-family:monospace;font-weight:700;text-align:right;color:<?= $vc ?>;border-bottom:1px solid rgba(255,255,255,.03)"><?= number_format($valV, 4) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- STEP 7: Ranking & Keputusan -->
    <div class="glass-panel overflow-hidden mb-6">
        <div class="px-5 py-3 border-b flex items-center gap-3" style="border-color:var(--card-border);background:rgba(255,255,255,0.02)">
            <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0" style="background:white;color:black;box-shadow:0 0 8px rgba(255,255,255,.3)">7</div>
            <div>
                <h2 class="text-white font-semibold text-sm">Ranking &amp; Keputusan Akhir</h2>
                <p style="font-size:11px;color:#c0c8d2">Berdasarkan pengurutan nilai preferensi (V) tertinggi ke terendah.</p>
            </div>
        </div>
        <div class="overflow-auto" style="max-height:400px">
            <table style="width:100%;border-collapse:separate;border-spacing:0;font-size:12px">
                <thead style="position:sticky;top:0;z-index:2">
                    <tr>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:center;border-bottom:1px solid var(--card-border)">Rank</th>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;border-bottom:1px solid var(--card-border)">NIK</th>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;border-bottom:1px solid var(--card-border)">Nama Karyawan</th>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;border-bottom:1px solid var(--card-border)">Divisi</th>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:right;border-bottom:1px solid var(--card-border)">Nilai V</th>
                        <th style="background:rgba(13,37,36,.97);color:#c0c8d2;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.05em;padding:8px 14px;text-align:center;border-bottom:1px solid var(--card-border)">Keputusan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    $total = count($data['ranking'] ?? []);
                    foreach (($data['ranking'] ?? []) as $r):
                        $kary = null;
                        foreach ($karyawan as $k) { if ($k['id'] == $r['id_karyawan']) { $kary = $k; break; } }
                        if (!$kary) continue;
                        $isReward = ($tipe === 'reward' && $rank <= 3);
                        $isPunishment = ($tipe === 'punishment' && $rank >= $total - 2);
                        $rowBg = $isReward ? 'background:rgba(34,197,94,.05)' : ($isPunishment ? 'background:rgba(239,68,68,.05)' : '');
                    ?>
                    <tr style="<?= $rowBg ?>">
                        <td style="padding:7px 14px;text-align:center;font-weight:700;border-bottom:1px solid rgba(255,255,255,.03)">
                            <?php if ($rank==1): ?>🥇<?php elseif ($rank==2): ?>🥈<?php elseif ($rank==3): ?>🥉<?php else: ?><span style="color:#c0c8d2"><?= $rank ?></span><?php endif; ?>
                        </td>
                        <td style="padding:7px 14px;font-family:monospace;color:#c0c8d2;border-bottom:1px solid rgba(255,255,255,.03)"><?= htmlspecialchars($kary['nik'] ?? '-') ?></td>
                        <td style="padding:7px 14px;color:#fff;font-weight:500;border-bottom:1px solid rgba(255,255,255,.03)"><?= htmlspecialchars($kary['nama'] ?? '-') ?></td>
                        <td style="padding:7px 14px;color:#c0c8d2;border-bottom:1px solid rgba(255,255,255,.03)"><?= htmlspecialchars($kary['divisi'] ?? '-') ?></td>
                        <td style="padding:7px 14px;font-family:monospace;font-weight:700;text-align:right;color:var(--primary);border-bottom:1px solid rgba(255,255,255,.03)"><?= number_format((float)$r['nilai'], 4) ?></td>
                        <td style="padding:7px 14px;text-align:center;border-bottom:1px solid rgba(255,255,255,.03)">
                            <?php if ($isReward): ?>
                            <span class="badge-glass badge-success" style="font-size:10px;padding:2px 8px">REWARD</span>
                            <?php elseif ($isPunishment): ?>
                            <span class="badge-glass badge-danger" style="font-size:10px;padding:2px 8px">PUNISHMENT</span>
                            <?php else: ?><span style="color:#c0c8d2">—</span><?php endif; ?>
                        </td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Catatan -->
    <div class="glass-panel border-l-4 mb-6" style="border-left-color:var(--primary);padding:12px 16px">
        <p style="font-size:12px;color:#c0c8d2;line-height:1.6">
            <i class="bi bi-info-circle-fill text-primary mr-2"></i>
            <strong style="color:#fff">Keterangan:</strong>
            <?php if ($tipe === 'reward'): ?>
            3 karyawan dengan nilai preferensi tertinggi direkomendasikan mendapatkan <strong>REWARD</strong>.
            <?php else: ?>
            3 karyawan dengan nilai preferensi terendah direkomendasikan mendapatkan <strong>PUNISHMENT</strong>.
            <?php endif; ?>
            Perhitungan menggunakan metode TOPSIS dengan normalisasi akar jumlah kuadrat dan pembobotan kriteria.
        </p>
    </div>

    <!-- Debug Log (sementara - hapus setelah selesai debugging) -->
    <?php $debugLog = $detail['debug_log'] ?? []; ?>
    <?php if (!empty($debugLog)): ?>
    <div class="glass-panel overflow-hidden mb-8">
        <div class="px-6 py-4 border-b flex items-center gap-2" style="border-color: var(--card-border); background: rgba(245, 158, 11, 0.05);">
            <h3 class="text-white font-semibold"><i class="bi bi-bug text-warning mr-2"></i> Debug Log TOPSIS <span class="text-xs text-muted">(hapus setelah selesai debugging)</span></h3>
        </div>
        <div class="p-4 overflow-x-auto" style="max-height: 400px; overflow-y: auto;">
            <table class="glass-table text-xs">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th style="width: 200px;">Step</th>
                        <th>Data</th>
                        <th style="width: 80px;">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($debugLog as $idx => $log): ?>
                    <tr>
                        <td class="font-mono text-muted"><?= $idx + 1 ?></td>
                        <td class="font-mono text-primary font-bold"><?= htmlspecialchars($log['step']) ?></td>
                        <td class="font-mono text-muted" style="white-space: pre-wrap; word-break: break-all;">
                            <?php 
                            if (is_array($log['data'])) {
                                echo htmlspecialchars(json_encode($log['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            } else {
                                echo htmlspecialchars($log['data']);
                            }
                            ?>
                        </td>
                        <td class="font-mono text-muted"><?= htmlspecialchars($log['timestamp'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Navigasi -->
<div class="flex justify-between items-center mt-8 pb-10">
    <a href="index.php?act=<?= $tipe === 'reward' ? 'hasil_reward' : 'hasil_punishment' ?>" class="btn-glass px-6 py-2.5 flex items-center gap-2">
        <i class="bi bi-arrow-left"></i> Kembali ke Hasil
    </a>
    <a href="index.php?act=<?= $tipe === 'reward' ? 'hitung_reward_form' : 'hitung_punishment_form' ?>" class="btn-primary-glow px-6 py-2.5 flex items-center gap-2">
        <i class="bi bi-arrow-repeat"></i> Hitung Ulang
    </a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>