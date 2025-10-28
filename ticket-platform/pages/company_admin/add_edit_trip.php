<?php
/**
 * Firma Admin Dashboard
 * Sadece Firma Admin rolü erişebilir
 */

require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireRole(ROLE_COMPANY_ADMIN);

$companyId = $_SESSION['company_id'];

// Firma bilgilerini getir
$stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->execute([$companyId]);
$company = $stmt->fetch();

// İstatistikler
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM trips WHERE company_id = ?");
$stmt->execute([$companyId]);
$totalTrips = $stmt->fetch()['total'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM trips 
    WHERE company_id = ? AND departure_date >= DATE('now')
");
$stmt->execute([$companyId]);
$upcomingTrips = $stmt->fetch()['total'];

$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM tickets tk
    JOIN trips t ON tk.trip_id = t.id
    WHERE t.company_id = ? AND tk.status = 'active'
");
$stmt->execute([$companyId]);
$activeTickets = $stmt->fetch()['total'];

$stmt = $pdo->prepare("
    SELECT SUM(tk.final_price) as total 
    FROM tickets tk
    JOIN trips t ON tk.trip_id = t.id
    WHERE t.company_id = ? AND tk.status = 'active'
");
$stmt->execute([$companyId]);
$totalRevenue = $stmt->fetch()['total'] ?? 0;

// Son eklenen seferler
$stmt = $pdo->prepare("
    SELECT t.*, 
           (SELECT COUNT(*) FROM tickets WHERE trip_id = t.id AND status = 'active') as sold_tickets
    FROM trips t
    WHERE t.company_id = ?
    ORDER BY t.created_at DESC
    LIMIT 5
");
$stmt->execute([$companyId]);
$recentTrips = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-building"></i> Firma Yönetim Paneli
        </h2>
    </div>
</div>

<!-- Firma Bilgileri -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h4><i class="bi bi-building"></i> <?php echo clean($company['name']); ?></h4>
                <p class="mb-0">
                    <i class="bi bi-telephone"></i> <?php echo clean($company['phone']); ?> |
                    <i class="bi bi-envelope"></i> <?php echo clean($company['email']); ?>
                    <?php if ($company['address']): ?>
                        | <i class="bi bi-geo-alt"></i> <?php echo clean($company['address']); ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- İstatistikler -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-bus-front text-primary" style="font-size: 2rem;"></i>
                <h3 class="mt-2"><?php echo $totalTrips; ?></h3>
                <p class="text-muted mb-0">Toplam Sefer</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-check text-success" style="font-size: 2rem;"></i>
                <h3 class="mt-2"><?php echo $upcomingTrips; ?></h3>
                <p class="text-muted mb-0">Yaklaşan Sefer</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-ticket-perforated text-info" style="font-size: 2rem;"></i>
                <h3 class="mt-2"><?php echo $activeTickets; ?></h3>
                <p class="text-muted mb-0">Aktif Bilet</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-currency-exchange text-warning" style="font-size: 2rem;"></i>
                <h3 class="mt-2"><?php echo formatPrice($totalRevenue); ?></h3>
                <p class="text-muted mb-0">Toplam Gelir</p>
            </div>
        </div>
    </div>
</div>

<!-- Hızlı İşlemler -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-lightning"></i> Hızlı İşlemler
                </h5>
            </div>
            <div class="card-body">
                <a href="add_edit_trip.php" class="btn btn-success btn-lg me-2 mb-2">
                    <i class="bi bi-plus-circle"></i> Yeni Sefer Ekle
                </a>
                <a href="manage_trips.php" class="btn btn-primary btn-lg me-2 mb-2">
                    <i class="bi bi-list-ul"></i> Seferleri Yönet
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Son Eklenen Seferler -->
<?php if (count($recentTrips) > 0): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history"></i> Son Eklenen Seferler
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Güzergah</th>
                                <th>Tarih</th>
                                <th>Saat</th>
                                <th>Fiyat</th>
                                <th>Koltuk</th>
                                <th>Satılan</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTrips as $trip): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo clean($trip['departure_city']); ?></strong>
                                        <i class="bi bi-arrow-right"></i>
                                        <strong><?php echo clean($trip['arrival_city']); ?></strong>
                                    </td>
                                    <td><?php echo formatDate($trip['departure_date']); ?></td>
                                    <td><?php echo formatTime($trip['departure_time']); ?></td>
                                    <td><?php echo formatPrice($trip['price']); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo $trip['available_seats']; ?> / <?php echo $trip['total_seats']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo $trip['sold_tickets']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="add_edit_trip.php?id=<?php echo $trip['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../../includes/footer.php'; ?>