<?php
// api/validate_visitor.php
// Admin action: approve a visitor request → creates parent user + student record
header('Content-Type: application/json');
require_once '../includes/auth_check.php';  // must be admin
require_once '../includes/db.php';
checkRole('admin');

$data = json_decode(file_get_contents('php://input'), true);
$id   = trim($data['id'] ?? '');

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Request ID is required.']);
    exit;
}

try {
    // 1. Fetch request details from Firebase
    $request = $database->getReference('visitor_requests/' . $id)->getValue();

    if (!$request || ($request['status'] ?? '') !== 'Pending' && ($request['status'] ?? '') !== 'pending') {
        throw new Exception("Request not found or already processed.");
    }

    // 2. Check if email is already registered in Firebase Auth
    try {
        $auth->getUserByEmail($request['parent_email']);
        throw new Exception("A user with this email already exists.");
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        // Clear to proceed
    }

    // 3. Create Parent User account in Firebase Auth
    $newParent = $auth->createUser([
        'email' => $request['parent_email'],
        'password' => 'Welcome@123',
        'displayName' => $request['parent_first_name'] . ' ' . $request['parent_last_name']
    ]);
    $parentUserId = $newParent->uid;

    // 4. Create Parent profile in Realtime Database
    $database->getReference('users/' . $parentUserId)->set([
        'email' => $request['parent_email'],
        'role' => 'parent',
        'first_name' => $request['parent_first_name'],
        'last_name' => $request['parent_last_name'],
        'gender' => $request['child_gender'] ?? 'other',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    // 5. Create Student record in Realtime Database
    $dob = (date('Y') - (int)$request['child_age']) . '-01-01';
    $database->getReference('students')->push([
        'first_name' => $request['child_first_name'],
        'last_name' => $request['child_last_name'],
        'dob' => $dob,
        'gender' => $request['child_gender'] ?? 'other',
        'parent_id' => $parentUserId,
        'status' => 'Active',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    // 6. Mark request as Approved in Firebase
    $database->getReference('visitor_requests/' . $id)->update(['status' => 'Approved']);

    // 7. Send welcome notification to the new parent
    $database->getReference('notifications')->push([
        'user_id' => $parentUserId,
        'type' => 'success',
        'title' => 'Welcome to Al Amad School! 🎉',
        'message' => 'Your registration has been approved. Your child ' . $request['child_first_name'] . ' is now enrolled. Login with your email and password: Welcome@123',
        'link' => '../dashboard/parent.php',
        'is_read' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    echo json_encode([
        'success'      => true,
        'message'      => 'Parent account and student record created successfully.',
        'parent_email' => $request['parent_email'],
        'temp_password' => 'Welcome@123'
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
