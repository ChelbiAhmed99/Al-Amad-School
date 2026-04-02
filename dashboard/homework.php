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
$teacherKey = key($teachersDB) ?: '';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $target_role = trim($_POST['target_role']); // We'll hijack target_role to store the class name or 'all'
    
    if ($title && $content) {
        $database->getReference('announcements')->push([
            'title' => $title,
            'content' => $content,
            'target_role' => $target_role,
            'date' => date('Y-m-d H:i:s')
        ]);
        $message = "<div class='dash-alert success'>Assignment posted successfully!</div>";
    } else {
        $message = "<div class='dash-alert error'>Title and Content are required.</div>";
    }
}

// Fetch classes this teacher runs
$classesDB = $database->getReference('classes')->getValue() ?: [];
$classes = [];
foreach ($classesDB as $c) {
    if (($c['head_teacher_id'] ?? '') === $teacherKey) {
        $classes[] = $c['name'] ?? 'Class';
    }
}
sort($classes);

// Fetch recent announcements
$announcementsDB = $database->getReference('announcements')->getValue() ?: [];
$announcements = array_values($announcementsDB);
usort($announcements, fn($a, $b) => strcmp($b['date'] ?? '', $a['date'] ?? ''));
$announcements = array_slice($announcements, 0, 20);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homework - Al Amad School</title>
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
$dash_title = 'Homework & Announcements';
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
                <button type="button" class="dp-tab-btn active" data-tab="tab-list"><i class="fas fa-book-reader"></i> Recent Postings</button>
                <button type="button" class="dp-tab-btn" data-tab="tab-add"><i class="fas fa-edit"></i> Post Assignment</button>
            </div>
        </div>

        <!-- Tab 2: Add Form -->
        <div class="dp-tab-content" id="tab-add">
            <div class="form-card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.35rem;"><i class="fas fa-edit"></i> Post New Assignment</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-row">
                    <div class="form-group">
                        <label>Assignment Title</label>
                        <input type="text" name="title" placeholder="E.g. Chapter 4 Math Exercises" required>
                    </div>
                    <div class="form-group">
                        <label>Target Audience (Class)</label>
                        <select name="target_role" required>
                            <option value="All Classes">All My Classes</option>
                            <?php foreach($classes as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Instructions & Details</label>
                        <textarea name="content" placeholder="Please complete exercises 1 through 5 on page 42..." required></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem;">Publish to Portal</button>
            </form>
            </div>
        </div>

        <!-- Tab 1: List (Active by default) -->
        <div class="dp-tab-content active" id="tab-list">
            <div style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.35rem;"><i class="fas fa-clock"></i> Recent Postings</h3>
                <div class="homework-feed">
            <?php if (count($announcements) > 0): ?>
                <?php foreach($announcements as $a): ?>
                <div class="hw-card">
                    <div class="hw-header">
                        <div>
                            <div class="hw-title"><?= htmlspecialchars($a['title']) ?></div>
                            <div class="hw-meta" style="margin-top: 5px; opacity: 0.7; font-size: 0.85rem;">
                                <span><i class="far fa-calendar-alt"></i> <?= date('M d, Y', strtotime($a['date'])) ?></span>
                                <span class="hw-target" style="margin-left: 10px; color: var(--primary);"><i class="fas fa-bullseye"></i> <?= htmlspecialchars($a['target_role']) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="hw-content" style="margin-top: 1rem; line-height: 1.6; opacity: 0.9;"><?= nl2br(htmlspecialchars($a['content'])) ?></div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center; padding: 3rem; color:var(--text-muted); border: 1px dashed var(--glass-border); border-radius: 16px;">
                    No assignments posted.
                </div>
            <?php endif; ?>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
