<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SPK TOPSIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-bg min-h-screen flex items-center justify-center p-4 overflow-hidden">

<div class="max-w-md w-full relative z-10" id="loginContainer" style="opacity:0; transform: translateY(30px) scale(0.96); filter: blur(6px);">
    <div class="text-center mb-8">
        <img src="assets/img/logo.png" alt="SGrS Logo" class="h-24 w-auto mx-auto object-contain drop-shadow-lg">
    </div>
    
    <div class="glass-panel p-10 shadow-2xl rounded-[2rem]" id="loginCard">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold tracking-tight" style="color: #ffffff; text-shadow: 0 0 20px rgba(61,189,168,0.3);">SPK TOPSIS</h1>
            <p class="text-sm mt-2 font-medium" style="color: #a8b2bc;">Reward & Punishment Management System</p>
        </div>
    
    <?php if (isset($error)): ?>
    <div class="badge-glass badge-danger w-full mb-6 p-3 flex items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" class="space-y-6">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2" style="color: #c0c8d2;">Username</label>
            <div class="relative">
                <i class="bi bi-person-fill absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                <input type="text" name="username" class="glass-input w-full py-3 pl-10 pr-4" placeholder="admin / hrd / direktur" autofocus>
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2" style="color: #c0c8d2;">Password</label>
            <div class="relative">
                <i class="bi bi-lock-fill absolute left-3 top-1/2 transform -translate-y-1/2 text-muted"></i>
                <input type="password" name="password" id="passwordInput" class="glass-input w-full py-3 pl-10 pr-11" placeholder="••••••••">
                <button type="button" id="togglePassword" onclick="togglePasswordVisibility()" class="absolute right-3 top-1/2 transform -translate-y-1/2 transition-colors duration-200 focus:outline-none" style="color: #a8b2bc;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#a8b2bc'" tabindex="-1" title="Tampilkan / Sembunyikan Password">
                    <i class="bi bi-eye-slash" id="toggleIcon" style="font-size:1.1rem;"></i>
                </button>
            </div>
        </div>
        <button type="submit" class="w-full btn-primary-glow py-3 font-bold text-lg mt-4 flex items-center justify-center gap-2">
            <i class="bi bi-box-arrow-in-right"></i> Sign In
        </button>
    </form>
    
    </div>
    <div class="text-center mt-6 mb-6">
        <p class="text-[10px] uppercase tracking-[0.2em] font-bold" style="color: #8b9baa;">Secure Access • PT. Swadharma Griyasatya</p>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    }

    // Animate login card entry
    const container = document.getElementById('loginContainer');
    const card = document.getElementById('loginCard');
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            container.style.transition = 'all 0.8s cubic-bezier(0.22, 1, 0.36, 1)';
            container.style.opacity = '1';
            container.style.transform = 'translateY(0) scale(1)';
            container.style.filter = 'blur(0)';
        });
    });

    // Stagger animate form elements
    document.querySelectorAll('.glass-input, .btn-primary-glow, label').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(12px)';
        el.style.transition = `all 0.5s cubic-bezier(0.22, 1, 0.36, 1) ${0.4 + i * 0.08}s`;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            });
        });
    });

    // Mouse glow effect
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        card.style.background = `radial-gradient(circle at ${x}px ${y}px, rgba(34,87,79,0.08), transparent 60%), var(--card-bg)`;
    });
    card.addEventListener('mouseleave', () => {
        card.style.background = '';
    });
</script>
</body>
</html>
