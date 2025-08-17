<?php
require_once 'config.php';
$page_title = 'İletişim';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Here you would typically send an email
        // For now, we'll just show a success message
        $_SESSION['success'] = 'Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağız.';
    } else {
        $_SESSION['error'] = 'Lütfen tüm zorunlu alanları doldurun.';
    }
}

include 'includes/header.php';
?>

<div style="max-width: 800px; margin: 2rem auto;">
    <div class="confession-card" style="text-align: center; margin-bottom: 2rem;">
        <h1 style="color: var(--accent-primary); margin-bottom: 1rem;">📞 İletişim</h1>
        <p style="color: var(--text-secondary);">
            Sorularınız, önerileriniz veya geri bildirimleriniz için bizimle iletişime geçin
        </p>
    </div>
    
    <!-- Contact Information -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="confession-card" style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">📧</div>
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">E-posta</h3>
            <p style="color: var(--text-secondary);">info@confessionhub.com</p>
            <p style="color: var(--text-secondary);">support@confessionhub.com</p>
        </div>
        
        <div class="confession-card" style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">🕐</div>
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">Destek Saatleri</h3>
            <p style="color: var(--text-secondary);">Pazartesi - Cuma</p>
            <p style="color: var(--text-secondary);">09:00 - 18:00</p>
        </div>
        
        <div class="confession-card" style="text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 1rem;">💬</div>
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">Sosyal Medya</h3>
            <div class="social-links" style="justify-content: center;">
                <a href="#" class="social-link">📘</a>
                <a href="#" class="social-link">🐦</a>
                <a href="#" class="social-link">📷</a>
            </div>
        </div>
    </div>
    
    <!-- Contact Form -->
    <div class="confession-card">
        <h2 style="color: var(--accent-secondary); margin-bottom: 1.5rem;">✉️ Bize Mesaj Gönderin</h2>
        
        <form method="POST" id="contact-form">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label for="name" class="form-label">👤 Adınız *</label>
                    <input type="text" id="name" name="name" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">📧 E-posta *</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="subject" class="form-label">📋 Konu</label>
                <select id="subject" name="subject" class="form-select">
                    <option value="">Konu seçin</option>
                    <option value="support">Teknik Destek</option>
                    <option value="suggestion">Öneri</option>
                    <option value="complaint">Şikayet</option>
                    <option value="other">Diğer</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="message" class="form-label">💬 Mesajınız *</label>
                <textarea id="message" name="message" class="form-textarea" rows="6" placeholder="Mesajınızı buraya yazın..." required></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" name="send_message" class="btn btn-primary">
                    🚀 Mesajı Gönder
                </button>
            </div>
        </form>
    </div>
    
    <!-- FAQ Section -->
    <div class="confession-card" style="margin-top: 2rem;">
        <h2 style="color: var(--accent-secondary); margin-bottom: 1.5rem;">❓ Sık Sorulan Sorular</h2>
        
        <div style="display: grid; gap: 1rem;">
            <details style="padding: 1rem; background: var(--bg-hover); border-radius: var(--border-radius-sm);">
                <summary style="cursor: pointer; font-weight: 600; color: var(--accent-primary);">
                    Hesabımı nasıl silebilirim?
                </summary>
                <p style="margin-top: 0.5rem; color: var(--text-secondary);">
                    Profil sayfanızdan "Hesabı Sil" butonuna tıklayarak hesabınızı kalıcı olarak silebilirsiniz.
                </p>
            </details>
            
            <details style="padding: 1rem; background: var(--bg-hover); border-radius: var(--border-radius-sm);">
                <summary style="cursor: pointer; font-weight: 600; color: var(--accent-primary);">
                    İtiraflarım gerçekten anonim mi?
                </summary>
                <p style="margin-top: 0.5rem; color: var(--text-secondary);">
                    Evet, anonim seçeneğini işaretlediğinizde kullanıcı adınız gizlenir. Sadece admin panelinde IP adresi görünür.
                </p>
            </details>
            
            <details style="padding: 1rem; background: var(--bg-hover); border-radius: var(--border-radius-sm);">
                <summary style="cursor: pointer; font-weight: 600; color: var(--accent-primary);">
                    Resim yükleyemiyorum, neden?
                </summary>
                <p style="margin-top: 0.5rem; color: var(--text-secondary);">
                    Sadece JPG, PNG, GIF ve WebP formatları desteklenir. Maksimum dosya boyutu 10MB'dır.
                </p>
            </details>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>