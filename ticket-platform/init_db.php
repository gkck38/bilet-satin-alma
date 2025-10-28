<?php
/**
 * Veritabanı Başlatma Scripti
 * İlk kurulumda veritabanını oluşturur ve örnek verileri ekler
 */

$dbFile = __DIR__ . '/database.db';

// Eğer veritabanı varsa sil (temiz başlangıç için)
if (file_exists($dbFile)) {
    unlink($dbFile);
    echo "🗑️  Eski veritabanı silindi.\n";
}

try {
    // PDO bağlantısı oluştur
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    
    echo "📦 Veritabanı dosyası oluşturuluyor...\n";
    
    // SQL dosyasını oku ve çalıştır
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    if ($sql === false) {
        throw new Exception("database.sql dosyası okunamadı!");
    }
    
    // SQL komutlarını noktalı virgüle göre ayır ve çalıştır
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $executedCount = 0;
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                $executedCount++;
            } catch (PDOException $e) {
                echo "⚠️  Uyarı: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "✅ {$executedCount} SQL komutu başarıyla çalıştırıldı!\n";
    echo "✅ Veritabanı başarıyla oluşturuldu!\n";
    echo "📊 Örnek veriler eklendi.\n\n";
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔐 Demo Hesaplar:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "👨‍💼 Admin:\n";
    echo "   Kullanıcı: admin\n";
    echo "   Şifre: admin123\n";
    echo "   Yetki: Tam yetki\n\n";
    
    echo "🏢 Firma Admin:\n";
    echo "   Kullanıcı: metro_admin\n";
    echo "   Şifre: firma123\n";
    echo "   Firma: Metro Turizm\n\n";
    
    echo "👤 Kullanıcı:\n";
    echo "   Kullanıcı: demo_user\n";
    echo "   Şifre: user123\n";
    echo "   Kredi: 1000 ₺\n\n";
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📈 Örnek Veriler:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ 3 Otobüs Firması\n";
    echo "✅ 5 Sefer\n";
    echo "✅ 3 İndirim Kuponu\n";
    echo "✅ 1 Admin, 1 Firma Admin, 1 User\n\n";
    
    echo "✨ Uygulama hazır! http://localhost:8080 adresinden erişebilirsiniz.\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
} catch (PDOException $e) {
    echo "❌ Veritabanı hatası: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Hata: " . $e->getMessage() . "\n";
    exit(1);
}