<?php
/**
 * PDF Bilet İndirme
 * Sadece bilet sahibi indirebilir
 */

require_once __DIR__ . '/config/config.php';

requireLogin();

$ticketId = $_GET['id'] ?? 0;

// Bilet bilgilerini getir
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
        c.phone as company_phone,
        c.address as company_address,
        u.full_name as passenger_name,
        u.email as passenger_email,
        u.phone as passenger_phone
    FROM tickets tk
    JOIN trips t ON tk.trip_id = t.id
    JOIN companies c ON t.company_id = c.id
    JOIN users u ON tk.user_id = u.id
    WHERE tk.id = ? AND tk.user_id = ?
");
$stmt->execute([$ticketId, $_SESSION['user_id']]);
$ticket = $stmt->fetch();

if (!$ticket) {
    setFlashMessage('Bilet bulunamadı!', 'error');
    redirect('/pages/user/my_tickets.php');
}

// TCPDF kütüphanesi yerine basit HTML/CSS ile PDF benzeri çıktı oluştur
// Gerçek production için TCPDF veya FPDF kullanılmalı

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bilet - <?php echo $ticket['id']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .ticket {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 3px solid #0d6efd;
            border-radius: 10px;
            overflow: hidden;
        }
        .header {
            background: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin-bottom: 5px;
            font-size: 28px;
        }
        .status {
            background: <?php echo $ticket['status'] === 'active' ? '#198754' : '#dc3545'; ?>;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }
        .content {
            padding: 30px;
        }
        .section {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 2px dashed #dee2e6;
        }
        .section:last-child {
            border-bottom: none;
        }
        .section-title {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .route {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }
        .city {
            text-align: center;
            flex: 1;
        }