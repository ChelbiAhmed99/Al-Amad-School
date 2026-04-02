<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('teacher');
$current_role = 'teacher';

// Get Teacher info
$teacher_id = $_SESSION['user_id'] ?? '';
$teachersAll = $database->getReference('teachers')->getValue() ?: [];
$teachersDB = [];
foreach ($teachersAll as $k => $v) {
    if (($v['user_id'] ?? '') === $teacher_id) $teachersDB[$k] = $v;
}
$teacherProfile = reset($teachersDB) ?: [];
$teacherKey = key($teachersDB) ?: '';

$fn = $teacherProfile['first_name'] ?? 'T'; $ln = $teacherProfile['last_name'] ?? '';
$teacher_name = $teacherProfile ? htmlspecialchars($fn . ' ' . $ln) : 'Teacher';
$teacher_init = strtoupper(substr($fn, 0, 1) . substr($ln, 0, 1));
$teacher['specialty'] = $teacherProfile['specialty'] ?? 'Educator';

// Fetch Data
$classesDB = $database->getReference('classes')->getValue() ?: [];
$studentsDB = $database->getReference('students')->getValue() ?: [];
$gradesDB = $database->getReference('grades')->getValue() ?: [];
$attendanceDB = $database->getReference('attendance')->getValue() ?: [];
$announcementsDB = $database->getReference('announcements')->getValue() ?: [];

// Stats
$class_count = 0;
$my_classes_map = [];
$my_classes = [];
foreach ($classesDB as $c_id => $c) {
    if (($c['head_teacher_id'] ?? '') === $teacherKey) {
        $class_count++;
        $cname = $c['name'] ?? 'Class';
        $my_classes_map[$c_id] = $cname;
        $my_classes[] = $cname;
    }
}
sort($my_classes);

$student_count = 0;
foreach ($studentsDB as $s) {
    if (isset($my_classes_map[$s['class_id'] ?? ''])) $student_count++;
}

$grade_count = 0;
$recent_grades = [];
foreach ($gradesDB as $g) {
    if (($g['teacher_id'] ?? '') === $teacherKey) {
        $grade_count++;
        $sid = $g['student_id'] ?? '';
        if (isset($studentsDB[$sid])) {
            $g['first_name'] = $studentsDB[$sid]['first_name'] ?? '';
            $g['last_name'] = $studentsDB[$sid]['last_name'] ?? '';
            $recent_grades[] = $g;
        }
    }
}
usort($recent_grades, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
$recent_grades = array_slice($recent_grades, 0, 6);

$today = date('Y-m-d');
$present_today = 0;
foreach ($attendanceDB as $a) {
    if (($a['date'] ?? '') === $today && ($a['status'] ?? '') === 'Present') {
        $sid = $a['student_id'] ?? '';
        if (isset($studentsDB[$sid]) && isset($my_classes_map[$studentsDB[$sid]['class_id'] ?? ''])) {
            $present_today++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Al Amad School</title>
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
$dash_title = 'Teacher Dashboard';
$dash_sub   = date('l, F j, Y') . ' &nbsp;·&nbsp; <span style="color:var(--secondary);font-weight:600;">' . htmlspecialchars($teacher['specialty'] ?? 'Educator') . '</span>';
$user_init  = $teacher_init;
include '../includes/dash-header.php';
?>


    <div class="dashboard-content">
        <!-- Branded Hero Banner -->
        <div class="branded-hero-banner">
            <div class="bhb-content">
                <span class="bhb-badge">Classroom Spirit</span>
                <h3>Connecting with Your Students</h3>
                <p>
                    "Tell me and I forget. Teach me and I remember. Involve me and I learn." — Shaping the future, one lesson at a time.
                </p>
                <div class="bhb-actions">
                    <a href="attendance.php" class="btn btn-primary">Mark Attendance</a>
                    <a href="homework.php" class="btn btn-outline">My Lessons</a>
                </div>
            </div>
            <div class="bhb-image">
                <img src="../assets/img/students-learning.png" alt="Students Learning">
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card reveal" style="--kpi-color:#6366f1; animation-delay:.05s">
                <div class="kpi-icon" style="--kpi-color:#6366f1"><i class="fas fa-chalkboard"></i></div>
                <div>
                    <div class="kpi-label">My Classes</div>
                    <div class="kpi-value"><?= $class_count ?></div>
                    <div class="kpi-trend"><i class="fas fa-layer-group"></i> Assigned sections</div>
                </div>
            </div>
            <div class="kpi-card reveal" style="--kpi-color:#1dd1a1; animation-delay:.1s">
                <div class="kpi-icon" style="--kpi-color:#1dd1a1"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <div class="kpi-label">My Students</div>
                    <div class="kpi-value"><?= $student_count ?></div>
                    <div class="kpi-trend up"><i class="fas fa-users"></i> Total enrolled</div>
                </div>
            </div>
            <div class="kpi-card reveal" style="--kpi-color:#f9ca24; animation-delay:.15s">
                <div class="kpi-icon" style="--kpi-color:#f9ca24"><i class="fas fa-star"></i></div>
                <div>
                    <div class="kpi-label">Grades Logged</div>
                    <div class="kpi-value"><?= $grade_count ?></div>
                    <div class="kpi-trend"><i class="fas fa-chart-line"></i> Total records</div>
                </div>
            </div>
            <div class="kpi-card reveal" style="--kpi-color:#ff6b6b; animation-delay:.2s">
                <div class="kpi-icon" style="--kpi-color:#ff6b6b"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <div class="kpi-label">Present Today</div>
                    <div class="kpi-value"><?= $present_today ?></div>
                    <div class="kpi-trend up"><i class="fas fa-check-circle"></i> <?= date('D j M') ?></div>
                </div>
            </div>
        </div>

        <!-- Main Row -->
        <div class="main-row">
            <!-- Recent Grades Table -->
            <div class="table-card reveal" style="animation-delay:.25s">
                <div class="table-card-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <span><i class="fas fa-star" style="color:#f9ca24; margin-right:.5rem;"></i>Recently Logged Grades</span>
                    <a href="grades.php" class="btn btn-outline" style="font-size:.75rem; padding:.3rem .8rem; border-radius:8px;">View All</a>
                </div>
                <div class="table-responsive">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead><tr>
                            <th>Student</th><th>Subject</th><th>Score</th><th>Term</th>
                        </tr></thead>
                        <tbody>
                        <?php if (!empty($recent_grades)): ?>
                            <?php foreach($recent_grades as $g):
                                $score = (float)$g['score'];
                                $color = $score >= 15 ? '#10b981' : ($score >= 10 ? '#f9ca24' : '#ef4444');
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($g['first_name'] . ' ' . $g['last_name']) ?></strong></td>
                                <td><span style="opacity:.8"><?= htmlspecialchars($g['subject']) ?></span></td>
                                <td><span style="font-weight:700; color:<?= $color ?>"><?= number_format($score,1) ?>/20</span></td>
                                <td><span class="badge"><?= htmlspecialchars($g['term']) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; padding:2rem; color:var(--text-muted);"><i class="fas fa-inbox" style="display:block; font-size:1.8rem; opacity:.2; margin-bottom:.6rem;"></i>No grades logged yet.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="display:flex; flex-direction:column; gap:1.25rem;">
                <div class="table-card reveal" style="animation-delay:.3s; padding:1.5rem;">
                    <h3 style="font-size:.85rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:1rem;">Quick Actions</h3>
                    <div style="display:flex; flex-direction:column; gap:.6rem;">
                        <a href="attendance.php" class="btn btn-outline" style="justify-content:flex-start; gap:10px; padding:.85rem 1rem; border-radius:12px; font-weight:600;">
                            <span style="width:34px; height:34px; border-radius:10px; background:rgba(29,209,161,.15); color:#1dd1a1; display:flex; align-items:center; justify-content:center;"><i class="fas fa-calendar-check"></i></span> Mark Attendance
                        </a>
                        <a href="grades.php" class="btn btn-outline" style="justify-content:flex-start; gap:10px; padding:.85rem 1rem; border-radius:12px; font-weight:600;">
                            <span style="width:34px; height:34px; border-radius:10px; background:rgba(99,102,241,.15); color:var(--primary); display:flex; align-items:center; justify-content:center;"><i class="fas fa-star"></i></span> Enter Grades
                        </a>
                        <a href="homework.php" class="btn btn-primary" style="justify-content:flex-start; gap:10px; padding:.85rem 1rem; border-radius:12px; font-weight:600;">
                            <span style="width:34px; height:34px; border-radius:10px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center;"><i class="fas fa-bullhorn"></i></span> Post Announcement
                        </a>
                        <a href="messages.php" class="btn btn-outline" style="justify-content:flex-start; gap:10px; padding:.85rem 1rem; border-radius:12px; font-weight:600;">
                            <span style="width:34px; height:34px; border-radius:10px; background:rgba(255,107,107,.15); color:#ff6b6b; display:flex; align-items:center; justify-content:center;"><i class="fas fa-comment-dots"></i></span> Messages
                        </a>
                    </div>
                </div>

                <!-- My Classes pill list -->
                <?php if (!empty($my_classes)): ?>
                <div class="table-card reveal" style="animation-delay:.35s; padding:1.5rem;">
                    <h3 style="font-size:.85rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:1rem;">My Assigned Classes</h3>
                    <div style="display:flex; flex-wrap:wrap; gap:.5rem;">
                        <?php foreach($my_classes as $cls): ?>
                        <span style="padding:.35rem .9rem; background:rgba(99,102,241,.12); color:var(--primary); border-radius:50px; font-size:.78rem; font-weight:700; border:1px solid rgba(99,102,241,.2);"><?= htmlspecialchars($cls) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
</body>
</html>
