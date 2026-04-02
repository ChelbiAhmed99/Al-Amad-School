<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('admin');
$current_role = 'admin';

// Handle POST actions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name'] ?? '');
        $dob        = $_POST['dob'] ?? '';
        $gender     = $_POST['gender'] ?? 'male';
        $class_id   = !empty($_POST['class_id']) ? $_POST['class_id'] : null;
        $parent_id  = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
        
        if ($first_name && $last_name && $dob) {
            try {
                $database->getReference('students')->push([
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'dob'        => $dob,
                    'gender'     => $gender,
                    'class_id'   => $class_id,
                    'parent_id'  => $parent_id,
                    'status'     => 'Active',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $message = "<div class='alert success'>Student $first_name $last_name added successfully!</div>";
            } catch (Exception $e) {
                $message = "<div class='alert error'>Failed to add student: " . $e->getMessage() . "</div>";
            }
        } else {
            $message = "<div class='alert error'>Please fill all required fields.</div>";
        }
    } elseif ($_POST['action'] === 'edit') {
        $student_id = $_POST['student_id'] ?? null;
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name'] ?? '');
        $dob        = $_POST['dob'] ?? '';
        $gender     = $_POST['gender'] ?? 'male';
        $class_id   = !empty($_POST['class_id']) ? $_POST['class_id'] : null;
        $parent_id  = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;
        $status     = trim($_POST['status'] ?? 'Active');
        
        if ($student_id && $first_name && $last_name && $dob) {
            try {
                $database->getReference('students/' . $student_id)->update([
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'dob'        => $dob,
                    'gender'     => $gender,
                    'class_id'   => $class_id,
                    'parent_id'  => $parent_id,
                    'status'     => $status
                ]);
                $message = "<div class='alert success'>Student updated successfully!</div>";
            } catch (Exception $e) {
                $message = "<div class='alert error'>Update failed: " . $e->getMessage() . "</div>";
            }
        }
    } elseif ($_POST['action'] === 'delete') {
        $delete_id = $_POST['student_id'] ?? null;
        if ($delete_id) {
            try {
                $database->getReference('students/' . $delete_id)->remove();
                $message = "<div class='alert success'>Student removed completely.</div>";
            } catch (Exception $e) {
                $message = "<div class='alert error'>Could not delete student: " . $e->getMessage() . "</div>";
            }
        }
    }
}

// Fetch Data from Firebase
$studentsDB = $database->getReference('students')->getValue() ?: [];
$classesDB  = $database->getReference('classes')->getValue() ?: [];
$usersDB    = $database->getReference('users')->getValue() ?: [];

// Process Classes for dropdown
$classes = [];
foreach ($classesDB as $c_id => $c) {
    if (!$c) continue;
    $c['id'] = $c_id;
    $classes[] = $c;
}
usort($classes, fn($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''));

// Process Parents for dropdown
$parents = [];
foreach ($usersDB as $u_id => $u) {
    if (($u['role'] ?? '') === 'parent') {
        $u['id'] = $u_id;
        $parents[] = $u;
    }
}
usort($parents, fn($a, $b) => strcmp($a['last_name'] ?? '', $b['last_name'] ?? ''));

// Map students and inject relational data
$students = [];
foreach ($studentsDB as $s_id => $s) {
    if (!$s) continue;
    $s['id'] = $s_id;
    
    // Join Class
    $cid = $s['class_id'] ?? null;
    $s['class_name'] = $cid ? ($classesDB[$cid]['name'] ?? 'Unknown Class') : '—';
    
    // Join Parent
    $pid = $s['parent_id'] ?? null;
    if ($pid && isset($usersDB[$pid])) {
        $s['parent_email'] = $usersDB[$pid]['email'] ?? '';
        $s['parent_fname'] = $usersDB[$pid]['first_name'] ?? '';
        $s['parent_lname'] = $usersDB[$pid]['last_name'] ?? '';
    } else {
        $s['parent_email'] = $s['parent_fname'] = $s['parent_lname'] = null;
    }
    
    $students[] = $s;
}

// Sort students by last name
usort($students, fn($a, $b) => strcmp($a['last_name'] ?? '', $b['last_name'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Al Amad School</title>
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
$dash_title = 'Students Management';
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
                <button type="button" class="dp-tab-btn active" data-tab="tab-list"><i class="fas fa-users"></i> All Students</button>
                <button type="button" class="dp-tab-btn" data-tab="tab-add"><i class="fas fa-user-plus"></i> Enroll Student</button>
            </div>
        </div>

        <!-- Tab 1: List (Active by default) -->
        <div class="dp-tab-content active" id="tab-list">
            <div class="table-card" style="margin-bottom: 2rem;">
                <div class="table-card-header">Enrolled Students Overview</div>
                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Date of Birth</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach($students as $s): ?>
                        <tr>
                            <td><strong>#<?= strtoupper(substr($s['id'], -5)) ?></strong></td>
                            <td style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; overflow: hidden; border: 1px solid var(--glass-border); flex-shrink: 0;">
                                    <img src="../<?= htmlspecialchars($s['avatar'] ?? 'assets/img/logo.png') ?>" alt="S" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <span><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></span>
                            </td>
                            <td><?= date('M d, Y', strtotime($s['dob'])) ?></td>
                            <td><?= htmlspecialchars($s['class_name'] ?? '—') ?></td>
                            <td>
                                <div><span class="badge <?= strtolower($s['status'] ?? '') == 'active' ? 'active' : '' ?>"><?= htmlspecialchars($s['status'] ?? 'Active') ?></span></div>
                                <small style="display:block; margin-top:4px; opacity:.6;"><?= htmlspecialchars($s['parent_lname'] ?? 'No Parent') ?></small>
                            </td>
                            <td>
                                <button class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;" 
                                        onclick='openEditModal(<?= json_encode($s) ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this student? This is permanent.');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="student_id" value="<?= $s['id'] ?>">
                                    <button type="submit" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; border-color: rgba(239,68,68,0.5); color: #ef4444;"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding: 3rem;">No students enrolled yet.</td></tr>
                    <?php endif; ?>
                </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Add Student Form -->
        <div class="dp-tab-content" id="tab-add">
            <div class="form-card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem; font-size: 1.35rem;"><i class="fas fa-user-plus"></i> Enroll New Student</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" placeholder="E.g. Ahmed" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" placeholder="E.g. Mansour" required>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Assign to Class</label>
                            <select name="class_id">
                                <option value="">-- Select Class (Optional) --</option>
                                <?php foreach($classes as $cls): ?>
                                <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['name']) ?> (<?= $cls['level'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Parent / Guardian</label>
                            <select name="parent_id">
                                <option value="">-- Select Parent (Optional) --</option>
                                <?php foreach($parents as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?> (<?= $p['email'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem;"><i class="fas fa-check-circle"></i> Complete Enrollment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update Student Profile</h3>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="student_id" id="edit_student_id">
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
            <div class="form-row">
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" id="edit_dob" required>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" id="edit_gender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Class</label>
                    <select name="class_id" id="edit_class_id">
                        <option value="">-- Unassigned --</option>
                        <?php foreach($classes as $cls): ?>
                        <option value="<?= $cls['id'] ?>"><?= htmlspecialchars($cls['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Parent</label>
                    <select name="parent_id" id="edit_parent_id">
                        <option value="">-- No Parent --</option>
                        <?php foreach($parents as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_status">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Suspended">Suspended</option>
                </select>
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
function openEditModal(student) {
    document.getElementById('edit_student_id').value = student.id;
    document.getElementById('edit_first_name').value = student.first_name;
    document.getElementById('edit_last_name').value = student.last_name;
    document.getElementById('edit_dob').value = student.dob;
    document.getElementById('edit_gender').value = student.gender || 'male';
    document.getElementById('edit_class_id').value = student.class_id || '';
    document.getElementById('edit_parent_id').value = student.parent_id || '';
    document.getElementById('edit_status').value = student.status || 'Active';
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
