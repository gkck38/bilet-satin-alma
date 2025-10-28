<?php
/**
 * Bilet İptal Etme
 * Sadece User rolü erişebilir
 */

require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireRole(ROLE_USER);

$ticketId = $_GET['id'] ?? 0;

// Bilet bilgilerini getir
$stmt = $pdo->prepare("
    SELECT 
        tk.*,
        t.departure_date,
        t.departure_time
    FROM tickets tk
    JOIN trips t ON tk.trip_id = t.id
    WHERE tk.id = ? AND tk.user_id = ? AND tk.status = 'active'
");
$stmt->execute([$ticketId, $_SESSION['user_id']]);
$ticket = $stmt->fetch();

if (!$ticket) {
    setFlashMessage('Bilet bulunamadı veya iptal edilemez!', 'error');
    redirect('/pages/user/my_tickets.php');
}

// Son 1 saat kontrolü
if (!canCancelTicket($ticket['departure_date'], $ticket['departure_time'])) {
    setFlashMessage('Sefer saatine 1 saatten az kaldığı için bilet iptal edilemez!', 'error');
    redirect('/pages/user/my_tickets.php');
}

try {
    $pdo->beginTransaction();
    
    // Bileti iptal et
    $stmt = $pdo->prepare("
        UPDATE tickets 
        SET status = 'cancelled', cancelled_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    $stmt->execute([$ticketId]);
    
    // Kullanıcının kredisini iade et
    updateUserCredit($ticket['user_id'], $ticket['final_price'], 'add');
    
    // Seferin müsait koltuk sayısını artır
    updateTripSeats($ticket['trip_id'], 'increase');
    
    // Kupon kullanıldıysa sayacı azalt
    if ($ticket['coupon_code']) {
        decrementCouponUsage($ticket['coupon_code']);
    }
    
    $pdo->commit();
    
    setFlashMessage('Biletiniz başarıyla iptal edildi. ' . formatPrice($ticket['final_price']) . ' hesabınıza iade edildi.', 'success');
    
} catch (Exception $e) {
    $pdo->rollBack();
    setFlashMessage('Bilet iptali sırasında bir hata oluştu!', 'error');
}

redirect('/pages/user/my_tickets.php');