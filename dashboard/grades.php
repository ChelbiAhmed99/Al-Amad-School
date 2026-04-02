<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('teacher');
$current_role = 'teacher';

// Get Teacher ID
$teacher_id = $_SESSION['user_id'] ?? '';
$teachersAll = $database->getReference('teachers')->getValue() ?: [];
$teachersDB = [];
foreach ($teachersAll as $k => $v) {
    if (($v['user_id'] ?? '') === $teacher_id) $teachersDB[$k] = $v;
}
$teacherProfile = reset($teachersDB) ?: [];
$teacherKey = key($teachersDB) ?: '';
$subject = $teacherProfile['specialty'] ?? 'General';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $student_id = !empty($_POST['student_id']) ? $_POST['student_id'] : null;
    $term = trim($_POST['term'] ?? '');
    $score = trim($_POST['score'] ?? '');
    
    if ($student_id && $term && $score !== '') {
        $database->getReference('grades')->push([
            'student_id' => $student_id,
            'subject' => $subject,
            'score'   => (float)$score,
            'term'    => $term,
            'teacher_id' => $teacherKey,
            'date'    => date('Y-m-d H:i:s')
        ]);
        $message = "<div class='dash-alert success'>Grade ($score/20) saved successfully!</div>";
    } else {
        $message = "<div class='dash-alert error'>All fields are required.</div>";
    }
}

$studentsDB = $database->getReference('students')->getValue() ?: [];
$classesDB = $database->getReference('classes')->getValue() ?: [];
$gradesDB = $database->getReference('grades')->getValue() ?: [];

// Map my classes
$my_classes_map = [];
foreach ($classesDB as $cid => $c) {
    if (($c['head_teacher_id'] ?? '') === $teacherKey) {
        $my_classes_map[$cid] = $c['name'] ?? 'Class';
    }
}

$students = [];
foreach ($studentsDB as $sid => $s) {
    $cid = $s['class_id'] ?? '';
    if (isset($my_classes_map[$cid])) {
        $s['id'] = $sid;
        $s['class_name'] = $my_classes_map[$cid];
        $students[] = $s;
    }
}
usort($students, fn($a, $b) => strcmp($a['last_name'] ?? '', $b['last_name'] ?? ''));

$records = [];
foreach ($gradesDB as $g) {
    if (($g['teacher_id'] ?? '') === $teacherKey) {
        $sid = $g['student_id'] ?? '';
        if (isset($studentsDB[$sid])) {
            $g['first_name'] = $studentsDB[$sid]['first_name'] ?? '';
            $g['last_name']  = $studentsDB[$sid]['last_name'] ?? '';
            $records[] = $g;
        }
    }
}
usort($records, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
$records = array_slice($records, 0, 50);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades - Al Amad School</title>
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
$dash_title = 'Grades Management';
$dash_sub   = date('l, F j, Y');
$user_init  = 'T';
include '../includes/dash-header.php';
?>

    <div class="dashboard-content">
        <?= $message ?>
        
        <!-- Premium Tabs Navigation -->
        <div class="dp-tabs-container reveal">
            <div class="dp-tabs-nav">
                <div class="dp-tab-indicator"></div>
                <button type="button" class="dp-tab-btn active" data-tab="tab-list"><i class="fas fa-clipboard-list"></i> Student Grades</button>
                <button type="button" class="dp-tab-btn" data-tab="tab-add"><i class="fas fa-pen"></i> Enter Marks</button>
            </div>
        </div>

        <!-- Tab 2: Add Form -->
        <div class="dp-tab-content" id="tab-add">
            <div class="form-card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.35rem;"><i class="fas fa-pen"></i> Enter Marks (/20)</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label>Student</label>
                        <select name="student_id" required>
                            <option value="">-- Select Student --</option>
                            <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?> (<?= htmlspecialchars($s['class_name']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Academic Term</label>
                        <select name="term" required>
                            <option value="Term 1 (Fall)">Term 1 (Fall)</option>
                            <option value="Term 2 (Spring)">Term 2 (Spring)</option>
                            <option value="Term 3 (Summer)">Term 3 (Summer)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Score (xx.xx / 20)</label>
                        <input type="number" step="0.25" max="20" min="0" name="score" placeholder="E.g. 15.5" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem;">Save Marks</button>
            </form>
            </div>
        </div>

        <!-- Tab 1: List (Active by default) -->
        <div class="dp-tab-content active" id="tab-list">
            <div class="table-card" style="margin-bottom: 2rem;">
                <div class="table-card-header">Student Grade Records</div>
                <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>Date Entered</th>
                        <th>Student Name</th>
                        <th>Term</th>
                        <th>Score (/20)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($records) > 0): ?>
                        <?php foreach($records as $r): 
                            $mark_class = $r['score'] >= 15 ? 'score-good' : ($r['score'] >= 10 ? 'score-avg' : 'score-bad');
                        ?>
                        <tr>
                            <td><span style="opacity:0.8;"><?= date('M d, Y', strtotime($r['date'])) ?></span></td>
                            <td><strong><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></strong></td>
                            <td><?= htmlspecialchars($r['term']) ?></td>
                            <td class="<?= $mark_class ?>"><?= number_format($r['score'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding: 3rem;">No grades recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
