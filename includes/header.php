<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Confession Hub</title>
    <meta name="description" content="Anonim itiraf platformu - Düşüncelerinizi güvenle paylaşın">
    <meta name="keywords" content="itiraf, anonim, paylaşım, confession, anonymous">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Confession Hub - Anonim İtiraf Platformu">
    <meta property="og:description" content="Düşüncelerinizi güvenle ve anonim olarak paylaşın">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    
    <!-- Additional CSS for specific pages -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Preload critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span>🗣️ Confession Hub</span>
            </a>
            
            <ul class="navbar-nav">
                <li><a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">Ana Sayfa</a></li>
                
                <?php if (isLoggedIn()): ?>
                    <li><a href="profile.php" class="nav-link">Profil</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin.php" class="nav-link">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="nav-link">Çıkış Yap (<?php echo $_SESSION['username']; ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'login.php' ? 'active' : ''; ?>">Giriş Yap</a></li>
                    <li><a href="register.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'register.php' ? 'active' : ''; ?>">Kayıt Ol</a></li>
                <?php endif; ?>
                
                <li>
                    <button class="theme-toggle" title="Tema Değiştir" aria-label="Toggle Theme">
                        🌙
                    </button>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content Container -->
    <main class="main-content">
        <div class="container">
            
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> fade-in-up">
                    <?php 
                        echo $_SESSION['flash_message'];
                        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Error Messages -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger fade-in-up">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <!-- Success Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success fade-in-up">
                    <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>