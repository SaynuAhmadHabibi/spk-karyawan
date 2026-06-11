<?php 
/**
 * @var array $karyawan
 */
$page_title = 'Data Karyawan';
$karyawan = $karyawan ?? [];
require_once __DIR__ . '/../layouts/header.php'; 
?>
<div class="glass-panel overflow-hidden">
    <div class="px-6 py-5 border-b flex justify-between items-center flex-wrap gap-4" style="border-color: var(--card-border)">
        <h2 class="font-semibold text-gray-800"><i class="bi bi-people-fill text-primary mr-2"></i> Daftar Karyawan</h2>
        <div class="flex items-center gap-3">
            <div class="relative">
                <i class="bi bi-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                <input type="text" placeholder="Cari karyawan..." class="glass-input py-2 pl-10 pr-4 text-sm w-64" disabled>
            </div>
            <?php if ($_SESSION['user']['role'] !== 'direktur'): ?>
            <a href="index.php?act=karyawan&sub=create" class="btn-primary-glow px-4 py-2 text-sm flex items-center gap-2"><i class="bi bi-plus-lg"></i> Tambah Karyawan</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="px-6 py-4 flex gap-4 text-sm" style="border-bottom: 1px solid var(--card-border); background: rgba(0,0,0,0.1);">
        <?php 
        $aktif = count(array_filter($karyawan, fn($k) => ($k['status'] ?? 'aktif') === 'aktif'));
        $nonaktif = count($karyawan) - $aktif;
        ?>
        <span class="badge-glass badge-success"><i class="bi bi-check-circle-fill"></i> <?= $aktif ?> Aktif</span>
        <span class="badge-glass" style="background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid rgba(255,255,255,0.1)"><i class="bi bi-dash-circle-fill"></i> <?= $nonaktif ?> Nonaktif</span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="glass-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Divisi</th>
                    <th>Jabatan</th>
                    <th>Tgl Masuk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($karyawan)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-muted">
                        <i class="bi bi-inbox text-4xl opacity-50 block mb-3"></i>
                        <p>Belum ada data karyawan.</p>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($karyawan as $i => $k): ?>
                <tr>
                    <td class="text-muted"><?= $i+1 ?></td>
                    <td class="font-medium text-gray-800"><?= htmlspecialchars($k['nama']) ?></td>
                    <td><?= htmlspecialchars($k['divisi'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($k['jabatan'] ?? '-') ?></td>
                    <td class="text-muted"><?= $k['tanggal_masuk'] ? date('d M Y', strtotime($k['tanggal_masuk'])) : '-' ?></td>
                    <td>
                        <?php if (($k['status'] ?? 'aktif') === 'aktif'): ?>
                        <span class="badge-glass badge-success px-2 py-1" style="font-size: 0.65rem;">Aktif</span>
                        <?php else: ?>
                        <span class="badge-glass px-2 py-1" style="font-size: 0.65rem; background: rgba(255,255,255,0.1); color: var(--text-muted);">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <?php if ($_SESSION['user']['role'] !== 'direktur'): ?>
                            <a href="index.php?act=karyawan&sub=edit&id=<?= $k['id'] ?>" class="text-primary hover:text-gray-800 transition p-1"><i class="bi bi-pencil-square"></i></a>
                            <button onclick="confirmDelete('index.php?act=karyawan&sub=delete&id=<?= $k['id'] ?>')" class="text-danger hover:text-gray-800 transition p-1"><i class="bi bi-trash-fill"></i></button>
                            <?php else: ?>
                            <span class="text-muted text-xs">Tidak ada aksi</span>
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
    if (confirm('Yakin ingin menghapus karyawan ini?')) { 
        window.location.href = url; 
    } 
}
</script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
