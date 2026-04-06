<?php
/**
 * API: Notifications
 * GET  ?unread=1       → list unread notifications for session user
 * POST {action:'mark_read', id}  → marks one read
 * POST {action:'mark_all_read'}  → marks all read
 */
require_once '../includes/auth_check.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
    $notesDB = $database->getReference('notifications')->getValue() ?: [];
    
    $notes = [];
    $unread_count = 0;
    foreach ($notesDB as $n_id => $n) {
        if (($n['user_id'] ?? '') === $user_id) {
            $n['id'] = $n_id;
            $notes[] = $n;
            if (empty($n['is_read'])) {
                $unread_count++;
            }
        }
    }
    usort($notes, fn($a, $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));
    $notes = array_slice($notes, 0, $limit);

    echo json_encode(['success' => true, 'notifications' => $notes, 'unread_count' => $unread_count]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    $action = $body['action'] ?? '';

    if ($action === 'mark_read' && isset($body['id'])) {
        $database->getReference('notifications/' . $body['id'])->update(['is_read' => 1]);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($action === 'mark_all_read') {
        $notesDB = $database->getReference('notifications')->getValue() ?: [];
        $updates = [];
        foreach ($notesDB as $n_id => $n) {
            if (($n['user_id'] ?? '') === $user_id && empty($n['is_read'])) {
                $updates['notifications/' . $n_id . '/is_read'] = 1;
            }
        }
        if (!empty($updates)) {
            $database->getReference()->update($updates);
        }
        echo json_encode(['success' => true]);
        exit;
    }
    // Internal: create notification (called by other APIs)
    if ($action === 'create' && isset($body['target_user_id'])) {
        $newRef = $database->getReference('notifications')->push([
            'user_id' => $body['target_user_id'],
            'type' => $body['type'] ?? 'info',
            'title' => $body['title'] ?? 'Notification',
            'message' => $body['message'] ?? '',
            'link' => $body['link'] ?? null,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        echo json_encode(['success' => true, 'id' => $newRef->getKey()]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
