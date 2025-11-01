<?php
/**
 * Ana Konfigürasyon Dosyası
 * Genel ayarlar ve sabitler
 */

// Session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hata raporlama (production'da kapatılmalı)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone ayarla
date_default_timezone_set('Europe/Istanbul');

// Site sabitleri
define('SITE_NAME', 'Bilet Satın Alma Platformu');
define('SITE_URL', 'http://localhost:8080');

// Rol sabitleri
define('ROLE_ADMIN', 'admin');
define('ROLE_COMPANY_ADMIN', 'company_admin');
define('ROLE_USER', 'user');

// Bilet durumları
define('TICKET_ACTIVE', 'active');
define('TICKET_CANCELLED', 'cancelled');

// Veritabanı bağlantısını dahil et
require_once __DIR__ . '/database.php';

// Fonksiyonları dahil et
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';