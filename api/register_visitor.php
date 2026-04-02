<?php
// api/register_visitor.php
header('Content-Type: application/json');
require_once '../includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data provided.']);
    exit;
}

// Validate required fields
$required = ['parent_first_name', 'parent_last_name', 'parent_email', 'child_first_name', 'child_last_name', 'child_age', 'child_gender', 'payment_plan'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Field '$field' is required."]);
        exit;
    }
}

try {
    $visitorRef = $database->getReference('visitor_requests')->push([
        'parent_first_name' => $data['parent_first_name'],
        'parent_last_name' => $data['parent_last_name'],
        'parent_email' => $data['parent_email'],
        'child_first_name' => $data['child_first_name'],
        'child_last_name' => $data['child_last_name'],
        'child_age' => (int)$data['child_age'],
        'child_gender' => $data['child_gender'],
        'payment_plan' => $data['payment_plan'],
        'created_at' => date('Y-m-d H:i:s'),
        'status' => 'pending'
    ]);

    echo json_encode(['success' => true, 'message' => 'Registration submitted successfully.', 'request_id' => $visitorRef->getKey()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
