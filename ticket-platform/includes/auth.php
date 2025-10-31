<?php
/**
 * Kimlik Doğrulama Fonksiyonları
 */

/**
 * Kullanıcının giriş yapıp yapmadığını kontrol et
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Giriş yapmamış kullanıcıyı login sayfasına yönlendir
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlashMessage('Bu işlem için giriş yapmanız gerekiyor!', 'warning');
        redirect('/login.php');
    }
}

/**
 * Kullanıcı girişi yap
 */
function loginUser($username, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Session'a kullanıcı bilgilerini kaydet
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['company_id'] = $user['company_id'];
            
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Yeni kullanıcı kaydı oluştur
 */
function registerUser($username, $email, $password, $fullName, $phone = null) {
    global $pdo;
    
    try {
        // Kullanıcı adı veya email daha önce kullanılmış mı kontrol et
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Bu kullanıcı adı veya email zaten kullanılıyor!'];
        }
        
        // Şifreyi hashle
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Kullanıcıyı veritabanına ekle
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, full_name, phone, role, credit) 
            VALUES (?, ?, ?, ?, ?, 'user', 1000.00)
        ");
        
        if ($stmt->execute([$username, $email, $hashedPassword, $fullName, $phone])) {
            return ['success' => true, 'message' => 'Kayıt başarılı! Giriş yapabilirsiniz.'];
        }
        
        return ['success' => false, 'message' => 'Kayıt sırasında bir hata oluştu!'];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()];
    }
}

/**
 * Kullanıcı çıkışı yap
 */
function logoutUser() {
    // Session'daki tüm verileri temizle
    $_SESSION = array();
    
    // Session cookie'sini sil
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Session'ı yok et
    session_destroy();
}

/**
 * Mevcut kullanıcının bilgilerini getir
 */
function getCurrentUser() {
    global $pdo;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    return $stmt->fetch();
}

/**
 * Şifre güvenlik kontrolü
 */
function validatePassword($password) {
    if (strlen($password) < 6) {
        return ['valid' => false, 'message' => 'Şifre en az 6 karakter olmalıdır!'];
    }
    
    return ['valid' => true];
}

/**
 * Email formatı kontrolü
 */
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Geçerli bir email adresi giriniz!'];
    }
    
    return ['valid' => true];
}

/**
 * Kullanıcı adı kontrolü
 */
function validateUsername($username) {
    if (strlen($username) < 3) {
        return ['valid' => false, 'message' => 'Kullanıcı adı en az 3 karakter olmalıdır!'];
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return ['valid' => false, 'message' => 'Kullanıcı adı sadece harf, rakam ve alt çizgi içerebilir!'];
    }
    
    return ['valid' => true];
}