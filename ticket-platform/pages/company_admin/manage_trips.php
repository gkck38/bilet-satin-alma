<?php
/**
 * Sefer Yönetimi - Firma Admin
 * Sadece kendi firmasının seferlerini yönetir
 */

require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireRole(ROLE_COMPANY_ADMIN);

$companyId = $_SESSION['company_id'];

// Sefer silme işlemi
if (isset($_GET['delete'])) {
    $tripId = intval($_GET['delete']);
    
    // Seferin firmaya ait olduğunu kontrol et
    $stmt = $pdo->prepare("SELECT id FROM trips WHERE id = ? AND company_id = ?");
    $stmt->execute([$tripId, $companyId]);
    
    if ($stmt->fetch()) {
        // Bilet satılmış mı kontrol et
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM tickets WHERE trip_id = ? AND status = 'active'");
        $stmt->execute([$tripId]);
        $ticketCount = $stmt->fetch()['count'];
        
        if ($ticketCount > 0) {
            setFlashMessage('Bu sefer için satılmış biletler var. Sefer silinemez!', 'error');
        } else {
            $stmt = $pdo->prepare("DELETE FROM trips WHERE id = ?");
            if ($stmt->execute([$tripId])) {
                setFlashMessage('Sefer başarıyla silindi!', 'success');
            }
        }
    }
    
    redirect('/pages/company_admin/manage_trips.php');
}

// Seferleri getir
$stmt = $pdo->prepare("
    SELECT t.*, 
           (SELECT COUNT(*) FROM tickets WHERE trip_id = t.id AND status = 'active') as sold_tickets
    FROM trips t
    WHERE t.company_id = ?
    ORDER BY t.departure_date DESC, t.departure_time DESC
");
$stmt->execute([$companyId]);
$trips = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-bus-front"></i> Seferlerim
            </h2>
            <a href="add_edit_trip.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Yeni Sefer Ekle
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <?php if (count($trips) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Güzergah</th>
                                    <th>Tarih</th>
                                    <th>Kalkış</th>
                                    <th>Fiyat</th>
                                    <th>Koltuklar</th>
                                    <th>Satılan</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trips as $trip): ?>
                                    <?php
                                    $isPast = strtotime($trip['departure_date'] . ' ' . $trip['departure_time']) < time();
                                    $isFull = $trip['available_seats'] == 0;
                                    ?>
                                    <tr class="<?php echo $isPast ? 'table-secondary' : ''; ?>">
                                        <td><?php echo $trip['id']; ?></td>
                                        <td>
                                            <strong><?php echo clean($trip['departure_city']); ?></strong>
                                            <i class="bi bi-arrow-right"></i>
                                            <strong><?php echo clean($trip['arrival_city']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo clean($trip['bus_number']); ?></small>
                                        </td>
                                        <td><?php echo formatDate($trip['departure_date']); ?></td>
                                        <td>
                                            <?php echo formatTime($trip['departure_time']); ?>
                                            <br>
                                            <small class="text-muted"><?php echo formatTime($trip['arrival_time']); ?></small>
                                        </td>
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
                                            <?php if ($isPast): ?>
                                                <span class="badge bg-secondary">Tamamlandı</span>
                                            <?php elseif ($isFull): ?>
                                                <span class="badge bg-warning">Dolu</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="add_edit_trip.php?id=<?php echo $trip['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Düzenle">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <?php if ($trip['sold_tickets'] == 0): ?>
                                                <a href="?delete=<?php echo $trip['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Bu seferi silmek istediğinizden emin misiniz?');"
                                                   title="Sil">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-secondary" disabled title="Bilet satıldı, silinemez">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-bus-front text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Henüz sefer eklemediniz</h5>
                        <a href="add_edit_trip.php" class="btn btn-success mt-3">
                            <i class="bi bi-plus-circle"></i> İlk Seferi Ekle
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>