<?php
/**
 * Sefer Detayları Sayfası
 * Tüm roller erişebilir
 */

require_once __DIR__ . '/config/config.php';

$tripId = $_GET['id'] ?? 0;

// Sefer bilgilerini getir
$stmt = $pdo->prepare("
    SELECT t.*, c.name as company_name, c.phone as company_phone 
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

// Dolu koltukları getir
$bookedSeats = getBookedSeats($tripId);

include __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-bus-front"></i> Sefer Detayları
                </h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="text-primary"><?php echo clean($trip['company_name']); ?></h5>
                        <p class="text-muted mb-1">
                            <i class="bi bi-telephone"></i> <?php echo clean($trip['company_phone']); ?>
                        </p>
                        <p class="text-muted">
                            <i class="bi bi-bus"></i> Otobüs No: <?php echo clean($trip['bus_number']); ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h3 class="text-success mb-0"><?php echo formatPrice($trip['price']); ?></h3>
                        <p class="text-muted">Kişi Başı</p>
                    </div>
                </div>
                
                <hr>
                
                <div class="row mb-4">
                    <div class="col-md-5">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-geo-alt-fill fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Kalkış</h6>
                                <h5 class="mb-0"><?php echo clean($trip['departure_city']); ?></h5>
                                <p class="text-muted mb-0">
                                    <?php echo formatDate($trip['departure_date']); ?> -
                                    <?php echo formatTime($trip['departure_time']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2 text-center d-flex align-items-center justify-content-center">
                        <i class="bi bi-arrow-right-circle-fill text-primary fs-1"></i>
                    </div>
                    
                    <div class="col-md-5">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success text-white rounded-circle p-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-geo-fill fs-4"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">Varış</h6>
                                <h5 class="mb-0"><?php echo clean($trip['arrival_city']); ?></h5>
                                <p class="text-muted mb-0">
                                    <?php echo formatTime($trip['arrival_time']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-calendar-check text-primary fs-3"></i>
                            <p class="mb-0 mt-2"><strong>Tarih</strong></p>
                            <p class="mb-0"><?php echo formatDate($trip['departure_date']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-people text-success fs-3"></i>
                            <p class="mb-0 mt-2"><strong>Boş Koltuk</strong></p>
                            <p class="mb-0"><?php echo $trip['available_seats']; ?> / <?php echo $trip['total_seats']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-light rounded">
                            <i class="bi bi-currency-exchange text-warning fs-3"></i>
                            <p class="mb-0 mt-2"><strong>Fiyat</strong></p>
                            <p class="mb-0"><?php echo formatPrice($trip['price']); ?></p>
                        </div>
                    </div>
                </div>
                
                <?php if (isLoggedIn() && hasRole(ROLE_USER)): ?>
                    <?php if ($trip['available_seats'] > 0): ?>
                        <div class="text-center">
                            <a href="/pages/user/buy_ticket.php?trip_id=<?php echo $trip['id']; ?>" 
                               class="btn btn-success btn-lg">
                                <i class="bi bi-ticket-perforated"></i> Bilet Satın Al
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle"></i>
                            Bu sefer için tüm koltuklar dolmuştur.
                        </div>
                    <?php endif; ?>
                <?php elseif (!isLoggedIn()): ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i>
                        Bilet satın almak için <a href="/login.php" class="alert-link">giriş yapmalısınız</a>.
                    </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="/index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Geri Dön
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>