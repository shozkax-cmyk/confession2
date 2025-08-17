<?php
require_once 'config.php';
require_once 'functions.php';

$page_title = 'Admin Panel';

// Check admin access
if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
    header('Location: login.php');
    exit;
}

// Handle actions
$action = $_GET['action'] ?? 'dashboard';
$message = '';
$messageType = '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'delete_user':
            $userId = intval($_POST['user_id']);
            if ($userId > 0 && $db->deleteUser($userId)) {
                $message = 'Kullanıcı başarıyla silindi.';
                $messageType = 'success';
            } else {
                $message = 'Kullanıcı silinemedi.';
                $messageType = 'error';
            }
            break;
            
        case 'delete_confession':
            $confessionId = intval($_POST['confession_id']);
            if ($confessionId > 0 && $db->deleteConfession($confessionId)) {
                $message = 'İtiraf başarıyla silindi.';
                $messageType = 'success';
            } else {
                $message = 'İtiraf silinemedi.';
                $messageType = 'error';
            }
            break;
            
        case 'toggle_user_status':
            $userId = intval($_POST['user_id']);
            $newStatus = intval($_POST['new_status']);
            
            $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ? AND is_admin = 0");
            if ($stmt->execute([$newStatus, $userId])) {
                $message = 'Kullanıcı durumu güncellendi.';
                $messageType = 'success';
            } else {
                $message = 'Kullanıcı durumu güncellenemedi.';
                $messageType = 'error';
            }
            break;
    }
}

// Get statistics
$stats = [
    'total_users' => $db->getTotalUsers(),
    'total_confessions' => $db->getTotalConfessions(),
    'total_comments' => $db->getTotalComments(),
    'active_users' => $db->getActiveUsers()
];

// Get recent activity
$recentConfessions = $db->getAllConfessions(1, 5);
$recentUsers = $db->getAllUsers(1, 5);

include 'includes/header.php';
?>

<div class="admin-container">
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                ⚙️ Admin Panel
            </h1>
            <p style="opacity: 0.9; font-size: 1.1rem;">
                Confession Hub yönetim merkezi - Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </p>
        </div>
    </div>
    
    <div class="container">
        <!-- Flash Messages -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> fade-in-up" style="margin-bottom: 2rem;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Navigation Tabs -->
        <div class="admin-nav" style="margin-bottom: 2rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="?action=dashboard" class="btn <?php echo $action === 'dashboard' ? 'btn-primary' : 'btn-secondary'; ?>">
                📊 Dashboard
            </a>
            <a href="?action=users" class="btn <?php echo $action === 'users' ? 'btn-primary' : 'btn-secondary'; ?>">
                👥 Kullanıcılar
            </a>
            <a href="?action=confessions" class="btn <?php echo $action === 'confessions' ? 'btn-primary' : 'btn-secondary'; ?>">
                💬 İtiraflar
            </a>
            <a href="?action=comments" class="btn <?php echo $action === 'comments' ? 'btn-primary' : 'btn-secondary'; ?>">
                💭 Yorumlar
            </a>
            <a href="?action=settings" class="btn <?php echo $action === 'settings' ? 'btn-primary' : 'btn-secondary'; ?>">
                ⚙️ Ayarlar
            </a>
        </div>
        
        <?php if ($action === 'dashboard'): ?>
            <!-- Dashboard -->
            <div class="admin-stats">
                <div class="admin-stat-card">
                    <h3 style="color: var(--accent-primary); font-size: 2rem; margin-bottom: 0.5rem;">
                        <?php echo $stats['total_users']; ?>
                    </h3>
                    <p style="color: var(--text-secondary);">Toplam Kullanıcı</p>
                    <div style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--text-muted);">
                        📈 Son 30 gün: +15
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <h3 style="color: var(--accent-secondary); font-size: 2rem; margin-bottom: 0.5rem;">
                        <?php echo $stats['total_confessions']; ?>
                    </h3>
                    <p style="color: var(--text-secondary);">Toplam İtiraf</p>
                    <div style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--text-muted);">
                        📈 Bugün: +5
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <h3 style="color: var(--accent-warning); font-size: 2rem; margin-bottom: 0.5rem;">
                        <?php echo $stats['total_comments']; ?>
                    </h3>
                    <p style="color: var(--text-secondary);">Toplam Yorum</p>
                    <div style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--text-muted);">
                        📈 Bu hafta: +28
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <h3 style="color: var(--accent-danger); font-size: 2rem; margin-bottom: 0.5rem;">
                        <?php echo $stats['active_users']; ?>
                    </h3>
                    <p style="color: var(--text-secondary);">Aktif Kullanıcı</p>
                    <div style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--text-muted);">
                        📊 Son 30 gün içinde aktif
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; margin-top: 2rem;">
                <!-- Recent Confessions -->
                <div class="confession-card">
                    <h3 style="color: var(--accent-primary); margin-bottom: 1rem;">
                        💬 Son İtiraflar
                    </h3>
                    <?php foreach ($recentConfessions as $confession): ?>
                        <div style="padding: 0.75rem; border-left: 3px solid var(--accent-primary); margin-bottom: 0.75rem; background: var(--bg-hover);">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                                <strong style="color: var(--text-primary);"><?php echo htmlspecialchars($confession['username']); ?></strong>
                                <small style="color: var(--text-muted);"><?php echo formatDate($confession['date']); ?></small>
                            </div>
                            <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.25rem;">
                                <?php echo htmlspecialchars(substr($confession['text'], 0, 100)) . (strlen($confession['text']) > 100 ? '...' : ''); ?>
                            </p>
                            <small style="color: var(--text-muted);">
                                IP: <?php echo $confession['ip']; ?> | 
                                Yorumlar: <?php echo $confession['comments_count']; ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                    <a href="?action=confessions" class="btn btn-secondary btn-sm w-full">Tümünü Görüntüle</a>
                </div>
                
                <!-- Recent Users -->
                <div class="confession-card">
                    <h3 style="color: var(--accent-secondary); margin-bottom: 1rem;">
                        👥 Yeni Kullanıcılar
                    </h3>
                    <?php foreach ($recentUsers as $user): ?>
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: var(--bg-hover); margin-bottom: 0.75rem; border-radius: var(--border-radius-sm);">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                            </div>
                            <div style="flex: 1;">
                                <strong style="color: var(--text-primary);"><?php echo htmlspecialchars($user['username']); ?></strong>
                                <br>
                                <small style="color: var(--text-muted);">
                                    <?php echo formatDate($user['registration_date']); ?> | 
                                    <?php echo $user['confessions_count']; ?> itiraf
                                </small>
                            </div>
                            <span style="padding: 0.25rem 0.5rem; border-radius: var(--border-radius-sm); background: <?php echo $user['is_active'] ? 'var(--accent-secondary)' : 'var(--accent-danger)'; ?>; color: white; font-size: 0.8rem;">
                                <?php echo $user['is_active'] ? 'Aktif' : 'Pasif'; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                    <a href="?action=users" class="btn btn-secondary btn-sm w-full">Tümünü Görüntüle</a>
                </div>
            </div>
            
        <?php elseif ($action === 'users'): ?>
            <!-- Users Management -->
            <div class="confession-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="color: var(--accent-primary);">👥 Kullanıcı Yönetimi</h2>
                    <button onclick="exportUsers()" class="btn btn-secondary">
                        📊 Excel'e Aktar
                    </button>
                </div>
                
                <!-- Search and Filter -->
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <input type="text" id="user-search" placeholder="Kullanıcı ara..." class="form-input" style="flex: 1; min-width: 200px;">
                    <select id="user-filter" class="form-select" style="width: auto;">
                        <option value="all">Tüm Kullanıcılar</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Pasif</option>
                        <option value="recent">Yeni Kayıt</option>
                    </select>
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kullanıcı Adı</th>
                                <th>E-posta</th>
                                <th>Kayıt Tarihi</th>
                                <th>İtiraflar</th>
                                <th>Son IP</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($db->getAllUsers(1, 50) as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div class="user-avatar" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <?php echo htmlspecialchars($user['username']); ?>
                                            <?php if ($user['is_admin']): ?>
                                                <span style="background: var(--accent-warning); color: white; padding: 0.1rem 0.3rem; border-radius: 3px; font-size: 0.7rem;">Admin</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo formatDate($user['registration_date']); ?></td>
                                    <td><?php echo $user['confessions_count']; ?></td>
                                    <td><?php echo $user['ip']; ?></td>
                                    <td>
                                        <span style="padding: 0.25rem 0.5rem; border-radius: var(--border-radius-sm); background: <?php echo $user['is_active'] ? 'var(--accent-secondary)' : 'var(--accent-danger)'; ?>; color: white; font-size: 0.8rem;">
                                            <?php echo $user['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!$user['is_admin']): ?>
                                            <div style="display: flex; gap: 0.25rem;">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="toggle_user_status">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <input type="hidden" name="new_status" value="<?php echo $user['is_active'] ? 0 : 1; ?>">
                                                    <button type="submit" class="btn btn-secondary btn-sm">
                                                        <?php echo $user['is_active'] ? '🚫' : '✅'; ?>
                                                    </button>
                                                </form>
                                                
                                                <button onclick="viewUserDetail(<?php echo $user['id']; ?>)" class="btn btn-secondary btn-sm">
                                                    👁️
                                                </button>
                                                
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
                                                    <input type="hidden" name="action" value="delete_user">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        <?php elseif ($action === 'confessions'): ?>
            <!-- Confessions Management -->
            <div class="confession-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 style="color: var(--accent-primary);">💬 İtiraf Yönetimi</h2>
                    <div style="display: flex; gap: 0.5rem;">
                        <button onclick="exportConfessions()" class="btn btn-secondary">📊 Excel'e Aktar</button>
                        <button onclick="moderateAll()" class="btn btn-warning">🔍 Toplu Moderasyon</button>
                    </div>
                </div>
                
                <!-- Search and Filter -->
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <input type="text" id="confession-search" placeholder="İtiraf ara..." class="form-input" style="flex: 1; min-width: 200px;">
                    <select id="confession-filter" class="form-select" style="width: auto;">
                        <option value="all">Tüm İtiraflar</option>
                        <option value="recent">Son 24 Saat</option>
                        <option value="popular">Popüler</option>
                        <option value="reported">Şikayetli</option>
                    </select>
                    <input type="text" id="ip-filter" placeholder="IP adresine göre filtrele..." class="form-input" style="width: 200px;">
                </div>
                
                <div style="overflow-x: auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kullanıcı</th>
                                <th>İçerik</th>
                                <th>Tarih</th>
                                <th>IP</th>
                                <th>Yorumlar</th>
                                <th>Resim</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($db->getAllConfessions(1, 50) as $confession): ?>
                                <tr>
                                    <td><?php echo $confession['id']; ?></td>
                                    <td>
                                        <?php if ($confession['is_anonymous']): ?>
                                            <span style="color: var(--text-muted);">🕶️ Anonim</span>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($confession['username']); ?>
                                        <?php endif; ?>
                                        <br>
                                        <small style="color: var(--text-muted);"><?php echo $confession['email']; ?></small>
                                    </td>
                                    <td>
                                        <div style="max-width: 300px;">
                                            <?php echo htmlspecialchars(substr($confession['text'], 0, 100)) . (strlen($confession['text']) > 100 ? '...' : ''); ?>
                                            <?php if ($confession['style']): ?>
                                                <br><small style="color: var(--accent-primary);">🎨 Styled</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo formatDate($confession['date']); ?></td>
                                    <td>
                                        <span style="font-family: monospace; font-size: 0.8rem;"><?php echo $confession['ip']; ?></span>
                                        <br>
                                        <button onclick="filterByIP('<?php echo $confession['ip']; ?>')" class="btn btn-secondary btn-sm" style="font-size: 0.7rem;">
                                            🔍 IP'ye göre filtrele
                                        </button>
                                    </td>
                                    <td><?php echo $confession['comments_count']; ?></td>
                                    <td>
                                        <?php if ($confession['image']): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($confession['image']); ?>" 
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: var(--border-radius-sm); cursor: pointer;"
                                                 onclick="viewImage('uploads/<?php echo htmlspecialchars($confession['image']); ?>')">
                                        <?php else: ?>
                                            <span style="color: var(--text-muted);">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                            <button onclick="viewConfessionDetail(<?php echo $confession['id']; ?>)" class="btn btn-secondary btn-sm">
                                                👁️ Detay
                                            </button>
                                            
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Bu itirafı silmek istediğinizden emin misiniz?')">
                                                <input type="hidden" name="action" value="delete_confession">
                                                <input type="hidden" name="confession_id" value="<?php echo $confession['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">🗑️ Sil</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        <?php else: ?>
            <div class="confession-card" style="text-align: center; padding: 3rem;">
                <h2 style="color: var(--text-muted); margin-bottom: 1rem;">🚧 Bu bölüm henüz hazırlanıyor</h2>
                <p style="color: var(--text-secondary);">Seçtiğiniz sayfa yakında kullanıma açılacak.</p>
                <a href="?action=dashboard" class="btn btn-primary" style="margin-top: 1rem;">📊 Dashboard'a Dön</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// User search functionality
document.getElementById('user-search')?.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// IP filter functionality
function filterByIP(ip) {
    const ipFilter = document.getElementById('ip-filter');
    if (ipFilter) {
        ipFilter.value = ip;
        ipFilter.dispatchEvent(new Event('input'));
    }
}

document.getElementById('ip-filter')?.addEventListener('input', function() {
    const filterIP = this.value.toLowerCase();
    const rows = document.querySelectorAll('.data-table tbody tr');
    
    rows.forEach(row => {
        const ipCell = row.cells[4]; // IP column
        const ip = ipCell?.textContent.toLowerCase() || '';
        row.style.display = ip.includes(filterIP) ? '' : 'none';
    });
});

// View image modal
function viewImage(src) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0,0,0,0.8); display: flex; align-items: center; 
        justify-content: center; z-index: 9999; cursor: pointer;
    `;
    modal.onclick = () => modal.remove();
    
    const img = document.createElement('img');
    img.src = src;
    img.style.cssText = 'max-width: 90%; max-height: 90%; border-radius: var(--border-radius);';
    
    modal.appendChild(img);
    document.body.appendChild(modal);
}

// View user detail
function viewUserDetail(userId) {
    // Open user detail in new window/modal
    window.open(`user-detail.php?id=${userId}`, '_blank', 'width=800,height=600');
}

// View confession detail
function viewConfessionDetail(confessionId) {
    // Open confession detail in new window/modal
    window.open(`confession-detail.php?id=${confessionId}`, '_blank', 'width=800,height=600');
}

// Export functions
function exportUsers() {
    window.location.href = 'admin/export.php?type=users';
}

function exportConfessions() {
    window.location.href = 'admin/export.php?type=confessions';
}

// Moderate all
function moderateAll() {
    if (confirm('Tüm itirafları moderasyon için işaretlemek istiyor musunuz?')) {
        // Implement moderation logic
        alert('Moderasyon işlemi başlatıldı.');
    }
}

// Real-time updates (optional)
function refreshStats() {
    fetch('admin/api.php?action=stats')
        .then(response => response.json())
        .then(data => {
            // Update dashboard stats
            console.log('Stats updated:', data);
        })
        .catch(error => console.error('Stats update failed:', error));
}

// Refresh stats every 30 seconds on dashboard
if (window.location.search.includes('action=dashboard') || !window.location.search.includes('action=')) {
    setInterval(refreshStats, 30000);
}

// Confirmation dialogs
document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (!confirm('Bu işlemi gerçekleştirmek istediğinizden emin misiniz?')) {
            e.preventDefault();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>