<?php
// sidebar.php
// Expected variable: $current_role (admin, teacher, parent)
$page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
    <div class="sidebar-header">
        <div class="logo" style="display:flex; align-items:center; gap:10px;">
            <img src="../assets/img/logo.png" alt="Al Amad" class="sidebar-logo-img">
            <div>
                <span class="logo-text" style="font-weight:800; font-size:.95rem;">Al Amad</span>
                <span style="display:block; font-size:.65rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">School Portal</span>
            </div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item <?= $page == 'index.php' ? 'active' : '' ?>">
                <a href="index.php"><span class="icon" style="color: var(--primary)"><i class="fas fa-th-large"></i></span> Dashboard</a>
            </li>
            
            <li class="nav-item <?= $page == 'messages.php' ? 'active' : '' ?>">
                <a href="messages.php"><span class="icon" style="color: var(--info)"><i class="fas fa-comment-dots"></i></span> Messages</a>
            </li>

            <?php if ($current_role == 'admin'): ?>
                <li class="nav-section">Management</li>
                <li class="nav-item <?= $page == 'students.php' ? 'active' : '' ?>"><a href="students.php"><span class="icon" style="color: var(--secondary)"><i class="fas fa-user-graduate"></i></span> Students</a></li>
                <li class="nav-item <?= $page == 'parents.php' ? 'active' : '' ?>"><a href="parents.php"><span class="icon" style="color: var(--primary)"><i class="fas fa-user-friends"></i></span> Parents</a></li>
                <li class="nav-item <?= $page == 'teachers.php' ? 'active' : '' ?>"><a href="teachers.php"><span class="icon" style="color: var(--accent)"><i class="fas fa-chalkboard-teacher"></i></span> Teachers</a></li>
                <li class="nav-item <?= $page == 'classes.php' ? 'active' : '' ?>"><a href="classes.php"><span class="icon" style="color: var(--purple)"><i class="fas fa-school"></i></span> Classes</a></li>
                <li class="nav-item <?= $page == 'finance.php' ? 'active' : '' ?>"><a href="finance.php"><span class="icon" style="color: var(--primary)"><i class="fas fa-wallet"></i></span> Finance</a></li>
                <li class="nav-item <?= $page == 'attendance.php' ? 'active' : '' ?>"><a href="attendance.php"><span class="icon" style="color: var(--secondary)"><i class="fas fa-calendar-check"></i></span> Attendance</a></li>
            <?php endif; ?>
            
            <?php if ($current_role == 'teacher'): ?>
                <li class="nav-section">Teaching</li>
                <li class="nav-item <?= $page == 'grades.php' ? 'active' : '' ?>"><a href="grades.php"><span class="icon" style="color: var(--primary)"><i class="fas fa-chart-bar"></i></span> Grades</a></li>
                <li class="nav-item <?= $page == 'attendance.php' ? 'active' : '' ?>"><a href="attendance.php"><span class="icon" style="color: var(--secondary)"><i class="fas fa-calendar-check"></i></span> Attendance</a></li>
                <li class="nav-item <?= $page == 'quizzes.php' ? 'active' : '' ?>"><a href="quizzes.php"><span class="icon" style="color: var(--purple)"><i class="fas fa-question-circle"></i></span> Quizzes</a></li>
                <li class="nav-item <?= $page == 'homework.php' ? 'active' : '' ?>"><a href="homework.php"><span class="icon" style="color: var(--accent)"><i class="fas fa-book"></i></span> Homework</a></li>
            <?php endif; ?>
            
            <?php if ($current_role == 'parent'): ?>
                <li class="nav-section">My Children</li>
                <li class="nav-item <?= $page == 'child-report.php' ? 'active' : '' ?>"><a href="child-report.php"><span class="icon" style="color: var(--accent)"><i class="fas fa-file-contract"></i></span> Report Cards</a></li>
                <li class="nav-item <?= $page == 'parent-attendance.php' ? 'active' : '' ?>"><a href="parent-attendance.php"><span class="icon" style="color: var(--secondary)"><i class="fas fa-user-check"></i></span> Attendance</a></li>
                <li class="nav-item <?= $page == 'quizzes.php' ? 'active' : '' ?>"><a href="quizzes.php"><span class="icon" style="color: var(--purple)"><i class="fas fa-question-circle"></i></span> My Quizzes</a></li>
                <li class="nav-item <?= $page == 'parent-payments.php' ? 'active' : '' ?>"><a href="parent-payments.php"><span class="icon" style="color: var(--primary)"><i class="fas fa-credit-card"></i></span> Payments</a></li>
            <?php endif; ?>
            
            <li class="nav-section">System</li>
            <li class="nav-item <?= $page == 'profile.php' ? 'active' : '' ?>"><a href="profile.php"><span class="icon" style="color: var(--primary)"><i class="fas fa-user-circle"></i></span> My Profile</a></li>
            <li class="nav-item"><a href="../auth/logout.php"><span class="icon" style="color: var(--text-muted)"><i class="fas fa-sign-out-alt"></i></span> Logout</a></li>
        </ul>
    </nav>

    <!-- Sidebar user footer -->
    <div class="sidebar-user-footer">
        <?php if (!empty($_SESSION['avatar'])): ?>
            <img src="../<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Avatar" 
                 style="width:38px; height:38px; border-radius:12px; object-fit:cover; flex-shrink:0; box-shadow:0 4px 12px rgba(255,123,84,0.2);">
        <?php else: ?>
            <div class="sidebar-user-avatar">
                <?= strtoupper(substr($_SESSION['name'] ?? $_SESSION['email'] ?? 'U', 0, 2)) ?>
            </div>
        <?php endif; ?>
        <div style="min-width:0; flex:1;">
            <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['name'] ?? strtoupper($current_role)) ?></div>
            <div class="sidebar-user-email"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></div>
        </div>
        <a href="../auth/logout.php" title="Logout" style="color:var(--ps-orange); opacity:0.7; font-size:1rem; transition:opacity .2s; flex-shrink:0;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.7">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</aside>

<style>
/* ─── Sidebar Shell ─── */
.sidebar {
    width: 280px;
    height: 100vh;
    background: #ffffff;
    border-right: 2px solid rgba(255, 123, 84, 0.10);
    position: fixed; top: 0; left: 0;
    display: flex; flex-direction: column;
    z-index: 1000;
    transition: transform .35s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 4px 0 28px rgba(255, 123, 84, 0.07);
    overflow: hidden;
}
/* Warm subtle texture */
.sidebar::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(circle at top right, rgba(255,123,84,0.04), transparent 60%);
    pointer-events: none; z-index: 0;
}

/* ─── Sidebar Header ─── */
.sidebar-header {
    padding: 1.75rem 1.5rem 1.25rem;
    border-bottom: 1.5px solid rgba(255,123,84,0.08);
    position: relative; z-index: 2; flex-shrink: 0;
    background: linear-gradient(135deg, rgba(255,123,84,0.04) 0%, transparent 100%);
}
.sidebar-logo-img {
    width: 40px; height: 40px; border-radius: 12px;
    object-fit: contain;
    box-shadow: 0 4px 12px rgba(255,123,84,0.18);
}
.logo-text {
    font-size: 1.05rem; font-weight: 900;
    color: #2d3436; letter-spacing: -0.3px;
}

/* ─── Nav ─── */
.sidebar-nav {
    flex: 1; padding: 1rem 0.75rem;
    overflow-y: auto; position: relative; z-index: 2;
}
.sidebar-nav::-webkit-scrollbar { width: 4px; }
.sidebar-nav::-webkit-scrollbar-track { background: transparent; }
.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,123,84,0.18); border-radius: 10px; }
.sidebar-nav ul { list-style: none; }

.nav-section {
    font-size: 0.65rem; text-transform: uppercase;
    color: var(--ps-orange); opacity: 0.7;
    margin: 1.75rem 0 0.6rem 1rem;
    letter-spacing: 2px; font-weight: 900;
}

.nav-item { margin-bottom: 4px; }
.nav-item a {
    display: flex; align-items: center; gap: 13px;
    padding: 0.82rem 1.15rem;
    text-decoration: none; color: #636e72;
    border-radius: 14px;
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.9rem; font-weight: 600;
    position: relative;
}

/* Active State */
.nav-item.active a {
    background: linear-gradient(135deg, var(--ps-orange) 0%, #ff9575 100%) !important;
    color: white !important;
    box-shadow: 0 6px 18px rgba(255, 123, 84, 0.28);
    font-weight: 700;
}
.nav-item.active a .icon i { color: white !important; }

/* Hover State */
.nav-item a:hover {
    background: rgba(255, 123, 84, 0.07) !important;
    color: var(--ps-orange) !important;
    transform: translateX(5px);
}

.nav-item .icon {
    font-size: 1.05rem; width: 22px; text-align: center;
    transition: transform 0.25s; flex-shrink: 0;
}
.nav-item a:hover .icon { transform: scale(1.15) rotate(-6deg); }

/* ─── User Footer ─── */
.sidebar-user-footer {
    padding: 1.2rem 1.5rem;
    border-top: 1.5px solid rgba(255,123,84,0.08);
    display: flex; align-items: center; gap: 0.75rem;
    position: relative; z-index: 2;
    background: rgba(255, 123, 84, 0.025);
}
.sidebar-user-avatar {
    width: 38px; height: 38px; border-radius: 12px;
    background: linear-gradient(135deg, var(--ps-orange), #ff9f7f);
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 800; font-size: 0.85rem;
    flex-shrink: 0; box-shadow: 0 4px 12px rgba(255,123,84,0.22);
}
.sidebar-user-name  { font-size: 0.84rem; font-weight: 700; color: #2d3436; }
.sidebar-user-email { font-size: 0.7rem; color: #636e72; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* ─── Mobile ─── */
.sidebar-close {
    display: none;
    position: absolute; top: 1rem; right: -45px;
    background: var(--ps-orange); border: none;
    border-radius: 10px; width: 38px; height: 38px;
    align-items: center; justify-content: center;
    cursor: pointer; color: white; font-size: 1.1rem;
    z-index: 1001; box-shadow: 0 4px 15px rgba(255,123,84,0.35);
}
.sidebar-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(45,52,54,0.50);
    z-index: 999; backdrop-filter: blur(4px);
}
.sidebar-overlay.active { display: block; }

@media (max-width: 1024px) {
    .sidebar { transform: translateX(-110%); }
    .sidebar.open { transform: translateX(0); }
    .sidebar.open .sidebar-close { display: flex; }
}
</style>
<script src="../assets/js/dashboard.js"></script>
