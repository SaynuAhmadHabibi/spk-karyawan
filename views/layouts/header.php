<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($page_title ?? 'SPK TOPSIS') ?> — SPK TOPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: { 900: '#081a19', 800: '#0d2524', 700: '#112c2b', 600: '#163634' },
                        accent: { DEFAULT: '#ea580c', hover: '#c2410c' },
                        primary: { DEFAULT: '#22574f', hover: '#1b453e' },
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/nav.css">
</head>
<body class="app-bg font-sans antialiased text-white min-h-screen">
<?php
$currentAct = $_GET['act'] ?? 'dashboard';
$user  = $_SESSION['user'] ?? null;
$role  = $user['role'] ?? '';
$uname = $user['username'] ?? 'User';
$initial = strtoupper(substr($uname, 0, 1));

// Role label & color
$roleInfo = match($role) {
    'admin'    => ['label' => 'Admin',    'color' => '#ea580c', 'bg' => 'rgba(234,88,12,0.15)'],
    'hrd'      => ['label' => 'HRD',      'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,0.15)'],
    'direktur' => ['label' => 'Direktur', 'color' => '#a855f7', 'bg' => 'rgba(168,85,247,0.15)'],
    default    => ['label' => 'User',     'color' => '#8b949e', 'bg' => 'rgba(139,148,158,0.15)'],
};

// ── Menu definition per role ──────────────────────────────────────────────
$allMenus = [
    ['id'=>'dashboard',  'label'=>'Dashboard',       'icon'=>'bi-grid-1x2-fill',       'href'=>'index.php?act=dashboard',          'match'=>['dashboard'],                            'roles'=>['admin','hrd','direktur','user']],
    ['id'=>'karyawan',   'label'=>'Data Karyawan',   'icon'=>'bi-people-fill',          'href'=>'index.php?act=karyawan',           'match'=>['karyawan'],                             'roles'=>['admin','hrd']],
    ['id'=>'penilaian',  'label'=>'Penilaian',       'icon'=>'bi-pencil-square',        'href'=>'index.php?act=penilaian_input',    'match'=>['penilaian_input','penilaian_history'],   'roles'=>['admin','hrd']],
    ['id'=>'kriteria',   'label'=>'Kriteria',        'icon'=>'bi-sliders2',             'href'=>'index.php?act=kriteria',           'match'=>['kriteria'],                             'roles'=>['admin']],
    ['id'=>'topsis',     'label'=>'Perhitungan',     'icon'=>'bi-lightning-charge-fill','href'=>'index.php?act=hitung_reward_form', 'match'=>['hitung_reward_form','hitung_punishment_form','hitung_reward','hitung_punishment'], 'roles'=>['admin','hrd','direktur']],
    ['id'=>'hasil',      'label'=>'Reward & Punishment','icon'=>'bi-bar-chart-fill',   'href'=>'index.php?act=hasil_reward',       'match'=>['hasil_reward','hasil_punishment','detail_perhitungan'], 'roles'=>['admin','hrd','direktur']],
    ['id'=>'history',    'label'=>'History Penilaian','icon'=>'bi-clock-history',      'href'=>'index.php?act=penilaian_history',  'match'=>['penilaian_history'],                    'roles'=>['direktur']],
    ['id'=>'user',       'label'=>'Pengguna',        'icon'=>'bi-person-badge-fill',    'href'=>'index.php?act=user',               'match'=>['user'],                                 'roles'=>['admin']],
];

$menus = array_filter($allMenus, fn($m) => in_array($role, $m['roles']));

function isActive(array $item, string $act): bool {
    return in_array($act, $item['match']);
}
?>

<!-- ════════════════════════════════════════════════════════════
     NAVBAR LAYOUT
     ════════════════════════════════════════════════════════════ -->

<!-- ── TOP NAVBAR ──────────────────────────────────────────── -->
<header class="navbar-main">
    <div class="navbar-inner">
        <!-- Logo -->
        <a href="index.php?act=dashboard" class="navbar-brand">
            <img src="assets/img/logo.png" alt="Logo" class="navbar-brand-img">
            <div class="navbar-brand-text">
                <span class="navbar-brand-title">SPK TOPSIS</span>
                <span class="navbar-brand-sub">PT. Swadarma Griyasatya</span>
            </div>
        </a>

        <!-- Desktop Menu -->
        <nav class="navbar-menu" id="navbarMenu">
            <?php foreach ($menus as $m):
                $active = isActive($m, $currentAct);
            ?>
            <a href="<?= $m['href'] ?>"
               id="menu-<?= $m['id'] ?>"
               class="navbar-menu-item <?= $active ? 'active' : '' ?>"
               title="<?= htmlspecialchars($m['label']) ?>">
                <i class="bi <?= $m['icon'] ?> navbar-menu-icon"></i>
                <span class="navbar-menu-label"><?= htmlspecialchars($m['label']) ?></span>
            </a>
            <?php endforeach; ?>
        </nav>

        <!-- Right side: flash + user -->
        <div class="navbar-right">
            <!-- Flash messages -->
            <?php if (isset($_SESSION['success'])): ?>
            <div class="flash-msg flash-success">
                <i class="bi bi-check-circle-fill"></i>
                <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <?php unset($_SESSION['success']); endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
            <div class="flash-msg flash-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            </div>
            <?php unset($_SESSION['error']); endif; ?>

            <!-- User avatar & dropdown -->
            <div class="topbar-user-wrap" id="userDropdownToggle">
                <?php $userPhoto = $_SESSION['user']['photo'] ?? null; ?>
                <div class="topbar-avatar" style="background:linear-gradient(135deg,<?= $roleInfo['color'] ?>,#22574f);overflow:hidden;padding:0">
                    <?php if ($userPhoto): ?>
                    <img src="assets/uploads/photos/<?= htmlspecialchars($userPhoto) ?>"
                         alt="Foto" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                    <?php else: ?>
                    <span style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:inherit;font-weight:inherit"><?= $initial ?></span>
                    <?php endif; ?>
                </div>
                <div class="topbar-user-info">
                    <span class="topbar-username"><?= htmlspecialchars(ucfirst($uname)) ?></span>
                    <span class="topbar-role" style="color:<?= $roleInfo['color'] ?>"><?= $roleInfo['label'] ?></span>
                </div>
                <i class="bi bi-chevron-down text-xs text-slate-500 transition-transform duration-200" id="userChevron"></i>
            </div>
            <!-- Dropdown -->
            <div id="userDropdown" class="user-dropdown hidden">
                <a href="index.php?act=profil" class="user-dropdown-item">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </a>
                <div class="user-dropdown-divider"></div>
                <a href="index.php?act=logout" class="user-dropdown-item text-red-400 hover:bg-red-500/10">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>

            <!-- Mobile hamburger -->
            <button id="mobileMenuBtn" class="navbar-hamburger" title="Menu">
                <i class="bi bi-list text-xl"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <nav class="navbar-mobile-menu hidden" id="mobileMenu">
        <?php foreach ($menus as $m):
            $active = isActive($m, $currentAct);
        ?>
        <a href="<?= $m['href'] ?>"
           class="navbar-mobile-item <?= $active ? 'active' : '' ?>">
            <i class="bi <?= $m['icon'] ?> navbar-mobile-icon"></i>
            <span><?= htmlspecialchars($m['label']) ?></span>
        </a>
        <?php endforeach; ?>
        <div class="navbar-mobile-divider"></div>
        <a href="index.php?act=logout" class="navbar-mobile-item navbar-mobile-logout">
            <i class="bi bi-box-arrow-right navbar-mobile-icon"></i>
            <span>Keluar</span>
        </a>
    </nav>
</header>

<!-- ── MAIN CONTENT AREA ──────────────────────────────────── -->
<div class="navbar-layout">
    <!-- Breadcrumb -->
    <div class="navbar-breadcrumb-bar">
        <p class="topbar-breadcrumb"><i class="bi bi-house-fill mr-1"></i>SPK TOPSIS / <?= htmlspecialchars($page_title ?? 'Dashboard') ?></p>
    </div>

    <!-- PAGE CONTENT -->
    <main class="page-content-navbar">