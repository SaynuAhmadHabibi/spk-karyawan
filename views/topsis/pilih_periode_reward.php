<?php $page_title = 'Hitung Reward - Pilih Periode Akhir'; include __DIR__ . '/../layouts/header.php'; ?>
<div class="glass-panel p-8 max-w-md mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-white"><i class="bi bi-trophy-fill text-primary mr-2"></i> Hitung Reward</h2>
    <form action="index.php?act=hitung_reward" method="GET">
        <input type="hidden" name="act" value="hitung_reward">
        <div class="mb-6">
            <label class="block text-sm font-medium text-muted mb-2">Pilih Periode Akhir (Bulan)</label>
            <input type="month" name="periode" class="glass-input w-full px-4 py-2.5" required>
            <p class="text-[10px] text-muted mt-2 italic">* Reward dihitung dari akumulasi 6 bulan terakhir hingga bulan yang dipilih.</p>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary-glow px-6 py-2.5 flex-1">Jalankan TOPSIS</button>
            <a href="index.php?act=dashboard" class="btn-glass px-6 py-2.5 text-muted hover:text-white">Batal</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>