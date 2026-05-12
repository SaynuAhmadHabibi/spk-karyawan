<?php $page_title = 'Hitung Punishment - Pilih Periode'; include __DIR__ . '/../layouts/header.php'; ?>
<div class="glass-panel p-8 max-w-md mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-white"><i class="bi bi-exclamation-triangle-fill text-danger mr-2"></i> Hitung Punishment</h2>
    <form action="index.php" method="GET">
        <input type="hidden" name="act" value="hitung_punishment">
        <div class="mb-6">
            <label class="block text-sm font-medium text-muted mb-2">Pilih Periode Penilaian (Bulan)</label>
            <input type="month" name="periode" class="glass-input w-full px-4 py-2.5" required>
            <p class="text-[10px] text-muted mt-2 italic">* Punishment dihitung berdasarkan penilaian performa pada bulan yang dipilih.</p>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary-glow px-6 py-2.5 flex-1" style="background: var(--danger) !important; box-shadow: 0 0 15px var(--danger-glow) !important;">Hitung Sekarang</button>
            <a href="index.php?act=dashboard" class="btn-glass px-6 py-2.5 text-muted hover:text-white">Batal</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>