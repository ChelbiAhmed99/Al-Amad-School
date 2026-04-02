<?php
// setup_db.php
require_once 'includes/db.php';

echo "Initializing Firebase Realtime Database Migration...\n\n";

try {
    echo "Creating/Fetching Users in Firebase Auth & Realtime DB...\n";

    // 1. Insert Admin
    try {
        $adminAuth = $auth->getUserByEmail('olfagammoudi5@gmail.com');
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        $adminAuth = $auth->createUser([
            'email' => 'olfagammoudi5@gmail.com',
            'emailVerified' => true,
            'password' => 'password123',
            'displayName' => 'Olfa Gammoudi',
        ]);
    }
    
    $database->getReference('users/' . $adminAuth->uid)->set([
        'email' => 'olfagammoudi5@gmail.com',
        'role' => 'admin',
        'first_name' => 'Olfa',
        'last_name' => 'Gammoudi',
        'phone' => '+21697431801',
        'gender' => 'female',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    echo "- Admin seeded.\n";

    // 2. Insert Teacher
    try {
        $teacherAuth = $auth->getUserByEmail('teacher@alamad.edu');
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        $teacherAuth = $auth->createUser([
            'email' => 'teacher@alamad.edu',
            'emailVerified' => true,
            'password' => 'password123',
            'displayName' => 'Ahmed Ben Ali',
        ]);
    }

    $database->getReference('users/' . $teacherAuth->uid)->set([
        'email' => 'teacher@alamad.edu',
        'role' => 'teacher',
        'first_name' => 'Ahmed',
        'last_name' => 'Ben Ali',
        'phone' => '55 123 456',
        'gender' => 'male',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    // Clear teachers list initially
    $database->getReference('teachers')->remove();

    $teacherRef = $database->getReference('teachers')->push([
        'user_id' => $teacherAuth->uid,
        'first_name' => 'Ahmed',
        'last_name' => 'Ben Ali',
        'phone' => '55 123 456',
        'specialty' => 'Mathematics'
    ]);
    $teacherKey = $teacherRef->getKey();
    echo "- Teacher seeded.\n";

    // 3. Insert Parent
    try {
        $parentAuth = $auth->getUserByEmail('parent@alamad.edu');
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        $parentAuth = $auth->createUser([
            'email' => 'parent@alamad.edu',
            'emailVerified' => true,
            'password' => 'password123',
            'displayName' => 'Parent User',
        ]);
    }

    $database->getReference('users/' . $parentAuth->uid)->set([
        'email' => 'parent@alamad.edu',
        'role' => 'parent',
        'first_name' => 'Parent',
        'last_name' => 'User',
        'gender' => 'female',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    echo "- Parent seeded.\n";

    // 4. Create Class
    $database->getReference('classes')->remove();
    $database->getReference('students')->remove();
    $database->getReference('attendance')->remove();
    $database->getReference('grades')->remove();
    $database->getReference('payments')->remove();
    $database->getReference('announcements')->remove();

    $classRef = $database->getReference('classes')->push([
        'name' => 'Grade 5A',
        'level' => 'Primary',
        'head_teacher_id' => $teacherKey
    ]);
    echo "- Class created.\n";

    // 5. Create Students linked to Parent and Class
    $student1Ref = $database->getReference('students')->push([
        'first_name' => 'Yessine',
        'last_name' => 'Ben Ali',
        'dob' => '2015-05-14',
        'gender' => 'male',
        'parent_id' => $parentAuth->uid,
        'class_id' => $classRef->getKey(),
        'status' => 'Active'
    ]);

    $student2Ref = $database->getReference('students')->push([
        'first_name' => 'Sarra',
        'last_name' => 'Ben Ali',
        'dob' => '2017-08-20',
        'gender' => 'female',
        'parent_id' => $parentAuth->uid,
        'class_id' => $classRef->getKey(),
        'status' => 'Active'
    ]);
    echo "- Students created.\n";

    // 6. Seed Attendance, Grades, Payments
    $database->getReference('attendance')->push([
        'student_id' => $student1Ref->getKey(),
        'date' => date('Y-m-d', strtotime('-1 day')),
        'status' => 'Present',
        'justified' => true
    ]);

    $database->getReference('grades')->push([
        'student_id' => $student1Ref->getKey(),
        'subject' => 'Mathematics',
        'score' => 18.5,
        'term' => 'Term 1',
        'teacher_id' => $teacherKey,
        'date' => date('Y-m-d H:i:s')
    ]);

    $database->getReference('payments')->push([
        'student_id' => $student1Ref->getKey(),
        'amount' => 200,
        'payment_type' => 'Monthly',
        'period' => '2024-01',
        'status' => 'Paid',
        'date' => date('Y-m-d H:i:s')
    ]);

    $database->getReference('announcements')->push([
        'title' => 'Welcome Back!',
        'content' => 'The new semester has started. Please check the timetable.',
        'target_role' => 'all',
        'date' => date('Y-m-d H:i:s')
    ]);

    echo "\nFirebase Demo data seeded successfully! You can now log in.\n";

} catch (Exception $e) {
    echo "Setup failed:\n";
    echo $e;
    echo "\n";
    exit(1);
}
?>
