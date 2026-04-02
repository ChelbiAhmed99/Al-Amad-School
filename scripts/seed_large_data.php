<?php
// scripts/seed_large_data.php
require_once __DIR__ . '/../includes/db.php';

echo "Starting Professional Large-Scale Data Seeding with Admin Account...\n";

// Helper functions for random data
function getRandomName($gender = 'male') {
    $males = ['Mohamed', 'Ahmed', 'Yassine', 'Sami', 'Hichem', 'Ali', 'Omar', 'Khaled', 'Mourad', 'Zied', 'Walid', 'Fares', 'Nabil', 'Sofiene', 'Karim', 'Habib', 'Tarek', 'Adel', 'Riadh', 'Mounir'];
    $females = ['Olfa', 'Sarra', 'Ines', 'Meryem', 'Amel', 'Leila', 'Fatma', 'Sonia', 'Rym', 'Hela', 'Houda', 'Zohra', 'Noura', 'Salma', 'Donia', 'Khadija', 'Basma', 'Aicha', 'Monia'];
    $lastNames = ['Ben Ali', 'Gammoudi', 'Trabelsi', 'Mansouri', 'Ayadi', 'Dridi', 'Mejri', 'Said', 'Abidi', 'Jaziri', 'Hammami', 'Gharbi', 'Selmi', 'Louati', 'Karray', 'Rekik', 'Ellouze', 'Bouaziz', 'Chaari'];
    
    $firstName = ($gender == 'male') ? $males[array_rand($males)] : $females[array_rand($females)];
    $lastName = $lastNames[array_rand($lastNames)];
    
    return [$firstName, $lastName];
}

function getRandomSpecialty() {
    $specialties = ['Mathematics', 'Physics', 'Arabic', 'French', 'English', 'History', 'Geography', 'Science', 'Physical Education', 'Arts', 'Philosophy', 'Information Technology'];
    return $specialties[array_rand($specialties)];
}

try {
    // 0. Clear Existing Data
    echo "Clearing existing data...\n";
    $database->getReference('users')->remove();
    $database->getReference('teachers')->remove();
    $database->getReference('classes')->remove();
    $database->getReference('students')->remove();
    $database->getReference('attendance')->remove();
    $database->getReference('grades')->remove();
    $database->getReference('payments')->remove();
    $database->getReference('announcements')->remove();
    echo "Data cleared.\n";

    // 1. Create Single Admin
    echo "Creating Admin Account...\n";
    $adminEmail = 'olfagammoudi5@gmail.com';
    try {
        $adminAuth = $auth->getUserByEmail($adminEmail);
        $auth->deleteUser($adminAuth->uid);
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {}

    $adminAuth = $auth->createUser([
        'email' => $adminEmail,
        'emailVerified' => true,
        'password' => 'password123',
        'displayName' => 'Olfa Gammoudi',
    ]);

    $database->getReference('users/' . $adminAuth->uid)->set([
        'email' => $adminEmail,
        'role' => 'admin',
        'first_name' => 'Olfa',
        'last_name' => 'Gammoudi',
        'phone' => '+21697431801',
        'gender' => 'female',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    echo "Admin created.\n";

    // 2. Seed Teachers
    echo "Creating Teachers...\n";
    $teacherKeys = [];
    for ($i = 1; $i <= 20; $i++) {
        [$fn, $ln] = getRandomName($i % 2 == 0 ? 'female' : 'male');
        $email = strtolower($fn . "." . str_replace(' ', '', $ln)) . "@alamad.edu";
        
        try {
            $userAuth = $auth->getUserByEmail($email);
            $auth->deleteUser($userAuth->uid);
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {}

        $userAuth = $auth->createUser([
            'email' => $email,
            'password' => 'password123',
            'displayName' => "$fn $ln",
        ]);

        $database->getReference('users/' . $userAuth->uid)->set([
            'email' => $email,
            'role' => 'teacher',
            'first_name' => $fn,
            'last_name' => $ln,
            'phone' => '55 ' . rand(100, 999) . ' ' . rand(100, 999),
            'gender' => $i % 2 == 0 ? 'female' : 'male',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $teacherRef = $database->getReference('teachers')->push([
            'user_id' => $userAuth->uid,
            'first_name' => $fn,
            'last_name' => $ln,
            'phone' => '55 ' . rand(100, 999) . ' ' . rand(100, 999),
            'specialty' => getRandomSpecialty()
        ]);
        $teacherKeys[] = $teacherRef->getKey();
        echo ".";
    }
    echo "\nTeachers created.\n";

    // 3. Seed Classes
    echo "Creating Classes...\n";
    $classKeys = [];
    $levels = ['Primary', 'Middle', 'Secondary'];
    for ($i = 1; $i <= 10; $i++) {
        $level = $levels[array_rand($levels)];
        $classRef = $database->getReference('classes')->push([
            'name' => "Grade " . ceil($i/2) . (chr(64 + ($i % 2 == 0 ? 2 : 1))), 
            'level' => $level,
            'head_teacher_id' => $teacherKeys[array_rand($teacherKeys)]
        ]);
        $classKeys[] = $classRef->getKey();
        echo ".";
    }
    echo "\nClasses created.\n";

    // 4. Seed Parents
    echo "Creating Professional Parent Accounts...\n";
    $parentUids = [];
    for ($i = 1; $i <= 50; $i++) {
        [$fn, $ln] = getRandomName($i % 2 == 0 ? 'female' : 'male');
        $email = strtolower($fn . "." . str_replace(' ', '', $ln)) . rand(10, 99) . "@gmail.com";
        
        try {
            $userAuth = $auth->getUserByEmail($email);
            $auth->deleteUser($userAuth->uid);
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {}

        $userAuth = $auth->createUser([
            'email' => $email,
            'password' => 'password123',
            'displayName' => "$fn $ln",
        ]);

        $database->getReference('users/' . $userAuth->uid)->set([
            'email' => $email,
            'role' => 'parent',
            'first_name' => $fn,
            'last_name' => $ln,
            'phone' => '98 ' . rand(100, 999) . ' ' . rand(100, 999),
            'gender' => $i % 2 == 0 ? 'female' : 'male',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $parentUids[] = $userAuth->uid;
        echo ".";
    }
    echo "\nParents created.\n";

    // 5. Seed Students
    echo "Creating Students (this may take a while)...\n";
    $studentKeys = [];
    for ($i = 1; $i <= 100; $i++) {
        $isMale = rand(0, 1) == 1;
        [$fn, $ln] = getRandomName($isMale ? 'male' : 'female');
        $parentId = $parentUids[array_rand($parentUids)];
        $classId = $classKeys[array_rand($classKeys)];
        
        $dobYear = rand(2010, 2018);
        $dob = "$dobYear-" . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . "-" . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

        $studentRef = $database->getReference('students')->push([
            'first_name' => $fn,
            'last_name' => $ln,
            'dob' => $dob,
            'gender' => $isMale ? 'male' : 'female',
            'parent_id' => $parentId,
            'class_id' => $classId,
            'status' => 'Active'
        ]);
        $sKey = $studentRef->getKey();
        $studentKeys[] = $sKey;

        // 6. Seed Attendance (Last 20 school days)
        for ($d = 1; $d <= 20; $d++) {
            $date = date('Y-m-d', strtotime("-$d days"));
            if (date('N', strtotime($date)) >= 6) continue;
            
            $database->getReference('attendance')->push([
                'student_id' => $sKey,
                'date' => $date,
                'status' => rand(0, 10) > 1 ? 'Present' : 'Absent',
                'justified' => rand(0, 1) == 1
            ]);
        }

        // 7. Seed Grades (6-10 subjects)
        $subjectsCount = rand(6, 10);
        $subjects = ['Mathematics', 'Arabic', 'French', 'Science', 'History', 'Physics', 'English', 'Geography', 'Philosophy', 'IT', 'Arts', 'Sports'];
        for ($s = 0; $s < $subjectsCount; $s++) {
            $database->getReference('grades')->push([
                'student_id' => $sKey,
                'subject' => $subjects[$s % count($subjects)],
                'score' => rand(70, 198) / 10, 
                'term' => 'Term 1',
                'teacher_id' => $teacherKeys[array_rand($teacherKeys)],
                'date' => date('Y-m-d H:i:s', strtotime("-" . rand(1, 40) . " days"))
            ]);
        }

        // 8. Seed Payments (All year 2024 monthly)
        for ($m = 1; $m <= 4; $m++) {
            $period = "2024-0" . $m;
            $database->getReference('payments')->push([
                'student_id' => $sKey,
                'amount' => rand(150, 450),
                'payment_type' => 'Monthly',
                'period' => $period,
                'status' => rand(0, 10) > 1 ? 'Paid' : 'Pending',
                'date' => date('Y-m-d H:i:s', strtotime("-$m month"))
            ]);
        }
        echo ".";
    }
    echo "\nStudents and related data created.\n";

    // 9. Seed Announcements
    echo "Creating Announcements...\n";
    $announcements = [
        ['title' => 'End of Term Exams', 'content' => 'The end of term exams will start on April 15th. Please prepare your children.', 'target' => 'parent'],
        ['title' => 'Faculty Workshop', 'content' => 'Pedagogic training workshop this Friday at 2 PM.', 'target' => 'teacher'],
        ['title' => 'Ramadan Working Hours', 'content' => 'School hours will be adjusted for Ramadan. Check the portal for details.', 'target' => 'all'],
        ['title' => 'Sports Tournament', 'content' => 'Interschool tournament starts next week. Go Team Al Amad!', 'target' => 'all'],
        ['title' => 'Grade Submission Deadline', 'content' => 'All teachers must submit Term 1 grades by end of this week.', 'target' => 'teacher'],
        ['title' => 'Professional Platform Update', 'content' => 'The Al Amad Management System is now fully populated with professional data for school operation.', 'target' => 'all'],
    ];

    foreach ($announcements as $a) {
        $database->getReference('announcements')->push([
            'title' => $a['title'],
            'content' => $a['content'],
            'target_role' => $a['target'],
            'date' => date('Y-m-d H:i:s')
        ]);
        echo ".";
    }
    echo "\nAnnouncements created.\n";

    echo "\nScale-Up Professional Data Seeding Completed Successfully!\n";

} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
    exit(1);
}
