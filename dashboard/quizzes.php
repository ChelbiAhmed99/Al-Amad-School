<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('any');

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Handle Quiz Creation (Teacher only)
if ($role === 'teacher' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_quiz'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $class_id = $_POST['class_id'];
    
    // Get teacher_id from user_id
    $teachersAll = $database->getReference('teachers')->getValue() ?: [];
    $teachersDB = [];
    foreach ($teachersAll as $k => $v) {
        if (($v['user_id'] ?? '') === $user_id) $teachersDB[$k] = $v;
    }
    $teacherKey = key($teachersDB) ?: '';

    $quizRef = $database->getReference('quizzes')->push([
        'title' => $title,
        'description' => $desc,
        'class_id' => $class_id,
        'teacher_id' => $teacherKey,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Add demo questions
    $database->getReference('quiz_questions')->push([
        'quiz_id' => $quizRef->getKey(),
        'question' => 'What is 5 + 7?',
        'option_a' => '10', 'option_b' => '12', 'option_c' => '13',
        'correct_option' => 'b'
    ]);
    $database->getReference('quiz_questions')->push([
        'quiz_id' => $quizRef->getKey(),
        'question' => 'Which is a primary color?',
        'option_a' => 'Green', 'option_b' => 'Purple', 'option_c' => 'Blue',
        'correct_option' => 'c'
    ]);
    
    $success = "Quiz '$title' created successfully! ✨";
}

// Data fetching based on role
$quizzesDB = $database->getReference('quizzes')->getValue() ?: [];
$classesDB = $database->getReference('classes')->getValue() ?: [];
$teachersDB = $database->getReference('teachers')->getValue() ?: [];
$studentsDB = $database->getReference('students')->getValue() ?: [];
$quizzes = [];
$classes = [];

$buildQuiz = function($q_id, $q) use ($classesDB, $teachersDB) {
    $q['id'] = $q_id;
    $cid = $q['class_id'] ?? '';
    $tid = $q['teacher_id'] ?? '';
    $q['class_name'] = isset($classesDB[$cid]) ? ($classesDB[$cid]['name'] ?? 'Unknown') : 'Unassigned';
    if (isset($teachersDB[$tid])) {
        $q['teacher_name'] = ($teachersDB[$tid]['first_name'] ?? '') . ' ' . ($teachersDB[$tid]['last_name'] ?? '');
    } else {
        $q['teacher_name'] = 'Unknown Teacher';
    }
    return $q;
};

if ($role === 'admin') {
    foreach ($quizzesDB as $qid => $q) {
        $quizzes[] = $buildQuiz($qid, $q);
    }
} elseif ($role === 'teacher') {
    $tid = '';
    foreach ($teachersDB as $tk => $tv) {
        if (($tv['user_id'] ?? '') === $user_id) { $tid = $tk; break; }
    }
    foreach ($quizzesDB as $qid => $q) {
        if (($q['teacher_id'] ?? '') === $tid) {
            $quizzes[] = $buildQuiz($qid, $q);
        }
    }
    foreach ($classesDB as $cid => $c) {
        $classes[] = ['id' => $cid, 'name' => $c['name'] ?? 'Class'];
    }
} elseif ($role === 'parent') {
    $parent_cids = [];
    foreach ($studentsDB as $s) {
        if (($s['parent_id'] ?? '') === $user_id) {
            $parent_cids[] = $s['class_id'] ?? '';
        }
    }
    foreach ($quizzesDB as $qid => $q) {
        if (in_array($q['class_id'] ?? '', $parent_cids)) {
            $quizzes[] = $buildQuiz($qid, $q);
        }
    }
}

usort($quizzes, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

$current_role = $role;
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes & Exams - Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard-premium.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="dashboard-content">
            <div class="page-title-bar">
                <div>
                    <span class="page-badge">Learning Hub</span>
                    <h2>Quizzes & Assessments 📝</h2>
                </div>
                <?php if ($role === 'teacher'): ?>
                    <button class="btn btn-primary" onclick="document.getElementById('createQuizModal').style.display='flex'">
                        <i class="fas fa-plus-circle"></i> Create New Quiz
                    </button>
                <?php endif; ?>
            </div>

            <?php if (isset($success)): ?>
                <div class="dash-alert success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
            <?php endif; ?>

            <div class="quiz-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="quiz-card" style="background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 24px; overflow: hidden; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative;">
                        <!-- Vibrant border top based on class -->
                        <div style="height: 6px; background: linear-gradient(to right, var(--primary), var(--secondary));"></div>
                        
                        <div style="padding: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <span style="background: rgba(255,123,84,0.1); color: var(--ps-orange); padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">
                                    <?= htmlspecialchars($quiz['class_name']) ?>
                                </span>
                                <span style="font-size: 0.7rem; color: var(--text-muted);">
                                    <?= date('M d, Y', strtotime($quiz['created_at'])) ?>
                                </span>
                            </div>
                            
                            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem; color: var(--text);"><?= htmlspecialchars($quiz['title']) ?></h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 1.5rem;">
                                <?= htmlspecialchars(substr($quiz['description'], 0, 100)) . (strlen($quiz['description']) > 100 ? '...' : '') ?>
                            </p>
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--glass-border); padding-top: 1rem;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--secondary); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.6rem;">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--text);">
                                        <?= htmlspecialchars($quiz['teacher_name'] ?? 'Assigned') ?>
                                    </span>
                                </div>
                                <a href="quiz-view.php?id=<?= $quiz['id'] ?>" class="btn btn-outline" style="padding: 6px 15px; font-size: 0.75rem;">
                                    <?= $role === 'teacher' ? 'View/Edit' : 'Take Quiz' ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($quizzes)): ?>
                    <div style="grid-column: 1/-1; text-align: center; padding: 4rem; background: var(--card-bg); border-radius: 24px; border: 2px dashed var(--glass-border);">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📔</div>
                        <h4 style="color: var(--text);">No quizzes found yet.</h4>
                        <p style="color: var(--text-muted);">Quizzes assigned to your classes will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Create Quiz Modal (Teacher Only) -->
            <?php if ($role === 'teacher'): ?>
                <div id="createQuizModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
                    <div style="background: var(--card-bg); padding: 2.5rem; border-radius: 32px; width: 500px; max-width: 90%; box-shadow: 0 25px 50px rgba(0,0,0,0.3); border: 1px solid var(--glass-border);">
                        <h3 style="margin-bottom: 1.5rem; font-weight: 800;">Create New Assessment 🎯</h3>
                        <form action="" method="POST">
                            <input type="hidden" name="create_quiz" value="1">
                            <div class="form-group">
                                <label>Quiz Title</label>
                                <input type="text" name="title" placeholder="e.g., Math Weekly Quiz #1" required>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" rows="3" placeholder="Brief topics covered..." required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Assign to Class</label>
                                <select name="class_id" required>
                                    <?php foreach ($classes as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                                <button type="submit" class="btn btn-primary" style="flex: 2;">Create Quiz & Add Questions</button>
                                <button type="button" class="btn btn-outline" style="flex: 1;" onclick="document.getElementById('createQuizModal').style.display='none'">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
