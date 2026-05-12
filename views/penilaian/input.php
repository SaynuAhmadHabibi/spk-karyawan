<?php $page_title = 'Input Penilaian Karyawan'; include __DIR__ . '/../layouts/header.php'; ?>
<div class="glass-panel overflow-hidden">
    <div class="px-6 py-5 border-b" style="border-color: var(--card-border);">
        <h2 class="text-xl font-bold text-white"><i class="bi bi-pencil-square text-primary mr-2"></i> Input Nilai Karyawan</h2>
        <p class="text-muted text-xs mt-1">Periode Penilaian: <span class="text-white font-semibold"><?= htmlspecialchars(date('F Y', strtotime($periode))) ?></span></p>
    </div>

    <div class="p-6">

        <!-- ===== NAVIGASI PERIODE (GET) - TERPISAH dari form simpan ===== -->
        <div class="mb-6 p-4 rounded-xl border" style="border-color: var(--card-border); background: rgba(255,255,255,0.02);">
            <form method="GET" action="index.php" id="formPeriodeNav" class="flex items-end gap-4 flex-wrap">
                <input type="hidden" name="act" value="penilaian_input">
                <div class="flex-1 max-w-xs">
                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">
                        <i class="bi bi-calendar3 mr-1 text-primary"></i> Pilih Periode (Bulan)
                    </label>
                    <input type="month" name="periode" value="<?= htmlspecialchars(substr($periode, 0, 7)) ?>"
                        id="inputPeriodeNav"
                        class="glass-input w-full py-2.5 px-4" required>
                </div>
                <button type="submit" class="btn-glass px-5 py-2.5 flex items-center gap-2 hover:text-white">
                    <i class="bi bi-arrow-right-circle text-primary"></i> Tampilkan Data
                </button>
                <div class="badge-glass" style="background: rgba(255,255,255,0.02)">
                    <i class="bi bi-info-circle text-primary mr-2"></i> Nilai dalam rentang 0 &ndash; 100
                </div>
            </form>
        </div>

        <!-- ===== FORM SIMPAN PENILAIAN (POST) ===== -->
        <!-- periode dikunci sebagai hidden field agar tidak bisa berubah saat submit -->
        <form method="POST" action="index.php?act=penilaian_input" id="formPenilaian">
            <input type="hidden" name="periode" value="<?= htmlspecialchars(substr($periode, 0, 7)) ?>">

            <div class="overflow-x-auto rounded-xl border" style="border-color: var(--card-border);">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <?php foreach($kriteria as $k): ?>
                            <th class="text-center">
                                <div class="text-white"><?= htmlspecialchars($k['nama_kriteria']) ?></div>
                                <div class="text-[10px] text-muted normal-case font-normal mt-1">Bobot: <?= number_format($k['bobot'], 2) ?></div>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($karyawan)): ?>
                        <tr><td colspan="<?= count($kriteria) + 1 ?>" class="text-center py-10 text-muted">Belum ada data karyawan aktif.</td></tr>
                        <?php else: ?>
                        <?php foreach($karyawan as $kar): ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="font-medium text-white border-r" style="border-color: var(--card-border)"><?= htmlspecialchars($kar['nama']) ?></td>
                            <?php foreach($kriteria as $krit): ?>
                            <td class="text-center">
                                <input type="number" step="0.01" min="0" max="100"
                                    name="nilai[<?= $kar['id'] ?>][<?= $krit['id'] ?>]"
                                    value="<?= isset($existing[$kar['id']][$krit['id']]) ? htmlspecialchars($existing[$kar['id']][$krit['id']]) : '' ?>"
                                    class="glass-input w-24 py-2 px-3 text-center focus:ring-2 focus:ring-primary/50"
                                    placeholder="0-100">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-8 flex justify-between items-center border-t pt-6" style="border-color: var(--card-border);">
                <div class="flex gap-3 items-center flex-wrap">
                    <button type="submit" class="btn-primary-glow px-6 py-2.5 flex items-center gap-2">
                        <i class="bi bi-cloud-arrow-up"></i> Simpan Penilaian
                        <span class="text-xs opacity-70">(<?= htmlspecialchars(date('F Y', strtotime($periode))) ?>)</span>
                    </button>
                    <button type="button"
                        onclick="document.querySelectorAll('#formPenilaian input[type=\'number\']').forEach(i => i.value='')"
                        class="btn-glass px-6 py-2.5 text-muted hover:text-white">
                        Kosongkan Input
                    </button>
                </div>
                <a href="index.php?act=penilaian_input&periode=<?= htmlspecialchars(substr($periode, 0, 7)) ?>&clear=1"
                    class="text-danger hover:text-white transition flex items-center gap-1 text-sm font-semibold"
                    onclick="return confirm('Kosongkan semua nilai di database untuk periode <?= htmlspecialchars(date('F Y', strtotime($periode))) ?>?\nTindakan ini tidak dapat dibatalkan.')">
                    <i class="bi bi-trash"></i> Hapus Penilaian Bulan Ini
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Validasi input: ganti koma dengan titik secara otomatis
document.querySelectorAll('#formPenilaian input[type="number"]').forEach(function(el) {
    el.addEventListener('input', function(e) {
        var v = e.target.value;
        if (typeof v === 'string' && v.indexOf(',') !== -1) {
            e.target.value = v.replace(/,/g, '.');
        }
    });
});
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>