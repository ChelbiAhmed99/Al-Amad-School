<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('admin');
$current_role = 'admin';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $phone      = trim($_POST['phone'] ?? '');
        $specialty  = trim($_POST['specialty'] ?? '');
        
        if ($first_name && $last_name && $email) {
            try {
                // Check if email exists in Auth
                $auth->getUserByEmail($email);
                $message = "<div class='alert error'>Email already in use.</div>";
            } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
                try {
                    // Create user in Auth
                    $newUser = $auth->createUser([
                        'email'         => $email,
                        'emailVerified' => false,
                        'password'      => 'password123',
                        'displayName'   => "$first_name $last_name"
                    ]);
                    $user_id = $newUser->uid;
                    
                    // Create user profile in Realtime DB
                    $database->getReference('users/' . $user_id)->set([
                        'email'      => $email,
                        'role'       => 'teacher',
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'phone'      => $phone,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    
                    // Create teacher profile
                    $database->getReference('teachers')->push([
                        'user_id'    => $user_id,
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'phone'      => $phone,
                        'specialty'  => $specialty
                    ]);
                    
                    $message = "<div class='alert success'>Teacher added successfully! Default pass: password123</div>";
                } catch (Exception $e) {
                    $message = "<div class='alert error'>Failed: " . $e->getMessage() . "</div>";
                }
            }
        } else {
            $message = "<div class='alert error'>Please fill all required fields.</div>";
        }
    } elseif ($_POST['action'] === 'edit') {
        $teacher_id = $_POST['teacher_id'] ?? null;
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $phone      = trim($_POST['phone'] ?? '');
        $specialty  = trim($_POST['specialty'] ?? '');
        
        if ($teacher_id && $first_name && $last_name && $email) {
            try {
                // Get user_id first
                $tDoc = $database->getReference('teachers/' . $teacher_id)->getValue();
                if ($tDoc && isset($tDoc['user_id'])) {
                    $user_id = $tDoc['user_id'];
                    
                    // Update Auth email
                    $auth->changeUserEmail($user_id, $email);
                    
                    // Update User profile
                    $database->getReference('users/' . $user_id)->update([
                        'email'      => $email,
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'phone'      => $phone
                    ]);
                    
                    // Update Teacher profile
                    $database->getReference('teachers/' . $teacher_id)->update([
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'phone'      => $phone,
                        'specialty'  => $specialty
                    ]);
                    
                    $message = "<div class='alert success'>Teacher updated successfully!</div>";
                }
            } catch (Exception $e) {
                $message = "<div class='alert error'>Update failed: " . $e->getMessage() . "</div>";
            }
        }
    } elseif ($_POST['action'] === 'delete') {
        $delete_id = $_POST['teacher_id'] ?? null;
        $user_id_to_del = $_POST['user_id'] ?? null;
        if ($delete_id && $user_id_to_del) {
            try {
                $auth->deleteUser($user_id_to_del);
                $database->getReference('users/' . $user_id_to_del)->remove();
                $database->getReference('teachers/' . $delete_id)->remove();
                
                $message = "<div class='alert success'>Teacher and user account deleted successfully.</div>";
            } catch (Exception $e) {
                $message = "<div class='alert error'>Could not delete teacher: " . $e->getMessage() . "</div>";
            }
        }
    }
}

// Fetch Teachers
$teachersDB = $database->getReference('teachers')->getValue() ?: [];
$usersDB    = $database->getReference('users')->getValue() ?: [];

$teachers = [];
foreach ($teachersDB as $t_id => $t) {
    if (!$t) continue;
    $t['id'] = $t_id;
    $uid = $t['user_id'] ?? '';
    
    // Merge user fields manually
    $t['email']  = $usersDB[$uid]['email'] ?? '';
    $t['avatar'] = $usersDB[$uid]['avatar'] ?? null;
    
    $teachers[] = $t;
}

// Optional: sort by last name
usort($teachers, function($a, $b) {
    return strcmp($a['last_name'] ?? '', $b['last_name'] ?? '');
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
<script>if(localStorage.getItem('theme')==='light') document.documentElement.setAttribute('data-theme', 'light');</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
<?php include '../includes/sidebar.php'; ?>

<div class="dashboard-main">
    <?php
$dash_title = 'Teachers Management';
$dash_sub   = date('l, F j, Y');
$user_init  = 'AD';
include '../includes/dash-header.php';
?>

    <div class="dashboard-content">
        <?= $message ?>
        
        <!-- Premium Tabs Navigation -->
        <div class="dp-tabs-container reveal">
            <div class="dp-tabs-nav">
                <div class="dp-tab-indicator"></div>
                <button type="button" class="dp-tab-btn active" data-tab="tab-list"><i class="fas fa-chalkboard-teacher"></i> Active Staff</button>
                <button type="button" class="dp-tab-btn" data-tab="tab-add"><i class="fas fa-user-plus"></i> Recruit Teacher</button>
            </div>
        </div>

        <!-- Tab 2: Add Form -->
        <div class="dp-tab-content" id="tab-add">
            <div class="form-card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.35rem;"><i class="fas fa-user-plus"></i> Recruit Teacher</h3>
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
                    <div class="form-group">
                        <label>Specialty / Subject</label>
                        <input type="text" name="specialty">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem;">Add Teacher Account</button>
            </form>
            </div>
        </div>

        <!-- Tab 1: List (Active by default) -->
        <div class="dp-tab-content active" id="tab-list">
            <div class="table-card" style="margin-bottom: 2rem;">
                <div class="table-card-header">Active Teaching Staff</div>
                <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject / Specialty</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($teachers) > 0): ?>
                            <?php foreach($teachers as $t): ?>
                            <tr>
                                <td><strong>#<?= strtoupper(substr($t['id'], -5)) ?></strong></td>
                                <td style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; border: 1px solid var(--glass-border); flex-shrink: 0;">
                                        <img src="../<?= htmlspecialchars($t['avatar'] ?? 'assets/img/logo.png') ?>" alt="T" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <span><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?></span>
                                </td>
                                <td><span style="opacity: 0.8;"><?= htmlspecialchars($t['email']) ?></span></td>
                                <td><?= htmlspecialchars($t['phone'] ?? '—') ?></td>
                                <td><span style="color:var(--primary); font-weight: 500;"><?= htmlspecialchars($t['specialty'] ?? 'General') ?></span></td>
                                <td>
                                    <button class="btn btn-outline edit-btn" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" 
                                            onclick='openEditModal(<?= json_encode($t) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this teacher and their login account?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="teacher_id" value="<?= $t['id'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $t['user_id'] ?>">
                                        <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-color: rgba(239,68,68,0.5); color: #ef4444;"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding: 3rem;">No teachers registered yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update Teacher Profile</h3>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="teacher_id" id="edit_teacher_id">
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
                <label>Email (Login ID)</label>
                <input type="email" name="email" id="edit_email" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" id="edit_phone">
                </div>
                <div class="form-group">
                    <label>Specialty</label>
                    <input type="text" name="specialty" id="edit_specialty">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%; margin-top:1rem;">Save Changes</button>
        </form>
    </div>
</div>

<style>
.modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
.modal-content { background: var(--card-bg); padding: 2rem; border-radius: 20px; width: 100%; max-width: 500px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.close { cursor: pointer; font-size: 1.5rem; color: var(--text-muted); }
</style>

<script>
function openEditModal(teacher) {
    document.getElementById('edit_teacher_id').value = teacher.id;
    document.getElementById('edit_first_name').value = teacher.first_name;
    document.getElementById('edit_last_name').value = teacher.last_name;
    document.getElementById('edit_email').value = teacher.email;
    document.getElementById('edit_phone').value = teacher.phone;
    document.getElementById('edit_specialty').value = teacher.specialty;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) closeEditModal();
}
</script>
</body>
</html>
