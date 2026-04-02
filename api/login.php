<?php
// api/login.php
header('Content-Type: application/json');

// Start session before anything else
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$data     = json_decode(file_get_contents('php://input'), true);
$email    = trim($data['email']    ?? '');
$password = trim($data['password'] ?? '');

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

try {
    require_once '../includes/db.php';

    $signInResult = $auth->signInWithEmailAndPassword($email, $password);
    $firebaseUserId = $signInResult->firebaseUserId();

    // Fetch user details from Realtime Database
    $userData = $database->getReference('users/' . $firebaseUserId)->getValue();
    
    if ($userData) {
        
        // Regenerate session ID to prevent fixation attacks
        session_regenerate_id(true);

        $_SESSION['user_id'] = $firebaseUserId; // Firebase UID is string
        $_SESSION['role']    = $userData['role'] ?? 'user';
        $_SESSION['email']   = $email;
        $_SESSION['name']    = ($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? '');

        // Ensure no-cache headers so back button won't show authenticated pages after logout
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $redirect = '../dashboard/index.php';
        if ($_SESSION['role'] === 'teacher') $redirect = '../dashboard/teacher.php';
        if ($_SESSION['role'] === 'parent')  $redirect = '../dashboard/parent.php';

        echo json_encode(['success' => true, 'redirect' => $redirect]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User profile not found in database.']);
    }

} catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
} catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
} catch (Exception $e) {
    $msg = $e->getMessage();
    if (str_contains($msg, 'INVALID_LOGIN_CREDENTIALS') || str_contains($msg, 'INVALID_PASSWORD')) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    } else {
        error_log('Login error: ' . $msg);
        echo json_encode(['success' => false, 'message' => 'A server error occurred. Please try again later.']);
    }
}
?>
