<?php 
$page_title = 'History Penilaian';
include __DIR__ . '/../layouts/header.php'; 
$role = $_SESSION['user']['role'] ?? '';
?>
<div class="glass-panel overflow-hidden">
    <div class="px-6 py-5 border-b" style="border-color: var(--card-border);">
        <h2 class="text-xl font-bold text-gray-800"><i class="bi bi-clock-history text-primary mr-2"></i> Riwayat Periode Penilaian</h2>
        <p class="text-muted text-xs mt-1">Daftar periode yang telah diproses dan dihitung.</p>
    </div>
    
    <div class="overflow-x-auto">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th><i class="bi bi-trophy text-success mr-1"></i> Reward (3 Terbaik)</th>
                    <th><i class="bi bi-exclamation-triangle text-danger mr-1"></i> Punishment (3 Terbawah)</th>
                    <?php if ($role !== 'direktur'): ?>
                    <th class="text-center">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($periodeData)): ?>
                <tr>
                    <td colspan="<?= ($role !== 'direktur') ? 4 : 3 ?>" class="px-6 py-12 text-center text-muted">
                        <i class="bi bi-calendar-x text-4xl opacity-50 block mb-3"></i>
                        <p>Belum ada data penilaian tersimpan.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach($periodeData as $pd): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="font-bold text-gray-800"><?= $pd['periode_name'] ?></td>
                    <td>
                        <?php if (!empty($pd['reward'])): ?>
                        <div class="flex flex-col gap-1">
                            <?php foreach ($pd['reward'] as $name): ?>
                            <div class="flex items-center gap-2 text-xs font-medium text-success">
                                <i class="bi bi-check-circle-fill text-[8px]"></i> <?= $name ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <span class="text-muted text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($pd['punishment'])): ?>
                        <div class="flex flex-col gap-1">
                            <?php foreach ($pd['punishment'] as $name): ?>
                            <div class="flex items-center gap-2 text-xs font-medium text-danger">
                                <i class="bi bi-exclamation-circle-fill text-[8px]"></i> <?= $name ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <span class="text-muted text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <?php if ($role !== 'direktur'): ?>
                    <td class="text-center">
                        <div class="flex justify-center gap-3">
                            <a href="index.php?act=penilaian_edit&periode=<?= urlencode($pd['periode']) ?>" class="badge-glass badge-primary py-1 px-3 hover:bg-primary/20 transition">
                                <i class="bi bi-pencil-square mr-1"></i> Edit
                            </a>
                            <button onclick="confirmDeletePeriode('<?= urlencode($pd['periode']) ?>')" class="badge-glass badge-danger py-1 px-3 hover:bg-danger/20 transition">
                                <i class="bi bi-trash-fill mr-1"></i> Hapus
                            </button>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($role !== 'direktur'): ?>
<script>
function confirmDeletePeriode(periode) { 
    if (confirm('Yakin ingin menghapus seluruh data penilaian untuk periode ini?\nTindakan ini juga akan menghapus history ranking periode ini.')) { 
        window.location.href = 'index.php?act=penilaian_delete&periode=' + encodeURIComponent(periode); 
    } 
}
</script>
<?php endif; ?>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
