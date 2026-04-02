<?php
// api/messages.php — Full messaging API with notification triggers
header('Content-Type: application/json');
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('any');

$user_id = $_SESSION['user_id'];
$method  = $_SERVER['REQUEST_METHOD'];

// Extract IDs for contacts query
if ($method === 'GET') {
    if (isset($_GET['contacts'])) {
        $usersDB = $database->getReference('users')->getValue() ?: [];
        $messagesDB = $database->getReference('messages')->getValue() ?: [];
        
        $role = $_SESSION['role'];
        $contacts = [];
        
        foreach ($usersDB as $u_id => $u) {
            if ($u_id === $user_id) continue;
            
            if ($role === 'parent' && !in_array($u['role'] ?? '', ['admin', 'teacher'])) {
                continue;
            }
            
            // Calculate unread
            $unread_count = 0;
            foreach ($messagesDB as $m) {
                if (($m['sender_id'] ?? '') === $u_id && ($m['receiver_id'] ?? '') === $user_id && empty($m['is_read'])) {
                    $unread_count++;
                }
            }
            
            $contacts[] = [
                'id' => $u_id,
                'email' => $u['email'] ?? '',
                'role' => $u['role'] ?? '',
                'unread_count' => $unread_count
            ];
        }
        usort($contacts, fn($a, $b) => strcmp($a['role'], $b['role']) ?: strcmp($a['email'], $b['email']));
        
        echo json_encode(['success' => true, 'contacts' => $contacts]);
        exit;
    }

    if (isset($_GET['with_id'])) {
        $other_id = trim($_GET['with_id']);
        $messagesDB = $database->getReference('messages')->getValue() ?: [];
        $usersDB = $database->getReference('users')->getValue() ?: [];
        
        $thread = [];
        $updates = [];
        foreach ($messagesDB as $m_id => $m) {
            if (!$m) continue;
            $sid = $m['sender_id'] ?? '';
            $rid = $m['receiver_id'] ?? '';
            if (($sid === $other_id && $rid === $user_id) || ($sid === $user_id && $rid === $other_id)) {
                // Mark incoming as read
                if ($sid === $other_id && empty($m['is_read'])) {
                    $updates['messages/' . $m_id . '/is_read'] = 1;
                }
                $m['id'] = $m_id;
                $m['sender_email'] = $usersDB[$sid]['email'] ?? '';
                $thread[] = $m;
            }
        }
        if (!empty($updates)) {
            $database->getReference()->update($updates);
        }
        
        usort($thread, fn($a, $b) => strcmp($a['created_at'] ?? '', $b['created_at'] ?? ''));
        
        echo json_encode(['success' => true, 'messages' => $thread]);
        exit;
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $receiver_id = trim($data['receiver_id'] ?? '');
    $content = trim($data['content'] ?? '');

    if (!$receiver_id || $content === '') {
        echo json_encode(['success' => false, 'message' => 'Receiver and content are required.']);
        exit;
    }

    if ($receiver_id === $user_id) {
        echo json_encode(['success' => false, 'message' => 'You cannot message yourself.']);
        exit;
    }

    try {
        $database->getReference('messages')->push([
            'sender_id' => $user_id,
            'receiver_id' => $receiver_id,
            'content' => $content,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        $senderEmail = $database->getReference('users/' . $user_id . '/email')->getValue() ?: 'Someone';
        $senderName = explode('@', $senderEmail)[0];
        
        $database->getReference('notifications')->push([
            'user_id' => $receiver_id,
            'type' => 'message',
            'title' => 'New message from ' . $senderName,
            'message' => mb_substr($content, 0, 80) . (mb_strlen($content) > 80 ? '…' : ''),
            'link' => 'messages.php',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
}
?>
