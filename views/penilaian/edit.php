<?php 
$page_title = 'Edit Penilaian Karyawan';
include __DIR__ . '/../layouts/header.php'; 
?>
<div class="glass-panel p-8 max-w-2xl mx-auto">
    <h2 class="text-2xl font-bold mb-2 text-white"><i class="bi bi-pencil-square text-primary mr-2"></i> Edit Penilaian</h2>
    <div class="mb-8 p-3 badge-glass" style="background: rgba(255,255,255,0.02)">
        <div class="text-sm font-medium text-white"><?= htmlspecialchars($karyawan['nama']) ?></div>
        <div class="text-xs text-muted">Periode: <?= date('F Y', strtotime($periode)) ?></div>
    </div>

    <form method="POST">
        <input type="hidden" name="id_karyawan" value="<?= $karyawan['id'] ?>">
        <input type="hidden" name="periode" value="<?= $periode ?>">
        
        <div class="overflow-hidden rounded-xl border mb-6" style="border-color: var(--card-border);">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th class="text-center">Nilai (0-100)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($kriteria as $krit): ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="font-medium text-white"><?= htmlspecialchars($krit['nama_kriteria']) ?></td>
                        <td class="text-center">
                            <input type="number" step="0.01" min="0" max="100" 
                                   name="nilai[<?= $krit['id'] ?>]" 
                                   value="<?= $nilaiExisting[$krit['id']] ?? '' ?>" 
                                   class="glass-input w-32 py-2 px-3 text-center" required
                                   placeholder="0-100">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="flex gap-3 pt-4 border-t" style="border-color: var(--card-border);">
            <button type="submit" class="btn-primary-glow px-6 py-2.5 flex items-center gap-2">
                <i class="bi bi-cloud-check"></i> Simpan Perubahan
            </button>
            <a href="index.php?act=penilaian_history" class="btn-glass px-6 py-2.5 text-muted hover:text-white">
                Batal & Kembali
            </a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>