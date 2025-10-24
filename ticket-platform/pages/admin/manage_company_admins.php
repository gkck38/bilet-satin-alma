<?php
/**
 * Firma Admin Yönetimi - Admin
 */

require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireRole(ROLE_ADMIN);

// Silme işlemi
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'company_admin'");
    if ($stmt->execute([$userId])) {
        setFlashMessage('Firma yöneticisi başarıyla silindi!', 'success');
    }
    
    redirect('/pages/admin/manage_company_admins.php');
}

$errors = [];

// Firma Admin oluşturma
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = clean($_POST['full_name'] ?? '');
    $companyId = intval($_POST['company_id'] ?? 0);
    
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
    
    if (empty($fullName)) {
        $errors[] = 'Ad Soyad gereklidir!';
    }
    
    if ($companyId <= 0) {
        $errors[] = 'Bir firma seçmelisiniz!';
    }
    
    if (empty($errors)) {
        // Kullanıcı adı veya email kontrolü
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $errors[] = 'Bu kullanıcı adı veya email zaten kullanılıyor!';
        } else {
            try {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password, full_name, role, company_id, credit)
                    VALUES (?, ?, ?, ?, 'company_admin', ?, 0)
                ");
                
                if ($stmt->execute([$username, $email, $hashedPassword, $fullName, $companyId])) {
                    setFlashMessage('Firma yöneticisi başarıyla oluşturuldu!', 'success');
                    redirect('/pages/admin/manage_company_admins.php');
                }
            } catch (PDOException $e) {
                $errors[] = 'Veritabanı hatası: ' . $e->getMessage();
            }
        }
    }
}

// Firmaları getir
$companies = $pdo->query("SELECT id, name FROM companies ORDER BY name")->fetchAll();

// Firma adminlerini getir
$stmt = $pdo->query("
    SELECT u.*, c.name as company_name
    FROM users u
    LEFT JOIN companies c ON u.company_id = c.id
    WHERE u.role = 'company_admin'
    ORDER BY c.name, u.full_name
");
$companyAdmins = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus"></i> Yeni Firma Yöneticisi
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Kullanıcı Adı *</label>
                        <input type="text" name="username" class="form-control" 
                               value="<?php echo $_POST['username'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo $_POST['email'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad *</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?php echo $_POST['full_name'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Şifre *</label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">En az 6 karakter</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Firma *</label>
                        <select name="company_id" class="form-select" required>
                            <option value="">Firma Seçin</option>
                            <?php foreach ($companies as $company): ?>
                                <option value="<?php echo $company['id']; ?>"
                                    <?php echo (isset($_POST['company_id']) && $_POST['company_id'] == $company['id']) ? 'selected' : ''; ?>>
                                    <?php echo clean($company['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-person-badge"></i> Firma Yöneticileri
                    <span class="badge bg-success"><?php echo count($companyAdmins); ?></span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($companyAdmins) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Ad Soyad</th>
                                    <th>Kullanıcı Adı</th>
                                    <th>Email</th>
                                    <th>Firma</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($companyAdmins as $admin): ?>
                                    <tr>
                                        <td><?php echo $admin['id']; ?></td>
                                        <td><strong><?php echo clean($admin['full_name']); ?></strong></td>
                                        <td><?php echo clean($admin['username']); ?></td>
                                        <td><?php echo clean($admin['email']); ?></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php echo clean($admin['company_name']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($admin['created_at']); ?></td>
                                        <td>
                                            <a href="?delete=<?php echo $admin['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Bu yöneticiyi silmek istediğinizden emin misiniz?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-person-badge text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">Henüz firma yöneticisi eklenmemiş</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>