# Güvenlik Politikası

## Desteklenen Versiyonlar

Şu anda hangi versiyonların güvenlik güncellemeleri aldığını görebilirsiniz:

| Versiyon | Destekleniyor          |
| -------- | --------------------- |
| 1.0.x    | :white_check_mark:    |
| < 1.0    | :x:                   |

## Güvenlik Açığı Bildirme

Projemizde bir güvenlik açığı keşfettiyseniz, lütfen **hemen** bize bildirin.

### Bildirme Süreci

1. **GitHub Issues KULLANMAYIN**: Güvenlik açıkları genel olarak paylaşılmamalıdır
2. **Email gönderin**: security@yourproject.com (veya GitHub Security Advisory kullanın)
3. **Detayları ekleyin**:
   - Güvenlik açığının türü
   - Hangi dosya/fonksiyon etkileniyor
   - Nasıl tekrar üretilebilir
   - Potansiyel etki değerlendirmesi
   - Önerilen çözüm (varsa)

### Yanıt Süresi

- İlk yanıt: **48 saat** içinde
- Düzeltme planı: **7 gün** içinde
- Güvenlik yaması: Kritikliğe göre **30 gün** içinde

### Sorumluluk Açıklaması

Güvenlik açığı bildirerek:
- Açığı kamu ile paylaşmadan önce bize zaman tanıyın
- Açığı kötüye kullanmayın
- Verilere yetkisiz erişim yapmayın

## Bilinen Güvenlik Önlemleri

### Uygulanmış Korumalar

✅ **SQL Injection Koruması**
- PDO Prepared Statements kullanımı
- Parametreli sorgular
- Input validasyonu

✅ **Cross-Site Scripting (XSS) Koruması**
- `htmlspecialchars()` ile output temizleme
- CSP headers
- Input sanitizasyonu

✅ **Cross-Site Request Forgery (CSRF) Koruması**
- Session tabanlı doğrulama
- Form token'ları (geliştirilecek)

✅ **Şifre Güvenliği**
- `password_hash()` ile bcrypt kullanımı
- Minimum 6 karakter zorunluluğu
- Güvenli şifre doğrulama

✅ **Session Güvenliği**
- Secure session ayarları
- Session hijacking koruması
- Session timeout

✅ **Dosya Yükleme Güvenliği**
- Şu an dosya yükleme yok
- Gelecekte eklenir ise: MIME type kontrolü, boyut limiti

✅ **Veritabanı Güvenliği**
- SQLite file permissions
- Foreign key constraints
- Transaction kullanımı

### Önerilen Ek Önlemler (Production)

🔸 **HTTPS Kullanımı**
```apache
# .htaccess
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

🔸 **Rate Limiting**
- Brute force saldırı koruması
- API rate limiting
- Login denemesi limiti

🔸 **Güvenlik Headers**
```apache
Header set X-XSS-Protection "1; mode=block"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-Content-Type-Options "nosniff"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Content-Security-Policy "default-src 'self'"
```

🔸 **2FA (Two-Factor Authentication)**
- Google Authenticator entegrasyonu
- SMS doğrulama

🔸 **Logging ve Monitoring**
- Başarısız login denemeleri
- Şüpheli aktiviteler
- SQL query logları

## Güvenlik En İyi Uygulamaları

### Geliştiriciler İçin

1. **Asla şifreleri plain text olarak saklamayın**
2. **Kullanıcı input'unu asla güvenmeyin**
3. **SQL sorgularında string concatenation kullanmayın**
4. **Hassas bilgileri loglara yazmayın**
5. **Hata mesajlarında sistem detayları vermeyin**
6. **Production'da debug mode kapalı olsun**
7. **Düzenli güvenlik güncellemeleri yapın**
8. **Dependency'leri güncel tutun**

### Sistem Yöneticileri İçin

1. **Düzenli yedek alın**
2. **Firewall kurallarını yapılandırın**
3. **Gereksiz servisleri kapatın**
4. **Dosya izinlerini kontrol edin**
5. **SSL/TLS sertifikası kullanın**
6. **Güvenlik loglarını inceleyin**
7. **Otomatik güvenlik güncellemeleri aktif edin**

### Kullanıcılar İçin

1. **Güçlü şifreler kullanın**
2. **Şifrenizi paylaşmayın**
3. **Şüpheli aktiviteleri bildirin**
4. **Genel WiFi'larda dikkatli olun**
5. **Çıkış yapmayı unutmayın**

## Güvenlik Testleri

### Yapılması Gereken Testler

- [ ] SQL Injection testleri
- [ ] XSS testleri
- [ ] CSRF testleri
- [ ] Authentication bypass testleri
- [ ] Session hijacking testleri
- [ ] Brute force testleri
- [ ] File upload testleri (varsa)
- [ ] API security testleri (varsa)

### Test Araçları

- **OWASP ZAP**: Web güvenlik taraması
- **SQLMap**: SQL injection testi
- **Burp Suite**: Web app security testi
- **Nikto**: Web server taraması

## Compliance

Bu proje şu standartlara uygun geliştirilmiştir:

- OWASP Top 10
- PHP Security Best Practices
- GDPR temel prensipleri (veri gizliliği)

## Güvenlik Güncellemeleri

Güvenlik güncellemelerinden haberdar olmak için:

1. GitHub'da "Watch" edin
2. Security Advisory'lere abone olun
3. CHANGELOG.md dosyasını takip edin

## İletişim

Güvenlik sorunları için:
- 📧 Email: security@yourproject.com
- 🔒 GitHub Security Advisory
- 🚨 Acil durumlar: +90 xxx xxx xx xx

## Hall of Fame

Güvenlik açığı bildiren katkıcılar:

| İsim | Tarih | Açıklama |
|------|-------|----------|
| -    | -     | -        |

Katkılarınız için teşekkürler! 🙏

---

**Son Güncelleme**: 2025-10-21
**Versiyon**: 1.0.0