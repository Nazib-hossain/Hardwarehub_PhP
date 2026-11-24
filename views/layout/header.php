<?php
/**
 * Header Layout
 * HardwareHub E-Commerce
 */
require_once __DIR__ . '/../../includes/helpers.php';
$cartTotalCount = cartCount();
$currentPage = $_GET['page'] ?? 'catalog';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HardwareHub - Premier PC hardware, components, and inventory store">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?>HardwareHub</title>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Main Style -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <!-- Top Global Header / Navigation -->
    <header class="site-header">
        <div class="container nav-container">
            <a href="index.php?page=catalog" class="brand-logo">
                <img src="public/images/logo.jpg" alt="HardwareHub Logo" class="brand-logo-img">
                <span class="logo-text">Hardware<span class="highlight">Hub</span></span>
            </a>

            <!-- Search bar in header -->
            <form action="index.php" method="GET" class="header-search">
                <input type="hidden" name="page" value="catalog">
                <input type="text" name="search" placeholder="Search CPUs, GPUs, RAM, Storage..." value="<?= e($_GET['search'] ?? '') ?>">
                <button type="submit" aria-label="Search">🔍</button>
            </form>

            <!-- Navigation Links -->
            <nav class="main-nav">
                <a href="index.php?page=catalog" class="nav-link <?= $currentPage === 'catalog' ? 'active' : '' ?>">
                    <span>🖥️</span> Catalog
                </a>

                <?php if (isAdmin()): ?>
                    <a href="index.php?page=admin" class="nav-link admin-badge-link <?= $currentPage === 'admin' ? 'active' : '' ?>">
                        <span>⚙️</span> Admin Dashboard
                    </a>
                <?php endif; ?>

                <?php if (isLoggedIn()): ?>
                    <a href="index.php?page=orders" class="nav-link <?= $currentPage === 'orders' ? 'active' : '' ?>">
                        <span>📦</span> My Orders
                    </a>
                <?php endif; ?>

                <a href="index.php?page=cart" class="nav-link cart-link <?= $currentPage === 'cart' ? 'active' : '' ?>">
                    <span>🛒</span> Cart
                    <span class="cart-badge" id="headerCartCount"><?= $cartTotalCount ?></span>
                </a>

                <?php if (isLoggedIn()): ?>
                    <div class="user-menu">
                        <span class="user-greeting">Hi, <strong><?= e($_SESSION['user_name']) ?></strong></span>
                        <a href="index.php?page=logout" class="btn-logout" title="Log out">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn-login <?= $currentPage === 'login' ? 'active' : '' ?>">
                        Sign In
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Global Flash Alerts Container -->
    <div class="container alerts-container">
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success" role="alert">
                <span class="alert-icon">✓</span>
                <span class="alert-message"><?= e($_SESSION['flash_success']) ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger" role="alert">
                <span class="alert-icon">⚠️</span>
                <span class="alert-message"><?= e($_SESSION['flash_error']) ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
    </div>

    <!-- Main Content Wrapper -->
    <main class="site-main container">
