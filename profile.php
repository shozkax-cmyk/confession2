<?php
require_once 'config.php';
require_once 'functions.php';

$page_title = 'Profil';

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Bu sayfaya erişmek için giriş yapmalısınız.';
    header('Location: login.php');
    exit;
}

// Get user data
$user = $db->getUserById($_SESSION['user_id']);

if (!$user) {
    $_SESSION['error'] = 'Kullanıcı bulunamadı.';
    header('Location: logout.php');
    exit;
}

include 'includes/header.php';
?>

<div style="max-width: 800px; margin: 2rem auto;">
    <!-- Profile Header -->
    <div class="confession-card" style="text-align: center; margin-bottom: 2rem;">
        <div class="user-avatar" style="width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 1rem;">
            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
        </div>
        <h1 style="color: var(--accent-primary); margin-bottom: 0.5rem;">
            👤 <?php echo htmlspecialchars($user['username']); ?>
        </h1>
        <p style="color: var(--text-secondary);">
            📅 Üye oldu: <?php echo formatDate($user['registration_date']); ?>
            <?php if ($user['is_admin']): ?>
                <span style="background: var(--accent-warning); color: white; padding: 0.25rem 0.5rem; border-radius: var(--border-radius-sm); font-size: 0.8rem; margin-left: 0.5rem;">
                    ⚙️ Admin
                </span>
            <?php endif; ?>
        </p>
    </div>
    
    <!-- Profile Stats -->
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="stat-item">
            <span class="stat-number">5</span>
            <span class="stat-label">İtiraf Sayısı</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">12</span>
            <span class="stat-label">Yorum Sayısı</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">25</span>
            <span class="stat-label">Beğeni Sayısı</span>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="confession-card">
        <h3 style="color: var(--accent-primary); margin-bottom: 1rem;">⚡ Hızlı İşlemler</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="index.php" class="btn btn-primary">✍️ Yeni İtiraf Yaz</a>
            <a href="my-confessions.php" class="btn btn-secondary">📝 İtiraflarım</a>
            <a href="my-comments.php" class="btn btn-secondary">💬 Yorumlarım</a>
            <?php if (isAdmin()): ?>
                <a href="admin.php" class="btn btn-warning">⚙️ Admin Panel</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Account Settings -->
    <div class="confession-card" style="margin-top: 2rem;">
        <h3 style="color: var(--accent-secondary); margin-bottom: 1rem;">⚙️ Hesap Ayarları</h3>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <button class="btn btn-secondary" onclick="alert('Şifre değiştirme özelliği yakında eklenecek!')">
                🔐 Şifre Değiştir
            </button>
            <button class="btn btn-secondary" onclick="alert('E-posta güncelleme özelliği yakında eklenecek!')">
                📧 E-posta Güncelle
            </button>
            <button class="btn btn-danger" onclick="confirmAccountDeletion()">
                🗑️ Hesabı Sil
            </button>
        </div>
    </div>
</div>

<script>
function confirmAccountDeletion() {
    if (confirm('Hesabınızı silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
        if (confirm('Tüm itiraflarınız ve yorumlarınız silinecek. Devam etmek istiyor musunuz?')) {
            // Implement account deletion
            alert('Hesap silme özelliği yakında eklenecek!');
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?>