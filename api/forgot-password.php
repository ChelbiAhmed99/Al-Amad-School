<?php
// api/forgot-password.php
header('Content-Type: text/html; charset=utf-8');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (!$email) {
        die("Email is required.");
    }
    
    try {
        // Attempt to generate a Firebase password reset link
        // If the user does not exist, it throws an exception.
        $link = $auth->getPasswordResetLink($email);
        
        echo "<!DOCTYPE html><html><head><title>Reset Link Sent</title><link rel='stylesheet' href='../assets/css/style.css'><style>body{background:#fff8f5;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;}</style></head><body>";
        echo "<div style='background:white;padding:3rem;border-radius:20px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.05);max-width:400px;'>";
        echo "<h2 style='color:#FF7B54;'>Check your email!</h2>";
        echo "<p>We've generated a password reset link for: <strong>" . htmlspecialchars($email) . "</strong></p>";
        echo "<p style='font-size:0.8rem;color:#888;margin-top:2rem;'>[DEMO MODE] Click this Firebase link to reset:</p>";
        echo "<a href='$link' style='word-break:break-all;color:#FF7B54;font-size:0.9rem;'>Reset Password</a>";
        echo "<br><br><a href='../auth/login.php' class='btn btn-primary'>Return to Login</a>";
        echo "</div></body></html>";
    } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
        echo "<script>alert('Email not found.'); window.history.back();</script>";
    } catch (Exception $e) {
        echo "<script>alert('Error generating reset link.'); window.history.back();</script>";
    }
}
?>
