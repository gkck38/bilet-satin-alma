<?php
/**
 * Kupon Yönetimi - Admin
 * Sadece Admin rolü erişebilir
 */

require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireRole(ROLE_ADMIN);

// Silme işlemi
if (isset($_GET['delete'])) {
    $couponId = intval($_GET['delete']);
    
    $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
    if ($stmt->execute([$couponId])) {
        setFlashMessage('Kupon başarıyla silindi!', 'success');
    }
    
    redirect('/pages/admin/manage_coupons.php');
}

// Aktif/Pasif değiştirme
if (isset($_GET['toggle'])) {
    $couponId = intval($_GET['toggle']);
    
    $stmt = $pdo->prepare("UPDATE coupons SET is_active = NOT is_active WHERE id = ?");
    if ($stmt->execute([$couponId])) {
        setFlashMessage('Kupon durumu değiştirildi!', 'success');
    }
    
    redirect('/pages/admin/manage_coupons.php');
}

// Düzenleme
$editId = $_GET['edit'] ?? 0;
$editCoupon = null;

if ($editId) {
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE id = ?");
    $stmt->execute([$editId]);
    $editCoupon = $stmt->fetch();
}

$errors = [];

// Kupon oluşturma/güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(clean($_POST['code'] ?? ''));
    $discountRate = floatval($_POST['discount_rate'] ?? 0);
    $usageLimit = intval($_POST['usage_limit'] ?? 0);
    $expiryDate = clean($_POST['expiry_date'] ?? '');
    $couponId = intval($_POST['coupon_id'] ?? 0);
    
    // Validasyonlar
    if (empty($code)) {
        $errors[] = 'Kupon kodu gereklidir!';
    }
    
    if ($discountRate <= 0 || $discountRate > 100) {
        $errors[] = 'İndirim oranı 1-100 arasında olmalıdır!';
    }
    
    if ($usageLimit <= 0) {
        $errors[] = 'Kullanım limiti pozitif bir sayı olmalıdır!';
    }
    
    if (empty($expiryDate)) {
        $errors[] = 'Son kullanma tarihi gereklidir!';
    }
    
    if (empty($errors)) {
        try {
            if ($couponId > 0) {
                // Güncelleme
                $stmt = $pdo->prepare("
                    UPDATE coupons SET code = ?, discount_rate = ?, usage_limit = ?, expiry_date = ?
                    WHERE id = ?
                ");
                $stmt->execute([$code, $discountRate, $usageLimit, $expiryDate, $couponId]);
                setFlashMessage('Kupon başarıyla güncellendi!', 'success');
            } else {
                // Kod kontrolü
                $stmt = $pdo->prepare("SELECT id FROM coupons WHERE code = ?");
                $stmt->execute([$code]);
                
                if ($stmt->fetch()) {
                    $errors[] = 'Bu kupon kodu zaten kullanılıyor!';
                } else {
                    // Yeni kayıt
                    $stmt = $pdo->prepare("
                        INSERT INTO coupons (code, discount_rate, usage_limit, expiry_date, is_active)
                        VALUES (?, ?, ?, ?, 1)
                    ");
                    $stmt->execute([$code, $discountRate, $usageLimit, $expiryDate]);
                    setFlashMessage('Kupon başarıyla oluşturuldu!', 'success');
                }
            }
            
            if (empty($errors)) {
                redirect('/pages/admin/manage_coupons.php');
            }
        } catch (PDOException $e) {
            $errors[] = 'Veritabanı hatası: ' . $e->getMessage();
        }
    }
}

// Kuponları listele
$stmt = $pdo->query("SELECT * FROM coupons ORDER BY is_active DESC, expiry_date DESC");
$coupons = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0">
                    <i class="bi bi-<?php echo $editCoupon ? 'pencil' : 'plus-circle'; ?>"></i>
                    <?php echo $editCoupon ? 'Kupon Düzenle' : 'Yeni Kupon Oluştur'; ?>
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
                    <input type="hidden" name="coupon_id" value="<?php echo $editCoupon['id'] ?? 0; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Kupon Kodu *</label>
                        <input type="text" name="code" class="form-control text-uppercase" 
                               value="<?php echo $editCoupon['code'] ?? ''; ?>" 
                               placeholder="ÖRNEK2025" required
                               <?php echo $editCoupon ? 'readonly' : ''; ?>>
                        <small class="text-muted">Büyük harf ve rakam kullanın</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İndirim Oranı (%) *</label>
                        <input type="number" name="discount_rate" class="form-control" 
                               value="<?php echo $editCoupon['discount_rate'] ?? ''; ?>" 
                               min="1" max="100" step="0.01" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kullanım Limiti *</label>
                        <input type="number" name="usage_limit" class="form-control" 
                               value="<?php echo $editCoupon['usage_limit'] ?? ''; ?>" 
                               min="1" required>
                        <small class="text-muted">Kaç kez kullanılabilir?</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Son Kullanma Tarihi *</label>
                        <input type="date" name="expiry_date" class="form-control" 
                               value="<?php echo $editCoupon['expiry_date'] ?? ''; ?>" 
                               min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> <?php echo $editCoupon ? 'Güncelle' : 'Oluştur'; ?>
                        </button>
                        <?php if ($editCoupon): ?>
                            <a href="manage_coupons.php" class="btn btn-secondary">
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
                    <i class="bi bi-tag"></i> Kuponlar
                    <span class="badge bg-warning"><?php echo count($coupons); ?></span>
                </h5>
            </div>
            <div class="card-body">
                <?php if (count($coupons) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kod</th>
                                    <th>İndirim</th>
                                    <th>Kullanım</th>
                                    <th>Son Tarih</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($coupons as $coupon): ?>
                                    <?php
                                    $isExpired = strtotime($coupon['expiry_date']) < time();
                                    $isLimitReached = $coupon['used_count'] >= $coupon['usage_limit'];
                                    ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary"><?php echo clean($coupon['code']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">%<?php echo $coupon['discount_rate']; ?></span>
                                        </td>
                                        <td>
                                            <?php echo $coupon['used_count']; ?> / <?php echo $coupon['usage_limit']; ?>
                                            <?php if ($isLimitReached): ?>
                                                <span class="badge bg-warning">Limit Doldu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo formatDate($coupon['expiry_date']); ?>
                                            <?php if ($isExpired): ?>
                                                <span class="badge bg-secondary">Süresi Doldu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($coupon['is_active']): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Pasif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?edit=<?php echo $coupon['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="?toggle=<?php echo $coupon['id']; ?>" 
                                               class="btn btn-sm btn-outline-<?php echo $coupon['is_active'] ? 'warning' : 'success'; ?>"
                                               title="<?php echo $coupon['is_active'] ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                                <i class="bi bi-<?php echo $coupon['is_active'] ? 'pause' : 'play'; ?>"></i>
                                            </a>
                                            <a href="?delete=<?php echo $coupon['id']; ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Bu kuponu silmek istediğinizden emin misiniz?');"
                                               title="Sil">
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
                        <i class="bi bi-tag text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">Henüz kupon oluşturulmamış</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>