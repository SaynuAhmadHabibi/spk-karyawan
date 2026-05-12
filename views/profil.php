<?php include 'views/layouts/header.php'; ?>

<?php
$photo    = $user['photo'] ?? null;
$photoUrl = $photo ? 'assets/uploads/photos/' . htmlspecialchars($photo) : null;
$initial  = strtoupper(substr($user['username'], 0, 1));
?>

<div class="max-w-lg mx-auto mt-4 pb-10">

    <?php if (!empty($_SESSION['success'])): ?>
    <div class="badge-glass badge-success w-full mb-4 p-3 flex items-center gap-2 rounded-xl">
        <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
    <div class="badge-glass badge-danger w-full mb-4 p-3 flex items-center gap-2 rounded-xl">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <div class="glass-panel p-8 relative overflow-hidden rounded-[2rem] shadow-2xl">
        <!-- Decorative glow -->
        <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-br from-primary to-accent opacity-20 blur-xl"></div>

        <div class="relative z-10 flex flex-col items-center">

            <!-- ── Avatar + Upload Button ── -->
            <form action="index.php?act=profil_upload_photo" method="POST" enctype="multipart/form-data" id="photoForm">
                <div class="relative mb-5 group cursor-pointer" onclick="document.getElementById('photoInput').click()">
                    <!-- Avatar circle -->
                    <div class="w-28 h-28 rounded-full shadow-[0_8px_30px_rgba(34,87,79,0.5)] flex items-center justify-center text-4xl font-black text-white border-4 border-[#0d2524] overflow-hidden transition-transform group-hover:scale-105"
                         style="background: linear-gradient(135deg, #22574f, #ea580c);">
                        <?php if ($photoUrl): ?>
                        <img id="avatarPreview" src="<?= $photoUrl ?>" alt="Foto Profil"
                             class="w-full h-full object-cover rounded-full">
                        <?php else: ?>
                        <img id="avatarPreview" src="" alt="" class="w-full h-full object-cover rounded-full hidden">
                        <span id="avatarInitial"><?= $initial ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Overlay on hover -->
                    <div class="absolute inset-0 rounded-full bg-black/50 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <i class="bi bi-camera-fill text-white text-xl"></i>
                        <span class="text-white text-[10px] font-bold mt-1">Ganti Foto</span>
                    </div>

                    <!-- Hidden file input -->
                    <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden"
                           onchange="previewAndSubmit(this)">
                </div>
            </form>

            <h2 class="text-2xl font-extrabold text-white tracking-tight mb-1"><?= htmlspecialchars($user['username']) ?></h2>
            <div class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-primary/20 text-teal-300 border border-primary/30 shadow-inner mb-8 tracking-wider">
                <i class="bi bi-person-badge-fill mr-2"></i> <?= strtoupper(htmlspecialchars($user['role'])) ?>
            </div>

            <!-- ── Info Cards ── -->
            <div class="w-full space-y-3">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-teal-400">
                        <i class="bi bi-person-fill text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Username</p>
                        <p class="text-sm font-semibold text-white"><?= htmlspecialchars($user['username']) ?></p>
                    </div>
                </div>

                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-accent/20 flex items-center justify-center text-orange-400">
                        <i class="bi bi-shield-lock-fill text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Hak Akses</p>
                        <p class="text-sm font-semibold text-white"><?= ucfirst(htmlspecialchars($user['role'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- ── Ganti Password (collapsible) ── -->
            <div class="w-full mt-5">
                <button onclick="togglePwdForm()" id="togglePwdBtn"
                        class="w-full py-3 px-4 rounded-2xl bg-white/5 border border-white/10 text-sm font-semibold text-muted hover:text-white hover:bg-white/10 transition flex items-center justify-between">
                    <span class="flex items-center gap-2"><i class="bi bi-key-fill text-primary"></i> Ganti Password</span>
                    <i class="bi bi-chevron-down text-xs transition-transform" id="pwdChevron"></i>
                </button>

                <div id="pwdForm" class="hidden mt-3">
                    <form action="index.php?act=profil_change_password" method="POST"
                          class="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Password Saat Ini</label>
                            <div class="relative">
                                <i class="bi bi-lock-fill absolute left-3 top-1/2 -translate-y-1/2 text-muted"></i>
                                <input type="password" name="current_password" id="cur_pwd"
                                       class="glass-input w-full py-2.5 pl-9 pr-10" required placeholder="••••••••">
                                <button type="button" onclick="togglePwd('cur_pwd','cur_eye')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-white transition" tabindex="-1">
                                    <i class="bi bi-eye-slash" id="cur_eye"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Password Baru</label>
                            <div class="relative">
                                <i class="bi bi-lock-fill absolute left-3 top-1/2 -translate-y-1/2 text-muted"></i>
                                <input type="password" name="new_password" id="new_pwd"
                                       class="glass-input w-full py-2.5 pl-9 pr-10" required placeholder="Min. 6 karakter">
                                <button type="button" onclick="togglePwd('new_pwd','new_eye')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-white transition" tabindex="-1">
                                    <i class="bi bi-eye-slash" id="new_eye"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <i class="bi bi-lock-fill absolute left-3 top-1/2 -translate-y-1/2 text-muted"></i>
                                <input type="password" name="confirm_password" id="conf_pwd"
                                       class="glass-input w-full py-2.5 pl-9 pr-10" required placeholder="Ulangi password baru">
                                <button type="button" onclick="togglePwd('conf_pwd','conf_eye')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-white transition" tabindex="-1">
                                    <i class="bi bi-eye-slash" id="conf_eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit"
                                class="w-full btn-primary-glow py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2">
                            <i class="bi bi-check-circle-fill"></i> Simpan Password Baru
                        </button>
                    </form>
                </div>
            </div>

            <!-- ── Logout ── -->
            <div class="mt-5 w-full">
                <a href="index.php?act=logout"
                   class="w-full py-3.5 rounded-2xl bg-danger/10 text-danger border border-danger/20 flex items-center justify-center gap-3 font-bold hover:bg-danger hover:text-white hover:shadow-[0_8px_20px_rgba(239,68,68,0.3)] transition-all duration-300 group">
                    <i class="bi bi-box-arrow-right text-lg group-hover:-translate-x-1 transition-transform"></i> Keluar (Logout)
                </a>
            </div>

        </div><!-- /flex flex-col -->
    </div><!-- /glass-panel -->
</div>

<script>
// Preview foto sebelum upload & auto-submit
function previewAndSubmit(input) {
    if (!input.files || !input.files[0]) return;
    const file  = input.files[0];
    const maxMB = 3;
    if (file.size > maxMB * 1024 * 1024) {
        alert('Ukuran foto maksimal 3 MB.');
        input.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('avatarPreview');
        const initial = document.getElementById('avatarInitial');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        if (initial) initial.classList.add('hidden');
    };
    reader.readAsDataURL(file);
    // Auto submit after short delay (so preview shows)
    setTimeout(() => document.getElementById('photoForm').submit(), 300);
}

// Toggle password visibility
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    }
}

// Toggle ganti password section
function togglePwdForm() {
    const form    = document.getElementById('pwdForm');
    const chevron = document.getElementById('pwdChevron');
    const isHidden = form.classList.contains('hidden');
    form.classList.toggle('hidden');
    chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
}
</script>

<?php include 'views/layouts/footer.php'; ?>
