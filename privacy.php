<?php
require_once 'config.php';
$page_title = 'Gizlilik Politikası';
include 'includes/header.php';
?>

<div style="max-width: 800px; margin: 2rem auto;">
    <div class="confession-card">
        <h1 style="color: var(--accent-primary); margin-bottom: 2rem;">🔒 Gizlilik Politikası</h1>
        
        <div style="line-height: 1.8; color: var(--text-secondary);">
            <h2 style="color: var(--accent-secondary); margin-top: 2rem; margin-bottom: 1rem;">1. Toplanan Veriler</h2>
            <ul style="margin-left: 2rem;">
                <li>Kullanıcı adı ve e-posta adresi</li>
                <li>Paylaştığınız itiraflar ve yorumlar</li>
                <li>IP adresi (güvenlik amaçlı)</li>
                <li>Çerezler (tema tercihi vb.)</li>
            </ul>
            
            <h2 style="color: var(--accent-secondary); margin-top: 2rem; margin-bottom: 1rem;">2. Veri Kullanımı</h2>
            <p>Verileriniz sadece platform işlevselliği için kullanılır ve üçüncü taraflarla paylaşılmaz.</p>
            
            <h2 style="color: var(--accent-secondary); margin-top: 2rem; margin-bottom: 1rem;">3. Veri Güvenliği</h2>
            <p>Tüm veriler şifreli olarak saklanır ve güvenlik önlemleriyle korunur.</p>
            
            <h2 style="color: var(--accent-secondary); margin-top: 2rem; margin-bottom: 1rem;">4. Kullanıcı Hakları</h2>
            <ul style="margin-left: 2rem;">
                <li>Verilerinizi görüntüleme hakkı</li>
                <li>Düzeltme ve silme hakkı</li>
                <li>Hesap kapatma hakkı</li>
            </ul>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>