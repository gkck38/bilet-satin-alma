<?php
/**
 * Veritabanı Bağlantı Dosyası
 * SQLite veritabanı bağlantısını yönetir
 */

// Veritabanı dosya yolu
$db_file = __DIR__ . '/../database.db';

try {
    // PDO ile SQLite bağlantısı oluştur
    $pdo = new PDO('sqlite:' . $db_file);
    
    // Hata modunu exception olarak ayarla
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Foreign key desteğini aktifleştir
    $pdo->exec('PRAGMA foreign_keys = ON;');
    
    // Fetch modunu associative array olarak ayarla
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}