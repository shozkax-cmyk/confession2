# 🗣️ Confession Hub - Modern İtiraf Platformu

## 📖 Proje Hakkında

Confession Hub, kullanıcıların düşüncelerini güvenle ve anonim olarak paylaşabilecekleri modern bir PHP tabanlı web platformudur. Zengin özellikler ve güvenli altyapı ile tasarlanmış, topluluk odaklı bir itiraf sistemidir.

## ✨ Özellikler

### 🎨 Modern Tasarım
- **Çift Tema Sistemi:** Karanlık ve aydınlık tema seçeneği
- **Responsive Tasarım:** Mobil ve masaüstü uyumlu
- **Gradient Arka Planlar:** Chakra Petch font ailesi
- **Animasyonlar:** Hover efektleri ve geçiş animasyonları
- **Box Shadow & Border Radius:** Modern görsel tasarım

### ✍️ İtiraf Sistemi
- **Zengin Metin Editörü:** Bold, italic, underline, strikethrough
- **Yazı Tipi Özelleştirmesi:** Font boyutu ve renk seçimi
- **Resim Desteği:** 10MB'a kadar resim yükleme
- **Anonim Seçenek:** Kullanıcı adı gizleme imkanı
- **Stil Kaydetme:** Metin biçimlendirmesi veritabanında saklanır

### 💬 Yorum Sistemi
- **Gelişmiş Yorumlama:** Her itiraf altında yorum yapabilme
- **Resimli Yorumlar:** Yorum ile birlikte resim paylaşımı
- **Zengin Metin Desteği:** Yorumlarda da biçimlendirme
- **Real-time Yükleme:** AJAX ile dinamik yorum yükleme

### 👥 Kullanıcı Yönetimi
- **Güvenli Kayıt:** Şifre hash'leme ve validasyon
- **Oturum Yönetimi:** Güvenli session handling
- **Profil Sistemi:** Kullanıcı profil sayfaları
- **Beni Hatırla:** Kalıcı oturum seçeneği

### ⚙️ Admin Panel
- **Kapsamlı Dashboard:** İstatistikler ve genel bakış
- **Kullanıcı Yönetimi:** Kullanıcı ekleme, silme, düzenleme
- **İtiraf Moderasyonu:** İtiraf silme ve düzenleme
- **IP Takibi:** Güvenlik için IP adresi kaydetme
- **Arama ve Filtreleme:** Gelişmiş arama özellikleri
- **Bulk İşlemler:** Toplu moderasyon araçları

### 🛡️ Güvenlik Özellikleri
- **XSS Koruması:** HTML input sanitization
- **SQL Injection Koruması:** Prepared statements
- **File Upload Güvenliği:** Dosya türü ve boyut kontrolü
- **Session Güvenliği:** Secure session management
- **Input Validasyonu:** Comprehensive form validation

## 🚀 Teknoloji Stack

- **Backend:** PHP 7.4+ (PDO, Sessions)
- **Veritabanı:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Styling:** CSS Grid, Flexbox, CSS Variables
- **Icons:** Unicode Emoji
- **Fonts:** Google Fonts (Chakra Petch)

## 📁 Dosya Yapısı

```
/app/
├── index.php              # Ana sayfa
├── login.php              # Giriş sayfası  
├── register.php           # Kayıt sayfası
├── admin.php              # Admin panel
├── profile.php            # Kullanıcı profili
├── logout.php             # Çıkış işlemi
├── config.php             # Konfigürasyon
├── functions.php          # PHP fonksiyonları
├── database.sql           # Veritabanı şeması
├── process-comment.php    # Yorum işleme
├── css/
│   └── style.css          # Ana stil dosyası
├── js/
│   └── main.js           # JavaScript fonksiyonları
├── api/
│   ├── comments.php      # Yorum API'si
│   ├── check-username.php # Kullanıcı adı kontrolü
│   └── delete-confession.php # İtiraf silme
├── includes/
│   ├── header.php        # Sayfa başlığı
│   └── footer.php        # Sayfa altbilgisi
├── uploads/              # Yüklenen resimler
└── .htaccess            # Apache konfigürasyonu
```

## 🗄️ Veritabanı Şeması

### Users Tablosu
```sql
- id (Primary Key)
- username (Unique)
- password (Hashed)
- email (Unique)
- ip (Registration IP)
- registration_date
- is_admin (Boolean)
- is_active (Boolean)
```

### Confessions Tablosu
```sql
- id (Primary Key)
- user_id (Foreign Key)
- text (Confession content)
- date (Creation timestamp)
- ip (Submission IP)
- style (JSON - formatting)
- image (Filename)
- is_anonymous (Boolean)
- comments_count
- is_approved (Boolean)
```

### Comments Tablosu
```sql
- id (Primary Key)
- confession_id (Foreign Key)
- user_id (Foreign Key)  
- text (Comment content)
- date (Creation timestamp)
- ip (Submission IP)
- style (JSON - formatting)
- image (Filename)
- is_approved (Boolean)
```

## 🎯 Demo Bilgileri

### Admin Hesabı
- **Kullanıcı Adı:** admin
- **Şifre:** admin

### Örnek Özellikler
- ✅ 3 örnek itiraf önceden yüklenmiş
- ✅ Tema değiştirme butonu aktif
- ✅ Tüm CRUD işlemleri çalışır durumda
- ✅ Resim yükleme ve önizleme
- ✅ Responsive tasarım test edilmiş

## 📊 İstatistikler

Footer'da dinamik olarak gösterilen:
- 📈 Toplam İtiraf Sayısı
- 👥 Kayıtlı Kullanıcı Sayısı  
- 💬 Toplam Yorum Sayısı
- ⚡ Aktif Kullanıcı Sayısı (Son 30 gün)

## 🔗 Sosyal Özellikler

- 📤 İtiraf paylaşma (URL kopyalama)
- ❤️ Beğeni sistemi (gelecek sürümde)
- 🔄 Real-time güncellemeler
- 📱 PWA desteği (gelecek sürümde)

## 🎨 Tasarım Detayları

### CSS Özellikleri
- **Box Shadow:** `0px 4px 10px rgba(0,0,0,0.2)`
- **Border Radius:** 8px ve üzeri değerler
- **Gradient Backgrounds:** Linear gradientler
- **Hover Effects:** Transform ve renk geçişleri
- **Typography:** Chakra Petch font family

### JavaScript Özellikleri
- **Rich Text Editor:** Tam özellikli metin editörü
- **Theme Manager:** Tema değiştirme sistemi
- **Image Uploader:** Drag & drop desteği
- **Form Validation:** Client-side doğrulama
- **AJAX Requests:** Asenkron veri transferi

## 📋 Kurulum

Detaylı kurulum talimatları için `KURULUM_REHBERI.md` dosyasına bakınız.

### Hızlı Kurulum
```bash
1. Dosyaları web server'a yükleyin
2. MySQL'de confession_db veritabanını oluşturun
3. database.sql dosyasını içe aktarın
4. config.php dosyasını düzenleyin
5. uploads/ dizinine yazma izni verin
```

## 🤝 Katkıda Bulunma

Bu proje açık kaynak geliştirilmiştir. Katkılarınızı bekliyoruz:

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Commit yapın (`git commit -m 'Add some AmazingFeature'`)
4. Push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## 📝 Lisans

Bu proje MIT lisansı altında dağıtılmaktadır. Detaylar için `LICENSE` dosyasına bakınız.

## 🚀 Gelecek Özellikler

- [ ] E-posta bildirimleri
- [ ] Sosyal medya entegrasyonu  
- [ ] Gelişmiş arama ve filtreleme
- [ ] Kullanıcı rozetleri
- [ ] İtiraf kategorileri
- [ ] Real-time chat
- [ ] Mobile app
- [ ] API endpoints

## 📞 İletişim

- **Geliştirici:** Confession Hub Team
- **E-posta:** info@confessionhub.com
- **Website:** https://confessionhub.com

---

**Confession Hub ile düşüncelerinizi özgürce paylaşın! 🗣️💭**
