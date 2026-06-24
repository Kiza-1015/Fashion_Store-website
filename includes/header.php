<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$cart_count = 0;
$wish_count = 0;
if (is_logged_in()) {
    $cid = $_SESSION['customer_id'];
    $cart_count = $conn->query("SELECT COALESCE(SUM(quantity),0) FROM cart WHERE customer_id=$cid")->fetch_row()[0];
    $wish_count = $conn->query("SELECT COUNT(*) FROM wishlist WHERE customer_id=$cid")->fetch_row()[0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'LuxeMode - Fashion & Style' ?></title>
    <link rel="stylesheet" href="/fashion-store/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="container">
        <div class="topbar-inner">
            <span><i class="fas fa-truck"></i> Free shipping on orders over $75</span>
            <div class="topbar-right">
                <?php if (is_logged_in()): ?>
                <span><i class="fas fa-user"></i> Hi, <?= clean($_SESSION['fullname']) ?>!</span>
                <a href="/fashion-store/logout.php">Logout</a>
                <?php else: ?>
                <a href="/fashion-store/login.php">Login</a>
                <a href="/fashion-store/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- MAIN HEADER -->
<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <button class="hamburger" id="hamburger"><i class="fas fa-bars"></i></button>
            <a href="/fashion-store/index.php" class="logo">LuxeMode</a>
            <nav class="main-nav" id="mainNav">
                <a href="/fashion-store/index.php">Home</a>
                <a href="/fashion-store/mens.php">Men</a>
                <a href="/fashion-store/womens.php">Women</a>
                <a href="/fashion-store/products.php">All Products</a>
                <a href="/fashion-store/contact.php">Contact</a>
                <?php if (is_admin()): ?>
                <a href="/fashion-store/admin/dashboard.php" class="admin-nav-link">Admin</a>
                <?php endif; ?>
            </nav>
            <div class="header-actions">
                <a href="/fashion-store/products.php" class="icon-btn" title="Search"><i class="fas fa-search"></i></a>
                <?php if (is_logged_in()): ?>
                <a href="/fashion-store/wishlist.php" class="icon-btn" title="Wishlist">
                    <i class="fas fa-heart"></i>
                    <?php if ($wish_count > 0): ?><span class="badge"><?= $wish_count ?></span><?php endif; ?>
                </a>
                <a href="/fashion-store/profile.php" class="icon-btn" title="Profile"><i class="fas fa-user"></i></a>
                <?php endif; ?>
                <a href="/fashion-store/cart.php" class="icon-btn" title="Cart">
                    <i class="fas fa-shopping-bag"></i>
                    <?php if ($cart_count > 0): ?><span class="badge"><?= $cart_count ?></span><?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</header>
<main>
