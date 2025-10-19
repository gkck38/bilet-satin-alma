<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/index.php">
                <i class="bi bi-bus-front"></i> <?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">Ana Sayfa</a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php if (hasRole(ROLE_USER)): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/pages/user/my_tickets.php">Biletlerim</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if (hasRole(ROLE_COMPANY_ADMIN)): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/pages/company_admin/dashboard.php">Firma Paneli</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php if (hasRole(ROLE_ADMIN)): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/pages/admin/dashboard.php">Admin Paneli</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo clean($_SESSION['full_name']); ?>
                                <?php if (hasRole(ROLE_USER)): ?>
                                    <span class="badge bg-success"><?php echo formatPrice(getUserCredit($_SESSION['user_id'])); ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (hasRole(ROLE_USER)): ?>
                                    <li><a class="dropdown-item" href="/pages/user/my_tickets.php">
                                        <i class="bi bi-ticket-perforated"></i> Biletlerim
                                    </a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Çıkış Yap
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login.php">Giriş Yap</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register.php">Kayıt Ol</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="py-4">
        <div class="container"