<?php
require_once 'config.php';
require_once 'functions.php';

$page_title = 'Giriş Yap';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Kullanıcı adı ve şifre zorunludur.';
    } else {
        if ($db->loginUser($username, $password)) {
            $_SESSION['success'] = 'Başarıyla giriş yaptınız! Hoş geldiniz.';
            
            // Handle remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true); // 30 days
                // Store token in database (implement if needed)
            }
            
            // Redirect to intended page or home
            $redirectUrl = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $_SESSION['error'] = 'Geçersiz kullanıcı adı veya şifre.';
        }
    }
}

include 'includes/header.php';
?>

<div style="max-width: 500px; margin: 2rem auto;">
    <!-- Login Header -->
    <div class="confession-card" style="text-align: center; margin-bottom: 2rem;">
        <h1 style="color: var(--accent-primary); font-size: 2rem; margin-bottom: 0.5rem;">
            🔐 Giriş Yap
        </h1>
        <p style="color: var(--text-secondary);">
            Hesabınıza giriş yapın ve itiraflarınızı paylaşmaya başlayın
        </p>
    </div>
    
    <!-- Login Form -->
    <div class="confession-card">
        <form method="POST" id="login-form">
            <!-- Username Field -->
            <div class="form-group">
                <label for="username" class="form-label">
                    👤 Kullanıcı Adı
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-input" 
                    placeholder="Kullanıcı adınızı girin"
                    required
                    autocomplete="username"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                >
            </div>
            
            <!-- Password Field -->
            <div class="form-group">
                <label for="password" class="form-label">
                    🔒 Şifre
                </label>
                <div style="position: relative;">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Şifrenizi girin"
                        required
                        autocomplete="current-password"
                        style="padding-right: 3rem;"
                    >
                    <button 
                        type="button" 
                        onclick="togglePassword()" 
                        style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2rem;"
                        title="Şifreyi göster/gizle"
                    >
                        👁️
                    </button>
                </div>
            </div>
            
            <!-- Remember Me & Forgot Password -->
            <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-secondary);">
                    <input type="checkbox" name="remember" value="1" style="width: auto;">
                    💾 Beni hatırla
                </label>
                
                <a href="forgot-password.php" style="color: var(--accent-primary); text-decoration: none; font-size: 0.9rem;">
                    🤔 Şifremi unuttum
                </a>
            </div>
            
            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" name="login" class="btn btn-primary w-full">
                    🚀 Giriş Yap
                </button>
            </div>
            
            <!-- Social Login (Future Enhancement) -->
            <div class="social-login" style="margin-top: 1.5rem;">
                <div style="text-align: center; margin-bottom: 1rem; color: var(--text-muted);">
                    <span style="background: var(--bg-card); padding: 0 1rem; position: relative; z-index: 1;">
                        veya
                    </span>
                    <hr style="margin: 0; border: none; height: 1px; background: var(--border-color); position: relative; top: -0.7rem; z-index: 0;">
                </div>
                
                <div style="display: grid; gap: 0.5rem;">
                    <button type="button" class="btn btn-secondary w-full" disabled style="opacity: 0.6;">
                        🔵 Facebook ile Giriş (Yakında)
                    </button>
                    <button type="button" class="btn btn-secondary w-full" disabled style="opacity: 0.6;">
                        🔴 Google ile Giriş (Yakında)
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Register Link -->
    <div class="confession-card" style="text-align: center; margin-top: 1.5rem;">
        <p style="color: var(--text-secondary);">
            Henüz hesabınız yok mu?
            <a href="register.php" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">
                📝 Hemen kayıt olun
            </a>
        </p>
    </div>
    
    <!-- Demo Account Info -->
    <div class="confession-card" style="background: linear-gradient(135deg, var(--bg-hover), var(--bg-card)); border-left: 4px solid var(--accent-secondary);">
        <h3 style="color: var(--accent-secondary); margin-bottom: 1rem; font-size: 1.1rem;">
            🎭 Demo Hesap
        </h3>
        <div style="background: var(--bg-secondary); padding: 1rem; border-radius: var(--border-radius-sm); font-family: monospace; font-size: 0.9rem;">
            <strong>Admin Hesabı:</strong><br>
            Kullanıcı Adı: <span style="color: var(--accent-primary);">admin</span><br>
            Şifre: <span style="color: var(--accent-primary);">admin</span>
        </div>
        <p style="color: var(--text-muted); margin-top: 0.5rem; font-size: 0.9rem;">
            ⚠️ Bu demo hesap ile tüm admin özelliklerini test edebilirsiniz.
        </p>
    </div>
    
    <!-- Security Info -->
    <div style="text-align: center; margin-top: 2rem; padding: 1rem; color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
        <p>
            🔐 <strong>Güvenlik:</strong> Tüm şifreler güvenli algoritmalarla şifrelenir.<br>
            🛡️ <strong>Gizlilik:</strong> Kişisel bilgileriniz üçüncü taraflarla paylaşılmaz.<br>
            📱 <strong>Erişim:</strong> Hesabınıza sadece siz erişebilirsiniz.
        </p>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleBtn = passwordField.nextElementSibling;
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleBtn.innerHTML = '🙈';
    } else {
        passwordField.type = 'password';
        toggleBtn.innerHTML = '👁️';
    }
}

// Auto-fill demo credentials
document.addEventListener('DOMContentLoaded', function() {
    // Add demo fill buttons
    const demoSection = document.querySelector('.confession-card:last-of-type .bg-secondary');
    if (demoSection) {
        const fillBtn = document.createElement('button');
        fillBtn.type = 'button';
        fillBtn.className = 'btn btn-sm';
        fillBtn.style.cssText = 'margin-top: 0.5rem; font-size: 0.8rem; padding: 0.25rem 0.5rem; background: var(--accent-primary); color: white;';
        fillBtn.innerHTML = '🎯 Otomatik Doldur';
        fillBtn.onclick = function() {
            document.getElementById('username').value = 'admin';
            document.getElementById('password').value = 'admin';
        };
        demoSection.appendChild(fillBtn);
    }
    
    // Form validation enhancement
    const form = document.getElementById('login-form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        
        if (!username || !password) {
            e.preventDefault();
            alert('Lütfen tüm alanları doldurun!');
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Giriş yapılıyor...';
        
        // Re-enable button after a delay if form doesn't submit
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '🚀 Giriş Yap';
        }, 5000);
    });
    
    // Focus on username field
    document.getElementById('username').focus();
    
    // Check for caps lock
    document.getElementById('password').addEventListener('keypress', function(e) {
        const capsLockOn = e.getModifierState && e.getModifierState('CapsLock');
        const warningDiv = document.getElementById('caps-warning');
        
        if (capsLockOn && !warningDiv) {
            const warning = document.createElement('div');
            warning.id = 'caps-warning';
            warning.style.cssText = 'color: var(--accent-warning); font-size: 0.8rem; margin-top: 0.25rem;';
            warning.innerHTML = '⚠️ Caps Lock açık';
            this.parentNode.appendChild(warning);
        } else if (!capsLockOn && warningDiv) {
            warningDiv.remove();
        }
    });
});

// Handle enter key on form fields
document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && e.target.matches('#username, #password')) {
        e.preventDefault();
        
        if (e.target.id === 'username') {
            document.getElementById('password').focus();
        } else if (e.target.id === 'password') {
            document.getElementById('login-form').submit();
        }
    }
});

// Auto-logout warning for security
function checkSessionTimeout() {
    const loginTime = <?php echo isset($_SESSION['login_time']) ? $_SESSION['login_time'] : 'null'; ?>;
    const sessionTimeout = <?php echo SESSION_TIMEOUT; ?>;
    
    if (loginTime) {
        const currentTime = Math.floor(Date.now() / 1000);
        const timeLeft = sessionTimeout - (currentTime - loginTime);
        
        if (timeLeft < 300) { // 5 minutes warning
            if (confirm('Oturumunuz yakında sona erecek. Devam etmek istiyor musunuz?')) {
                // Refresh session
                fetch('refresh-session.php', { method: 'POST' });
            }
        }
    }
}

// Check session every 5 minutes
if (<?php echo isLoggedIn() ? 'true' : 'false'; ?>) {
    setInterval(checkSessionTimeout, 300000);
}
</script>

<?php include 'includes/footer.php'; ?>