<?php
/**
 * Kayıt Olma Sayfası
 */

require_once __DIR__ . '/config/config.php';

// Zaten giriş yapmışsa ana sayfaya yönlendir
if (isLoggedIn()) {
    redirect('/index.php');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $fullName = clean($_POST['full_name'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    
    // Validasyonlar
    $usernameValidation = validateUsername($username);
    if (!$usernameValidation['valid']) {
        $errors[] = $usernameValidation['message'];
    }
    
    $emailValidation = validateEmail($email);
    if (!$emailValidation['valid']) {
        $errors[] = $emailValidation['message'];
    }
    
    $passwordValidation = validatePassword($password);
    if (!$passwordValidation['valid']) {
        $errors[] = $passwordValidation['message'];
    }
    
    if ($password !== $passwordConfirm) {
        $errors[] = 'Şifreler eşleşmiyor!';
    }
    
    if (empty($fullName)) {
        $errors[] = 'Ad Soyad alanı zorunludur!';
    }
    
    // Hata yoksa kayıt işlemini yap
    if (empty($errors)) {
        $result = registerUser($username, $email, $password, $fullName, $phone);
        
        if ($result['success']) {
            setFlashMessage($result['message'], 'success');
            redirect('/login.php');
        } else {
            $errors[] = $result['message'];
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-plus-fill text-primary" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Kayıt Ol</h3>
                    <p class="text-muted">Yeni hesap oluşturun</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="register.php">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kullanıcı Adı *</label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?php echo $_POST['username'] ?? ''; ?>" required>
                            <small class="text-muted">Sadece harf, rakam ve alt çizgi</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo $_POST['email'] ?? ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad *</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?php echo $_POST['full_name'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?php echo $_POST['phone'] ?? ''; ?>" 
                               placeholder="0555 123 45 67">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Şifre *</label>
                            <input type="password" name="password" class="form-control" required>
                            <small class="text-muted">En az 6 karakter</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Şifre Tekrar *</label>
                            <input type="password" name="password_confirm" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle"></i>
                        Kayıt olduğunuzda hesabınıza <strong>1000 ₺</strong> sanal kredi yüklenecektir.
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-person-plus"></i> Kayıt Ol
                    </button>
                    
                    <div class="text-center">
                        <p class="mb-0">Zaten hesabınız var mı? 
                            <a href="login.php" class="text-decoration-none">Giriş Yapın</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>