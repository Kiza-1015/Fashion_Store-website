<?php
$page_title = 'My Orders - LuxeMode';
require_once 'includes/header.php';
require_login();

$cid    = $_SESSION['customer_id'];
$orders = $conn->query("SELECT * FROM orders WHERE customer_id=$cid ORDER BY order_date DESC");
$placed = (int)($_GET['placed'] ?? 0);
?>
<div class="page-banner"><div class="container"><h1>My Orders</h1><p>Track your purchases</p></div></div>

<section class="orders-page">
    <div class="container">
        <?php if ($placed): ?>
        <div class="alert alert-success">
            <strong><i class="fas fa-check-circle"></i> Order #<?= $placed ?> confirmed!</strong> We'll process it shortly. Thank you for shopping with LuxeMode!
        </div>
        <?php endif; ?>

        <?php if ($orders->num_rows === 0): ?>
        <div style="text-align:center;padding:60px 20px;">
            <i class="fas fa-box-open" style="font-size:3.5rem;color:#d1d5db;display:block;margin-bottom:16px;"></i>
            <h3 style="font-family:'Playfair Display',serif;">No orders yet</h3>
            <p style="color:#6b7280;margin:8px 0 24px;">Start exploring our collections!</p>
            <a href="products.php" class="btn btn-dark">Shop Now</a>
        </div>
        <?php else: ?>
        <?php while ($order = $orders->fetch_assoc()):
            $items = $conn->query("SELECT oi.*, p.title, p.image FROM order_items oi JOIN products p ON oi.product_id=p.product_id WHERE oi.order_id={$order['order_id']}");
        ?>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <strong>Order #<?= $order['order_id'] ?></strong>
                    <div style="font-size:.8rem;color:#6b7280;margin-top:3px;">
                        <i class="fas fa-calendar-alt"></i> <?= date('d F Y, g:i a', strtotime($order['order_date'])) ?>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:16px;">
                    <strong style="font-size:1.05rem;">$<?= number_format($order['amount'], 2) ?></strong>
                    <span class="order-status status-<?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span>
                </div>
            </div>
            <div class="order-body">
                <?php while ($item = $items->fetch_assoc()): ?>
                <div class="order-row">
                    <span><i class="fas fa-tag" style="color:#9ca3af;margin-right:6px;"></i><?= htmlspecialchars($item['title']) ?> × <?= $item['quantity'] ?><?= $item['size']?" (Size: {$item['size']})":'' ?></span>
                    <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </div>
                <?php endwhile; ?>
                <?php if ($order['shipping_address']): ?>
                <div style="font-size:.8rem;color:#6b7280;margin-top:10px;padding-top:10px;border-top:1px solid #f3f4f6;">
                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($order['shipping_address']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
        <?php endif; ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
