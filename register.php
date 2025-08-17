<?php
require_once 'config.php';
require_once 'functions.php';

$page_title = 'Kayıt Ol';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $agreeTerms = isset($_POST['agree_terms']);
    
    $errors = [];
    
    // Validation
    if (empty($username)) {
        $errors[] = 'Kullanıcı adı zorunludur.';
    } elseif (strlen($username) < USERNAME_MIN_LENGTH) {
        $errors[] = 'Kullanıcı adı en az ' . USERNAME_MIN_LENGTH . ' karakter olmalıdır.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Kullanıcı adı sadece harf, rakam ve alt çizgi içerebilir.';
    }
    
    if (empty($email)) {
        $errors[] = 'E-posta adresi zorunludur.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta adresi girin.';
    }
    
    if (empty($password)) {
        $errors[] = 'Şifre zorunludur.';
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = 'Şifre en az ' . PASSWORD_MIN_LENGTH . ' karakter olmalıdır.';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Şifreler eşleşmiyor.';
    }
    
    if (!$agreeTerms) {
        $errors[] = 'Kullanım şartlarını kabul etmelisiniz.';
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Bu kullanıcı adı veya e-posta adresi zaten kullanımda.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Kayıt kontrolü sırasında hata oluştu.';
        }
    }
    
    // Register user if no errors
    if (empty($errors)) {
        if ($db->registerUser($username, $email, $password)) {
            $_SESSION['success'] = 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.';
            header('Location: login.php');
            exit;
        } else {
            $errors[] = 'Kayıt sırasında bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

include 'includes/header.php';
?>

<div style="max-width: 600px; margin: 2rem auto;">
    <!-- Registration Header -->
    <div class="confession-card" style="text-align: center; margin-bottom: 2rem;">
        <h1 style="color: var(--accent-primary); font-size: 2rem; margin-bottom: 0.5rem;">
            📝 Kayıt Ol
        </h1>
        <p style="color: var(--text-secondary);">
            Confession Hub ailesine katılın ve düşüncelerinizi özgürce paylaşın
        </p>
    </div>
    
    <!-- Registration Benefits -->
    <div class="confession-card" style="background: linear-gradient(135deg, var(--bg-hover), var(--bg-card)); margin-bottom: 2rem;">
        <h3 style="color: var(--accent-secondary); margin-bottom: 1rem;">
            🎉 Üyelik Avantajları
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                ✍️ <span>Sınırsız itiraf paylaşımı</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                💬 <span>Yorum yapabilme</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                📷 <span>Resim paylaşımı</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                🎨 <span>Zengin metin editörü</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                🕶️ <span>Anonim paylaşım seçeneği</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-secondary);">
                🔐 <span>Güvenli ve özel profil</span>
            </div>
        </div>
    </div>
    
    <!-- Registration Form -->
    <div class="confession-card">
        <form method="POST" id="register-form">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <!-- Username Field -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        👤 Kullanıcı Adı *
                    </label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        placeholder="Kullanıcı adınızı seçin"
                        required
                        minlength="<?php echo USERNAME_MIN_LENGTH; ?>"
                        pattern="[a-zA-Z0-9_]+"
                        autocomplete="username"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    >
                    <small style="color: var(--text-muted); font-size: 0.8rem;">
                        En az <?php echo USERNAME_MIN_LENGTH; ?> karakter, sadece harf, rakam ve alt çizgi
                    </small>
                </div>
                
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        📧 E-posta Adresi *
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="email@example.com"
                        required
                        autocomplete="email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    >
                    <small style="color: var(--text-muted); font-size: 0.8rem;">
                        Doğrulama için geçerli bir e-posta adresi girin
                    </small>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                <!-- Password Field -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        🔒 Şifre *
                    </label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Güçlü bir şifre seçin"
                            required
                            minlength="<?php echo PASSWORD_MIN_LENGTH; ?>"
                            autocomplete="new-password"
                            style="padding-right: 3rem;"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('password')" 
                            style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem;"
                        >
                            👁️
                        </button>
                    </div>
                    <div id="password-strength" style="margin-top: 0.5rem;"></div>
                </div>
                
                <!-- Confirm Password Field -->
                <div class="form-group">
                    <label for="confirm_password" class="form-label">
                        🔓 Şifre Tekrarı *
                    </label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            class="form-input" 
                            placeholder="Şifrenizi tekrar girin"
                            required
                            autocomplete="new-password"
                            style="padding-right: 3rem;"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('confirm_password')" 
                            style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem;"
                        >
                            👁️
                        </button>
                    </div>
                    <div id="password-match" style="margin-top: 0.5rem;"></div>
                </div>
            </div>
            
            <!-- Terms and Privacy -->
            <div class="form-group">
                <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer; line-height: 1.5;">
                    <input type="checkbox" name="agree_terms" value="1" required style="width: auto; margin-top: 0.2rem;">
                    <span style="color: var(--text-secondary);">
                        📋 <a href="terms.php" target="_blank" style="color: var(--accent-primary); text-decoration: none;">Kullanım Şartları</a> 
                        ve <a href="privacy.php" target="_blank" style="color: var(--accent-primary); text-decoration: none;">Gizlilik Politikası</a>'nı 
                        okudum ve kabul ediyorum *
                    </span>
                </label>
            </div>
            
            <!-- Newsletter Subscription -->
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary);">
                    <input type="checkbox" name="subscribe_newsletter" value="1" style="width: auto;">
                    📬 Yeni özellikler ve güncellemeler hakkında e-posta almak istiyorum
                </label>
            </div>
            
            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" name="register" class="btn btn-primary w-full" style="padding: 1rem;">
                    🚀 Hesabımı Oluştur
                </button>
            </div>
            
            <!-- Social Registration (Future Enhancement) -->
            <div class="social-registration" style="margin-top: 1.5rem;">
                <div style="text-align: center; margin-bottom: 1rem; color: var(--text-muted);">
                    <span style="background: var(--bg-card); padding: 0 1rem; position: relative; z-index: 1;">
                        veya
                    </span>
                    <hr style="margin: 0; border: none; height: 1px; background: var(--border-color); position: relative; top: -0.7rem; z-index: 0;">
                </div>
                
                <div style="display: grid; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary w-full" disabled style="opacity: 0.6;">
                        🔵 Facebook ile Kayıt Ol (Yakında)
                    </button>
                    <button type="button" class="btn btn-secondary w-full" disabled style="opacity: 0.6;">
                        🔴 Google ile Kayıt Ol (Yakında)
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Login Link -->
    <div class="confession-card" style="text-align: center; margin-top: 1.5rem;">
        <p style="color: var(--text-secondary);">
            Zaten hesabınız var mı?
            <a href="login.php" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">
                🔐 Giriş yapın
            </a>
        </p>
    </div>
    
    <!-- Security & Privacy Info -->
    <div style="background: var(--bg-hover); padding: 1.5rem; border-radius: var(--border-radius); margin-top: 2rem; border-left: 4px solid var(--accent-secondary);">
        <h3 style="color: var(--accent-secondary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            🛡️ Güvenlik ve Gizlilik
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; color: var(--text-secondary); font-size: 0.9rem;">
            <div>
                <strong>🔐 Şifre Güvenliği:</strong><br>
                Şifreleriniz gelişmiş algoritmarla şifrelenir ve asla açık metin olarak saklanmaz.
            </div>
            <div>
                <strong>📧 E-posta Gizliliği:</strong><br>
                E-posta adresiniz sadece bildirimler için kullanılır ve üçüncü taraflarla paylaşılmaz.
            </div>
            <div>
                <strong>🕶️ Anonim Seçenek:</strong><br>
                İtiraflarınızı istediğiniz zaman anonim olarak paylaşabilirsiniz.
            </div>
            <div>
                <strong>🗑️ Veri Kontrolü:</strong><br>
                Hesabınızı ve tüm verilerinizi istediğiniz zaman silebilirsiniz.
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    
    if (field.type === 'password') {
        field.type = 'text';
        button.innerHTML = '🙈';
    } else {
        field.type = 'password';
        button.innerHTML = '👁️';
    }
}

// Password strength checker
function checkPasswordStrength(password) {
    const strengthIndicator = document.getElementById('password-strength');
    let strength = 0;
    let feedback = [];
    
    if (password.length >= <?php echo PASSWORD_MIN_LENGTH; ?>) strength++;
    else feedback.push('En az <?php echo PASSWORD_MIN_LENGTH; ?> karakter');
    
    if (/[a-z]/.test(password)) strength++;
    else feedback.push('Küçük harf');
    
    if (/[A-Z]/.test(password)) strength++;
    else feedback.push('Büyük harf');
    
    if (/[0-9]/.test(password)) strength++;
    else feedback.push('Rakam');
    
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    else feedback.push('Özel karakter');
    
    const colors = ['#ef4444', '#f59e0b', '#eab308', '#22c55e', '#16a34a'];
    const texts = ['Çok Zayıf', 'Zayıf', 'Orta', 'Güçlü', 'Çok Güçlü'];
    
    if (password.length > 0) {
        strengthIndicator.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="flex: 1; height: 4px; background: var(--border-color); border-radius: 2px;">
                    <div style="height: 100%; width: ${(strength / 5) * 100}%; background: ${colors[strength - 1] || colors[0]}; border-radius: 2px; transition: all 0.3s;"></div>
                </div>
                <span style="font-size: 0.8rem; color: ${colors[strength - 1] || colors[0]};">
                    ${texts[strength - 1] || texts[0]}
                </span>
            </div>
            ${feedback.length > 0 ? `<div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Eklenebilir: ${feedback.join(', ')}</div>` : ''}
        `;
    } else {
        strengthIndicator.innerHTML = '';
    }
    
    return strength;
}

// Password match checker
function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const matchIndicator = document.getElementById('password-match');
    
    if (confirmPassword.length > 0) {
        if (password === confirmPassword) {
            matchIndicator.innerHTML = '<span style="color: var(--accent-secondary); font-size: 0.8rem;">✅ Şifreler eşleşiyor</span>';
        } else {
            matchIndicator.innerHTML = '<span style="color: var(--accent-danger); font-size: 0.8rem;">❌ Şifreler eşleşmiyor</span>';
        }
    } else {
        matchIndicator.innerHTML = '';
    }
}

// Username availability checker
async function checkUsernameAvailability(username) {
    if (username.length < <?php echo USERNAME_MIN_LENGTH; ?>) return;
    
    try {
        const response = await fetch('api/check-username.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: username })
        });
        
        const data = await response.json();
        const usernameField = document.getElementById('username');
        const feedback = usernameField.parentNode.querySelector('.username-feedback');
        
        if (feedback) feedback.remove();
        
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = 'username-feedback';
        feedbackDiv.style.cssText = 'font-size: 0.8rem; margin-top: 0.25rem;';
        
        if (data.available) {
            feedbackDiv.innerHTML = '<span style="color: var(--accent-secondary);">✅ Kullanıcı adı uygun</span>';
            usernameField.style.borderColor = 'var(--accent-secondary)';
        } else {
            feedbackDiv.innerHTML = '<span style="color: var(--accent-danger);">❌ Kullanıcı adı kullanımda</span>';
            usernameField.style.borderColor = 'var(--accent-danger)';
        }
        
        usernameField.parentNode.appendChild(feedbackDiv);
    } catch (error) {
        console.log('Username check failed:', error);
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    const usernameField = document.getElementById('username');
    const form = document.getElementById('register-form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Password strength checking
    passwordField.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });
    
    // Password match checking
    confirmPasswordField.addEventListener('input', checkPasswordMatch);
    
    // Username availability checking (with debounce)
    let usernameTimeout;
    usernameField.addEventListener('input', function() {
        clearTimeout(usernameTimeout);
        const username = this.value.trim();
        
        // Clear previous styling
        this.style.borderColor = 'var(--border-color)';
        const feedback = this.parentNode.querySelector('.username-feedback');
        if (feedback) feedback.remove();
        
        if (username.length >= <?php echo USERNAME_MIN_LENGTH; ?>) {
            usernameTimeout = setTimeout(() => {
                checkUsernameAvailability(username);
            }, 500);
        }
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;
        const agreeTerms = document.querySelector('input[name="agree_terms"]').checked;
        
        // Final validation
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Şifreler eşleşmiyor!');
            return;
        }
        
        if (checkPasswordStrength(password) < 2) {
            e.preventDefault();
            alert('Lütfen daha güçlü bir şifre seçin!');
            return;
        }
        
        if (!agreeTerms) {
            e.preventDefault();
            alert('Kullanım şartlarını kabul etmelisiniz!');
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Hesap oluşturuluyor...';
        
        // Re-enable after timeout
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '🚀 Hesabımı Oluştur';
        }, 10000);
    });
    
    // Focus on first field
    usernameField.focus();
    
    // Real-time validation feedback
    const inputs = form.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = 'var(--accent-danger)';
            } else {
                this.style.borderColor = 'var(--accent-secondary)';
            }
        });
        
        input.addEventListener('input', function() {
            if (this.style.borderColor === 'rgb(239, 68, 68)') { // danger color
                this.style.borderColor = 'var(--border-color)';
            }
        });
    });
});

// Prevent form resubmission
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

<?php include 'includes/footer.php'; ?>