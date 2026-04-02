<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('parent');
$current_role = 'parent';

// Fetch linked children
$user_id = $_SESSION['user_id'];
$studentsDB = $database->getReference('students')->getValue() ?: [];
$gradesDB = $database->getReference('grades')->getValue() ?: [];

$child_ids = [];
$childrenMap = [];
foreach ($studentsDB as $sid => $s) {
    if (($s['parent_id'] ?? '') === $user_id) {
        $child_ids[] = $sid;
        $childrenMap[$sid] = $s;
    }
}

$grades = [];
foreach ($gradesDB as $g) {
    $sid = $g['student_id'] ?? '';
    if (in_array($sid, $child_ids)) {
        $g['first_name'] = $childrenMap[$sid]['first_name'] ?? '';
        $g['last_name']  = $childrenMap[$sid]['last_name'] ?? '';
        $grades[] = $g;
    }
}
usort($grades, function($a, $b) {
    $t = strcmp($a['term'] ?? '', $b['term'] ?? '');
    if ($t !== 0) return $t;
    $f = strcmp($a['first_name'] ?? '', $b['first_name'] ?? '');
    if ($f !== 0) return $f;
    return strcmp($a['subject'] ?? '', $b['subject'] ?? '');
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Cards - Al Amad School</title>
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
$dash_title = 'Child Report Card';
$dash_sub   = date('l, F j, Y');
$user_init  = 'PA';
include '../includes/dash-header.php';
?>

    <div class="dashboard-content">
        <?php if (count($child_ids) == 0): ?>
            <div style="padding: 2rem; color: var(--text-muted);">No children linked to your account yet.</div>
        <?php else: ?>
            <div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
                <button class="download-btn" onclick="alert('PDF Generation will be securely handled by TCPDF in production.')">📥 Download Full PDF Report</button>
            </div>
            
            <div class="table-card reveal" style="animation-delay: 0.3s;">
                <div class="table-card-header">Subject Marks & Feedback</div>
                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Term</th>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Score (/20)</th>
                                <th>Date Posted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($grades) > 0): ?>
                                <?php foreach($grades as $g): 
                                    $mark = $g['score'];
                                    $mark_class = $mark >= 15 ? 'score-good' : ($mark >= 10 ? 'score-avg' : 'score-bad');
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($g['term']) ?></strong></td>
                                    <td><?= htmlspecialchars($g['first_name']) ?></td>
                                    <td><span style="opacity: 0.8;"><?= htmlspecialchars($g['subject']) ?></span></td>
                                    <td class="<?= $mark_class ?>"><?= number_format($mark, 2) ?> <small style="color:var(--text-muted); font-weight:normal;">/ 20</small></td>
                                    <td><span style="font-size:0.85rem; opacity:0.7;"><?= date('M d, Y', strtotime($g['date'])) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding: 3rem;">No grades have been posted yet.</td></tr>
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
