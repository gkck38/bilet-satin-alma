<?php
/**
 * Ana Sayfa - Sefer Arama ve Listeleme
 * Tüm roller erişebilir
 */

require_once __DIR__ . '/config/config.php';

// Arama parametreleri
$departureCity = $_GET['departure_city'] ?? '';
$arrivalCity = $_GET['arrival_city'] ?? '';
$departureDate = $_GET['departure_date'] ?? '';

// Sefer listesini getir
$trips = [];
$searchPerformed = false;

if (!empty($departureCity) && !empty($arrivalCity) && !empty($departureDate)) {
    $searchPerformed = true;
    
    $sql = "
        SELECT t.*, c.name as company_name 
        FROM trips t
        JOIN companies c ON t.company_id = c.id
        WHERE t.departure_city = ? 
        AND t.arrival_city = ? 
        AND t.departure_date = ?
        AND t.available_seats > 0
        ORDER BY t.departure_time
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$departureCity, $arrivalCity, $departureDate]);
    $trips = $stmt->fetchAll();
}

include __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <!-- Arama Formu -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="card-title mb-4">
                    <i class="bi bi-search"></i> Sefer Ara
                </h4>
                
                <form method="GET" action="index.php">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Nereden</label>
                            <?php renderCitySelect('departure_city', $departureCity, true); ?>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Nereye</label>
                            <?php renderCitySelect('arrival_city', $arrivalCity, true); ?>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Tarih</label>
                            <input type="date" name="departure_date" class="form-control" 
                                   value="<?php echo clean($departureDate); ?>" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Sefer Ara
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Sefer Listesi -->
        <?php if ($searchPerformed): ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="bi bi-list-ul"></i> Bulunan Seferler
                        <?php if (count($trips) > 0): ?>
                            <span class="badge bg-primary"><?php echo count($trips); ?> sefer</span>
                        <?php endif; ?>
                    </h5>
                    
                    <?php if (count($trips) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Firma</th>
                                        <th>Güzergah</th>
                                        <th>Tarih</th>
                                        <th>Kalkış</th>
                                        <th>Varış</th>
                                        <th>Fiyat</th>
                                        <th>Boş Koltuk</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trips as $trip): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo clean($trip['company_name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo clean($trip['bus_number']); ?></small>
                                            </td>
                                            <td>
                                                <?php echo clean($trip['departure_city']); ?>
                                                <i class="bi bi-arrow-right"></i>
                                                <?php echo clean($trip['arrival_city']); ?>
                                            </td>
                                            <td><?php echo formatDate($trip['departure_date']); ?></td>
                                            <td>
                                                <i class="bi bi-clock"></i>
                                                <?php echo formatTime($trip['departure_time']); ?>
                                            </td>
                                            <td>
                                                <i class="bi bi-clock-fill"></i>
                                                <?php echo formatTime($trip['arrival_time']); ?>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    <?php echo formatPrice($trip['price']); ?>
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo $trip['available_seats']; ?> / <?php echo $trip['total_seats']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="trip_details.php?id=<?php echo $trip['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Detay
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Aradığınız kriterlere uygun sefer bulunamadı.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- İlk Açılış Mesajı -->
            <div class="text-center py-5">
                <i class="bi bi-bus-front-fill text-primary" style="font-size: 4rem;"></i>
                <h3 class="mt-3">Hoş Geldiniz!</h3>
                <p class="text-muted">Yukarıdaki formu kullanarak sefer araması yapabilirsiniz.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>