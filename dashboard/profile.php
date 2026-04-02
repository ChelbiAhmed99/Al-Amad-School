<?php
require_once '../includes/auth_check.php';
require_once '../includes/db.php';
checkRole('any'); // Accessible by admin, teacher, or parent

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Fetch current user details
$user = $database->getReference('users/' . $user_id)->getValue() ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dob = $_POST['dob'] ?? ($user['dob'] ?? null);
    $gender = $_POST['gender'] ?? ($user['gender'] ?? null);
    $new_password = $_POST['new_password'] ?? '';
    
    try {
        // Handle Avatar Upload
        $avatar_path = $user['avatar'] ?? null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['avatar']['tmp_name'];
            $file_name = $_FILES['avatar']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_ext, $allowed)) {
                $new_file_name = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
                $upload_dir = '../assets/uploads/avatars/';
                if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                    $avatar_path = 'assets/uploads/avatars/' . $new_file_name;
                    $_SESSION['avatar'] = $avatar_path; // Update session
                }
            } else {
                throw new Exception("Invalid file type. Only JPG, PNG, and GIF allowed.");
            }
        }

        // Update Auth properties if password changed
        if (!empty($new_password)) {
            $auth->changeUserPassword($user_id, $new_password);
        }

        // Update Realtime DB Details
        $updates = [
            'dob' => $dob,
            'gender' => $gender,
            'avatar' => $avatar_path
        ];
        $database->getReference('users/' . $user_id)->update($updates);

        $success = "Profile updated successfully! ✨";
        
        // Refresh local user data
        $user = $database->getReference('users/' . $user_id)->getValue() ?: [];
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$current_role = $user['role'];
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard-premium.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>if(localStorage.getItem('theme')==='light') document.documentElement.setAttribute('data-theme', 'light');</script>
</head>
<body class="dashboard-body">
    <div class="dashboard-wrapper">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="dashboard-content">
            <div class="page-title-bar">
                <div>
                    <span class="page-badge">Personal Settings</span>
                    <h2>Edit My Profile 👤</h2>
                </div>
            </div>

            <?php if ($success): ?>
                <div class="dash-alert success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="dash-alert error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>

            <!-- Premium Tabs Navigation -->
            <div class="dp-tabs-container reveal">
                <div class="dp-tabs-nav" style="margin-bottom: 2rem;">
                    <div class="dp-tab-indicator"></div>
                    <button type="button" class="dp-tab-btn active" data-tab="tab-general"><i class="fas fa-id-card"></i> General Info</button>
                    <button type="button" class="dp-tab-btn" data-tab="tab-security"><i class="fas fa-shield-alt"></i> Security</button>
                </div>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
                <!-- Tab 1: General Info -->
                <div class="dp-tab-content active" id="tab-general">
                    <div class="form-card" style="max-width: 800px; margin-bottom: 2rem;">
                        <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
                        
                        <!-- Avatar Column -->
                        <div style="flex: 0 0 200px; text-align: center;">
                            <div style="position: relative; width: 150px; height: 150px; margin: 0 auto 1.5rem; border-radius: 50%; overflow: hidden; border: 4px solid var(--primary); box-shadow: var(--dp-shadow);">
                                <img src="<?= '../' . ($user['avatar'] ?? 'assets/img/logo.png') ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;" id="avatar-preview">
                                <label for="avatar-input" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.5); color: white; padding: 5px; font-size: 0.7rem; cursor: pointer; transition: background 0.3s;">
                                    <i class="fas fa-camera"></i> Change
                                </label>
                                <input type="file" name="avatar" id="avatar-input" style="display: none;" onchange="previewImage(this)">
                            </div>
                            <p style="font-size: 0.8rem; color: var(--text-muted);">Allowed: JPG, PNG. Max 2MB.</p>
                        </div>

                        <!-- Info Column -->
                        <div style="flex: 1; min-width: 300px;">
                            <div class="form-group" style="margin-bottom: 1.5rem;">
                                <label>Email Address</label>
                                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="opacity: 0.7; background: #e2e8f0; cursor: not-allowed;">
                                <small style="display: block; margin-top: 5px; color: var(--ps-orange);">Email cannot be changed contact admin if needed.</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Gender 🚻</label>
                                    <select name="gender">
                                        <option value="male" <?= $user['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                                        <option value="female" <?= $user['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Date of Birth 🎂</label>
                                    <input type="date" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Save Buttons for General Tab -->
                            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                                <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fas fa-save"></i> Save Changes</button>
                                <a href="index.php" class="btn btn-outline" style="flex: 1;">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Security Settings -->
            <div class="dp-tab-content" id="tab-security">
                <div class="form-card" style="max-width: 800px; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.35rem;"><i class="fas fa-shield-alt"></i> Security Settings</h3>
                    <div class="form-group" style="max-width: 400px; margin-bottom: 2rem;">
                        <label>New Password (leave blank to keep current)</label>
                        <input type="password" name="new_password" placeholder="••••••••">
                    </div>
                    <div style="max-width: 400px; display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fas fa-save"></i> Update Password</button>
                        <a href="index.php" class="btn btn-outline" style="flex: 1;">Cancel</a>
                    </div>
                </div>
            </div>
            </form>
        </main>
    </div>

    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
