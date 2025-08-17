<?php
require_once 'config.php';

class ConfessionDB {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // User functions
    public function registerUser($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $ip = getClientIP();
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, email, password, ip) 
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$username, $email, $hashedPassword, $ip]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function loginUser($username, $password) {
        $stmt = $this->pdo->prepare("
            SELECT id, username, password, is_admin, is_active 
            FROM users 
            WHERE username = ? AND is_active = 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['login_time'] = time();
            return true;
        }
        
        return false;
    }
    
    public function getUserById($id) {
        $stmt = $this->pdo->prepare("
            SELECT id, username, email, registration_date, avatar, is_admin 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Confession functions
    public function createConfession($userId, $text, $style = null, $image = null, $isAnonymous = 0) {
        $ip = getClientIP();
        
        $stmt = $this->pdo->prepare("
            INSERT INTO confessions (user_id, text, ip, style, image, is_anonymous) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $userId, 
            $text, 
            $ip, 
            $style ? json_encode($style) : null, 
            $image, 
            $isAnonymous
        ]);
    }
    
    public function getConfessions($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.username, u.avatar,
                   (SELECT COUNT(*) FROM comments WHERE confession_id = c.id AND is_approved = 1) as comments_count
            FROM confessions c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.is_approved = 1 
            ORDER BY c.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getConfessionById($id) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.username, u.avatar 
            FROM confessions c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.id = ? AND c.is_approved = 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Comment functions
    public function createComment($confessionId, $userId, $text, $style = null, $image = null) {
        $ip = getClientIP();
        
        $stmt = $this->pdo->prepare("
            INSERT INTO comments (confession_id, user_id, text, ip, style, image) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $confessionId, 
            $userId, 
            $text, 
            $ip, 
            $style ? json_encode($style) : null, 
            $image
        ]);
    }
    
    public function getCommentsByConfessionId($confessionId) {
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.username, u.avatar 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.confession_id = ? AND c.is_approved = 1 
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$confessionId]);
        return $stmt->fetchAll();
    }
    
    // Statistics functions
    public function getTotalConfessions() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM confessions WHERE is_approved = 1");
        return $stmt->fetchColumn();
    }
    
    public function getTotalUsers() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1");
        return $stmt->fetchColumn();
    }
    
    public function getTotalComments() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM comments WHERE is_approved = 1");
        return $stmt->fetchColumn();
    }
    
    public function getActiveUsers() {
        // Users who posted in last 30 days
        $stmt = $this->pdo->query("
            SELECT COUNT(DISTINCT user_id) 
            FROM confessions 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        return $stmt->fetchColumn();
    }
    
    // Admin functions
    public function getAllUsers($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->pdo->prepare("
            SELECT *, 
                   (SELECT COUNT(*) FROM confessions WHERE user_id = users.id) as confessions_count
            FROM users 
            ORDER BY registration_date DESC 
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function getAllConfessions($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $stmt = $this->pdo->prepare("
            SELECT c.*, u.username, u.email, u.ip as user_ip,
                   (SELECT COUNT(*) FROM comments WHERE confession_id = c.id) as comments_count
            FROM confessions c 
            JOIN users u ON c.user_id = u.id 
            ORDER BY c.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function deleteConfession($id) {
        $stmt = $this->pdo->prepare("DELETE FROM confessions WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function deleteUser($id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0");
        return $stmt->execute([$id]);
    }
}

// File upload handler
function handleImageUpload($file, $uploadDir = 'uploads/') {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error occurred'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File too large (max 10MB)'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $filepath];
    } else {
        return ['success' => false, 'message' => 'Failed to save file'];
    }
}

// Initialize database class
$db = new ConfessionDB($pdo);
?>