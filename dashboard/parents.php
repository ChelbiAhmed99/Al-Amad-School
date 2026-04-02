<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('admin');
$current_role = 'admin';

// Handle POST
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($first_name && $last_name && $email) {
            try {
                // Check Auth
                $auth->getUserByEmail($email);
                $message = "<div class='alert error'>Email already in use.</div>";
            }
            catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                try {
                    $newUser = $auth->createUser([
                        'email' => $email,
                        'password' => 'password123',
                        'displayName' => "$first_name $last_name"
                    ]);
                    $user_id = $newUser->uid;

                    $database->getReference('users/' . $user_id)->set([
                        'email' => $email,
                        'role' => 'parent',
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'phone' => $phone,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $message = "<div class='alert success'>Parent account created! Default pass: password123</div>";
                }
                catch (Exception $ex) {
                    $message = "<div class='alert error'>Failed to create account: " . $ex->getMessage() . "</div>";
                }
            }
        }
        else {
            $message = "<div class='alert error'>Required fields missing.</div>";
        }
    }
    elseif ($_POST['action'] === 'edit') {
        $user_id = $_POST['user_id'] ?? null;
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($user_id && $first_name && $last_name && $email) {
            try {
                $auth->changeUserEmail($user_id, $email);
                $database->getReference('users/' . $user_id)->update([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone
                ]);
                $message = "<div class='alert success'>Parent updated successfully!</div>";
            }
            catch (Exception $e) {
                $message = "<div class='alert error'>Update failed: " . $e->getMessage() . "</div>";
            }
        }
    }
    elseif ($_POST['action'] === 'delete') {
        $user_id = $_POST['user_id'] ?? null;
        if ($user_id) {
            try {
                $auth->deleteUser($user_id);
                $database->getReference('users/' . $user_id)->remove();

                // Optional: remove parent_id reference from students? We can just leave it to fail gracefully

                $message = "<div class='alert success'>Parent account deleted.</div>";
            }
            catch (Exception $e) {
                $message = "<div class='alert error'>Could not delete account.</div>";
            }
        }
    }
}

// Fetch Parents and children count
$usersDB = $database->getReference('users')->getValue() ?: [];
$studentsDB = $database->getReference('students')->getValue() ?: [];

$parents = [];
foreach ($usersDB as $u_id => $u) {
    if (($u['role'] ?? '') === 'parent') {
        $u['id'] = $u_id;
        $u['children_count'] = 0;
        foreach ($studentsDB as $s) {
            if (($s['parent_id'] ?? null) === $u_id) {
                $u['children_count']++;
            }
        }
        $parents[] = $u;
    }
}
usort($parents, fn($a, $b) => strcmp($a['last_name'] ?? '', $b['last_name'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Parents - Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>if (localStorage.getItem('theme') === 'light') document.documentElement.setAttribute('data-theme', 'light');</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>

    <div class="dashboard-main">
        <?php
$dash_title = 'Parents Management';
$dash_sub = date('l, F j, Y');
$user_init = 'AD';
include '../includes/dash-header.php';
?>

        <div class="dashboard-content">
            <?= $message?>

            <!-- Premium Tabs Navigation -->
            <div class="dp-tabs-container reveal">
                <div class="dp-tabs-nav">
                    <div class="dp-tab-indicator"></div>
                    <button type="button" class="dp-tab-btn active" data-tab="tab-list"><i
                            class="fas fa-users-rectangle"></i> Registered Parents</button>
                    <button type="button" class="dp-tab-btn" data-tab="tab-add"><i class="fas fa-user-plus"></i>
                        Register Parent</button>
                </div>
            </div>

            <!-- Tab 2: Add Form -->
            <div class="dp-tab-content" id="tab-add">
                <div class="form-card" style="margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.35rem;"><i class="fas fa-user-plus"></i> Register
                        Parent</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="form-row">
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" name="first_name" required>
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" name="last_name" required>
                            </div>
                            <div class="form-group">
                                <label>Email (Login ID)</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Parent Account</button>
                    </form>
                </div>
            </div>

            <!-- Tab 1: List (Active by default) -->
            <div class="dp-tab-content active" id="tab-list">
                <div class="table-card" style="margin-bottom: 2rem;">
                    <div class="table-card-header">Registered Parents</div>
                    <div class="table-responsive">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Children</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($parents) > 0): ?>
                                <?php foreach ($parents as $p): ?>
                                <tr>
                                    <td><strong>
                                            <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name'])?>
                                        </strong></td>
                                    <td>
                                        <?= htmlspecialchars($p['email'])?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($p['phone'] ?? '—')?>
                                    </td>
                                    <td><span class="badge active">
                                            <?= $p['children_count']?> linked
                                        </span></td>
                                    <td>
                                        <button class="btn btn-outline"
                                            onclick='openEditModal(<?= json_encode($p)?>)'><i
                                                class="fas fa-edit"></i></button>
                                        <form method="POST" style="display:inline;"
                                            onsubmit="return confirm('Delete this parent?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?= $p['id']?>">
                                            <button type="submit" class="btn btn-outline" style="color:#ef4444;"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
    endforeach; ?>
                                <?php
else: ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding: 3rem;">No parents registered yet.
                                    </td>
                                </tr>
                                <?php
endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Parent</h3>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" id="edit_phone">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">Save Changes</button>
            </form>
        </div>
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .close {
            cursor: pointer;
            font-size: 1.5rem;
        }
    </style>

    <script>
        function openEditModal(parent) {
            document.getElementById('edit_user_id').value = parent.id;
            document.getElementById('edit_first_name').value = parent.first_name;
            document.getElementById('edit_last_name').value = parent.last_name;
            document.getElementById('edit_email').value = parent.email;
            document.getElementById('edit_phone').value = parent.phone;
            document.getElementById('editModal').style.display = 'flex';
        }
        function closeEditModal() { document.getElementById('editModal').style.display = 'none'; }
        window.onclick = function (event) { if (event.target == document.getElementById('editModal')) closeEditModal(); }
    </script>
</body>

</html>