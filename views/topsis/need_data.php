<?php
$page_title = 'Data Tidak Lengkap';
include __DIR__ . '/../layouts/header.php';
$role = $_SESSION['user']['role'] ?? '';
?>
<div class="glass-panel p-10 max-w-2xl mx-auto text-center">
    <div class="w-20 h-20 bg-danger/10 text-danger rounded-full flex items-center justify-center text-4xl mx-auto mb-6" style="box-shadow: 0 0 20px var(--danger-glow)">
        <i class="bi bi-database-exclamation"></i>
    </div>
    <h2 class="text-2xl font-bold mb-4 text-white">Data Perhitungan Belum Lengkap</h2>
    <p class="text-muted mb-8">Sistem memerlukan minimal satu karyawan aktif dan satu kriteria dengan bobot yang valid untuk menjalankan algoritma TOPSIS.</p>
    
    <div class="space-y-4 text-left">
        <?php if (!empty($missingKaryawan)): ?>
        <div class="badge-glass badge-danger w-full p-5 flex flex-col gap-3">
            <div class="font-bold flex items-center gap-2"><i class="bi bi-people"></i> Belum ada data karyawan aktif</div>
            <div class="text-xs opacity-80">Tambahkan minimal satu karyawan untuk mulai memberikan penilaian.</div>
            <?php if ($role !== 'direktur'): ?>
            <div><a href="index.php?act=karyawan&sub=create" class="btn-primary-glow px-4 py-2 text-xs">Tambah Karyawan Sekarang</a></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($missingKriteria)): ?>
        <div class="badge-glass badge-danger w-full p-5 flex flex-col gap-3">
            <div class="font-bold flex items-center gap-2"><i class="bi bi-sliders"></i> Belum ada kriteria & bobot</div>
            <div class="text-xs opacity-80">Kriteria diperlukan sebagai parameter pembanding dalam perhitungan.</div>
            <?php if ($role === 'admin'): ?>
            <div><a href="index.php?act=kriteria&sub=create" class="btn-primary-glow px-4 py-2 text-xs">Tambah Kriteria Sekarang</a></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="mt-10 pt-6 border-t" style="border-color: var(--card-border)">
        <a href="index.php?act=dashboard" class="btn-glass px-6 py-2.5 text-muted hover:text-white"><i class="bi bi-arrow-left mr-2"></i> Kembali ke Dashboard</a>
    </div>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>