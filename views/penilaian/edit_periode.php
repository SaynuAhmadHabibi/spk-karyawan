<?php 
$page_title = 'Edit Penilaian Periode';
include __DIR__ . '/../layouts/header.php'; 
?>
<div class="glass-panel overflow-hidden">
    <div class="px-6 py-5 border-b" style="border-color: var(--card-border);">
        <h2 class="text-xl font-bold text-white"><i class="bi bi-pencil-square text-primary mr-2"></i> Edit Penilaian Periode</h2>
        <p class="text-muted text-xs mt-1">Periode: <span class="text-white font-semibold"><?= date('F Y', strtotime($periode)) ?></span></p>
    </div>
    
    <div class="p-6">
        <form method="POST">
            <div class="overflow-x-auto rounded-xl border mb-6" style="border-color: var(--card-border);">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <?php foreach($kriteria as $krit): ?>
                            <th class="text-center"><?= htmlspecialchars($krit['nama_kriteria']) ?> (0-100)</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($karyawan as $kar): ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="font-medium text-white border-r" style="border-color: var(--card-border)"><?= htmlspecialchars($kar['nama']) ?></td>
                            <?php foreach($kriteria as $krit): ?>
                            <td class="text-center">
                                <input type="number" step="0.01" min="0" max="100" 
                                    name="nilai[<?= $kar['id'] ?>][<?= $krit['id'] ?>]" 
                                    value="<?= isset($existing[$kar['id']][$krit['id']]) ? htmlspecialchars($existing[$kar['id']][$krit['id']]) : '' ?>" 
                                    class="glass-input w-24 py-2 px-3 text-center focus:ring-2 focus:ring-primary/50" required
                                    placeholder="0-100">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="flex gap-3 pt-6 border-t" style="border-color: var(--card-border);">
                <button type="submit" class="btn-primary-glow px-6 py-2.5 flex items-center gap-2">
                    <i class="bi bi-cloud-check"></i> Simpan Perubahan
                </button>
                <a href="index.php?act=penilaian_history" class="btn-glass px-6 py-2.5 text-muted hover:text-white">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>