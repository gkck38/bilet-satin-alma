<?php
/**
 * Admin Dashboard
 * Sadece Admin rolü erişebilir
 */

require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireRole(ROLE_ADMIN);

// İstatistikler
$stmt = $pdo->query("SELECT COUNT(*) as total FROM companies");
$totalCompanies = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'company_admin'");
$totalCompanyAdmins = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$totalUsers = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM trips");
$totalTrips = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets WHERE status = 'active'");
$totalTickets = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM coupons WHERE is_active = 1");
$activeCoupons = $stmt->fetch()['total'];

include __DIR__ . '/../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-speedometer2"></i> Admin Yönetim Paneli
        </h2>
    </div>
</div>

<!-- İstatistikler -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center border-primary">
            <div class="card-body">
                <i class="bi bi-building text-primary" style="font-size: 2.5rem;"></i>
                <h2 class="mt-2"><?php echo $totalCompanies; ?></h2>
                <p class="text-muted mb-0">Otobüs Firması</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center border-success">
            <div class="card-body">
                <i class="bi bi-person-badge text-success" style="font-size: 2.5rem;"></i>
                <h2 class="mt-2"><?php echo $totalCompanyAdmins; ?></h2>
                <p class="text-muted mb-0">Firma Yöneticisi</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center border-info">
            <div class="card-body">
                <i class="bi bi-people text-info" style="font-size: 2.5rem;"></i>
                <h2 class="mt-2"><?php echo $totalUsers; ?></h2>
                <p class="text-muted mb-0">Kayıtlı Kullanıcı</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center border-warning">
            <div class="card-body">
                <i class="bi bi-bus-front text-warning" style="font-size: 2.5rem;"></i>
                <h2 class="mt-2"><?php echo $totalTrips; ?></h2>
                <p class="text-muted mb-0">Toplam Sefer</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center border-danger">
            <div class="card-body">
                <i class="bi bi-ticket-perforated text-danger" style="font-size: 2.5rem;"></i>
                <h2 class="mt-2"><?php echo $totalTickets; ?></h2>
                <p class="text-muted mb-0">Aktif Bilet</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card text-center border-secondary">
            <div class="card-body">
                <i class="bi bi-tag text-secondary" style="font-size: 2.5rem;"></i>
                <h2 class="mt-2"><?php echo $activeCoupons; ?></h2>
                <p class="text-muted mb-0">Aktif Kupon</p>
            </div>
        </div>
    </div>
</div>

<!-- Hızlı İşlemler -->
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-lightning"></i> Hızlı İşlemler
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <a href="manage_companies.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-building"></i> Firmaları Yönet
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <a href="manage_company_admins.php" class="btn btn-success btn-lg">
                                <i class="bi bi-person-badge"></i> Firma Adminleri
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="d-grid">
                            <a href="manage_coupons.php" class="btn btn-warning btn-lg">
                                <i class="bi bi-tag"></i> Kuponları Yönet
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>