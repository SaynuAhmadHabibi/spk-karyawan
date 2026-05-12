        </main><!-- end page-content -->
    </div><!-- end main-area -->
</div><!-- end app-shell -->

<!-- ════════════════════════════════════════════════════════════
     BOTTOM NAVIGATION (mobile only)
     ════════════════════════════════════════════════════════════ -->
<?php
$currentAct = $_GET['act'] ?? 'dashboard';
$user  = $_SESSION['user'] ?? null;
$role  = $user['role'] ?? '';

$allMenus = [
    ['id'=>'dashboard', 'label'=>'Home',        'icon'=>'bi-grid-1x2-fill',        'href'=>'index.php?act=dashboard',          'match'=>['dashboard'],                          'roles'=>['admin','hrd','direktur','user']],
    ['id'=>'karyawan',  'label'=>'Karyawan',    'icon'=>'bi-people-fill',           'href'=>'index.php?act=karyawan',           'match'=>['karyawan'],                           'roles'=>['admin','hrd']],
    ['id'=>'penilaian', 'label'=>'Penilaian',   'icon'=>'bi-pencil-square',         'href'=>'index.php?act=penilaian_input',    'match'=>['penilaian_input','penilaian_history'],'roles'=>['admin','hrd']],
    ['id'=>'topsis',    'label'=>'Hitung',      'icon'=>'bi-lightning-charge-fill', 'href'=>'index.php?act=hitung_reward_form', 'match'=>['hitung_reward_form','hitung_punishment_form','hitung_reward','hitung_punishment'], 'roles'=>['admin','hrd','direktur']],
    ['id'=>'hasil',     'label'=>'Hasil',       'icon'=>'bi-bar-chart-fill',        'href'=>'index.php?act=hasil_reward',       'match'=>['hasil_reward','hasil_punishment','detail_perhitungan'], 'roles'=>['admin','hrd','direktur']],
    ['id'=>'history',   'label'=>'History',     'icon'=>'bi-clock-history',         'href'=>'index.php?act=penilaian_history',  'match'=>['penilaian_history'],                  'roles'=>['direktur']],
    ['id'=>'kriteria',  'label'=>'Kriteria',    'icon'=>'bi-sliders2',              'href'=>'index.php?act=kriteria',           'match'=>['kriteria'],                           'roles'=>['admin']],
    ['id'=>'user',      'label'=>'Pengguna',    'icon'=>'bi-person-badge-fill',     'href'=>'index.php?act=user',               'match'=>['user'],                               'roles'=>['admin']],
];

// Filter by role, max 5 for bottom nav
$filtered = array_values(array_filter($allMenus, fn($m) => in_array($role, $m['roles'])));
$bottomMenus = array_slice($filtered, 0, 5);
?>
<nav class="bottom-nav md:hidden">
    <div class="bottom-nav-inner">
        <?php foreach ($bottomMenus as $item):
            $active = in_array($currentAct, $item['match']);
        ?>
        <a href="<?= $item['href'] ?>" class="bottom-nav-item <?= $active ? 'active' : '' ?>">
            <i class="bi <?= $item['icon'] ?> bottom-nav-icon"></i>
            <span class="bottom-nav-label"><?= $item['label'] ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</nav>

<!-- Mobile Sidebar Overlay -->
<div id="mobileSidebarOverlay" class="mobile-overlay hidden" onclick="closeMobileSidebar()"></div>

<script>
// ── Sidebar collapse (desktop) ─────────────────────────────────────────────
const sidebar   = document.getElementById('sidebar');
const mainArea  = document.querySelector('.main-area');
const toggleBtn = document.getElementById('sidebarToggle');
let collapsed   = localStorage.getItem('sidebarCollapsed') === '1';

function applySidebar() {
    if (collapsed) {
        sidebar.classList.add('collapsed');
        mainArea.classList.add('sidebar-collapsed');
    } else {
        sidebar.classList.remove('collapsed');
        mainArea.classList.remove('sidebar-collapsed');
    }
}
applySidebar();

if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
        collapsed = !collapsed;
        localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
        applySidebar();
    });
}

// ── Mobile sidebar toggle ──────────────────────────────────────────────────
const mobileBtn     = document.getElementById('mobileMenuBtn');
const mobileOverlay = document.getElementById('mobileSidebarOverlay');

function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    mobileOverlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    mobileOverlay.classList.add('hidden');
    document.body.style.overflow = '';
}
if (mobileBtn) mobileBtn.addEventListener('click', openMobileSidebar);

// ── User dropdown ──────────────────────────────────────────────────────────
const dropToggle  = document.getElementById('userDropdownToggle');
const dropdown    = document.getElementById('userDropdown');
const chevron     = document.getElementById('userChevron');

if (dropToggle) {
    dropToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        const hidden = dropdown.classList.toggle('hidden');
        chevron.style.transform = hidden ? '' : 'rotate(180deg)';
    });
    document.addEventListener('click', () => {
        dropdown.classList.add('hidden');
        chevron.style.transform = '';
    });
}

// ── Flash auto-dismiss ─────────────────────────────────────────────────────
setTimeout(() => {
    document.querySelectorAll('.flash-msg').forEach(el => {
        el.style.transition = 'opacity .5s, transform .5s';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        setTimeout(() => el.remove(), 500);
    });
}, 5000);

// ── Ambient orbs ──────────────────────────────────────────────────────────
(function() {
    const orbs = document.createElement('div');
    orbs.style.cssText = 'position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;';
    orbs.innerHTML = `
        <div class="ambient-orb ambient-orb-1"></div>
        <div class="ambient-orb ambient-orb-2"></div>
        <div class="ambient-orb ambient-orb-3"></div>`;
    document.body.prepend(orbs);

    // Entry animation
    const targets = document.querySelectorAll('.glass-card,.glass-panel,.neuro-card-primary,form,canvas');
    targets.forEach((el, i) => {
        el.style.cssText += `opacity:0;transform:translateY(20px) scale(0.98);filter:blur(4px);transition:all .55s cubic-bezier(.22,1,.36,1) ${i*.06}s`;
        requestAnimationFrame(() => requestAnimationFrame(() => {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0) scale(1)';
            el.style.filter = 'blur(0)';
        }));
    });

    // Table row stagger
    document.querySelectorAll('.glass-table tbody tr').forEach((row, i) => {
        row.style.cssText += `opacity:0;transform:translateX(-10px);transition:all .4s cubic-bezier(.22,1,.36,1) ${.2+i*.04}s`;
        requestAnimationFrame(() => requestAnimationFrame(() => {
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }));
    });

    // Mouse glow on glass cards
    document.querySelectorAll('.glass-card,.glass-panel,.neuro-card-primary').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const r = card.getBoundingClientRect();
            card.style.background = `radial-gradient(circle at ${e.clientX-r.left}px ${e.clientY-r.top}px,rgba(34,87,79,.08),transparent 60%),var(--card-bg)`;
        });
        card.addEventListener('mouseleave', () => { card.style.background = ''; });
    });
})();
</script>
</body>
</html>