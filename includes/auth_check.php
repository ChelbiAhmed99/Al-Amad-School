<?php
// includes/auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Strictly prevent caching for all authenticated pages
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Helper generic function to protect routes strictly for a role
function checkRole($requiredRole) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    if ($requiredRole !== 'any' && $_SESSION['role'] !== $requiredRole) {
        // Simple error or redirect
        header('Location: ../auth/login.php?error=unauthorized');
        exit;
    }
    return true;
}
?>
