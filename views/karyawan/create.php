<?php $page_title = 'Tambah Karyawan'; include __DIR__ . '/../layouts/header.php'; ?>
<div class="glass-panel p-8 max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-white"><i class="bi bi-person-plus-fill text-primary mr-2"></i> Tambah Karyawan Baru</h2>
    <?php if(isset($error)): ?>
    <div class="badge-glass badge-danger w-full mb-6 p-3 flex items-center gap-2">
        <i class="bi bi-exclamation-octagon"></i> <?= $error ?>
    </div>
    <?php endif; ?>
    <form method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-muted mb-2">NIK</label>
            <input type="text" name="nik" required class="glass-input w-full px-4 py-2.5" placeholder="Contoh: 123456">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Nama Lengkap</label>
            <input type="text" name="nama" required class="glass-input w-full px-4 py-2.5" placeholder="Nama Karyawan">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Jabatan</label>
            <input type="text" name="jabatan" class="glass-input w-full px-4 py-2.5" placeholder="Contoh: Manager">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Divisi</label>
            <input type="text" name="divisi" class="glass-input w-full px-4 py-2.5" placeholder="Contoh: HRD">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" class="glass-input w-full px-4 py-2.5">
        </div>
        <div>
            <label class="block text-sm font-medium text-muted mb-2">Status Kepegawaian</label>
            <select name="status" class="glass-input w-full px-4 py-2.5 appearance-none">
                <option value="aktif" class="bg-slate-900">Aktif</option>
                <option value="nonaktif" class="bg-slate-900">Nonaktif</option>
            </select>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="submit" class="btn-primary-glow px-6 py-2.5">Simpan Data</button>
            <a href="index.php?act=karyawan" class="btn-glass px-6 py-2.5 text-muted hover:text-white">Batal</a>
        </div>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>