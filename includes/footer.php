</div> <!-- End container -->
    </main> <!-- End main-content -->
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <!-- Statistics Section -->
                <div class="footer-section">
                    <h3>📊 İstatistikler</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span class="stat-number" id="total-confessions">
                                <?php echo $db->getTotalConfessions(); ?>
                            </span>
                            <span class="stat-label">Toplam İtiraf</span>
                        </div>
                        
                        <div class="stat-item">
                            <span class="stat-number" id="active-users">
                                <?php echo $db->getActiveUsers(); ?>
                            </span>
                            <span class="stat-label">Aktif Kullanıcı</span>
                        </div>
                        
                        <div class="stat-item">
                            <span class="stat-number" id="total-comments">
                                <?php echo $db->getTotalComments(); ?>
                            </span>
                            <span class="stat-label">Toplam Yorum</span>
                        </div>
                        
                        <div class="stat-item">
                            <span class="stat-number" id="total-users">
                                <?php echo $db->getTotalUsers(); ?>
                            </span>
                            <span class="stat-label">Kayıtlı Üye</span>
                        </div>
                    </div>
                </div>
                
                <!-- About Section -->
                <div class="footer-section">
                    <h3>ℹ️ Hakkımızda</h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 1rem;">
                        Confession Hub, düşüncelerinizi güvenle ve anonim olarak paylaşabileceğiniz 
                        modern bir platform. Toplumumuzda açık iletişimi destekler ve herkesin 
                        sesini duyurmasına olanak sağlarız.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link" title="Facebook" aria-label="Facebook">📘</a>
                        <a href="#" class="social-link" title="Twitter" aria-label="Twitter">🐦</a>
                        <a href="#" class="social-link" title="Instagram" aria-label="Instagram">📷</a>
                        <a href="#" class="social-link" title="LinkedIn" aria-label="LinkedIn">💼</a>
                    </div>
                </div>
                
                <!-- Quick Links Section -->
                <div class="footer-section">
                    <h3>🔗 Hızlı Bağlantılar</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="index.php" style="color: var(--text-secondary); text-decoration: none; transition: var(--transition);" 
                           onmouseover="this.style.color='var(--accent-primary)'" 
                           onmouseout="this.style.color='var(--text-secondary)'">🏠 Ana Sayfa</a>
                        
                        <?php if (!isLoggedIn()): ?>
                            <a href="register.php" style="color: var(--text-secondary); text-decoration: none; transition: var(--transition);" 
                               onmouseover="this.style.color='var(--accent-primary)'" 
                               onmouseout="this.style.color='var(--text-secondary)'">📝 Kayıt Ol</a>
                            <a href="login.php" style="color: var(--text-secondary); text-decoration: none; transition: var(--transition);" 
                               onmouseover="this.style.color='var(--accent-primary)'" 
                               onmouseout="this.style.color='var(--text-secondary)'">🔐 Giriş Yap</a>
                        <?php else: ?>
                            <a href="profile.php" style="color: var(--text-secondary); text-decoration: none; transition: var(--transition);" 
                               onmouseover="this.style.color='var(--accent-primary)'" 
                               onmouseout="this.style.color='var(--text-secondary)'">👤 Profilim</a>
                            <?php if (isAdmin()): ?>
                                <a href="admin.php" style="color: var(--text-secondary); text-decoration: none; transition: var(--transition);" 
                                   onmouseover="this.style.color='var(--accent-primary)'" 
                                   onmouseout="this.style.color='var(--text-secondary)'">⚙️ Admin Panel</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <a href="privacy.php" style="color: var(--text-secondary); text-decoration: none; transition: var(--transition);" 
                           onmouseover="this.style.color='var(--accent-primary)'" 
                           onmouseout="this.style.color='var(--text-secondary)'">🔒 Gizlilik Politikası</a>
                        <a href="terms.php" style="color: var(--text-secondary); text-decoration: none; transition: var(--transition);" 
                           onmouseover="this.style.color='var(--accent-primary)'" 
                           onmouseout="this.style.color='var(--text-secondary)'">📋 Kullanım Şartları</a>
                        <a href="contact.php" style="color: var(--text-secondary); text-decoration: none; transition: var(--transition);" 
                           onmouseover="this.style.color='var(--accent-primary)'" 
                           onmouseout="this.style.color='var(--text-secondary)'">📞 İletişim</a>
                    </div>
                </div>
                
                <!-- Contact & Support Section -->
                <div class="footer-section">
                    <h3>📞 İletişim & Destek</h3>
                    <div style="color: var(--text-secondary); line-height: 1.6;">
                        <p>📧 <strong>E-posta:</strong> info@confessionhub.com</p>
                        <p>💬 <strong>Destek:</strong> support@confessionhub.com</p>
                        <p>🕐 <strong>Destek Saatleri:</strong> 09:00 - 18:00 (Pazartesi-Cuma)</p>
                        <p style="margin-top: 1rem; font-size: 0.9rem;">
                            <strong>🔐 Gizlilik Taahhüdü:</strong><br>
                            Tüm itiraflarınız güvenli şekilde saklanır ve gizliliğiniz korunur.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Copyright Section -->
            <div style="text-align: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); color: var(--text-muted);">
                <p>© <?php echo date('Y'); ?> Confession Hub. Tüm hakları saklıdır. 💜</p>
                <p style="font-size: 0.9rem; margin-top: 0.5rem;">
                    Modern PHP ile geliştirilmiştir | Güvenli & Anonim Platform
                </p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="js/main.js"></script>
    
    <!-- Additional JavaScript for specific pages -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Google Analytics (if needed) -->
    <?php if (defined('GA_TRACKING_ID') && GA_TRACKING_ID): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo GA_TRACKING_ID; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo GA_TRACKING_ID; ?>');
    </script>
    <?php endif; ?>
    
    <!-- Service Worker (for future PWA support) -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                // navigator.registerServiceWorker('/sw.js'); // Uncomment when SW is implemented
            });
        }
    </script>
    
</body>
</html>