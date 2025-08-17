# Confession Hub - Kurulum Rehberi

## 📋 Sistem Gereksinimleri

### Server Gereksinimleri
- **PHP:** 7.4 veya üzeri (8.0+ önerilen)
- **Veritabanı:** MySQL 5.7+ veya MariaDB 10.3+
- **Web Server:** Apache 2.4+ veya Nginx 1.18+
- **Disk Alanı:** En az 500MB (resimler için)

### Gerekli PHP Eklentileri
```bash
# Ubuntu/Debian için
sudo apt update
sudo apt install php php-mysql php-gd php-json php-session php-mbstring php-curl

# CentOS/RHEL için
sudo yum install php php-mysqlnd php-gd php-json php-session php-mbstring php-curl
```

## 🚀 Kurulum Adımları

### 1. Dosyaları Sunucuya Yükleme
```bash
# Tüm dosyaları web server root dizinine kopyalayın
# Apache için: /var/www/html/
# Nginx için: /var/www/html/ veya /usr/share/nginx/html/

# Örnek:
sudo cp -r /path/to/confession-hub/* /var/www/html/
```

### 2. Dizin İzinlerini Ayarlama
```bash
# Web server kullanıcısını bulun (www-data, apache, nginx)
# Ubuntu/Debian için genellikle www-data
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/
sudo chmod -R 777 /var/www/html/uploads/
```

### 3. Veritabanı Kurulumu
```sql
-- MySQL/MariaDB'ye bağlanın
mysql -u root -p

-- Veritabanını oluşturun
CREATE DATABASE confession_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Kullanıcı oluşturun (isteğe bağlı)
CREATE USER 'confession_user'@'localhost' IDENTIFIED BY 'güçlü_şifre_buraya';
GRANT ALL PRIVILEGES ON confession_db.* TO 'confession_user'@'localhost';
FLUSH PRIVILEGES;

-- Veritabanından çıkın
EXIT;

-- SQL dosyasını içe aktarın
mysql -u root -p confession_db < database.sql
```

### 4. Konfigürasyon Ayarları
`config.php` dosyasını düzenleyin:

```php
// Veritabanı ayarları
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'confession_user');  // Veya 'root'
define('DB_PASSWORD', 'güçlü_şifre_buraya');
define('DB_NAME', 'confession_db');

// Site ayarları
define('SITE_NAME', 'Confession Hub');
define('SITE_URL', 'https://yourdomain.com');  // Gerçek domain
```

### 5. Web Server Konfigürasyonu

#### Apache için (.htaccess zaten mevcut)
```apache
# Virtual Host örneği (/etc/apache2/sites-available/confession.conf)
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/html
    
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/confession_error.log
    CustomLog ${APACHE_LOG_DIR}/confession_access.log combined
</VirtualHost>

# mod_rewrite'ı etkinleştirin
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx için
```nginx
# /etc/nginx/sites-available/confession
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/html;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;  # PHP versiyonuna göre ayarlayın
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location /uploads/ {
        location ~ \.php$ {
            deny all;
        }
    }
}

# Site etkinleştirme
sudo ln -s /etc/nginx/sites-available/confession /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 🔧 İlk Kurulum Sonrası

### 1. Admin Hesabı
- **Kullanıcı Adı:** admin
- **Şifre:** admin
- Giriş yaptıktan sonra mutlaka şifreyi değiştirin!

### 2. Güvenlik Kontrolleri
```bash
# Dosya izinlerini kontrol edin
ls -la /var/www/html/
ls -la /var/www/html/uploads/

# PHP hata loglarını kontrol edin
sudo tail -f /var/log/apache2/error.log  # Apache
sudo tail -f /var/log/nginx/error.log    # Nginx
```

### 3. Test İşlemleri
1. Ana sayfayı ziyaret edin: `http://yourdomain.com`
2. Yeni kullanıcı kayıt olun
3. İtiraf paylaşımını test edin
4. Resim yükleme özelliğini test edin
5. Admin paneli erişimini test edin

## 🛡️ Güvenlik Önerileri

### 1. Şifre Güvenliği
```php
// config.php içinde güçlü şifreler kullanın
define('DB_PASSWORD', 'En_az_16_karakter_güçlü_şifre!@#$');
```

### 2. Dosya İzinleri
```bash
# Güvenli izinler
sudo find /var/www/html -type f -exec chmod 644 {} \;
sudo find /var/www/html -type d -exec chmod 755 {} \;
sudo chmod 777 /var/www/html/uploads/  # Sadece uploads dizini
```

### 3. SSL Sertifikası (Önerilen)
```bash
# Let's Encrypt ile ücretsiz SSL
sudo apt install certbot python3-certbot-apache  # Apache için
sudo certbot --apache -d yourdomain.com

# Veya Nginx için
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

### 4. Güvenlik Duvarı
```bash
# UFW ile temel güvenlik duvarı
sudo ufw allow ssh
sudo ufw allow 'Apache Full'  # veya 'Nginx Full'
sudo ufw enable
```

## 🔧 Sorun Giderme

### Yaygın Sorunlar ve Çözümleri

1. **Veritabanı Bağlantı Hatası**
   ```bash
   # MySQL servisini kontrol edin
   sudo systemctl status mysql
   sudo systemctl restart mysql
   ```

2. **Resim Yükleme Sorunu**
   ```bash
   # Upload dizini izinlerini kontrol edin
   sudo chmod 777 /var/www/html/uploads/
   sudo chown www-data:www-data /var/www/html/uploads/
   ```

3. **PHP Hataları**
   ```bash
   # PHP error_log kontrol edin
   sudo tail -f /var/log/apache2/error.log
   
   # PHP ayarlarını kontrol edin
   php -m | grep -E "(pdo_mysql|gd|json)"
   ```

4. **404 Hataları**
   ```bash
   # Apache mod_rewrite kontrol edin
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

## 📊 Performans Optimizasyonu

### 1. PHP Ayarları (php.ini)
```ini
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 12M
max_execution_time = 300
```

### 2. MySQL Optimizasyonu
```sql
-- Büyük veritabanları için
SET GLOBAL innodb_buffer_pool_size = 256M;
```

### 3. Önbellek (İsteğe bağlı)
```bash
# APCu kurulumu
sudo apt install php-apcu
```

## 📞 Destek ve İletişim

### Log Dosyaları Konumları
- **Apache:** `/var/log/apache2/`
- **Nginx:** `/var/log/nginx/`
- **PHP:** `/var/log/php_errors.log`
- **MySQL:** `/var/log/mysql/`

### Sistem Bilgileri
```bash
# Sistem durumu kontrolü
php --version
mysql --version
apache2 -v  # veya nginx -v

# Disk kullanımı
df -h
du -sh /var/www/html/uploads/
```

---

## 🎉 Kurulum Tamamlandı!

Başarılı kurulum sonrası:
- Ana sayfa: `http://yourdomain.com`
- Admin panel: `http://yourdomain.com/admin.php`
- Admin giriş: **admin** / **admin** (değiştirin!)

**İyi kullanımlar! 🚀**