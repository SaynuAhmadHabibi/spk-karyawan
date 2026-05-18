<?php 
$page_title = 'Manajemen Kriteria & Bobot'; 
include __DIR__ . '/../layouts/header.php'; 
?>
<div class="glass-panel overflow-hidden">
    <div class="px-6 py-5 flex justify-between items-center flex-wrap gap-4 border-b" style="border-color: var(--card-border);">
        <h2 class="text-xl font-bold text-gray-800"><i class="bi bi-sliders text-primary mr-2"></i> Daftar Kriteria</h2>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="index.php?act=kriteria&sub=create" class="btn-primary-glow px-4 py-2 text-sm flex items-center gap-2"><i class="bi bi-plus-lg"></i> Tambah Kriteria</a>
        <?php endif; ?>
    </div>
    
    <div class="px-6 py-4 flex items-center justify-between" style="background: rgba(0,0,0,0.1); border-bottom: 1px solid var(--card-border);">
        <?php $totalBobot = array_sum(array_column($kriteria ?? [], 'bobot')); ?>
        <div class="flex items-center gap-3">
            <span class="text-sm text-muted">Total Bobot:</span>
            <span class="badge-glass <?= $totalBobot == 1 ? 'badge-success' : 'badge-danger' ?> text-lg">
                <?= number_format($totalBobot, 2) ?>
            </span>
        </div>
        <?php if ($totalBobot != 1): ?>
        <span class="text-xs text-danger"><i class="bi bi-exclamation-triangle-fill"></i> Peringatan: Total bobot harus = 1.00</span>
        <?php endif; ?>
    </div>

    <div class="overflow-x-auto">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>Nama Kriteria</th>
                    <th>Bobot</th>
                    <th>Progress</th>
                    <th>Atribut</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kriteria)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-muted">
                        <i class="bi bi-inbox text-4xl opacity-50 block mb-3"></i>
                        <p>Belum ada data kriteria.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach(($kriteria ?? []) as $k): ?>
                <tr>
                    <td class="font-medium text-gray-800"><?= htmlspecialchars($k['nama_kriteria']) ?></td>
                    <td class="font-mono text-primary font-bold"><?= number_format((float)$k['bobot'], 2) ?></td>
                    <td class="w-1/4">
                        <div class="w-full rounded-full h-1.5 mb-1" style="background: rgba(255,255,255,0.1);">
                            <div class="h-1.5 rounded-full" style="background: var(--primary); width: <?= ((float)$k['bobot'] * 100) ?>%"></div>
                        </div>
                        <span class="text-xs text-muted"><?= ((float)$k['bobot'] * 100) ?>%</span>
                    </td>
                    <td>
                        <?php if (strtolower($k['atribut']) == 'benefit'): ?>
                        <span class="badge-glass badge-success px-2 py-1" style="font-size: 0.65rem;">Benefit</span>
                        <?php else: ?>
                        <span class="badge-glass badge-warning px-2 py-1" style="font-size: 0.65rem;">Cost</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="index.php?act=kriteria&sub=edit&id=<?= $k['id'] ?>" class="text-primary hover:text-gray-800 transition p-1"><i class="bi bi-pencil-square"></i></a>
                            <a href="#" onclick="confirmDelete('index.php?act=kriteria&sub=delete&id=<?= $k['id'] ?>')" class="text-danger hover:text-gray-800 transition p-1"><i class="bi bi-trash-fill"></i></a>
                            <?php else: ?>
                            <span class="text-muted text-xs">—</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDelete(url) { 
    if (confirm('Yakin ingin menghapus kriteria ini?')) { 
        window.location.href = url; 
    } 
}
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
