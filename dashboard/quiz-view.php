<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('any');

$quiz_id = $_GET['id'] ?? null;
if (!$quiz_id) { header('Location: quizzes.php'); exit; }

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch Quiz & Questions
$quizRaw = $database->getReference('quizzes/' . $quiz_id)->getValue();
if (!$quizRaw) { header('Location: quizzes.php'); exit; }

$quiz = $quizRaw;
$quiz['id'] = $quiz_id;

$classesDB = $database->getReference('classes')->getValue() ?: [];
$teachersDB = $database->getReference('teachers')->getValue() ?: [];

$cid = $quiz['class_id'] ?? '';
$tid = $quiz['teacher_id'] ?? '';
$quiz['class_name'] = isset($classesDB[$cid]) ? ($classesDB[$cid]['name'] ?? 'Unknown') : 'Unassigned';
if (isset($teachersDB[$tid])) {
    $quiz['teacher_name'] = ($teachersDB[$tid]['first_name'] ?? '') . ' ' . ($teachersDB[$tid]['last_name'] ?? '');
} else {
    $quiz['teacher_name'] = 'Unknown Teacher';
}

$questionsDB = $database->getReference('quiz_questions')->getValue() ?: [];
$questions = [];
foreach ($questionsDB as $qid => $q) {
    if (($q['quiz_id'] ?? '') === $quiz_id) {
        $q['id'] = $qid;
        $questions[] = $q;
    }
}

// Handle Submission (Mocked)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $success_msg = "Your quiz has been submitted successfully! ✨ Great job!";
}

$current_role = $role;
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['title']) ?> - Al Amad School</title>
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
                    <span class="page-badge"><?= htmlspecialchars($quiz['class_name']) ?> Assessment</span>
                    <h2><?= htmlspecialchars($quiz['title']) ?> 📝</h2>
                </div>
                <a href="quizzes.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Quizzes
                </a>
            </div>

            <?php if (isset($success_msg)): ?>
                <div class="dash-alert success"><i class="fas fa-check-circle"></i> <?= $success_msg ?></div>
            <?php else: ?>
                <div style="max-width: 800px; margin-top: 2rem;">
                    <div style="background: var(--card-bg); border-radius: 24px; padding: 2rem; border: 1px solid var(--glass-border); margin-bottom: 2rem; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; right: 0; padding: 1rem; background: var(--primary); color: white; border-bottom-left-radius: 20px; font-weight: 700;">
                            <?= count($questions) ?> Questions
                        </div>
                        <h4 style="margin-bottom: 1rem; color: var(--text);">Instructions:</h4>
                        <p style="color: var(--text-muted); line-height: 1.6;"><?= htmlspecialchars($quiz['description']) ?></p>
                        <div style="margin-top: 1.5rem; display: flex; align-items: center; gap: 10px; font-size: 0.85rem; color: var(--text);">
                            <span style="font-weight: 700;">Teacher:</span> <?= htmlspecialchars($quiz['teacher_name']) ?>
                        </div>
                    </div>

                    <form action="" method="POST">
                        <input type="hidden" name="submit_quiz" value="1">
                        <?php foreach ($questions as $idx => $q): ?>
                            <div style="background: var(--card-bg); border-radius: 20px; padding: 2rem; border: 1px solid var(--glass-border); margin-bottom: 1.5rem;">
                                <h4 style="margin-bottom: 1.5rem; font-weight: 800; color: var(--text);">Question <?= $idx + 1 ?>: <?= htmlspecialchars($q['question']) ?></h4>
                                <div class="options-group" style="display: flex; flex-direction: column; gap: 1rem;">
                                    <label style="display: flex; align-items: center; gap: 12px; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 12px; cursor: pointer; transition: all 0.2s;">
                                        <input type="radio" name="q<?= $q['id'] ?>" value="a" required> 
                                        <span>A) <?= htmlspecialchars($q['option_a']) ?></span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 12px; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 12px; cursor: pointer; transition: all 0.2s;">
                                        <input type="radio" name="q<?= $q['id'] ?>" value="b"> 
                                        <span>B) <?= htmlspecialchars($q['option_b']) ?></span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 12px; padding: 1rem; border: 2px solid var(--glass-border); border-radius: 12px; cursor: pointer; transition: all 0.2s;">
                                        <input type="radio" name="q<?= $q['id'] ?>" value="c"> 
                                        <span>C) <?= htmlspecialchars($q['option_c']) ?></span>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div style="text-align: right; margin-top: 2rem; margin-bottom: 4rem;">
                            <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem; border-radius: 50px;">
                                <i class="fas fa-paper-plane"></i> Submit Assessment
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <style>
    .options-group label:has(input:checked) {
        border-color: var(--primary);
        background: rgba(var(--primary-rgb), 0.05);
        transform: translateX(10px);
    }
    </style>
</body>
</html>
