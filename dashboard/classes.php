<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('admin');
$current_role = 'admin';

// Handle POST
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = trim($_POST['name'] ?? '');
        $level = trim($_POST['level'] ?? '');
        $head_teacher_id = !empty($_POST['head_teacher_id']) ? $_POST['head_teacher_id'] : null;
        
        if ($name && $level) {
            try {
                $database->getReference('classes')->push([
                    'name' => $name,
                    'level' => $level,
                    'head_teacher_id' => $head_teacher_id
                ]);
                $message = "<div class='alert success'>Class added successfully!</div>";
            } catch (Exception $e) {
                $message = "<div class='alert error'>Failed to add class: " . $e->getMessage() . "</div>";
            }
        } else {
            $message = "<div class='alert error'>Name and Level are required.</div>";
        }
    } elseif ($_POST['action'] === 'delete') {
        $delete_id = $_POST['class_id'] ?? null;
        if ($delete_id) {
            try {
                $database->getReference('classes/' . $delete_id)->remove();
                $message = "<div class='alert success'>Class completely removed.</div>";
            } catch (Exception $e) {
                $message = "<div class='alert error'>Cannot delete class.</div>";
            }
        }
    }
}

// Fetch Data from Firebase
$classesDB  = $database->getReference('classes')->getValue() ?: [];
$teachersDB = $database->getReference('teachers')->getValue() ?: [];
$studentsDB = $database->getReference('students')->getValue() ?: [];

// Fetch Teachers for dropdown
$teachers = [];
foreach ($teachersDB as $t_id => $t) {
    if (!$t) continue;
    $t['id'] = $t_id;
    $teachers[] = $t;
}
usort($teachers, fn($a, $b) => strcmp($a['last_name'] ?? '', $b['last_name'] ?? ''));

// Fetch Classes with Head Teacher and Student Count
$classes = [];
foreach ($classesDB as $c_id => $c) {
    if (!$c) continue;
    $c['id'] = $c_id;
    
    // Count students
    $c['student_count'] = 0;
    foreach ($studentsDB as $s) {
        if (($s['class_id'] ?? null) === $c_id) {
            $c['student_count']++;
        }
    }
    
    // Map Head Teacher
    $hid = $c['head_teacher_id'] ?? null;
    if ($hid && isset($teachersDB[$hid])) {
        $c['first_name'] = $teachersDB[$hid]['first_name'] ?? '';
        $c['last_name']  = $teachersDB[$hid]['last_name'] ?? '';
    } else {
        $c['first_name'] = $c['last_name'] = null;
    }
    
    $classes[] = $c;
}
usort($classes, fn($a, $b) => strcmp($a['level'] ?? '', $b['level'] ?? '') ?: strcmp($a['name'] ?? '', $b['name'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes - Al Amad School</title>
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
$dash_title = 'Classes Management';
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
                <button type="button" class="dp-tab-btn active" data-tab="tab-list"><i class="fas fa-layer-group"></i> Class Catalog</button>
                <button type="button" class="dp-tab-btn" data-tab="tab-add"><i class="fas fa-plus-circle"></i> Create Class</button>
            </div>
        </div>

        <!-- Tab 2: Add Form -->
        <div class="dp-tab-content" id="tab-add">
            <div class="form-card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.35rem;"><i class="fas fa-plus-circle"></i> Create New Class</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label>Class Name</label>
                        <input type="text" name="name" placeholder="E.g. Grade 5A" required>
                    </div>
                    <div class="form-group">
                        <label>Level</label>
                        <select name="level" required>
                            <option value="Primary">Primary (Basic)</option>
                            <option value="Middle School">Middle School (Prep)</option>
                            <option value="High School">High School</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Head Teacher</label>
                        <select name="head_teacher_id">
                            <option value="">-- None --</option>
                            <?php foreach($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?> (<?= htmlspecialchars($t['specialty']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem;">Create Class</button>
            </form>
            </div>
        </div>

        <!-- Tab 1: List (Active by default) -->
        <div class="dp-tab-content active" id="tab-list">
            <div class="table-card" style="margin-bottom: 2rem;">
                <div class="table-card-header">School Classes Catalog</div>
                <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Class Name</th>
                            <th>Level</th>
                            <th>Head Teacher</th>
                            <th>Students Enrolled</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($classes) > 0): ?>
                            <?php foreach($classes as $c): ?>
                            <tr>
                                <td><strong>#<?= strtoupper(substr($c['id'], -4)) ?></strong></td>
                                <td><?= htmlspecialchars($c['name']) ?></td>
                                <td><span style="opacity: 0.8;"><?= htmlspecialchars($c['level']) ?></span></td>
                                <td><?= htmlspecialchars($c['first_name'] ? $c['first_name'] . ' ' . $c['last_name'] : '—') ?></td>
                                <td><span class="badge"><?= $c['student_count'] ?> Students</span></td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this class? This will orphan any assigned students.');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="class_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-color: rgba(239,68,68,0.5); color: #ef4444;"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding: 3rem;">No classes configured yet.</td></tr>
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
