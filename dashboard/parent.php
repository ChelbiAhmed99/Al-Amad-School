<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('parent');
$current_role = 'parent';

// Fetch linked children
$user_id = $_SESSION['user_id'];
$studentsDB = $database->getReference('students')->getValue() ?: [];
$classesDB = $database->getReference('classes')->getValue() ?: [];
$paymentsDB = $database->getReference('payments')->getValue() ?: [];
$attendanceDB = $database->getReference('attendance')->getValue() ?: [];
$announcementsDB = $database->getReference('announcements')->getValue() ?: [];

$children = [];
$child_ids = [];
$class_names = [];
foreach ($studentsDB as $s_id => $s) {
    if (($s['parent_id'] ?? '') === $user_id) {
        $s['id'] = $s_id;
        $cid = $s['class_id'] ?? '';
        $cname = isset($classesDB[$cid]) ? ($classesDB[$cid]['name'] ?? 'Unknown') : 'Unassigned';
        $s['class_name'] = $cname;
        $children[] = $s;
        $child_ids[] = $s_id;
        if ($cname !== 'Unassigned' && !in_array($cname, $class_names)) $class_names[] = $cname;
    }
}

// Stats
$total_children = count($children);
$total_paid = 0; $total_pending = 0;
$present_count = 0; $total_att = 0;

foreach ($paymentsDB as $p) {
    if (in_array($p['student_id'] ?? '', $child_ids)) {
        if (($p['status'] ?? '') === 'Paid') $total_paid += (float)($p['amount'] ?? 0);
        if (($p['status'] ?? '') === 'Pending') $total_pending += (float)($p['amount'] ?? 0);
    }
}

foreach ($attendanceDB as $a) {
    if (in_array($a['student_id'] ?? '', $child_ids)) {
        $total_att++;
        if (($a['status'] ?? '') === 'Present') $present_count++;
    }
}
$att_pct = $total_att > 0 ? round(($present_count / $total_att) * 100) : 100;

// Announcements
$announcements = [];
foreach ($announcementsDB as $a) {
    $tr = $a['target_role'] ?? '';
    if ($tr === 'All Classes' || $tr === 'all' || in_array($tr, $class_names)) {
        $announcements[] = $a;
    }
}
usort($announcements, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
$announcements = array_slice($announcements, 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Portal - Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script>if(localStorage.getItem('theme')==='light') document.documentElement.setAttribute('data-theme', 'light');</script>
<!-- No local style block needed, using dashboard-premium.css -->
</head>
<body>
<?php include '../includes/sidebar.php'; ?>

<div class="dashboard-main">
<?php
$dash_title = 'Parent Portal';
$dash_sub   = date('l, F j, Y') . ' &nbsp;·&nbsp; ' . $total_children . ' child' . ($total_children != 1 ? 'ren' : '') . ' enrolled';
$user_init  = 'PA';
include '../includes/dash-header.php';
?>


    <div class="dashboard-content">
        <!-- Branded Hero Banner -->
        <div class="branded-hero-banner">
            <div class="bhb-content">
                <span class="bhb-badge">Family Portal</span>
                <h3>Welcome Back, Parent</h3>
                <p>
                    Stay connected with your child's educational journey. Monitor progress, view attendance, and receive direct updates from teachers.
                </p>
                <div class="bhb-actions">
                    <a href="messages.php" class="btn btn-primary">Message School</a>
                    <a href="profile.php" class="btn btn-outline">My Profile</a>
                </div>
            </div>
            <div class="bhb-image">
                <img src="../assets/img/parents-support.png" alt="Parents Support">
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card reveal" style="--kpi-color:#6366f1; animation-delay:.05s">
                <div class="kpi-icon" style="--kpi-color:#6366f1"><i class="fas fa-child"></i></div>
                <div>
                    <div class="kpi-label">Children</div>
                    <div class="kpi-value"><?= $total_children ?></div>
                    <div class="kpi-trend"><i class="fas fa-heart"></i> Enrolled</div>
                </div>
            </div>
            <div class="kpi-card reveal" style="--kpi-color:#1dd1a1; animation-delay:.1s">
                <div class="kpi-icon" style="--kpi-color:#1dd1a1"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <div class="kpi-label">Attendance Rate</div>
                    <div class="kpi-value"><?= $att_pct ?>%</div>
                    <div class="kpi-trend<?= $att_pct >= 80 ? ' up' : ' warn' ?>"><?= $att_pct >= 80 ? '✓ Good standing' : '⚠ Low attendance' ?></div>
                </div>
            </div>
            <div class="kpi-card reveal" style="--kpi-color:#10b981; animation-delay:.15s">
                <div class="kpi-icon" style="--kpi-color:#10b981"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="kpi-label">Paid Fees</div>
                    <div class="kpi-value" style="font-size:1.35rem;"><?= number_format($total_paid, 0) ?> <small style="font-size:.6em; opacity:.7;">TND</small></div>
                    <div class="kpi-trend up"><i class="fas fa-receipt"></i> Confirmed</div>
                </div>
            </div>
            <div class="kpi-card reveal" style="--kpi-color:<?= $total_pending > 0 ? '#f59e0b' : '#10b981' ?>; animation-delay:.2s">
                <div class="kpi-icon" style="--kpi-color:<?= $total_pending > 0 ? '#f59e0b' : '#10b981' ?>">
                    <i class="fas fa-<?= $total_pending > 0 ? 'clock' : 'wallet' ?>"></i>
                </div>
                <div>
                    <div class="kpi-label">Balance Due</div>
                    <div class="kpi-value" style="font-size:1.35rem;"><?= number_format($total_pending, 0) ?> <small style="font-size:.6em; opacity:.7;">TND</small></div>
                    <div class="kpi-trend <?= $total_pending > 0 ? 'warn' : 'up' ?>"><?= $total_pending > 0 ? '⚠ Pending payment' : '✓ All clear' ?></div>
                </div>
            </div>
        </div>

        <!-- My Children Cards -->
        <h3 style="font-size:.9rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:1rem;"><i class="fas fa-child" style="color:var(--primary); margin-right:.5rem;"></i>My Children</h3>
        <?php if (!empty($children)): ?>
        <div class="main-row" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); margin-bottom: 2.5rem;">
            <?php foreach($children as $c): ?>
            <div class="feature-card reveal">
                <div style="display: flex; gap: 1.25rem; align-items: center; margin-bottom: 1.25rem;">
                    <div style="width: 64px; height: 64px; border-radius: 18px; overflow: hidden; border: 2.5px solid var(--ps-orange); flex-shrink: 0;">
                        <?php if (!empty($c['avatar'])): ?>
                            <img src="<?= htmlspecialchars($c['avatar']) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="width:100%; height:100%; background:var(--ps-orange); color:white; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:900;">
                                <?= strtoupper(substr($c['first_name'],0,1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 1.15rem; font-weight: 900; color: var(--text);"><?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?></div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--ps-orange); text-transform: uppercase; margin-top: 2px;">
                            <i class="fas fa-school"></i> <?= htmlspecialchars($c['class_name'] ?? 'Pending') ?>
                        </div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <a href="child-report.php?student_id=<?= $c['id'] ?>" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.6rem;"><i class="fas fa-chart-line"></i> Report</a>
                    <a href="parent-attendance.php" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.6rem;"><i class="fas fa-calendar-check"></i> Attendance</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="padding:2.5rem; text-align:center; color:var(--text-muted); background:var(--card-bg); border:1px dashed var(--glass-border); border-radius:20px; margin-bottom:2rem;">
            <i class="fas fa-child" style="font-size:2.5rem; opacity:.2; display:block; margin-bottom:.8rem;"></i>
            No children linked to your account yet. Please contact administration.
        </div>
        <?php endif; ?>

        <!-- Bottom: Announcements + Quick Actions -->
        <div class="main-lay">
            <!-- Announcements Feed -->
            <div>
                <h3 style="font-size:.9rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:1rem;"><i class="fas fa-bullhorn" style="color:var(--primary); margin-right:.5rem;"></i>School Announcements</h3>
                <div class="announcements-grid">
                    <?php if (!empty($announcements)): ?>
                        <?php foreach($announcements as $a): ?>
                        <div class="feed-item reveal">
                            <div class="feed-icon"><i class="fas fa-bullhorn"></i></div>
                            <div class="feed-content">
                                <h4><?= htmlspecialchars($a['title']) ?></h4>
                                <p><?= htmlspecialchars(mb_substr($a['content'], 0, 120)) ?>...</p>
                                <div class="feed-meta">
                                    <i class="far fa-calendar-alt"></i> <?= date('M d, Y', strtotime($a['date'])) ?> &nbsp;·&nbsp; 
                                    <i class="fas fa-bullseye"></i> <?= htmlspecialchars($a['target_role']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding:2.5rem; text-align:center; color:var(--text-muted); border:1px dashed var(--glass-border); border-radius:16px;">
                            <i class="fas fa-inbox" style="font-size:2rem; opacity:.2; display:block; margin-bottom:.6rem;"></i>No announcements at the moment.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="table-card reveal" style="padding:1.5rem; animation-delay:.3s; align-self:start;">
                <h3 style="font-size:.85rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:1rem;">Quick Access</h3>
                <div style="display:flex; flex-direction:column; gap:.7rem;">
                    <a href="child-report.php" class="btn btn-outline" style="justify-content:flex-start; gap:12px; padding:.9rem 1rem; border-radius:12px; font-weight:600;">
                        <span style="width:36px; height:36px; border-radius:10px; background:rgba(99,102,241,.15); color:var(--primary); display:flex; align-items:center; justify-content:center;"><i class="fas fa-file-alt"></i></span> View Report Cards
                    </a>
                    <a href="parent-attendance.php" class="btn btn-outline" style="justify-content:flex-start; gap:12px; padding:.9rem 1rem; border-radius:12px; font-weight:600;">
                        <span style="width:36px; height:36px; border-radius:10px; background:rgba(29,209,161,.15); color:#1dd1a1; display:flex; align-items:center; justify-content:center;"><i class="fas fa-calendar-check"></i></span> Check Attendance
                    </a>
                    <a href="parent-payments.php" class="btn btn-outline" style="justify-content:flex-start; gap:12px; padding:.9rem 1rem; border-radius:12px; font-weight:600;">
                        <span style="width:36px; height:36px; border-radius:10px; background:rgba(249,202,36,.15); color:#b8860b; display:flex; align-items:center; justify-content:center;"><i class="fas fa-credit-card"></i></span> Payment History
                    </a>
                    <a href="messages.php" class="btn btn-primary" style="justify-content:flex-start; gap:12px; padding:.9rem 1rem; border-radius:12px; font-weight:600;">
                        <span style="width:36px; height:36px; border-radius:10px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center;"><i class="fas fa-comments"></i></span> Message School
                    </a>
                </div>

                <?php if($total_pending > 0): ?>
                <div style="margin-top:1.25rem; padding:1rem; background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.3); border-radius:14px;">
                    <div style="font-weight:700; color:#f59e0b; font-size:.85rem; margin-bottom:.3rem;"><i class="fas fa-exclamation-triangle"></i> Outstanding Balance</div>
                    <div style="font-size:1.2rem; font-weight:800;"><?= number_format($total_pending,2) ?> TND</div>
                    <a href="parent-payments.php" class="btn btn-primary" style="margin-top:.75rem; padding:.6rem 1.2rem; font-size:.82rem; border-radius:10px; justify-content:center; display:flex; gap:6px;"><i class="fas fa-credit-card"></i> Make Payment</a>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
</body>
</html>
