<?php 
$page_title = 'Edit Kriteria';
include __DIR__ . '/../layouts/header.php'; 
$k = $kriteria;
?>
<div class="glass-panel p-8 max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-white"><i class="bi bi-pencil-square text-primary mr-2"></i> Edit Data Kriteria</h2>
    <?php if(isset($error)): ?>
    <div class="badge-glass badge-danger w-full mb-6 p-3 flex items-center gap-2">
        <i class="bi bi-exclamation-octagon"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    <form method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Nama Kriteria</label>
            <input type="text" name="nama_kriteria" value="<?= htmlspecialchars($k['nama_kriteria']) ?>" required class="glass-input w-full px-4 py-2.5" placeholder="Nama Kriteria">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Bobot (0.00 - 1.00)</label>
            <input type="number" step="0.01" min="0" max="1" name="bobot" value="<?= $k['bobot'] ?>" required class="glass-input w-full px-4 py-2.5">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Atribut Kriteria</label>
            <select name="atribut" class="glass-input w-full px-4 py-2.5 appearance-none">
                <option value="benefit" <?= $k['atribut']=='benefit' ? 'selected' : '' ?> class="bg-slate-900">Benefit</option>
                <option value="cost" <?= $k['atribut']=='cost' ? 'selected' : '' ?> class="bg-slate-900">Cost</option>
            </select>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary-glow px-6 py-2.5">Update Kriteria</button>
            <a href="index.php?act=kriteria" class="btn-glass px-6 py-2.5 text-muted hover:text-white">Batal</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>