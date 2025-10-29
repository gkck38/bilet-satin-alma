<?php
/**
 * Yardımcı Fonksiyonlar
 */

/**
 * XSS saldırılarını önlemek için veriyi temizle
 */
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Kullanıcıyı belirtilen sayfaya yönlendir
 */
function redirect($page) {
    header("Location: $page");
    exit();
}

/**
 * Flash mesaj ayarla
 */
function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Flash mesaj göster ve temizle
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        
        $alertClass = match($type) {
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            default => 'alert-info'
        };
        
        return "<div class='alert $alertClass alert-dismissible fade show' role='alert'>
                    $message
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    }
    return '';
}

/**
 * Kullanıcının belirli bir role sahip olup olmadığını kontrol et
 */
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Yetkisiz erişimi kontrol et ve yönlendir
 */
function requireRole($role) {
    if (!isLoggedIn() || !hasRole($role)) {
        setFlashMessage('Bu sayfaya erişim yetkiniz yok!', 'error');
        redirect('/index.php');
    }
}

/**
 * Tarih formatla (Türkçe)
 */
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d.m.Y', $timestamp);
}

/**
 * Saat formatla
 */
function formatTime($time) {
    return date('H:i', strtotime($time));
}

/**
 * Tarih ve saat formatla
 */
function formatDateTime($datetime) {
    $timestamp = strtotime($datetime);
    return date('d.m.Y H:i', $timestamp);
}

/**
 * Para formatla (TL)
 */
function formatPrice($amount) {
    return number_format($amount, 2, ',', '.') . ' ₺';
}

/**
 * Sefer saatine son 1 saat kaldı mı kontrol et
 */
function canCancelTicket($departureDate, $departureTime) {
    $departureDateTime = strtotime("$departureDate $departureTime");
    $currentDateTime = time();
    $timeDiff = $departureDateTime - $currentDateTime;
    
    // 1 saat = 3600 saniye
    return $timeDiff > 3600;
}

/**
 * Kupon kodunu doğrula ve indirim oranını döndür
 */
function validateCoupon($code) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM coupons 
        WHERE code = ? 
        AND is_active = 1 
        AND expiry_date >= DATE('now')
        AND used_count < usage_limit
    ");
    $stmt->execute([$code]);
    
    return $stmt->fetch();
}

/**
 * Kupon kullanım sayısını artır
 */
function incrementCouponUsage($couponId) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
    return $stmt->execute([$couponId]);
}

/**
 * Kupon kullanım sayısını azalt (iptal durumunda)
 */
function decrementCouponUsage($couponCode) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE coupons SET used_count = used_count - 1 WHERE code = ? AND used_count > 0");
    return $stmt->execute([$couponCode]);
}

/**
 * Sefere ait dolu koltukları getir
 */
function getBookedSeats($tripId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT seat_number 
        FROM tickets 
        WHERE trip_id = ? AND status = 'active'
        ORDER BY seat_number
    ");
    $stmt->execute([$tripId]);
    
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Koltuk numarasının dolu olup olmadığını kontrol et
 */
function isSeatBooked($tripId, $seatNumber) {
    $bookedSeats = getBookedSeats($tripId);
    return in_array($seatNumber, $bookedSeats);
}

/**
 * Kullanıcının kredi bakiyesini güncelle
 */
function updateUserCredit($userId, $amount, $operation = 'add') {
    global $pdo;
    
    if ($operation === 'add') {
        $stmt = $pdo->prepare("UPDATE users SET credit = credit + ? WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("UPDATE users SET credit = credit - ? WHERE id = ?");
    }
    
    return $stmt->execute([$amount, $userId]);
}

/**
 * Kullanıcının kredi bakiyesini getir
 */
function getUserCredit($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT credit FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    
    $result = $stmt->fetch();
    return $result ? $result['credit'] : 0;
}

/**
 * Seferin müsait koltuk sayısını güncelle
 */
function updateTripSeats($tripId, $operation = 'decrease') {
    global $pdo;
    
    if ($operation === 'decrease') {
        $stmt = $pdo->prepare("UPDATE trips SET available_seats = available_seats - 1 WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("UPDATE trips SET available_seats = available_seats + 1 WHERE id = ?");
    }
    
    return $stmt->execute([$tripId]);
}

/**
 * Şehir listesi
 */
function getCities() {
    return [
        'İstanbul', 'Ankara', 'İzmir', 'Antalya', 'Bursa', 'Adana', 
        'Gaziantep', 'Konya', 'Trabzon', 'Samsun', 'Kayseri', 'Eskişehir',
        'Diyarbakır', 'Urfa', 'Mersin', 'Denizli', 'Malatya', 'Erzurum',
        'Van', 'Batman', 'Elazığ', 'Tekirdağ', 'Balıkesir', 'Kocaeli'
    ];
}

/**
 * Şehir seçim dropdown'u oluştur
 */
function renderCitySelect($name, $selected = '', $required = false) {
    $cities = getCities();
    $requiredAttr = $required ? 'required' : '';
    
    echo "<select name='$name' class='form-select' $requiredAttr>";
    echo "<option value=''>Şehir Seçin</option>";
    
    foreach ($cities as $city) {
        $selectedAttr = ($city === $selected) ? 'selected' : '';
        echo "<option value='$city' $selectedAttr>$city</option>";
    }
    
    echo "</select>";
}