<?php
/**
 * Al Amad School - Data Seeder (V2)
 * Populates the database with realistic demonstration data including ALL user avatars.
 */

require_once __DIR__ . '/../includes/db.php';

echo "🌱 Starting database seeding (Full Avatars Edition)...\n";

try {
    $pdo->beginTransaction();

    // 1. Ensure Classes exist
    $classes = [
        ['Primary 1A', 'Primary'], ['Primary 1B', 'Primary'],
        ['Primary 2', 'Primary'], ['Primary 3', 'Primary'],
        ['Middle 1', 'Middle'], ['Middle 2', 'Middle'],
        ['High 1', 'High'], ['High 2', 'High']
    ];

    foreach ($classes as $c) {
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO classes (name, level) VALUES (?, ?)");
        $stmt->execute($c);
    }
    $class_ids = $pdo->query("SELECT id FROM classes")->fetchAll(PDO::FETCH_COLUMN);

    // 2. Clear previous entries for clean demo
    $pdo->exec("DELETE FROM students; DELETE FROM teachers; DELETE FROM users WHERE role != 'admin';");

    // 3. Create Teachers with Avatars
    $teacher_data = [
        ['Amel', 'Ben Ali', 'amel.teacher@alamad.tn', 'Mathematics', 'female'],
        ['Sami', 'Trabelsi', 'sami.teacher@alamad.tn', 'Physics', 'male'],
        ['Leila', 'Gharbi', 'leila.teacher@alamad.tn', 'French', 'female'],
        ['Omar', 'Mansour', 'omar.teacher@alamad.tn', 'Arabic', 'male'],
        ['Youssef', 'Zied', 'youssef.teacher@alamad.tn', 'Physical Education', 'male'],
        ['Rania', 'Haddad', 'rania.teacher@alamad.tn', 'Biology', 'female']
    ];

    foreach ($teacher_data as $idx => $t) {
        $avatar = "https://i.pravatar.cc/150?u=teacher_" . $idx;
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role, gender, avatar) VALUES (?, ?, 'teacher', ?, ?)");
        $stmt->execute([$t[2], password_hash('password123', PASSWORD_DEFAULT), $t[4], $avatar]);
        $user_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO teachers (user_id, first_name, last_name, specialty) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $t[0], $t[1], $t[3]]);
    }

    // 4. Create Parents & Students with Avatars
    $first_names = ['Yassine', 'Mariem', 'Ahmed', 'Sarra', 'Fedi', 'Eya', 'Zied', 'Ines', 'Rayan', 'Lina', 'Koussay', 'Maya'];
    $last_names  = ['Abidi', 'Jlassi', 'Masmoudi', 'Karray', 'Dridi', 'Louati', 'Hammami', 'Ben Salem', 'Belhaj', 'Guesmi'];

    for ($i = 1; $i <= 10; $i++) {
        $fn_p = $first_names[array_rand($first_names)];
        $ln_p = $last_names[array_rand($last_names)];
        $p_email = "parent$i@demo.com";
        $p_avatar = "https://i.pravatar.cc/150?u=parent_" . $i;
        
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, role, avatar) VALUES (?, ?, 'parent', ?)");
        $stmt->execute([$p_email, password_hash('parent123', PASSWORD_DEFAULT), $p_avatar]);
        $parent_user_id = $pdo->lastInsertId();

        // Create 1-2 Students per parent
        $num_kids = rand(1, 2);
        for ($k = 0; $k < $num_kids; $k++) {
            $fn = $first_names[array_rand($first_names)];
            $ln = $last_names[array_rand($last_names)];
            $cid = $class_ids[array_rand($class_ids)];
            $gender = (rand(0, 1) == 0) ? 'male' : 'female';
            $age = rand(6, 17);
            $dob = date('Y-m-d', strtotime("-$age years -" . rand(0, 365) . " days"));
            
            $s_avatar = "https://i.pravatar.cc/150?u=student_" . uniqid();

            $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, dob, gender, parent_id, class_id, avatar) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fn, $ln, $dob, $gender, $parent_user_id, $cid, $s_avatar]);
            $student_id = $pdo->lastInsertId();

            // Add basic payments
            for ($m = 1; $m <= 3; $m++) {
                $status = (rand(0, 5) > 1) ? 'Paid' : 'Pending';
                $stmt = $pdo->prepare("INSERT INTO payments (student_id, amount, month, status) VALUES (?, 250, ?, ?)");
                $stmt->execute([$student_id, "Month $m 2026", $status]);
            }
        }
    }

    // 5. Update Admin Avatar (if exists)
    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE role = 'admin'");
    $stmt->execute(['https://i.pravatar.cc/150?u=admin_alamad']);

    $pdo->commit();
    echo "✅ Seeding V2 Completed! All users now have professional avatars.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "❌ Error during seeding: " . $e->getMessage() . "\n";
}
