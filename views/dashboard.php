<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/layouts/header.php';
$username = $_SESSION['user']['username'] ?? 'User';
?>
<div class="mb-6 flex items-center justify-between">
    <div>
        <p id="realtime-clock" class="text-[10px] text-muted uppercase tracking-[0.2em] font-bold mb-1"></p>
        <h2 class="text-3xl font-extrabold text-white tracking-tight">Welcome back,<br><?= htmlspecialchars(ucfirst($username)) ?>!</h2>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Main Highlight Card (like AI Insights) -->
    <div onclick="window.location.href='index.php?act=penilaian_history'" class="lg:col-span-1 neuro-card-primary p-6 flex flex-col justify-between shadow-2xl cursor-pointer hover:shadow-[0_8px_30px_rgba(34,87,79,0.5)] transition-shadow">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-blue-500/30 bg-blue-500/10 text-xs text-blue-200 mb-6">
                <i class="bi bi-stars"></i> Rekapitulasi Data
            </div>
            <h3 class="text-2xl font-bold text-white mb-2 leading-tight">Sistem Siap<br>Digunakan</h3>
            <p class="text-sm text-blue-100/70 mt-4">
                Total <strong><?= $totalKaryawan ?> karyawan</strong> dan <strong><?= $totalKriteria ?> kriteria</strong> telah terdaftar bulan ini.
            </p>
        </div>
        <div class="mt-8 flex items-center justify-between">
            <div class="flex gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-white/30"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-white/30"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-white/30"></span>
            </div>
            <a href="index.php?act=hitung_reward" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition backdrop-blur-md">
                <i class="bi bi-arrow-up-right text-white"></i>
            </a>
        </div>
    </div>

    <!-- Stats grid -->
    <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div onclick="window.location.href='index.php?act=karyawan'" class="glass-card p-6 flex flex-col justify-between relative overflow-hidden cursor-pointer hover:shadow-[0_8px_30px_rgba(34,87,79,0.5)]">
            <div class="flex justify-between items-start mb-6">
                <h3 class="text-sm font-semibold text-white">Total Karyawan</h3>
                <a href="index.php?act=karyawan" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-muted hover:text-white transition">
                    <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2"><?= $totalKaryawan ?></div>
                <div class="flex items-center gap-2 text-xs font-medium">
                    <span class="text-success bg-success/10 px-2 py-1 rounded flex items-center gap-1"><i class="bi bi-arrow-up-short"></i> Aktif</span>
                    <span class="text-muted">Karyawan terdaftar</span>
                </div>
            </div>
            <!-- Decorative chart line in background -->
            <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-primary/10 to-transparent pointer-events-none" style="clip-path: polygon(0 100%, 0 50%, 20% 40%, 40% 60%, 60% 30%, 80% 50%, 100% 20%, 100% 100%);"></div>
        </div>

        <div onclick="window.location.href='index.php?act=kriteria'" class="glass-card p-6 flex flex-col justify-between relative overflow-hidden cursor-pointer hover:shadow-[0_8px_30px_rgba(34,87,79,0.5)]">
            <div class="flex justify-between items-start mb-6">
                <h3 class="text-sm font-semibold text-white">Total Kriteria</h3>
                <a href="index.php?act=kriteria" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-muted hover:text-white transition">
                    <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
            <div>
                <div class="text-4xl font-bold text-white mb-2"><?= $totalKriteria ?></div>
                <div class="flex items-center gap-2 text-xs font-medium">
                    <span class="text-primary bg-primary/10 px-2 py-1 rounded flex items-center gap-1"><i class="bi bi-sliders"></i> Pembobot</span>
                    <span class="text-muted">Digunakan</span>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 p-6 opacity-30 pointer-events-none">
                <div style="width: 8rem; height: 8rem; border-radius: 50%; background: conic-gradient(
                    #22c55e 0deg 72deg,
                    #16a34a 72deg 144deg,
                    #4ade80 144deg 216deg,
                    #eab308 216deg 288deg,
                    #ca8a04 288deg 360deg
                ); box-shadow: inset 0 0 20px rgba(0,0,0,0.5);"></div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($bestKaryawan) || !empty($worstKaryawan)): ?>
<!-- Hasil Seleksi TOPSIS Highlights -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Card Reward -->
    <?php if ($bestKaryawan): ?>
    <div onclick="window.location.href='index.php?act=hasil_reward'" class="glass-card p-6 flex items-center justify-between group transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_30px_rgba(34,197,94,0.2)] border-l-4 border-success relative overflow-hidden cursor-pointer">
        <div class="absolute inset-0 bg-gradient-to-r from-success/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        <div class="relative z-10 flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-success/20 flex items-center justify-center text-success shrink-0">
                    <i class="bi bi-trophy-fill text-sm"></i>
                </div>
                <h3 class="text-xs font-bold text-success uppercase tracking-wider">Karyawan Terbaik</h3>
            </div>
            <p class="text-2xl font-bold text-white mb-2 truncate"><?= htmlspecialchars($bestKaryawan['nama']) ?></p>
            <div class="flex flex-wrap items-center gap-2 text-xs font-medium">
                <span class="font-mono bg-black/20 px-2 py-1 rounded text-slate-300 border border-white/5">Skor: <?= number_format($bestKaryawan['nilai'], 4) ?></span>
                <span class="text-success bg-success/10 px-2 py-1 rounded border border-success/20"><i class="bi bi-check-circle-fill mr-1"></i>REWARD</span>
            </div>
        </div>
        <div class="relative z-10 w-16 h-16 rounded-full border border-success/30 flex items-center justify-center text-success ml-4 shrink-0 shadow-[0_0_20px_rgba(34,197,94,0.2)] bg-success/10 group-hover:bg-success/20 transition-colors">
            <i class="bi bi-star-fill text-2xl"></i>
        </div>
    </div>
    <?php endif; ?>

    <!-- Card Punishment -->
    <?php if ($worstKaryawan): ?>
    <div onclick="window.location.href='index.php?act=hasil_punishment'" class="glass-card p-6 flex items-center justify-between group transition-all duration-300 hover:scale-[1.02] hover:shadow-[0_8px_30px_rgba(239,68,68,0.2)] border-l-4 border-danger relative overflow-hidden cursor-pointer">
        <div class="absolute inset-0 bg-gradient-to-r from-danger/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
        <div class="relative z-10 flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-danger/20 flex items-center justify-center text-danger shrink-0">
                    <i class="bi bi-exclamation-triangle-fill text-sm"></i>
                </div>
                <h3 class="text-xs font-bold text-danger uppercase tracking-wider">Karyawan Terendah</h3>
            </div>
            <p class="text-2xl font-bold text-white mb-2 truncate"><?= htmlspecialchars($worstKaryawan['nama']) ?></p>
            <div class="flex flex-wrap items-center gap-2 text-xs font-medium">
                <span class="font-mono bg-black/20 px-2 py-1 rounded text-slate-300 border border-white/5">Skor: <?= number_format($worstKaryawan['nilai'], 4) ?></span>
                <span class="text-danger bg-danger/10 px-2 py-1 rounded border border-danger/20"><i class="bi bi-x-circle-fill mr-1"></i>PUNISHMENT</span>
            </div>
        </div>
        <div class="relative z-10 w-16 h-16 rounded-full border border-danger/30 flex items-center justify-center text-danger ml-4 shrink-0 shadow-[0_0_20px_rgba(239,68,68,0.2)] bg-danger/10 group-hover:bg-danger/20 transition-colors">
            <i class="bi bi-arrow-down-circle-fill text-2xl"></i>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 glass-panel p-6 flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-semibold text-white">Rata-rata Nilai per Kriteria</h3>
            <span class="text-xs text-muted"><i class="bi bi-diagram-3-fill mr-1"></i> Analisis Data</span>
        </div>
        <div class="flex-1 w-full flex items-center justify-center min-h-[200px]">
            <?php if (!empty($chartData)): ?>
            <canvas id="chartKriteria"></canvas>
            <?php else: ?>
            <div class="text-center text-muted">
                <i class="bi bi-bar-chart text-4xl opacity-50 mb-2"></i>
                <p class="text-sm">Belum ada data penilaian.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ranking Terbaik -->
    <div class="lg:col-span-1 glass-panel flex flex-col max-h-[350px]">
        <div class="px-6 py-5 border-b flex justify-between items-center" style="border-color: var(--card-border)">
            <h3 class="text-sm font-semibold text-white">Ranking Terbaik</h3>
            <!-- 3-dot dropdown -->
            <div class="relative" id="rankingMenuWrapper">
                <button id="rankingMenuBtn" onclick="toggleRankingMenu()" class="w-6 h-6 rounded bg-white/5 flex items-center justify-center text-muted hover:text-white hover:bg-white/10 transition">
                    <i class="bi bi-three-dots"></i>
                </button>
                <div id="rankingDropdown" class="hidden absolute right-0 top-8 w-52 glass-card border rounded-xl shadow-xl z-50 py-1 overflow-hidden" style="border-color:var(--card-border);min-width:180px">
                    <a href="index.php?act=hasil_reward" class="flex items-center gap-3 px-4 py-2.5 text-sm text-muted hover:text-white hover:bg-white/5 transition">
                        <i class="bi bi-trophy-fill text-yellow-400"></i> Lihat Hasil Reward
                    </a>
                    <a href="index.php?act=hasil_punishment" class="flex items-center gap-3 px-4 py-2.5 text-sm text-muted hover:text-white hover:bg-white/5 transition">
                        <i class="bi bi-exclamation-triangle-fill text-danger"></i> Lihat Hasil Punishment
                    </a>
                    <div class="border-t my-1" style="border-color:var(--card-border)"></div>
                    <a href="index.php?act=detail_perhitungan&tipe=reward" class="flex items-center gap-3 px-4 py-2.5 text-sm text-muted hover:text-white hover:bg-white/5 transition">
                        <i class="bi bi-calculator text-primary"></i> Detail Perhitungan
                    </a>
                    <a href="index.php?act=penilaian_history" class="flex items-center gap-3 px-4 py-2.5 text-sm text-muted hover:text-white hover:bg-white/5 transition">
                        <i class="bi bi-clock-history text-blue-400"></i> History Penilaian
                    </a>
                </div>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto px-2 py-2">
            <?php if (!empty($top5)): ?>
                <?php $icons = ['bi-trophy-fill text-yellow-500', 'bi-award-fill text-slate-300', 'bi-award-fill text-amber-700', 'bi-person-fill text-muted', 'bi-person-fill text-muted']; foreach ($top5 as $i => $r): ?>
                <a href="index.php?act=hasil_reward" class="flex items-center gap-4 p-3 rounded-xl hover:bg-white/5 transition mb-1 cursor-pointer no-underline group">
                    <div class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-white/10 transition">
                        <i class="bi <?= $icons[$i] ?> text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate group-hover:text-success transition"><?= htmlspecialchars($r['nama']) ?></p>
                        <p class="text-xs text-muted truncate"><?= htmlspecialchars($r['jabatan'] ?? 'Karyawan') ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-mono font-bold text-success">+<?= number_format($r['nilai'], 4) ?></p>
                        <p class="text-[10px] text-muted">Preferensi</p>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-8 text-center text-muted">
                    <i class="bi bi-bar-chart text-3xl opacity-30 block mb-2"></i>
                    <p class="text-sm">Hitung TOPSIS terlebih dahulu.</p>
                    <a href="index.php?act=hitung_reward_form" class="inline-block mt-3 text-xs text-primary hover:underline">Hitung Sekarang →</a>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($top5)): ?>
        <div class="p-4 mt-auto border-t" style="border-color: var(--card-border)">
            <a href="index.php?act=hasil_reward" class="block w-full py-2 text-center text-xs font-bold text-muted hover:text-white bg-white/5 hover:bg-white/10 rounded-lg transition">
                <i class="bi bi-arrow-right mr-1"></i> Lihat Semua Hasil
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#8b949e';
Chart.defaults.font.family = "'Inter', sans-serif";

<?php if (!empty($chartData)): ?>
const ctxKriteria = document.getElementById('chartKriteria').getContext('2d');
new Chart(ctxKriteria, { 
    type: 'bar', 
    data: { 
        labels: <?= json_encode(array_column($chartData, 'kriteria')) ?>, 
        datasets: [{ 
            label: 'Rata-rata Nilai', 
            data: <?= json_encode(array_map(fn($c) => round($c['rata'], 2), $chartData)) ?>, 
            backgroundColor: '#3b82f6', 
            borderRadius: 4
        }] 
    }, 
    options: { 
        responsive: true, 
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { 
            y: { 
                beginAtZero: true, 
                max: 100,
                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                border: { display: false }
            },
            x: {
                grid: { display: false },
                border: { display: false }
            }
        } 
    } 
});
<?php endif; ?>

// Real-time Clock Script
function updateRealtimeClock() {
    const now = new Date();
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const dayName = days[now.getDay()];
    const day = now.getDate();
    const month = months[now.getMonth()];
    const year = now.getFullYear();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    
    const clockEl = document.getElementById('realtime-clock');
    if(clockEl) {
        clockEl.textContent = `${dayName}, ${day} ${month} ${year} ${hours}:${minutes}:${seconds}`;
    }
}
setInterval(updateRealtimeClock, 1000);
updateRealtimeClock();

// Ranking menu dropdown
function toggleRankingMenu() {
    const dd = document.getElementById('rankingDropdown');
    dd.classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('rankingMenuWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('rankingDropdown')?.classList.add('hidden');
    }
});

</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>