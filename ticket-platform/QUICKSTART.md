# 🚀 Hızlı Başlangıç Rehberi

5 dakikada projeyi çalıştırın!

## 📋 Ön Gereksinimler

- Docker ve Docker Compose yüklü olmalı
- Port 8080 boş olmalı

## ⚡ Otomatik Kurulum (Önerilen)

### Linux/Mac:

```bash
# 1. Projeyi klonlayın
git clone https://github.com/kullanici-adi/bilet-satin-alma.git
cd bilet-satin-alma

# 2. Kurulum scriptini çalıştırın
chmod +x install.sh
./install.sh

# 3. Tarayıcınızda açın
open http://localhost:8080
```

### Windows (Git Bash):

```bash
# 1. Projeyi klonlayın
git clone https://github.com/kullanici-adi/bilet-satin-alma.git
cd bilet-satin-alma

# 2. Docker ile başlatın
docker-compose up -d --build

# 3. Veritabanını oluşturun
docker exec bilet-platform php init_db.php

# 4. Tarayıcınızda açın
start http://localhost:8080
```

## 🛠️ Manuel Kurulum

### Adım 1: Projeyi İndirin

```bash
git clone https://github.com/kullanici-adi/bilet-satin-alma.git
cd bilet-satin-alma
```

### Adım 2: Docker Container'ı Oluşturun

```bash
docker-compose build
```

### Adım 3: Veritabanını Başlatın

```bash
php init_db.php
```

### Adım 4: Uygulamayı Başlatın

```bash
docker-compose up -d
```

### Adım 5: Tarayıcıda Açın

```
http://localhost:8080
```

## 🔐 Demo Hesaplar

Hemen test etmek için hazır hesaplar:

| Rol | Kullanıcı Adı | Şifre | Özellikler |
|-----|---------------|-------|------------|
| 👨‍💼 **Admin** | `admin` | `admin123` | Tam yetki |
| 🏢 **Firma Admin** | `metro_admin` | `firma123` | Metro Turizm yetkisi |
| 👤 **Kullanıcı** | `demo_user` | `user123` | 1000₺ kredi |

## 🎯 İlk Adımlar

### 1. Kullanıcı Olarak Bilet Alın

```
1. Login: demo_user / user123
2. Ana Sayfa → Sefer Ara
3. İstanbul → Ankara seçin
4. Tarih: Yarın
5. Sefer bul ve bilet al
6. Koltuk seçin (örn: 15)
7. Kupon: YAZ2025 (%15 indirim)
8. Ödeme yap
9. Biletlerim → PDF İndir
```

### 2. Firma Admin Olarak Sefer Ekleyin

```
1. Login: metro_admin / firma123
2. Firma Paneli → Yeni Sefer Ekle
3. Kalkış: İstanbul
4. Varış: Trabzon
5. Tarih ve s