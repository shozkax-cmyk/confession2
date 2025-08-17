<?php
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Yorum yapmak için giriş yapmalısınız.';
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confessionId = intval($_POST['confession_id'] ?? 0);
    $text = trim($_POST['comment_text'] ?? '');
    
    if ($confessionId <= 0 || empty($text)) {
        $_SESSION['error'] = 'Geçersiz yorum verisi.';
        header('Location: index.php');
        exit;
    }
    
    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['comment_image']) && $_FILES['comment_image']['error'] === UPLOAD_ERR_OK) {
        $uploadResult = handleImageUpload($_FILES['comment_image']);
        if ($uploadResult['success']) {
            $imagePath = $uploadResult['filename'];
        } else {
            $_SESSION['error'] = $uploadResult['message'];
            header('Location: index.php#confession-' . $confessionId);
            exit;
        }
    }
    
    // Extract style information (if rich text editor is used)
    $style = null;
    if (isset($_POST['comment_style']) && !empty($_POST['comment_style'])) {
        $style = json_decode($_POST['comment_style'], true);
    }
    
    try {
        if ($db->createComment($confessionId, $_SESSION['user_id'], $text, $style, $imagePath)) {
            $_SESSION['success'] = 'Yorum başarıyla eklendi!';
        } else {
            $_SESSION['error'] = 'Yorum eklenirken bir hata oluştu.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Veritabanı hatası: ' . $e->getMessage();
    }
    
    // Redirect back to the confession
    header('Location: index.php#confession-' . $confessionId);
    exit;
} else {
    // Invalid request method
    header('Location: index.php');
    exit;
}
?>