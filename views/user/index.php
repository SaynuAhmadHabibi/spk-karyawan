<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-extrabold text-white tracking-tight">Manajemen Pengguna</h2>
        <p class="text-sm text-muted mt-1">Kelola akses akun admin, HRD, dan direktur.</p>
    </div>
    <button onclick="openAddModal()" class="btn-primary-glow px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2">
        <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
    </button>
</div>

<div class="glass-panel overflow-hidden">
    <div class="overflow-x-auto">
        <table class="glass-table w-full">
            <thead>
                <tr>
                    <th class="w-16 text-center">No</th>
                    <th>Username</th>
                    <th>Role (Hak Akses)</th>
                    <th class="text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="4" class="text-center py-10 text-muted">Belum ada data.</td>
                </tr>
                <?php else: ?>
                <?php foreach($users as $i => $u): ?>
                <tr class="hover:bg-white/5 transition">
                    <td class="text-center text-muted"><?= $i+1 ?></td>
                    <td class="font-semibold text-white"><?= htmlspecialchars($u['username']) ?></td>
                    <td>
                        <?php if ($u['role'] === 'admin'): ?>
                            <span class="px-2 py-1 bg-orange-500/20 text-orange-400 rounded text-xs font-bold uppercase tracking-wider"><i class="bi bi-shield-fill-check mr-1"></i>Admin</span>
                        <?php elseif ($u['role'] === 'hrd'): ?>
                            <span class="px-2 py-1 bg-blue-500/20 text-blue-400 rounded text-xs font-bold uppercase tracking-wider"><i class="bi bi-person-lines-fill mr-1"></i>HRD</span>
                        <?php elseif ($u['role'] === 'direktur'): ?>
                            <span class="px-2 py-1 bg-purple-500/20 text-purple-400 rounded text-xs font-bold uppercase tracking-wider"><i class="bi bi-eye-fill mr-1"></i>Direktur</span>
                        <?php else: ?>
                            <span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-bold uppercase tracking-wider"><i class="bi bi-person-fill mr-1"></i><?= ucfirst($u['role']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <div class="flex justify-center gap-2">
                            <button onclick="openEditModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['username'])) ?>', '<?= htmlspecialchars(addslashes($u['role'])) ?>')" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-primary/20 text-muted hover:text-primary transition flex items-center justify-center">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                            <a href="index.php?act=user_delete&id=<?= $u['id'] ?>" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-danger/20 text-muted hover:text-danger transition flex items-center justify-center" onclick="return confirm('Hapus user <?= htmlspecialchars(addslashes($u['username'])) ?> secara permanen?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php else: ?>
                            <div class="w-8 h-8 rounded-lg bg-white/5 text-muted/30 flex items-center justify-center cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                                <i class="bi bi-trash"></i>
                            </div>
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

<!-- Modal Tambah -->
<div id="addModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="glass-card w-full max-w-md p-6 relative" style="animation: slideUp 0.3s ease-out;">
        <button onclick="closeAddModal()" class="absolute top-4 right-4 text-muted hover:text-white transition"><i class="bi bi-x-lg"></i></button>
        <h3 class="text-xl font-bold text-white mb-6">Tambah Pengguna Baru</h3>
        <form action="index.php?act=user_store" method="POST">
            <div class="mb-4">
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Username</label>
                <input type="text" name="username" class="glass-input w-full px-4 py-2.5" required placeholder="Masukkan username">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" class="glass-input w-full px-4 py-2.5" required placeholder="Buat password">
            </div>
            <div class="mb-6">
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Role Akses</label>
                <select name="role" class="glass-input w-full px-4 py-2.5" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    <option value="admin">Admin (Full Access)</option>
                    <option value="hrd">HRD (Kelola Data &amp; Nilai)</option>
                    <option value="direktur">Direktur (Hanya Lihat &amp; Print)</option>
                </select>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeAddModal()" class="btn-glass px-5 py-2.5 text-muted hover:text-white">Batal</button>
                <button type="submit" class="btn-primary-glow px-5 py-2.5">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="glass-card w-full max-w-md p-6 relative" style="animation: slideUp 0.3s ease-out;">
        <button onclick="closeEditModal()" class="absolute top-4 right-4 text-muted hover:text-white transition"><i class="bi bi-x-lg"></i></button>
        <h3 class="text-xl font-bold text-white mb-6">Edit Pengguna</h3>
        <form action="index.php?act=user_update" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-4">
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Username</label>
                <input type="text" name="username" id="edit_username" class="glass-input w-full px-4 py-2.5" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Password Baru (Opsional)</label>
                <input type="password" name="password" class="glass-input w-full px-4 py-2.5" placeholder="Kosongkan jika tidak ingin mengubah password">
            </div>
            <div class="mb-6">
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Role Akses</label>
                <select name="role" id="edit_role" class="glass-input w-full px-4 py-2.5" required>
                    <option value="admin">Admin (Full Access)</option>
                    <option value="hrd">HRD (Kelola Data &amp; Nilai)</option>
                    <option value="direktur">Direktur (Hanya Lihat &amp; Print)</option>
                </select>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeEditModal()" class="btn-glass px-5 py-2.5 text-muted hover:text-white">Batal</button>
                <button type="submit" class="btn-primary-glow px-5 py-2.5">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}
function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}
function openEditModal(id, username, role) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_role').value = role;
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
