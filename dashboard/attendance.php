<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
// Both admin and teacher can view/mark attendance
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }
if (!in_array($_SESSION['role'] ?? '', ['admin','teacher'])) { header('Location: ../auth/login.php?error=unauthorized'); exit; }
$current_role = $_SESSION['role'];

// Determine context
$is_teacher = ($current_role === 'teacher');
$uid = $_SESSION['user_id'] ?? '';

// Fetch Data
$studentsDB   = $database->getReference('students')->getValue() ?: [];
$classesDB    = $database->getReference('classes')->getValue() ?: [];
$attendanceDB = $database->getReference('attendance')->getValue() ?: [];

$message = '';

// Handle POST Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $student_id = !empty($_POST['student_id']) ? $_POST['student_id'] : null;
        $date       = trim($_POST['date'] ?? '');
        $status     = trim($_POST['status'] ?? '');
        
        if ($student_id && $date && $status) {
            // Check if already marked
            $already = false;
            foreach ($attendanceDB as $a) {
                if (($a['student_id'] ?? '') === $student_id && ($a['date'] ?? '') === $date) {
                    $already = true; break;
                }
            }
            if ($already) {
                $message = "<div class='alert error'>Attendance already marked for this student on $date.</div>";
            } else {
                try {
                    $database->getReference('attendance')->push([
                        'student_id' => $student_id,
                        'date'       => $date,
                        'status'     => $status,
                        'justified'  => false,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $message = "<div class='alert success'>Attendance saved successfully!</div>";
                    // Refresh data
                    $attendanceDB = $database->getReference('attendance')->getValue() ?: [];
                } catch (Exception $e) {
                    $message = "<div class='alert error'>Failed to save records: " . $e->getMessage() . "</div>";
                }
            }
        } else {
            $message = "<div class='alert error'>All fields are required.</div>";
        }
}

// Stats for Admin and Teacher
$stats = ['Present' => 0, 'Absent' => 0, 'Late' => 0, 'Not Marked' => 0];
if ($current_role === 'admin') {
    $today = date('Y-m-d');
    foreach ($attendanceDB as $a) {
        if (($a['date'] ?? '') === $today) {
            $st = $a['status'] ?? '';
            if (isset($stats[$st])) $stats[$st]++;
        }
    }
    $total = count($studentsDB);
    $stats['Not Marked'] = $total - ($stats['Present'] + $stats['Absent'] + $stats['Late']);
}

// Evaluate Teacher's Classes
$teacher_classes = [];
if ($is_teacher) {
    foreach ($classesDB as $c_id => $c) {
        if (($c['head_teacher_id'] ?? '') === $uid) {
            $teacher_classes[] = $c_id;
        }
    }
}

// Build Students List
$students = [];
foreach ($studentsDB as $s_id => $s) {
    if (!$s) continue;
    $cid = $s['class_id'] ?? null;
    
    // Filter if teacher
    if ($is_teacher && !in_array($cid, $teacher_classes)) {
        continue;
    }
    
    $s['id'] = $s_id;
    $s['class_name'] = $cid && isset($classesDB[$cid]) ? ($classesDB[$cid]['name'] ?? 'Unknown') : 'Unassigned';
    $students[] = $s;
}
usort($students, fn($a, $b) => strcmp($a['last_name'] ?? '', $b['last_name'] ?? ''));

// Build Attendance Records List
$records = [];
foreach ($attendanceDB as $a_id => $a) {
    if (!$a) continue;
    $sid = $a['student_id'] ?? null;
    
    // Make sure we have the student
    if (!$sid || !isset($studentsDB[$sid])) continue;
    
    $cid = $studentsDB[$sid]['class_id'] ?? null;
    
    // Filter if teacher
    if ($is_teacher && !in_array($cid, $teacher_classes)) {
        continue;
    }
    
    $a['id'] = $a_id;
    $a['first_name'] = $studentsDB[$sid]['first_name'] ?? '';
    $a['last_name']  = $studentsDB[$sid]['last_name'] ?? '';
    $a['justified']  = $a['justified'] ?? false;
    
    $records[] = $a;
}
usort($records, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
$records = array_slice($records, 0, $is_teacher ? 50 : 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Al Amad School</title>
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
$dash_title = 'Attendance Records';
$dash_sub   = date('l, F j, Y');
$user_init  = 'AD';
include '../includes/dash-header.php';
?>

    <div class="dashboard-content">
        <?= $message ?>
        
        <!-- Premium Tabs Navigation -->
        <div class="dp-tabs-container reveal">
            <div class="dp-tabs-nav">
                <div class="dp-tab-indicator"></div>
                <button type="button" class="dp-tab-btn active" data-tab="tab-list"><i class="fas fa-clipboard-list"></i> Daily Overview</button>
                <button type="button" class="dp-tab-btn" data-tab="tab-add"><i class="fas fa-user-check"></i> Mark Attendance</button>
            </div>
        </div>

        <!-- Tab 2: Mark Attendance Form -->
        <div class="dp-tab-content" id="tab-add">
            <div class="form-card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.35rem;"><i class="fas fa-user-check"></i> Mark Attendance</h3>
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
                        <label>Date</label>
                        <input type="date" name="date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" required>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Late">Late</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem;">Save Record</button>
            </div>
        </div>

        <!-- Tab 1: List & Stats (Active by default) -->
        <div class="dp-tab-content active" id="tab-list">
        
            <?php if ($current_role === 'admin'): ?>
            <div class="stats-grid" style="margin-bottom: 2rem;">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3><?= $stats['Present'] ?></h3>
                        <p>Present Today</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;"><i class="fas fa-times-circle"></i></div>
                    <div class="stat-info">
                        <h3><?= $stats['Absent'] ?></h3>
                        <p>Absent Today</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><i class="fas fa-clock"></i></div>
                    <div class="stat-info">
                        <h3><?= $stats['Late'] ?? 0 ?></h3>
                        <p>Late Today</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;"><i class="fas fa-user-slash"></i></div>
                    <div class="stat-info">
                        <h3><?= $stats['Not Marked'] ?></h3>
                        <p>Pending</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="table-card" style="margin-bottom: 2rem;">
                <div class="table-card-header">Daily Attendance Records</div>
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
                        <?php if (count($records) > 0): ?>
                            <?php foreach($records as $r): ?>
                            <tr>
                                <td><strong><?= date('M d, Y', strtotime($r['date'])) ?></strong></td>
                                <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                                <td><span class="badge <?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                                <td><?= $r['justified'] ? '<span style="color:var(--secondary)"><i class="fas fa-check-circle"></i> Yes</span>' : '<span style="opacity:0.5;">—</span>' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; color:var(--text-muted); padding: 3rem;">No attendance records found for your classes.</td></tr>
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
