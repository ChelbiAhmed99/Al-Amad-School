<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('parent');
$current_role = 'parent';

// Fetch linked children
$user_id = $_SESSION['user_id'] ?? null;
$studentsAll = $database->getReference('students')->getValue() ?: [];
$studentsDB = [];
foreach ($studentsAll as $k => $v) {
    if (($v['parent_id'] ?? '') === $user_id) $studentsDB[$k] = $v;
}

$child_ids = [];
foreach ($studentsDB as $s_id => $s) {
    if (!$s) continue;
    $s['id'] = $s_id;
    $child_ids[$s_id] = $s;
}

$attendance = [];
if (count($child_ids) > 0) {
    $attendanceDB = $database->getReference('attendance')->getValue() ?: [];
    foreach ($attendanceDB as $a_id => $a) {
        if (!$a) continue;
        $sid = $a['student_id'] ?? null;
        if (isset($child_ids[$sid])) {
            $a['id'] = $a_id;
            $a['first_name'] = $child_ids[$sid]['first_name'] ?? '';
            $a['last_name']  = $child_ids[$sid]['last_name'] ?? '';
            $a['justified']  = $a['justified'] ?? false;
            $attendance[] = $a;
        }
    }
    usort($attendance, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance History - Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
<script>if(localStorage.getItem('theme')==='light') document.documentElement.setAttribute('data-theme', 'light');</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<?php include '../includes/sidebar.php'; ?>

<div class="dashboard-main">
    <?php
$dash_title = 'Attendance History';
$dash_sub   = date('l, F j, Y');
$user_init  = 'PA';
include '../includes/dash-header.php';
?>

    <div class="dashboard-content">
        <?php if (count($child_ids) == 0): ?>
            <div style="padding: 2rem; color: var(--text-muted);">No children linked to your account yet.</div>
        <?php else: ?>
        <div class="table-card reveal" style="animation-delay: 0.2s;">
            <div class="table-card-header">Activity History</div>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student Name</th>
                            <th>Status</th>
                            <th>Justified</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($attendance) > 0): ?>
                            <?php foreach($attendance as $a): ?>
                            <tr>
                                <td><strong><?= date('M d, Y', strtotime($a['date'])) ?></strong></td>
                                <td><?= htmlspecialchars($a['first_name']) ?></td>
                                <td><span class="badge <?= htmlspecialchars($a['status']) ?>"><?= htmlspecialchars($a['status']) ?></span></td>
                                <td><?= $a['justified'] ? '<span style="color:var(--secondary)"><i class="fas fa-check-circle"></i> Yes</span>' : '<span style="opacity:0.5;">—</span>' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding: 3rem;">No attendance records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
