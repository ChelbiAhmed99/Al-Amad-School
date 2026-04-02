<?php
/**
 * includes/dash-header.php
 * Reusable top-bar for all dashboard sessions.
 *
 * Expected variables from parent page:
 *   $dash_title  – page title (e.g. "Admin Dashboard")
 *   $dash_sub    – subtitle / date line
 *   $user_init   – 1-2 character initials (e.g. "AD", "T", "P")
 *   $avatar_grad – CSS gradient classes or inline style (optional)
 *   $current_role – 'admin' | 'teacher' | 'parent'
 */
$dash_title  = $dash_title  ?? 'Dashboard';
$dash_sub    = $dash_sub    ?? date('l, F j, Y');
$user_init   = $user_init   ?? strtoupper(substr($_SESSION['email'] ?? 'U', 0, 2));
?>
<header class="dashboard-header" id="dash-header">
    <div class="dh-left">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="dh-title-block">
            <h1><?= htmlspecialchars($dash_title) ?></h1>
            <p><?= $dash_sub ?></p>
        </div>
    </div>

    <div class="dh-right">
        <!-- Search bar -->
        <div class="dh-search" id="dh-search-wrap">
            <button class="dh-search-toggle" id="dh-search-toggle" title="Search">
                <i class="fas fa-search"></i>
            </button>
            <div class="dh-search-box" id="dh-search-box">
                <i class="fas fa-search dh-search-icon"></i>
                <input type="search" id="dh-search-input" placeholder="Search students, teachers…" autocomplete="off">
                <button class="dh-search-clear" id="dh-search-clear"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <!-- Notification Bell -->
        <?php include __DIR__ . '/notif-bell.php'; ?>

        <!-- Theme (light only - no dark toggle) -->

        <!-- User Menu -->
        <div class="dh-user-menu" id="dh-user-menu">
            <div class="dh-avatar" id="dh-avatar-btn" title="Account">
                <?= htmlspecialchars($user_init) ?>
            </div>
            <div class="dh-dropdown" id="dh-dropdown">
                <div class="dh-dd-head">
                    <span class="dh-dd-role"><?= htmlspecialchars($_SESSION['name'] ?? strtoupper($current_role)) ?></span>
                    <span class="dh-dd-email"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
                </div>
                <a href="messages.php" class="dh-dd-item"><i class="fas fa-comment-dots"></i> Messages</a>
                <a href="../auth/logout.php" class="dh-dd-item dh-dd-logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
            </div>
        </div>
    </div>
</header>

<!-- Premium Dashboard Overrides -->
<link rel="stylesheet" href="../assets/css/dashboard-premium.css">


<style>
/* ─── Dashboard Header ─── */
.dashboard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 2.5rem;
    height: 80px;
    background: var(--ps-white);
    border-bottom: 3px solid var(--ps-orange);
    box-shadow: 0 4px 20px rgba(255, 123, 84, .12);
    position: sticky;
    top: 0;
    z-index: 1000;
    gap: 1.5rem;
    flex-shrink: 0;
}
.dh-left {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    min-width: 0;
}
.dh-title-block h1 {
    font-size: 1.35rem;
    font-weight: 900;
    line-height: 1.1;
    color: var(--ps-dark);
}
.dh-title-block p {
    font-size: .8rem;
    font-weight: 600;
    color: var(--ps-mid);
    margin-top: 2px;
}
.dh-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Sidebar toggle */
.sidebar-toggle {
    width: 44px; height: 44px;
    background: var(--ps-light);
    border: 1.5px solid rgba(255, 123, 84, 0.2);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    color: var(--ps-orange);
    font-size: 1.1rem;
    transition: all .2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.sidebar-toggle:hover { background: var(--ps-orange); color: white; transform: scale(1.05); }

/* Generic icon button */
.dh-icon-btn {
    width: 42px; height: 42px;
    background: var(--ps-light);
    border: 1.5px solid rgba(255, 123, 84, 0.15);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    color: var(--ps-mid);
    font-size: 1.1rem;
    transition: all .2s;
}
.dh-icon-btn:hover { background: var(--ps-orange); color: white; border-color: transparent; }

/* Theme toggle special */
#theme-toggle i { transition: transform 0.4s; }
#theme-toggle:hover i { transform: rotate(180deg); }

/* User avatar */
.dh-avatar {
    width: 44px; height: 44px; border-radius: 14px;
    background: linear-gradient(135deg, var(--ps-orange), #ff9f7f);
    color: white; font-weight: 900; font-size: .9rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(255, 123, 84, 0.25);
    transition: all .2s;
}
.dh-avatar:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(255, 123, 84, 0.35); }

.dh-dropdown {
    position: absolute; right: 0; top: calc(100% + 15px);
    width: 240px;
    background: var(--ps-white);
    border: 1.5px solid rgba(255, 123, 84, 0.15);
    border-radius: 20px;
    box-shadow: 0 16px 48px rgba(255, 123, 84, 0.15);
    padding: 0.5rem;
    visibility: hidden; opacity: 0;
    transform: translateY(-10px) scale(.95);
    transition: all .25s cubic-bezier(.175,.885,.32,1.275);
    z-index: 1001;
}
.dh-dropdown.open { visibility: visible; opacity: 1; transform: translateY(0) scale(1); }
.dh-dd-head { padding: 1rem; border-bottom: 1.5px solid rgba(255,123,84,0.08); margin-bottom: 0.5rem; }
.dh-dd-role { color: var(--ps-orange); font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
.dh-dd-email { color: var(--ps-mid); font-size: 0.85rem; font-weight: 600; margin-top: 2px; }
.dh-dd-item { 
    display: flex; align-items: center; gap: 0.75rem; 
    padding: 0.85rem 1rem; border-radius: 12px;
    color: var(--ps-dark); text-decoration: none; font-size: 0.92rem; font-weight: 700;
    transition: all 0.2s;
}
.dh-dd-item:hover { background: var(--ps-light); color: var(--ps-orange); transform: translateX(5px); }
.dh-dd-item i { font-size: 1.1rem; width: 22px; text-align: center; }
.dh-dd-logout { color: var(--ps-red) !important; }
.dh-dd-logout:hover { background: rgba(239,71,111,0.08) !important; color: var(--ps-red) !important; }

/* Search tweaks */
.dh-search-box {
    background: var(--ps-white) !important;
    border: 2px solid var(--ps-orange) !important;
    border-radius: 16px !important;
    box-shadow: 0 10px 30px rgba(255, 123, 84, 0.2) !important;
}
.dh-search-box input { font-weight: 600; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .dashboard-header { padding: 0 1rem; height: 62px; }
    .dh-title-block h1 { font-size: .95rem; max-width: 160px; }
    .dh-title-block p  { display: none; }
    .dh-search-box { width: 200px; right: -60px; }
}
@media (max-width: 480px) {
    .dh-title-block { display: none; }
    .dh-search-box { width: 180px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Search toggle
    const searchToggle = document.getElementById('dh-search-toggle');
    const searchBox    = document.getElementById('dh-search-box');
    const searchClear  = document.getElementById('dh-search-clear');
    const searchInput  = document.getElementById('dh-search-input');
    if (searchToggle) {
        searchToggle.addEventListener('click', () => {
            searchBox.classList.toggle('open');
            if (searchBox.classList.contains('open')) searchInput.focus();
        });
        searchClear.addEventListener('click', () => {
            searchBox.classList.remove('open');
            searchInput.value = '';
        });
    }
    // User dropdown
    const avatarBtn  = document.getElementById('dh-avatar-btn');
    const dropdown   = document.getElementById('dh-dropdown');
    if (avatarBtn) {
        avatarBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });
        document.addEventListener('click', () => dropdown.classList.remove('open'));
    }

    // Premium Tabs System
    const tabBtns = document.querySelectorAll('.dp-tab-btn');
    if (tabBtns.length > 0) {
        const indicator = document.querySelector('.dp-tab-indicator');
        
        function activateTab(btn) {
            const targetId = btn.getAttribute('data-tab');
            if (!targetId) return;
            
            // Deactivate all
            document.querySelectorAll('.dp-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.dp-tab-content').forEach(c => c.classList.remove('active'));
            
            // Activate target
            btn.classList.add('active');
            const targetContent = document.getElementById(targetId);
            if(targetContent) targetContent.classList.add('active');
            
            // Move indicator
            if (indicator) {
                indicator.style.width = btn.offsetWidth + 'px';
                indicator.style.left = btn.offsetLeft + 'px';
            }
        }
        
        tabBtns.forEach(btn => btn.addEventListener('click', () => activateTab(btn)));
        
        // Init first tab indicator cleanly
        setTimeout(() => {
            const activeBtn = document.querySelector('.dp-tab-btn.active') || tabBtns[0];
            if (activeBtn) {
                activeBtn.classList.add('active'); // force if not set
                activateTab(activeBtn);
            }
        }, 50);
    }
});
</script>

<script src="../assets/js/theme-switcher.js"></script>

