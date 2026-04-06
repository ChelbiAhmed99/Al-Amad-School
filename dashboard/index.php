<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('admin');
$current_role = 'admin';

// Fetch Firebase Data
$studentsDB = $database->getReference('students')->getValue() ?: [];
$teachersDB = $database->getReference('teachers')->getValue() ?: [];
$classesDB  = $database->getReference('classes')->getValue() ?: [];
$paymentsDB = $database->getReference('payments')->getValue() ?: [];
$usersDB    = $database->getReference('users')->getValue() ?: [];
$visitorsDB = $database->getReference('visitor_requests')->getValue() ?: [];
$attendanceAll = $database->getReference('attendance')->getValue() ?: [];
$todayDate = date('Y-m-d');
$attendanceDB = [];
foreach ($attendanceAll as $k => $v) {
    if (($v['date'] ?? '') === $todayDate) $attendanceDB[$k] = $v;
}

// Basic Counts
$total_students = count($studentsDB);
$total_teachers = count($teachersDB);
$total_classes  = count($classesDB);

// Finance
$monthly_revenue = 0;
$pending_payments = 0;
foreach ($paymentsDB as $p) {
    $status = $p['status'] ?? '';
    if ($status === 'Paid') $monthly_revenue += (float)($p['amount'] ?? 0);
    elseif ($status === 'Pending') $pending_payments++;
}

// User Gender Stats
$male_count = 0;
$female_count = 0;
foreach ($usersDB as $u) {
    $g = strtolower($u['gender'] ?? '');
    if ($g === 'male') $male_count++;
    if ($g === 'female') $female_count++;
}

// Student Age Stats (Child Ages for Histogram)
$age_stats = ['Pre-school' => 0, '6-8 yrs' => 0, '9-11 yrs' => 0, '12+ yrs' => 0];
$now_year = (int)date('Y');
$recent_students = [];

foreach ($studentsDB as $id => $s) {
    if (!empty($s['dob'])) {
        $dob_year = (int)substr($s['dob'], 0, 4);
        $age = $now_year - $dob_year;
        if ($age < 6) $age_stats['Pre-school']++;
        elseif ($age <= 8) $age_stats['6-8 yrs']++;
        elseif ($age <= 11) $age_stats['9-11 yrs']++;
        else $age_stats['12+ yrs']++;
    }

    // Build array for sorting recent enrollments
    $s['id'] = $id;
    $s['class_name'] = $classesDB[$s['class_id']]['name'] ?? 'Unassigned';
    $recent_students[$id] = $s;
}

// Sort recent students by push key (chronological) descending
krsort($recent_students);
$recent_students = array_slice(array_values($recent_students), 0, 5);

// Fetch pending visitor requests
$visitor_requests = [];
foreach ($visitorsDB as $id => $vr) {
    if (($vr['status'] ?? '') === 'Pending') {
        $vr['id'] = $id;
        $visitor_requests[$id] = $vr;
    }
}
krsort($visitor_requests);
$visitor_count = count($visitor_requests);

// Attendance summary
$present_today = 0;
foreach ($attendanceDB as $a) {
    if (($a['status'] ?? '') === 'Present') {
        $present_today++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>if(localStorage.getItem('theme')==='light') document.documentElement.setAttribute('data-theme', 'light');</script>
<!-- No local style block needed, using dashboard-premium.css -->
</head>
<body>
<?php include '../includes/sidebar.php'; ?>

<div class="dashboard-main">
<?php
$dash_title = 'Admin Dashboard';
$dash_sub   = date('l, F j, Y') . ' &nbsp;·&nbsp; <i class="fas fa-circle" style="color:#10b981; font-size:.45rem; vertical-align:middle;"></i> System Online';
$user_init  = 'AD';
include '../includes/dash-header.php';
?>


    <div class="dashboard-content">
        <!-- Branded Hero Banner -->
        <div class="branded-hero-banner">
            <div class="bhb-content">
                <span class="bhb-badge">Educational Excellence</span>
                <h3>Inspiring Future Leaders</h3>
                <p>
                    Our dedicated team of educators is committed to providing a nurturing, innovative, and safe environment where every student can excel.
                </p>
                <div class="bhb-actions">
                    <button class="btn btn-primary">View Faculty</button>
                    <button class="btn btn-outline">Our Vision</button>
                </div>
            </div>
            <div class="bhb-image">
                <img src="../assets/img/staff-team.png" alt="School Staff">
            </div>
        </div>

        <!-- ── KPI Cards ── -->
        <div class="kpi-grid">
            <div class="kpi-card reveal" style="--kpi-color: #6366f1; animation-delay:.05s">
                <div class="kpi-icon" style="--kpi-color:#6366f1"><i class="fas fa-user-graduate"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Total Students</div>
                    <div class="kpi-value"><?= number_format($total_students) ?></div>
                    <div class="kpi-trend up"><i class="fas fa-arrow-trend-up"></i> Enrolled</div>
                </div>
            </div>
            <div class="kpi-card reveal" style="--kpi-color:#1dd1a1; animation-delay:.1s">
                <div class="kpi-icon" style="--kpi-color:#1dd1a1"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Teachers</div>
                    <div class="kpi-value"><?= number_format($total_teachers) ?></div>
                    <div class="kpi-trend info"><i class="fas fa-briefcase"></i> Active Staff</div>
                </div>
            </div>
            <div class="kpi-card reveal" style="--kpi-color:#f9ca24; animation-delay:.15s">
                <div class="kpi-icon" style="--kpi-color:#f9ca24"><i class="fas fa-wallet"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-value" style="font-size:1.35rem;"><?= number_format($monthly_revenue, 0) ?> <small style="font-size:.65em; font-weight:600; opacity:.7;">TND</small></div>
                    <?php if($pending_payments > 0): ?>
                    <div class="kpi-trend warn"><i class="fas fa-clock"></i> <?= $pending_payments ?> pending</div>
                    <?php else: ?>
                    <div class="kpi-trend up"><i class="fas fa-check-circle"></i> All clear</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="kpi-card reveal" style="--kpi-color:#ff6b6b; animation-delay:.2s">
                <div class="kpi-icon" style="--kpi-color:#ff6b6b"><i class="fas fa-school-flag"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Classes</div>
                    <div class="kpi-value"><?= number_format($total_classes) ?></div>
                    <div class="kpi-trend info"><i class="fas fa-layer-group"></i> School Sections</div>
                </div>
            </div>
        </div>

        <!-- ── Charts + Quick Actions ── -->
        <div class="main-row">
            <!-- Gender Doughnut -->
            <div class="chart-card reveal" style="animation-delay:.25s">
                <div class="chart-card-header">
                    <span><i class="fas fa-venus-mars" style="color:var(--primary); margin-right:.5rem;"></i>Gender Distribution</span>
                    <span class="badge-sm">Users</span>
                </div>
                <div class="chart-body"><canvas id="genderChart"></canvas></div>
            </div>

            <!-- Age Histogram -->
            <div class="chart-card reveal" style="animation-delay:.3s">
                <div class="chart-card-header">
                    <span><i class="fas fa-chart-bar" style="color:var(--secondary); margin-right:.5rem;"></i>Age Groups</span>
                    <span class="badge-sm">Students</span>
                </div>
                <div class="chart-body"><canvas id="ageChart"></canvas></div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions reveal" style="animation-delay:.35s">
                <h3>Quick Actions</h3>
                <a href="students.php" class="qa-item">
                    <div class="qa-icon" style="background:rgba(99,102,241,.15); color:var(--primary);"><i class="fas fa-user-plus"></i></div>
                    Add Student
                </a>
                <a href="teachers.php" class="qa-item">
                    <div class="qa-icon" style="background:rgba(29,209,161,.15); color:#1dd1a1;"><i class="fas fa-user-tie"></i></div>
                    Add Teacher
                </a>
                <a href="classes.php" class="qa-item">
                    <div class="qa-icon" style="background:rgba(249,202,36,.15); color:#f9ca24;"><i class="fas fa-chalkboard"></i></div>
                    Manage Classes
                </a>
                <a href="finance.php" class="qa-item">
                    <div class="qa-icon" style="background:rgba(255,107,107,.15); color:#ff6b6b;"><i class="fas fa-receipt"></i></div>
                    Record Payment
                </a>
                <a href="attendance.php" class="qa-item">
                    <div class="qa-icon" style="background:rgba(99,102,241,.12); color:var(--primary);"><i class="fas fa-calendar-check"></i></div>
                    Attendance
                </a>
                <a href="messages.php" class="qa-item">
                    <div class="qa-icon" style="background:rgba(29,209,161,.12); color:#1dd1a1;"><i class="fas fa-comments"></i></div>
                    Messages
                </a>
            </div>
        </div>

        <!-- ── Bottom Row: Visitors + Recent Students ── -->
        <div class="bottom-row">
            <!-- Pending Visitor Requests -->
            <div class="table-card reveal" style="animation-delay:.4s;">
                <div class="table-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <span><i class="fas fa-user-clock" style="color:var(--primary); margin-right:.5rem;"></i>Pending Visitor Registrations</span>
                    <?php if($visitor_count > 0): ?>
                    <span style="background:#ef4444; color:white; font-size:.7rem; padding:.25rem .7rem; border-radius:50px; font-weight:700;"><?= $visitor_count ?> New</span>
                    <?php endif; ?>
                </div>
                <div class="table-responsive">
                    <table class="recent-table" style="width:100%; border-collapse:collapse;">
                        <thead><tr>
                            <th>Parent</th>
                            <th>Child</th>
                            <th>Plan</th>
                            <th>Action</th>
                        </tr></thead>
                        <tbody>
                        <?php if ($visitor_count > 0): ?>
                            <?php foreach($visitor_requests as $vr): ?>
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:.7rem;">
                                        <div style="width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--primary),var(--secondary)); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.85rem; flex-shrink:0;">
                                            <?= strtoupper(substr($vr['parent_first_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= htmlspecialchars($vr['parent_first_name'] . ' ' . $vr['parent_last_name']) ?></strong><br>
                                            <small style="opacity:.7;"><?= htmlspecialchars($vr['parent_email']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($vr['child_first_name']) ?> <small style="opacity:.7;">(<?= $vr['child_age'] ?>y)</small></td>
                                <td><span class="badge" style="background:rgba(249,202,36,.15); color:#b8860b; border:1px solid rgba(249,202,36,.3);"><?= htmlspecialchars($vr['payment_plan']) ?></span></td>
                                <td>
                                    <button onclick="validateRequest('<?= $vr['id'] ?>')" class="btn btn-primary" style="padding:.35rem .9rem; font-size:.78rem; border-radius:8px;">
                                        <i class="fas fa-check"></i> Validate
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; padding:2.5rem; color:var(--text-muted);">
                                <i class="fas fa-inbox" style="font-size:2rem; opacity:.3; display:block; margin-bottom:.75rem;"></i>
                                No pending registrations.
                            </td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Students + Today's Attendance -->
            <div style="display:flex; flex-direction:column; gap:1.5rem;">
                <!-- Today Attendance Mini Card -->
                <div class="kpi-card reveal" style="--kpi-color:#1dd1a1; animation-delay:.4s; flex-direction:row; padding:1.25rem;">
                    <div class="kpi-icon" style="--kpi-color:#1dd1a1;"><i class="fas fa-calendar-check"></i></div>
                    <div class="kpi-body">
                        <div class="kpi-label">Present Today</div>
                        <div class="kpi-value"><?= $present_today ?></div>
                        <div class="kpi-trend info"><i class="fas fa-clock"></i> <?= date('D j M') ?></div>
                    </div>
                    <a href="attendance.php" class="btn btn-outline" style="align-self:center; font-size:.78rem; padding:.4rem .9rem; flex-shrink:0;">View All</a>
                </div>

                <!-- Recent Students -->
                <div class="table-card reveal" style="animation-delay:.45s;">
                    <div class="table-card-header"><i class="fas fa-users" style="color:var(--primary); margin-right:.5rem;"></i>Recent Enrollments</div>
                    <div class="table-responsive">
                        <table class="recent-table" style="width:100%; border-collapse:collapse;">
                            <thead><tr>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Status</th>
                            </tr></thead>
                            <tbody>
                            <?php if (!empty($recent_students)): ?>
                                <?php foreach($recent_students as $rs): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:.6rem;">
                                            <div style="width:30px; height:30px; border-radius:50%; overflow:hidden; border:1px solid var(--glass-border); flex-shrink:0;">
                                                <img src="../<?= htmlspecialchars($rs['avatar'] ?? 'assets/img/logo.png') ?>" alt="S" style="width:100%; height:100%; object-fit:cover;">
                                            </div>
                                            <strong><?= htmlspecialchars($rs['first_name'] . ' ' . $rs['last_name']) ?></strong>
                                        </div>
                                    </td>
                                    <td><span style="opacity:.8;"><?= htmlspecialchars($rs['class_name'] ?? 'Unassigned') ?></span></td>
                                    <td><span style="color:#10b981; font-weight:600; font-size:.82rem;"><i class="fas fa-circle" style="font-size:.45rem; vertical-align:middle; margin-right:4px;"></i><?= htmlspecialchars($rs['status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="text-align:center; padding:2rem; color:var(--text-muted);">No recent enrollments.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Resolve CSS variables for chart colors
const style = getComputedStyle(document.documentElement);
const isDark = !document.documentElement.dataset.theme || document.documentElement.dataset.theme === 'dark';

// Gender Chart
new Chart(document.getElementById('genderChart'), {
    type: 'doughnut',
    data: {
        labels: ['Male', 'Female'],
        datasets: [{
            data: [<?= $male_count ?>, <?= $female_count ?>],
            backgroundColor: ['#6366f1', '#ff6b6b'],
            borderWidth: 0,
            hoverOffset: 8
        }]
    },
    options: {
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: isDark ? '#cbd5e1' : '#475569', padding: 16, font: { size: 12, family: 'Outfit' } }
            }
        }
    }
});

// Age Chart (Histogram)
new Chart(document.getElementById('ageChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($age_stats)) ?>,
        datasets: [{
            label: 'Students',
            data: <?= json_encode(array_values($age_stats)) ?>,
            backgroundColor: ['#6366f1', '#1dd1a1', '#f9ca24', '#ff6b6b'],
            borderRadius: 10,
            borderSkipped: false
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: isDark ? 'rgba(255,255,255,.05)' : 'rgba(0,0,0,.05)' }, ticks: { color: isDark ? '#94a3b8' : '#64748b', font: {family:'Outfit'} } },
            x: { grid: { display: false }, ticks: { color: isDark ? '#94a3b8' : '#64748b', font: {family:'Outfit'} } }
        }
    }
});

function validateRequest(id) {
    if(!confirm('Validate this registration? This will create a Parent account and Student record.')) return;
    fetch('../api/validate_visitor.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) { alert('Registration validated successfully!'); location.reload(); }
        else { alert('Error: ' + data.message); }
    });
}
</script>
</body>
</html>
