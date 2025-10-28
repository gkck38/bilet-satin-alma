<?php
/**
 * Biletlerim Sayfası
 * Sadece User rolü erişebilir
 */

require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireRole(ROLE_USER);

// Kullanıcının biletlerini getir
$stmt = $pdo->prepare("
    SELECT 
        tk.*,
        t.departure_city,
        t.arrival_city,
        t.departure_date,
        t.departure_time,
        t.arrival_time,
        t.bus_number,
        c.name as company_name,
        c.phone as company_phone
    FROM tickets tk
    JOIN trips t ON tk.trip_id = t.id
    JOIN companies c ON t.company_id = c.id
    WHERE tk.user_id = ?
    ORDER BY t.departure_date DESC, t.departure_time DESC
");
$stmt->execute([$_SESSION['user_id']]);
$tickets = $stmt->fetchAll();

// Kullanıcı bilgilerini getir
$user = getCurrentUser();

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <!-- Kullanıcı Bilgileri -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-2">
                            <i class="bi bi-person-circle"></i>
                            <?php echo clean($user['full_name']); ?>
                        </h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-envelope"></i> <?php echo clean($user['email']); ?>
                            <?php if ($user['phone']): ?>
                                | <i class="bi bi-telephone"></i> <?php echo clean($user['phone']); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="bg-light p-3 rounded">
                            <small class="text-muted d-block">Kredi Bakiyeniz</small>
                            <h3 class="text-success mb-0"><?php echo formatPrice($user['credit']); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Biletler -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-ticket-perforated"></i> Biletlerim
                    <?php if (count($tickets) > 0): ?>
                        <span class="badge bg-light text-primary"><?php echo count($tickets); ?></span>
                    <?php endif; ?>
                </h4>
            </div>
            <div class="card-body">
                <?php if (count($tickets) > 0): ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <?php
                        $canCancel = canCancelTicket($ticket['departure_date'], $ticket['departure_time']);
                        $isPast = strtotime($ticket['departure_date'] . ' ' . $ticket['departure_time']) < time();
                        $statusClass = $ticket['status'] === 'active' ? 'success' : 'danger';
                        $statusText = $ticket['status'] === 'active' ? 'Aktif' : 'İptal Edildi';
                        ?>
                        
                        <div class="card mb-3 border-<?php echo $statusClass; ?>">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="mb-0">
                                                <?php echo clean($ticket['company_name']); ?>
                                            </h5>
                                            <span class="badge bg-<?php echo $statusClass; ?>">
                                                <?php echo $statusText; ?>
                                            </span>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <i class="bi bi-geo-alt text-primary"></i>
                                            <strong><?php echo clean($ticket['departure_city']); ?></strong>
                                            <i class="bi bi-arrow-right mx-2"></i>
                                            <strong><?php echo clean($ticket['arrival_city']); ?></strong>
                                        </div>
                                        
                                        <div class="row text-muted small">
                                            <div class="col-md-6">
                                                <i class="bi bi-calendar"></i>
                                                <?php echo formatDate($ticket['departure_date']); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <i class="bi bi-clock"></i>
                                                <?php echo formatTime($ticket['departure_time']); ?> -
                                                <?php echo formatTime($ticket['arrival_time']); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="row text-muted small mt-1">
                                            <div class="col-md-6">
                                                <i class="bi bi-bus"></i>
                                                Otobüs: <?php echo clean($ticket['bus_number']); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <i class="bi bi-square"></i>
                                                Koltuk: <?php echo $ticket['seat_number']; ?>
                                            </div>
                                        </div>
                                        
                                        <?php if ($ticket['coupon_code']): ?>
                                            <div class="mt-2">
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-tag"></i>
                                                    Kupon: <?php echo clean($ticket['coupon_code']); ?>
                                                    (<?php echo formatPrice($ticket['discount_amount']); ?> indirim)
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-4 text-md-end">
                                        <div class="mb-3">
                                            <?php if ($ticket['discount_amount'] > 0): ?>
                                                <small class="text-muted text-decoration-line-through d-block">
                                                    <?php echo formatPrice($ticket['price']); ?>
                                                </small>
                                            <?php endif; ?>
                                            <h4 class="text-success mb-0">
                                                <?php echo formatPrice($ticket['final_price']); ?>
                                            </h4>
                                        </div>
                                        
                                        <?php if ($ticket['status'] === 'active'): ?>
                                            <a href="/download_ticket.php?id=<?php echo $ticket['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary w-100 mb-2" target="_blank">
                                                <i class="bi bi-file-pdf"></i> PDF İndir
                                            </a>
                                            
                                            <?php if ($canCancel && !$isPast): ?>
                                                <a href="cancel_ticket.php?id=<?php echo $ticket['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger w-100"
                                                   onclick="return confirm('Bu bileti iptal etmek istediğinizden emin misiniz?');">
                                                    <i class="bi bi-x-circle"></i> İptal Et
                                                </a>
                                            <?php elseif (!$canCancel && !$isPast): ?>
                                                <button class="btn btn-sm btn-secondary w-100" disabled>
                                                    <i class="bi bi-exclamation-triangle"></i> İptal Süresi Geçti
                                                </button>
                                            <?php endif; ?>
                                        <?php elseif ($ticket['status'] === 'cancelled'): ?>
                                            <small class="text-muted">
                                                İptal: <?php echo formatDateTime($ticket['cancelled_at']); ?>
                                            </small>
                                        <?php endif; ?>
                                        
                                        <?php if ($isPast && $ticket['status'] === 'active'): ?>
                                            <span class="badge bg-secondary mt-2">Tamamlandı</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light small text-muted">
                                <i class="bi bi-calendar-plus"></i>
                                Satın Alınma: <?php echo formatDateTime($ticket['purchase_date']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-ticket-perforated text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">Henüz biletiniz bulunmuyor</h5>
                        <p class="text-muted">Sefer aramak için ana sayfaya gidin.</p>
                        <a href="/index.php" class="btn btn-primary">
                            <i class="bi bi-search"></i> Sefer Ara
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>