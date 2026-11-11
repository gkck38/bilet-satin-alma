# 🎫 Bilet Satın Alma Platformu

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-3-003B57?style=for-the-badge&logo=sqlite&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**Modern, güvenli ve kullanıcı dostu otobüs bileti satın alma platformu**

[Özellikler](#-özellikler) •
[Kurulum](#-kurulum) •
[Kullanım](#-kullanım) •
[Demo Hesaplar](#-demo-hesaplar) •
[Dokümantasyon](#-dokümantasyon)

</div>

---

## 📋 İçindekiler

- [Özellikler](#-özellikler)
- [Teknolojiler](#-teknolojiler)
- [Sistem Gereksinimleri](#-sistem-gereksinimleri)
- [Kurulum](#-kurulum)
  - [Docker ile Kurulum (Önerilen)](#docker-ile-kurulum-önerilen)
  - [Manuel Kurulum](#manuel-kurulum)
- [Kullanım](#-kullanım)
- [Demo Hesaplar](#-demo-hesaplar)
- [Proje Yapısı](#-proje-yapısı)
- [Veritabanı Şeması](#-veritabanı-şeması)
- [Güvenlik](#-güvenlik)
- [API Dokümantasyonu](#-api-dokümantasyonu)
- [Katkıda Bulunma](#-katkıda-bulunma)
- [Lisans](#-lisans)

---

## ✨ Özellikler

### 👥 4 Farklı Kullanıcı Rolü

<table>
<tr>
<td width="25%">

#### 🌐 Ziyaretçi
- Sefer arama
- Sefer listeleme
- Detayları görüntüleme

</td>
<td width="25%">

#### 👤 User (Yolcu)
- Bilet satın alma
- Koltuk seçimi
- Kupon kullanma
- PDF indirme
- Bilet iptali

</td>
<td width="25%">

#### 🏢 Firma Admin
- Sefer ekleme
- Sefer düzenleme
- Sefer silme
- Dashboard

</td>
<td width="25%">

#### 👨‍💼 Admin
- Firma yönetimi
- Admin atama
- Kupon yönetimi
- Sistem istatistikleri

</td>
</tr>
</table>

### 🎯 Ana Özellikler

✅ **Bilet İşlemleri**
- Koltuk seçimi (dolu koltuklar otomatik pasif)
- İndirim kuponu sistemi
- Sanal kredi ile ödeme
- PDF bilet üretimi
- Son 1 saat iptal kuralı

✅ **Güvenlik**
- SQL Injection koruması (PDO Prepared Statements)
- XSS koruması (htmlspecialchars)
- Password hashing (bcrypt)
- Session yönetimi
- Rol tabanlı yetkilendirme

✅ **Modern Arayüz**
- Bootstrap 5 ile responsive tasarım
- Kullanıcı dostu arayüz
- Flash mesaj sistemi
- Dinamik menüler

---

## 🛠 Teknolojiler

<table>
<tr>
<td align="center" width="96">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" width="48" height="48" alt="PHP" />
<br>PHP 8.2
</td>
<td align="center" width="96">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/sqlite/sqlite-original.svg" width="48" height="48" alt="SQLite" />
<br>SQLite 3
</td>
<td align="center" width="96">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg" width="48" height="48" alt="Bootstrap" />
<br>Bootstrap 5
</td>
<td align="center" width="96">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" width="48" height="48" alt="JavaScript" />
<br>JavaScript
</td>
<td align="center" width="96">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/docker/docker-original.svg" width="48" height="48" alt="Docker" />
<br>Docker
</td>
</tr>
</table>

- **Backend:** PHP 8.2
- **Veritabanı:** SQLite 3
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Server:** Apache 2.4
- **Containerization:** Docker & Docker Compose

---

## 💻 Sistem Gereksinimleri

### Docker ile Kurulum (Önerilen) ⭐
```
✅ Docker 20.10+
✅ Docker Compose 2.0+
```

### Manuel Kurulum
```
✅ PHP 8.0 veya üzeri
✅ SQLite3 PHP eklentisi
✅ Apache veya Nginx
✅ Composer (opsiyonel)
```

---

## 🚀 Kurulum

### Docker ile Kurulum (Önerilen) ⭐

#### 1️⃣ Projeyi Klonlayın

```bash
git clone https://github.com/gkck38/bilet-satin-alma.git
cd bilet-satin-alma
```

#### 2️⃣ Otomatik Kurulum

**Linux/Mac:**
```bash
chmod +x install.sh
./install.sh
```

**Windows (Git Bash):**
```bash
sh install.sh
```

#### 3️⃣ Manuel Docker Kurulumu

```bash
# Docker image'ı oluştur
docker-compose build

# Container'ı başlat
docker-compose up -d

# Veritabanını başlat
docker exec bilet-platform php init_db.php
```

#### 4️⃣ Tarayıcıda Açın

```
🌐 http://localhost:8080
```

🎉 **Tebrikler! Kurulum tamamlandı!**

---

### Manuel Kurulum

#### 1️⃣ Projeyi İndirin

```bash
git clone https://github.com/kullanici-adi/bilet-satin-alma.git
cd bilet-satin-alma
```

#### 2️⃣ Veritabanını Başlatın

```bash
php init_db.php
```

#### 3️⃣ Web Sunucusunu Ayarlayın

**Apache:**
```bash
# Proje dizinini DocumentRoot olarak ayarlayın
sudo ln -s /path/to/bilet-satin-alma /var/www/html/

# Apache'yi yeniden başlatın
sudo systemctl restart apache2
```

**PHP Built-in Server (Development):**
```bash
php -S localhost:8080 -t .
```

#### 4️⃣ İzinleri Ayarlayın

```bash
chmod -R 755 .
chmod 777 database.db
```

#### 5️⃣ Tarayıcıda Açın

```
🌐 http://localhost:8080
```

---

## 📖 Kullanım

### 🎬 Hızlı Başlangıç

#### 1. Ana Sayfa
- Kalkış ve varış şehri seçin
- Tarih seçin
- "Sefer Ara" butonuna tıklayın

#### 2. Bilet Satın Alma (User)
```
1. Giriş yapın (demo_user / user123)
2. Sefer arayın
3. Sefer detayına gidin
4. "Bilet Satın Al" butonuna tıklayın
5. Koltuk seçin (dolu koltuklar gri)
6. İsterseniz kupon kodu girin (YAZ2025)
7. "Ödemeyi Tamamla"
8. Biletlerim'den PDF indirin
```

#### 3. Sefer Ekleme (Firma Admin)
```
1. Giriş yapın (metro_admin / firma123)
2. "Firma Paneli" → "Yeni Sefer Ekle"
3. Formu doldurun
4. Kaydet
5. "Seferleri Yönet"'ten düzenleyin/silin
```

#### 4. Sistem Yönetimi (Admin)
```
1. Giriş yapın (admin / admin123)
2. "Admin Paneli"
3. Firma/Admin/Kupon yönetimi
```

---

## 🔐 Demo Hesaplar

<table>
<tr>
<th>Rol</th>
<th>Kullanıcı Adı</th>
<th>Şifre</th>
<th>Özellikler</th>
</tr>
<tr>
<td>👨‍💼 <strong>Admin</strong></td>
<td><code>admin</code></td>
<td><code>admin123</code></td>
<td>
• Tam sistem yetkisi<br>
• Firma yönetimi<br>
• Kupon yönetimi
</td>
</tr>
<tr>
<td>🏢 <strong>Firma Admin</strong></td>
<td><code>metro_admin</code></td>
<td><code>firma123</code></td>
<td>
• Metro Turizm yetkisi<br>
• Sefer CRUD işlemleri<br>
• Dashboard
</td>
</tr>
<tr>
<td>👤 <strong>User</strong></td>
<td><code>demo_user</code></td>
<td><code>user123</code></td>
<td>
• 1000₺ başlangıç kredisi<br>
• Bilet alma/iptal<br>
• PDF indirme
</td>
</tr>
</table>

### 🏷️ Örnek Kupon Kodları

| Kod | İndirim | Limit | Bitiş |
|-----|---------|-------|-------|
| `YAZ2025` | %15 | 100 | 31.12.2025 |
| `ILKSEFERIM` | %20 | 50 | 30.11.2025 |
| `OGRENCI10` | %10 | 200 | 31.12.2025 |

---

## 📁 Proje Yapısı

```
bilet-satin-alma/
│
├── 📄 index.php                      # Ana sayfa
├── 📄 login.php                      # Giriş
├── 📄 register.php                   # Kayıt
├── 📄 logout.php                     # Çıkış
├── 📄 trip_details.php               # Sefer detayları
├── 📄 download_ticket.php            # PDF bilet
│
├── 📁 config/                        # Yapılandırma
│   ├── config.php                    # Ana config
│   └── database.php                  # DB bağlantısı
│
├── 📁 includes/                      # Ortak dosyalar
│   ├── header.php                    # Başlık
│   ├── footer.php                    # Alt bilgi
│   ├── functions.php                 # 50+ fonksiyon
│   └── auth.php                      # Kimlik doğrulama
│
├── 📁 pages/                         # Sayfa dosyaları
│   ├── 📁 user/                      # Kullanıcı sayfaları
│   │   ├── my_tickets.php            # Biletlerim
│   │   ├── buy_ticket.php            # Bilet al
│   │   └── cancel_ticket.php         # Bilet iptal
│   │
│   ├── 📁 company_admin/             # Firma admin
│   │   ├── dashboard.php             # Dashboard
│   │   ├── manage_trips.php          # Sefer yönetimi
│   │   └── add_edit_trip.php         # Sefer ekle/düzenle
│   │
│   └── 📁 admin/                     # Admin sayfaları
│       ├── dashboard.php             # Dashboard
│       ├── manage_companies.php      # Firma yönetimi
│       ├── manage_company_admins.php # Admin yönetimi
│       └── manage_coupons.php        # Kupon yönetimi
│
├── 📁 assets/                        # Statik dosyalar
│   ├── 📁 css/
│   │   └── style.css                 # Özel CSS
│   └── 📁 js/
│       └── main.js                   # JavaScript
│
├── 🗄️ database.sql                   # DB şeması
├── 🗄️ database.db                    # SQLite dosyası
├── 📄 init_db.php                    # DB başlatma
│
├── 🐳 Dockerfile                     # Docker image
├── 🐳 docker-compose.yml             # Docker compose
├── 📄 .dockerignore                  # Docker ignore
├── 📄 .htaccess                      # Apache config
├── 📄 .gitignore                     # Git ignore
│
├── 📜 Makefile                       # Hızlı komutlar
├── 📜 install.sh                     # Kurulum scripti
├── 📜 deploy.sh                      # Deploy scripti
│
└── 📚 Dokümantasyon
    ├── README.md                     # Bu dosya
    ├── CONTRIBUTING.md               # Katkı rehberi
    ├── CHANGELOG.md                  # Değişiklikler
    └── SECURITY.md                   # Güvenlik
```

---

## 🗄️ Veritabanı Şeması

```
┌─────────────┐       ┌──────────────┐       ┌─────────────┐
│   users     │       │  companies   │       │    trips    │
├─────────────┤       ├──────────────┤       ├─────────────┤
│ id          │──┐    │ id           │◄──────│ company_id  │
│ username    │  │    │ name         │       │ departure   │
│ email       │  │    │ phone        │       │ arrival     │
│ password    │  │    │ email        │       │ date        │
│ full_name   │  │    │ address      │       │ time        │
│ phone       │  │    └──────────────┘       │ price       │
│ role        │  │                           │ seats       │
│ company_id  │──┘    ┌──────────────┐       └─────────────┘
│ credit      │       │   coupons    │             │
└─────────────┘       ├──────────────┤             │
      │               │ id           │             │
      │               │ code         │             │
      │               │ discount_rate│             │
      │               │ usage_limit  │             │
      │               │ used_count   │             │
      │               │ expiry_date  │             │
      │               │ is_active    │             │
      │               └──────────────┘             │
      │                                            │
      │               ┌──────────────┐             │
      └──────────────►│   tickets    │◄────────────┘
                      ├──────────────┤
                      │ id           │
                      │ user_id      │
                      │ trip_id      │
                      │ seat_number  │
                      │ price        │
                      │ coupon_code  │
                      │ discount     │
                      │ final_price  │
                      │ status       │
                      └──────────────┘
```

---

## 🔒 Güvenlik

### Uygulanan Güvenlik Önlemleri

✅ **SQL Injection Koruması**
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
```

✅ **XSS Koruması**
```php
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
```

✅ **Password Security**
```php
$hash = password_hash($password, PASSWORD_DEFAULT);
password_verify($password, $hash);
```

✅ **Session Yönetimi**
- Secure session ayarları
- Session hijacking koruması
- Otomatik logout

✅ **Role-Based Access Control**
```php
requireLogin();
requireRole(ROLE_ADMIN);
```

### Güvenlik Başlıkları (.htaccess)

```apache
Header set X-XSS-Protection "1; mode=block"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-Content-Type-Options "nosniff"
```

---

## ⚙️ Makefile Komutları

```bash
make help          # Tüm komutları listele
make install       # Projeyi kur
make start         # Başlat
make stop          # Durdur
make restart       # Yeniden başlat
make logs          # Logları göster
make shell         # Container'a gir
make clean         # Temizlik yap
make init-db       # DB'yi sıfırla
make backup        # DB yedekle
```

---

## 🐛 Sorun Giderme

### Port 8080 kullanımda

```bash
# docker-compose.yml dosyasında portu değiştirin
ports:
  - "8081:80"
```

### Veritabanı hatası

```bash
rm database.db
php init_db.php
```

### İzin hatası

```bash
chmod -R 755 .
chmod 777 database.db
```

### Docker container başlamıyor

```bash
docker-compose logs
docker-compose down
docker-compose up -d --build
```

---

## 📊 İstatistikler

<div align="center">

| Özellik | Sayı |
|---------|------|
| 📄 PHP Dosyası | 30+ |
| 🔧 Fonksiyon | 50+ |
| 📝 Kod Satırı | 7000+ |
| 🗄️ Veritabanı Tablosu | 5 |
| 👥 Kullanıcı Rolü | 4 |
| 🎫 Demo Bilet | 5 |
| 🏷️ Demo Kupon | 3 |

</div>

---

## 🤝 Katkıda Bulunma

Katkılarınızı bekliyoruz! Lütfen [CONTRIBUTING.md](CONTRIBUTING.md) dosyasını okuyun.

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing`)
3. Commit edin (`git commit -m 'Add amazing feature'`)
4. Push edin (`git push origin feature/amazing`)
5. Pull Request açın

---

## 📝 Lisans

Bu proje [MIT License](LICENSE) ile lisanslanmıştır.

---

## 📞 İletişim

- 📧 Email: muhammed@emingokcek.com
- 🐛 Issues: [GitHub Issues](https://github.com/gkck38/bilet-satin-alma/issues)
- 💬 Discussions: [GitHub Discussions](https://github.com/gkck38/bilet-satin-alma/discussions)

---

## 🌟 Yıldız Geçmişi

[![Star History Chart](https://api.star-history.com/svg?repos=kullanici-adi/bilet-satin-alma&type=Date)](https://star-history.com/#kullanici-adi/bilet-satin-alma&Date)

---

<div align="center">

**⭐ Projeyi beğendiyseniz yıldız vermeyi unutmayın!**

Made with ❤️ by Emin Gökçek

[⬆ Yukarı Çık](#-bilet-satın-alma-platformu)

</div>
