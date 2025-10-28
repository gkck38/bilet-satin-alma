<?php
/**
 * Giriş Yapma Sayfası
 */

require_once __DIR__ . '/config/config.php';

// Zaten giriş yapmışsa ana sayfaya yönlendir
if (isLoggedIn()) {
    redirect('/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Kullanıcı adı ve şifre gereklidir!';
    } else {
        if (loginUser($username, $password)) {
            setFlashMessage('Başarıyla giriş yaptınız!', 'success');
            
            // Role göre yönlendirme
            if (hasRole(ROLE_ADMIN)) {
                redirect('/pages/admin/dashboard.php');
            } elseif (hasRole(ROLE_COMPANY_ADMIN)) {
                redirect('/pages/company_admin/dashboard.php');
            } else {
                redirect('/index.php');
            }
        } else {
            $error = 'Kullanıcı adı veya şifre hatalı!';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="bi bi-person-circle text-primary" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">Giriş Yap</h3>
                    <p class="text-muted">Hesabınıza giriş yapın</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Adı veya Email</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?php echo $_POST['username'] ?? ''; ?>" required autofocus>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Şifre</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-box-arrow-in-right"></i> Giriş Yap
                    </button>
                    
                    <div class="text-center">
                        <p class="mb-0">Hesabınız yok mu? 
                            <a href="register.php" class="text-decoration-none">Kayıt Olun</a>
                        </p>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="small text-muted">
                    <strong>Demo Hesaplar:</strong><br>
                    • Admin: admin / admin123<br>
                    • Firma Admin: metro_admin / firma123<br>
                    • Kullanıcı: demo_user / user123
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>