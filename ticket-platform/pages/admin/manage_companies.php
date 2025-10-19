<?php
/**
 * Firma Yönetimi - Admin
 */

require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireRole(ROLE_ADMIN);

// Silme işlemi
if (isset($_GET['delete'])) {
    $companyId = intval($_GET['delete']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
        if ($stmt->execute([$companyId])) {
            setFlashMessage('Firma başarıyla silindi!', 'success');
        }
    } catch (PDOException $e) {
        setFlashMessage('Firma silinemedi! İlişkili kayıtlar var.', 'error');
    }
    
    redirect('/pages/admin/manage_companies.php');
}

// Yeni firma veya düzenleme
$editId = $_GET['edit'] ?? 0;
$editCompany = null;

if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
    $stmt->execute([$editId]);
    $editCompany = $stmt->fetch();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $address = clean($_POST['address'] ?? '');
    $companyId = intval($_POST['company_id'] ?? 0);
    
    if (empty($name)) {
        $errors[] = 'Firma adı gereklidir!';
    }
    
    if (empty($errors)) {
        try {
            if ($companyId > 0) {
                // Güncelleme
                $stmt = $pdo->prepare("UPDATE companies SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?");
                $stmt->execute([$name, $phone, $email, $address, $companyId]);
                setFlashMessage('Firma başarıyla güncellendi!', 'success');
            } else {
                // Yeni kayıt
                $stmt = $pdo->prepare("INSERT INTO companies (name, phone, email, address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $phone, $email, $address]);
                setFlashMessage('Firma başarıyla eklendi!', 'success');
            }
            
            redirect('/pages/admin/manage_companies.php');
        } catch (PDOException $e) {
            $errors[] = 'Veritabanı hatası: ' . $e->getMessage();
        }
    }
}

// Firmaları listele
$stmt = $pdo->query("
    SELECT c.*, 
           (SELECT COUNT(*) FROM users WHERE company_id = c.id AND role = 'company_admin') as admin_count,
           (SELECT COUNT(*) FROM trips WHERE company_id = c.id) as trip_count
    FROM companies c
    ORDER BY c.name
");
$companies = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-<?php echo $editCompany ? 'pencil' : 'plus-circle'; ?>"></i>
                    <?php echo $editCompany ? 'Firma Düzenle' : 'Yeni Firma Ekle'; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <div><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="company_id" value="<?php echo $editCompany['id'] ?? 0; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Firma Adı *</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?php echo $editCompany['name'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?php echo $editCompany['phone'] ?? ''; ?>"
                               placeholder="0850 123 45 67">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo $editCompany['email'] ?? ''; ?>"
                               placeholder="info@firma.com">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Adres</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo $editCompany['address'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> <?php echo $editCompany ? 'Güncelle' : 'Kaydet'; ?>
                        </button>
                        <?php if ($editCompany): ?>
                            <a href="manage_companies.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> İptal
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-building"></i> Firmalar
                    <span class="badge bg-primary"><?php echo count($companies); ?></span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($companies) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Firma Adı</th>
                                    <th>İletişim</th>
                                    <th>Yönetici</th>
                                    <th>Sefer</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($companies as $company): ?>
                                    <tr>
                                        <td><?php echo $company['id']; ?></td>
                                        <td>
                                            <strong><?php echo clean($company['name']); ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($company['phone']): ?>
                                                <i class="bi bi-telephone"></i> <?php echo clean($company['phone']); ?><br>
                                            <?php endif; ?>
                                            <?php if ($company['email']): ?>
                                                <i class="bi bi-envelope"></i> <?php echo clean($company['email']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $company['admin_count']; ?> Admin
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php echo $company['trip_count']; ?> Sefer
                                            </span>
                                        </td>
                                        <td>
                                            <a href="?edit=<?php echo $company['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?delete=<?php echo $company['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Bu firmayı silmek istediğinizden emin misiniz?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>