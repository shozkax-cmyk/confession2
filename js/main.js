// Main JavaScript file for Confession Website

// Theme Management
class ThemeManager {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }
    
    init() {
        this.applyTheme(this.theme);
        this.bindEvents();
    }
    
    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        this.theme = theme;
        
        // Update theme toggle icon
        const themeToggle = document.querySelector('.theme-toggle');
        if (themeToggle) {
            themeToggle.innerHTML = theme === 'dark' ? '☀️' : '🌙';
        }
    }
    
    toggle() {
        const newTheme = this.theme === 'dark' ? 'light' : 'dark';
        this.applyTheme(newTheme);
    }
    
    bindEvents() {
        const themeToggle = document.querySelector('.theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => this.toggle());
        }
    }
}

// Rich Text Editor
class RichTextEditor {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.content = null;
        this.toolbar = null;
        this.init();
    }
    
    init() {
        if (!this.container) return;
        
        this.createEditor();
        this.bindEvents();
    }
    
    createEditor() {
        this.container.innerHTML = `
            <div class="rich-editor">
                <div class="editor-toolbar">
                    <button type="button" class="editor-btn" data-command="bold" title="Bold">
                        <strong>B</strong>
                    </button>
                    <button type="button" class="editor-btn" data-command="italic" title="Italic">
                        <em>I</em>
                    </button>
                    <button type="button" class="editor-btn" data-command="underline" title="Underline">
                        <u>U</u>
                    </button>
                    <button type="button" class="editor-btn" data-command="strikeThrough" title="Strikethrough">
                        <s>S</s>
                    </button>
                    <div class="editor-separator"></div>
                    <select class="editor-btn" data-command="fontSize" title="Font Size">
                        <option value="12">12px</option>
                        <option value="14" selected>14px</option>
                        <option value="16">16px</option>
                        <option value="18">18px</option>
                        <option value="20">20px</option>
                        <option value="24">24px</option>
                    </select>
                    <input type="color" class="editor-btn" data-command="foreColor" title="Text Color" value="#000000">
                    <button type="button" class="editor-btn" data-command="removeFormat" title="Clear Formatting">
                        Clear
                    </button>
                </div>
                <div class="editor-content" contenteditable="true" placeholder="Write your confession..."></div>
            </div>
        `;
        
        this.toolbar = this.container.querySelector('.editor-toolbar');
        this.content = this.container.querySelector('.editor-content');
    }
    
    bindEvents() {
        if (!this.toolbar || !this.content) return;
        
        // Toolbar button events
        this.toolbar.addEventListener('click', (e) => {
            const button = e.target.closest('[data-command]');
            if (button) {
                e.preventDefault();
                this.executeCommand(button);
            }
        });
        
        // Content events
        this.content.addEventListener('keyup', () => {
            this.updateToolbar();
        });
        
        this.content.addEventListener('mouseup', () => {
            this.updateToolbar();
        });
        
        // Focus events
        this.content.addEventListener('focus', () => {
            this.container.style.borderColor = 'var(--accent-primary)';
        });
        
        this.content.addEventListener('blur', () => {
            this.container.style.borderColor = 'var(--border-color)';
        });
    }
    
    executeCommand(button) {
        const command = button.dataset.command;
        let value = null;
        
        if (command === 'fontSize') {
            value = button.value + 'px';
        } else if (command === 'foreColor') {
            value = button.value;
        }
        
        document.execCommand(command, false, value);
        this.content.focus();
        this.updateToolbar();
    }
    
    updateToolbar() {
        const buttons = this.toolbar.querySelectorAll('[data-command]');
        
        buttons.forEach(button => {
            const command = button.dataset.command;
            
            if (['bold', 'italic', 'underline', 'strikeThrough'].includes(command)) {
                if (document.queryCommandState(command)) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            }
        });
    }
    
    getContent() {
        return this.content ? this.content.innerHTML : '';
    }
    
    setContent(html) {
        if (this.content) {
            this.content.innerHTML = html;
        }
    }
    
    getTextStyle() {
        if (!this.content) return {};
        
        const selection = window.getSelection();
        if (selection.rangeCount === 0) return {};
        
        const range = selection.getRangeAt(0);
        const span = document.createElement('span');
        
        try {
            range.surroundContents(span);
            const computedStyle = window.getComputedStyle(span);
            
            const style = {
                fontWeight: computedStyle.fontWeight,
                fontStyle: computedStyle.fontStyle,
                textDecoration: computedStyle.textDecoration,
                fontSize: computedStyle.fontSize,
                color: computedStyle.color
            };
            
            span.outerHTML = span.innerHTML;
            return style;
        } catch (e) {
            return {};
        }
    }
}

// Image Upload Handler
class ImageUploader {
    constructor(inputId, previewId) {
        this.input = document.getElementById(inputId);
        this.preview = document.getElementById(previewId);
        this.init();
    }
    
    init() {
        if (!this.input) return;
        
        this.input.addEventListener('change', (e) => {
            this.handleFileSelect(e);
        });
    }
    
    handleFileSelect(event) {
        const file = event.target.files[0];
        
        if (!file) {
            this.clearPreview();
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('Geçersiz dosya türü! Sadece JPEG, PNG, GIF ve WebP dosyaları kabul edilir.');
            this.clearPreview();
            return;
        }
        
        // Validate file size (10MB)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('Dosya boyutu çok büyük! Maksimum 10MB olmalıdır.');
            this.clearPreview();
            return;
        }
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            this.showPreview(e.target.result, file.name);
        };
        reader.readAsDataURL(file);
    }
    
    showPreview(src, filename) {
        if (this.preview) {
            this.preview.innerHTML = `
                <div class="image-preview">
                    <img src="${src}" alt="Preview">
                    <p class="preview-filename">${filename}</p>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.image-preview').parentNode.innerHTML='';">
                        Remove
                    </button>
                </div>
            `;
            this.preview.style.display = 'block';
        }
    }
    
    clearPreview() {
        if (this.preview) {
            this.preview.innerHTML = '';
            this.preview.style.display = 'none';
        }
        if (this.input) {
            this.input.value = '';
        }
    }
}

// Form Validation
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.init();
    }
    
    init() {
        if (!this.form) return;
        
        this.form.addEventListener('submit', (e) => {
            if (!this.validate()) {
                e.preventDefault();
            }
        });
    }
    
    validate() {
        let isValid = true;
        const inputs = this.form.querySelectorAll('input[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                this.showError(input, 'Bu alan zorunludur.');
                isValid = false;
            } else {
                this.clearError(input);
            }
        });
        
        // Email validation
        const emailInputs = this.form.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            if (input.value && !this.isValidEmail(input.value)) {
                this.showError(input, 'Geçerli bir e-posta adresi girin.');
                isValid = false;
            }
        });
        
        // Password validation
        const passwordInputs = this.form.querySelectorAll('input[type="password"]');
        passwordInputs.forEach(input => {
            if (input.value && input.value.length < 6) {
                this.showError(input, 'Şifre en az 6 karakter olmalıdır.');
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    showError(input, message) {
        this.clearError(input);
        
        const error = document.createElement('div');
        error.className = 'error-message';
        error.textContent = message;
        error.style.color = 'var(--accent-danger)';
        error.style.fontSize = '0.875rem';
        error.style.marginTop = '0.25rem';
        
        input.style.borderColor = 'var(--accent-danger)';
        input.parentNode.appendChild(error);
    }
    
    clearError(input) {
        const existingError = input.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        input.style.borderColor = 'var(--border-color)';
    }
    
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
}

// AJAX Request Handler
class AjaxHandler {
    static async request(url, options = {}) {
        const defaultOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        };
        
        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, finalOptions);
            const data = await response.json();
            return { success: response.ok, data };
        } catch (error) {
            return { success: false, error: error.message };
        }
    }
    
    static async submitForm(form) {
        const formData = new FormData(form);
        const url = form.action || window.location.href;
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            
            return { success: response.ok, response };
        } catch (error) {
            return { success: false, error: error.message };
        }
    }
}

// Comment System
class CommentSystem {
    constructor() {
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadComments();
    }
    
    bindEvents() {
        // Comment form submissions
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('comment-form')) {
                e.preventDefault();
                this.submitComment(e.target);
            }
        });
        
        // Comment toggle buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('toggle-comments')) {
                e.preventDefault();
                this.toggleComments(e.target);
            }
        });
    }
    
    async submitComment(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        // Show loading state
        submitBtn.textContent = 'Gönderiliyor...';
        submitBtn.disabled = true;
        
        try {
            const result = await AjaxHandler.submitForm(form);
            
            if (result.success) {
                // Clear form
                form.reset();
                
                // Reload comments for this confession
                const confessionId = form.dataset.confessionId;
                this.loadCommentsForConfession(confessionId);
                
                // Show success message
                this.showMessage('Yorum başarıyla eklendi!', 'success');
            } else {
                this.showMessage('Yorum eklenirken hata oluştu.', 'error');
            }
        } catch (error) {
            this.showMessage('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
        }
        
        // Reset button
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
    
    toggleComments(button) {
        const confessionId = button.dataset.confessionId;
        const commentsSection = document.querySelector(`#comments-${confessionId}`);
        
        if (commentsSection.style.display === 'none' || !commentsSection.style.display) {
            commentsSection.style.display = 'block';
            button.textContent = 'Yorumları Gizle';
            this.loadCommentsForConfession(confessionId);
        } else {
            commentsSection.style.display = 'none';
            button.textContent = 'Yorumları Göster';
        }
    }
    
    async loadCommentsForConfession(confessionId) {
        const commentsContainer = document.querySelector(`#comments-list-${confessionId}`);
        if (!commentsContainer) return;
        
        try {
            const result = await AjaxHandler.request(`/api/comments.php?confession_id=${confessionId}`, {
                method: 'GET'
            });
            
            if (result.success && result.data.comments) {
                this.renderComments(result.data.comments, commentsContainer);
            }
        } catch (error) {
            console.error('Error loading comments:', error);
        }
    }
    
    renderComments(comments, container) {
        container.innerHTML = comments.map(comment => `
            <div class="comment fade-in-up">
                <div class="comment-header">
                    <div class="user-avatar">${comment.username.charAt(0).toUpperCase()}</div>
                    <div>
                        <span class="comment-author">${comment.username}</span>
                        <span class="comment-time">${this.formatTime(comment.date)}</span>
                    </div>
                </div>
                <div class="comment-content">${comment.text}</div>
                ${comment.image ? `<div class="comment-image"><img src="uploads/${comment.image}" alt="Comment image"></div>` : ''}
            </div>
        `).join('');
    }
    
    loadComments() {
        // Load comments for all visible confessions
        const confessions = document.querySelectorAll('[data-confession-id]');
        confessions.forEach(confession => {
            const confessionId = confession.dataset.confessionId;
            // Comments are loaded on demand when user clicks toggle
        });
    }
    
    formatTime(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diff = Math.floor((now - date) / 1000);
        
        if (diff < 60) return 'az önce';
        if (diff < 3600) return Math.floor(diff / 60) + ' dakika önce';
        if (diff < 86400) return Math.floor(diff / 3600) + ' saat önce';
        if (diff < 604800) return Math.floor(diff / 86400) + ' gün önce';
        
        return date.toLocaleDateString('tr-TR');
    }
    
    showMessage(message, type = 'info') {
        const messageDiv = document.createElement('div');
        messageDiv.className = `alert alert-${type}`;
        messageDiv.textContent = message;
        
        // Style the message
        Object.assign(messageDiv.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '1rem 1.5rem',
            borderRadius: 'var(--border-radius-sm)',
            backgroundColor: type === 'success' ? 'var(--accent-secondary)' : 'var(--accent-danger)',
            color: 'white',
            zIndex: '9999',
            boxShadow: 'var(--shadow-lg)',
            animation: 'slideIn 0.3s ease-out'
        });
        
        document.body.appendChild(messageDiv);
        
        // Remove after 3 seconds
        setTimeout(() => {
            messageDiv.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => messageDiv.remove(), 300);
        }, 3000);
    }
}

// Statistics Counter Animation
class StatsAnimator {
    constructor() {
        this.init();
    }
    
    init() {
        const statNumbers = document.querySelectorAll('.stat-number');
        
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.animateNumber(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            statNumbers.forEach(stat => observer.observe(stat));
        } else {
            statNumbers.forEach(stat => this.animateNumber(stat));
        }
    }
    
    animateNumber(element) {
        const finalNumber = parseInt(element.textContent) || 0;
        const duration = 2000;
        const steps = 60;
        const stepSize = finalNumber / steps;
        let currentNumber = 0;
        let step = 0;
        
        const timer = setInterval(() => {
            step++;
            currentNumber += stepSize;
            
            if (step >= steps) {
                currentNumber = finalNumber;
                clearInterval(timer);
            }
            
            element.textContent = Math.floor(currentNumber);
        }, duration / steps);
    }
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize theme manager
    new ThemeManager();
    
    // Initialize rich text editor if present
    const editorContainer = document.getElementById('confession-editor');
    if (editorContainer) {
        window.confessionEditor = new RichTextEditor('confession-editor');
    }
    
    // Initialize comment editor if present
    const commentEditorContainer = document.getElementById('comment-editor');
    if (commentEditorContainer) {
        window.commentEditor = new RichTextEditor('comment-editor');
    }
    
    // Initialize image uploaders
    const confessionImageInput = document.getElementById('confession-image');
    if (confessionImageInput) {
        new ImageUploader('confession-image', 'confession-image-preview');
    }
    
    const commentImageInput = document.getElementById('comment-image');
    if (commentImageInput) {
        new ImageUploader('comment-image', 'comment-image-preview');
    }
    
    // Initialize form validators
    const confessionForm = document.getElementById('confession-form');
    if (confessionForm) {
        new FormValidator('confession-form');
    }
    
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        new FormValidator('login-form');
    }
    
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        new FormValidator('register-form');
    }
    
    // Initialize comment system
    new CommentSystem();
    
    // Initialize stats animator
    new StatsAnimator();
    
    // Add smooth scrolling to anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Add fade-in animation to confession cards
    const confessionCards = document.querySelectorAll('.confession-card');
    confessionCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in-up');
        }, index * 100);
    });
});

// Export for global access
window.ThemeManager = ThemeManager;
window.RichTextEditor = RichTextEditor;
window.ImageUploader = ImageUploader;
window.FormValidator = FormValidator;
window.AjaxHandler = AjaxHandler;