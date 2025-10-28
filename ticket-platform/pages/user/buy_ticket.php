<?php
/**
 * Bilet Satın Alma Sayfası
 * Sadece User rolü erişebilir
 */

require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireRole(ROLE_USER);

$tripId = $_GET['trip_id'] ?? 0;

// Sefer bilgilerini getir
$stmt = $pdo->prepare("
    SELECT t.*, c.name as company_name 
    FROM trips t
    JOIN companies c ON t.company_id = c.id
    WHERE t.id = ?
");
$stmt->execute([$tripId]);
$trip = $stmt->fetch();

if (!$trip) {
    setFlashMessage('Sefer bulunamadı!', 'error');
    redirect('/index.php');
}

// Kullanıcının kredisini kontrol et
$userCredit = getUserCredit($_SESSION['user_id']);

// Dolu koltukları getir
$bookedSeats = getBookedSeats($tripId);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seatNumber = intval($_POST['seat_number'] ?? 0);
    $couponCode = clean($_POST['coupon_code'] ?? '');
    
    // Validasyonlar
    if ($seatNumber < 1 || $seatNumber > $trip['total_seats']) {
        $error = 'Geçersiz koltuk numarası!';
    } elseif (isSeatBooked($tripId, $seatNumber)) {
        $error = 'Bu koltuk zaten dolu!';
    } else {
        $price = $trip['price'];
        $discountAmount = 0;
        $finalPrice = $price;
        
        // Kupon kodu varsa kontrol et
        if (!empty($couponCode)) {
            $coupon = validateCoupon($couponCode);
            if ($coupon) {
                $discountAmount = ($price * $coupon['discount_rate']) / 100;
                $finalPrice = $price - $discountAmount;
            } else {
                $error = 'Geçersiz veya süresi dolmuş kupon kodu!';
            }
        }
        
        if (empty($error)) {
            // Kredi kontrolü
            if ($userCredit < $finalPrice) {
                $error = 'Yetersiz kredi! Bakiyeniz: ' . formatPrice($userCredit);
            } else {
                // Transaction başlat
                try {
                    $pdo->beginTransaction();
                    
                    // Bileti oluştur
                    $stmt = $pdo->prepare("
                        INSERT INTO tickets (user_id, trip_id, seat_number, price, coupon_code, discount_amount, final_price, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'active')
                    ");
                    $stmt->execute([
                        $_SESSION['user_id'],
                        $tripId,
                        $seatNumber,
                        $price,
                        $couponCode ?: null,
                        $discountAmount,
                        $finalPrice
                    ]);
                    
                    // Kullanıcının kredisini düş
                    updateUserCredit($_SESSION['user_id'], $finalPrice, 'subtract');
                    
                    // Seferin müsait koltuk sayısını azalt
                    updateTripSeats($tripId, 'decrease');
                    
                    // Kupon kullanıldıysa sayacı artır
                    if (!empty($couponCode) && $coupon) {
                        incrementCouponUsage($coupon['id']);
                    }
                    
                    $pdo->commit();
                    
                    setFlashMessage('Biletiniz başarıyla satın alındı!', 'success');
                    redirect('/pages/user/my_tickets.php');
                    
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = 'Bilet alımı sırasında bir hata oluştu!';
                }
            }
        }
    }
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="bi bi-ticket-perforated"></i> Bilet Satın Al
                </h4>
            </div>
            <div class="card-body">
                <!-- Sefer Özeti -->
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-2"><?php echo clean($trip['company_name']); ?></h5>
                            <p class="mb-1">
                                <i class="bi bi-geo-alt"></i>
                                <strong><?php echo clean($trip['departure_city']); ?></strong>
                                <i class="bi bi-arrow-right mx-2"></i>
                                <strong><?php echo clean($trip['arrival_city']); ?></strong>
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-calendar"></i>
                                <?php echo formatDate($trip['departure_date']); ?> -
                                <?php echo formatTime($trip['departure_time']); ?>
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <h3 class="text-success mb-0"><?php echo formatPrice($trip['price']); ?></h3>
                            <p class="mb-0 text-muted">Bilet Fiyatı</p>
                        </div>
                    </div>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Kullanıcı Bilgileri -->
                <div class="alert alert-secondary">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="bi bi-person"></i> Yolcu:</strong>
                            <?php echo clean($_SESSION['full_name']); ?>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong><i class="bi bi-wallet2"></i> Krediniz:</strong>
                            <span class="badge bg-success fs-6"><?php echo formatPrice($userCredit); ?></span>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="" id="buyTicketForm">
                    <!-- Koltuk Seçimi -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-grid-3x3"></i> Koltuk Seçimi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 justify-content-center">
                                <?php for ($i = 1; $i <= $trip['total_seats']; $i++): ?>
                                    <?php $isBooked = in_array($i, $bookedSeats); ?>
                                    <div class="col-2 col-md-1">
                                        <input type="radio" 
                                               class="btn-check" 
                                               name="seat_number" 
                                               id="seat<?php echo $i; ?>" 
                                               value="<?php echo $i; ?>"
                                               <?php echo $isBooked ? 'disabled' : ''; ?>
                                               required>
                                        <label class="btn btn-outline-primary w-100 <?php echo $isBooked ? 'btn-secondary disabled' : ''; ?>" 
                                               for="seat<?php echo $i; ?>">
                                            <i class="bi bi-square<?php echo $isBooked ? '-fill' : ''; ?>"></i><br>
                                            <?php echo $i; ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            
                            <div class="mt-3 text-center">
                                <span class="badge bg-primary me-2">
                                    <i class="bi bi-square"></i> Boş
                                </span>
                                <span class="badge bg-secondary me-2">
                                    <i class="bi bi-square-fill"></i> Dolu
                                </span>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-square"></i> Seçili
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kupon Kodu -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-tag"></i> İndirim Kuponu (İsteğe Bağlı)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" name="coupon_code" class="form-control" 
                                           placeholder="Kupon kodunu giriniz"
                                           value="<?php echo $_POST['coupon_code'] ?? ''; ?>">
                                    <small class="text-muted">
                                        Kupon kodunuz varsa buraya girebilirsiniz.
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-warning mb-0 small">
                                        <strong>Örnek Kuponlar:</strong><br>
                                        YAZ2025 (%15), ILKSEFERIM (%20), OGRENCI10 (%10)
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ödeme Özeti -->
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-receipt"></i> Ödeme Özeti
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Bilet Fiyatı:</span>
                                <strong><?php echo formatPrice($trip['price']); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-success" id="discountRow" style="display: none !important;">
                                <span>İndirim:</span>
                                <strong id="discountAmount">-</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <h5>Toplam:</h5>
                                <h5 class="text-success" id="totalAmount"><?php echo formatPrice($trip['price']); ?></h5>
                            </div>
                            <div class="mt-2 text-muted small">
                                <i class="bi bi-info-circle"></i>
                                Ödeme, kredi bakiyenizden otomatik olarak düşülecektir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="bi bi-credit-card"></i> Ödemeyi Tamamla
                        </button>
                        <a href="/trip_details.php?id=<?php echo $tripId; ?>" class="btn btn-outline-secondary btn-lg px-5">
                            <i class="bi bi-x-circle"></i> İptal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.btn-check:checked + .btn-outline-primary {
    background-color: #198754 !important;
    border-color: #198754 !important;
}
</style>

<?php include __DIR__ . '/../../includes/footer.php'; ?>