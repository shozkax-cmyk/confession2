-- Confession Website Database Schema
-- Database: confession_db
-- Auto-generated SQL file for confession website

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS confession_db;
-- USE confession_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    ip VARCHAR(45) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_admin TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Confessions table
CREATE TABLE IF NOT EXISTS confessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    text TEXT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45) NOT NULL,
    style JSON DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    is_anonymous TINYINT(1) DEFAULT 0,
    likes_count INT DEFAULT 0,
    comments_count INT DEFAULT 0,
    is_approved TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    confession_id INT NOT NULL,
    user_id INT NOT NULL,
    text TEXT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45) NOT NULL,
    style JSON DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    is_approved TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (confession_id) REFERENCES confessions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Likes table (for future enhancement)
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    confession_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (user_id, confession_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (confession_id) REFERENCES confessions(id) ON DELETE CASCADE
);

-- Insert default admin user
INSERT INTO users (username, password, email, ip, is_admin) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@confession.com', '127.0.0.1', 1)
ON DUPLICATE KEY UPDATE username=username;

-- Sample confessions for demo (optional)
INSERT INTO confessions (user_id, text, ip, style) VALUES 
(1, 'Bu itiraf sitesinin ilk mesajı! Hoş geldiniz arkadaşlar. 🎉', '127.0.0.1', '{"bold": true, "color": "#6366f1"}'),
(1, 'Bugün çok güzel bir gün geçirdim ve bunu paylaşmak istedim.', '127.0.0.1', '{"italic": true, "fontSize": "18"}'),
(1, 'Bazen en derin düşüncelerimizi paylaşmak rahatlatıcı oluyor.', '127.0.0.1', '{"underline": true, "color": "#10b981"}')
ON DUPLICATE KEY UPDATE id=id;