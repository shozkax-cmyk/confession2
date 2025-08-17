<?php
require_once 'config.php';

// Clear all session data
session_start();
session_unset();
session_destroy();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

// Start new session for flash message
session_start();
$_SESSION['success'] = 'Başarıyla çıkış yaptınız. Güle güle! 👋';

// Redirect to home page
header('Location: index.php');
exit;
?>