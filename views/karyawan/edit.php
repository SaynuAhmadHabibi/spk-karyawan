<?php 
/**
 * @var array $karyawan
 * @var string $error
 */
$page_title = 'Edit Karyawan';
include __DIR__ . '/../layouts/header.php'; 
$karyawan = $karyawan ?? [];
$error = $error ?? null;
$k = $karyawan;
?>
<div class="glass-panel p-8 max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-gray-800"><i class="bi bi-pencil-square text-primary mr-2"></i> Edit Data Karyawan</h2>
    <?php if(isset($error)): ?>
    <div class="badge-glass badge-danger w-full mb-6 p-3 flex items-center gap-2">
        <i class="bi bi-exclamation-octagon"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    <form method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($k['nama'] ?? '') ?>" required class="glass-input w-full px-4 py-2.5" placeholder="Nama Karyawan">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Jabatan</label>
            <input type="text" name="jabatan" value="<?= htmlspecialchars($k['jabatan'] ?? '') ?>" class="glass-input w-full px-4 py-2.5" placeholder="Jabatan">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Divisi</label>
            <input type="text" name="divisi" value="<?= htmlspecialchars($k['divisi'] ?? '') ?>" class="glass-input w-full px-4 py-2.5" placeholder="Divisi">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" value="<?= !empty($k['tanggal_masuk']) ? htmlspecialchars(substr($k['tanggal_masuk'],0,10)) : '' ?>" class="glass-input w-full px-4 py-2.5">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Status Kepegawaian</label>
            <select name="status" class="glass-input w-full px-4 py-2.5 appearance-none">
                <option value="aktif" <?= (isset($k['status']) && $k['status']==='aktif')?'selected':'' ?> class="bg-slate-900">Aktif</option>
                <option value="nonaktif" <?= (isset($k['status']) && $k['status']==='nonaktif')?'selected':'' ?> class="bg-slate-900">Nonaktif</option>
            </select>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary-glow px-6 py-2.5">Update Data</button>
            <a href="index.php?act=karyawan" class="btn-glass px-6 py-2.5 text-muted hover:text-gray-800">Batal</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
