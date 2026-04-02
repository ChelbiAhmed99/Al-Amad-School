<?php
// auth/reset-password.php
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
if (!$token || !$email) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Al Amad School</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root { --ps-orange: #FF7B54; --ps-light: #fff8f5; }
        body { background: var(--ps-light); font-family: 'Outfit', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .forgot-card { background: white; padding: 3rem; border-radius: 30px; box-shadow: 0 20px 40px rgba(255, 123, 84, 0.1); width: 100%; max-width: 450px; }
        .forgot-header { text-align: center; margin-bottom: 2rem; }
        .forgot-header i { font-size: 3rem; color: var(--ps-orange); margin-bottom: 1rem; }
        .forgot-header h2 { font-weight: 800; margin-bottom: 0.5rem; }
        .forgot-header p { color: #666; font-size: 0.9rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; font-weight: 700; margin-bottom: 0.5rem; color: #444; font-size: 0.8rem; text-transform: uppercase; }
        .form-group input { width: 100%; padding: 1rem; border-radius: 12px; border: 2px solid #eee; font-family: inherit; font-size: 1rem; outline: none; transition: 0.3s; box-sizing: border-box; }
        .form-group input:focus { border-color: var(--ps-orange); }
        .btn-reset { width: 100%; padding: 1rem; border-radius: 12px; border: none; background: var(--ps-orange); color: white; font-weight: 700; font-size: 1rem; cursor: pointer; transition: 0.3s; }
        .btn-reset:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 123, 84, 0.3); }
    </style>
</head>
<body>
    <div class="forgot-card">
        <div class="forgot-header">
            <i class="fas fa-lock-open"></i>
            <h2>New Password</h2>
            <p>Set a strong password for your account.</p>
        </div>
        <form action="../api/reset-password.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-reset">Update Password</button>
        </form>
    </div>
</body>
</html>
