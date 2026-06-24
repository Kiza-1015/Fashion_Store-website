<?php
$page_title = 'Admin Dashboard - LuxeMode';
require_once '../includes/header.php';
require_admin();

$customers = $conn->query("SELECT COUNT(*) FROM customers WHERE role='customer'")->fetch_row()[0];
$products  = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$orders    = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$revenue   = $conn->query("SELECT COALESCE(SUM(amount),0) FROM orders WHERE order_status!='cancelled'")->fetch_row()[0];

$recent_orders  = $conn->query("SELECT o.*, c.fullname FROM orders o JOIN customers c ON o.customer_id=c.customer_id ORDER BY o.order_date DESC LIMIT 8");
$pending_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE order_status='pending'")->fetch_row()[0];
$low_stock      = $conn->query("SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 6");
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header-bar">
            <h2>Dashboard</h2>
            <span style="font-size:.875rem;color:#6b7280;"><?= date('l, d F Y') ?></span>
        </div>

        <div class="stats-row">
            <div class="stat-box">
                <i class="fas fa-users stat-icon"></i>
                <div class="stat-num"><?= $customers ?></div>
                <div class="stat-lbl">Customers</div>
            </div>
            <div class="stat-box">
                <i class="fas fa-tshirt stat-icon"></i>
                <div class="stat-num"><?= $products ?></div>
                <div class="stat-lbl">Products</div>
            </div>
            <div class="stat-box">
                <i class="fas fa-shopping-bag stat-icon"></i>
                <div class="stat-num"><?= $orders ?></div>
                <div class="stat-lbl">Total Orders</div>
            </div>
            <div class="stat-box">
                <i class="fas fa-dollar-sign stat-icon"></i>
                <div class="stat-num">$<?= number_format($revenue, 0) ?></div>
                <div class="stat-lbl">Revenue</div>
            </div>
        </div>

        <?php if ($pending_orders > 0): ?>
        <div class="alert alert-info" style="margin-bottom:20px;">
            <i class="fas fa-bell"></i> You have <strong><?= $pending_orders ?></strong> pending order<?= $pending_orders!==1?'s':'' ?> awaiting confirmation.
            <a href="orders.php" style="font-weight:700;margin-left:8px;">View Orders</a>
        </div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;">Recent Orders</h3>
                    <a href="orders.php" class="btn btn-outline btn-sm">View All</a>
                </div>
                <table class="admin-table">
                    <thead><tr><th>#</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                        <?php while ($o = $recent_orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $o['order_id'] ?></td>
                            <td><?= clean($o['fullname']) ?></td>
                            <td><strong>$<?= number_format($o['amount'], 2) ?></strong></td>
                            <td><span class="order-status status-<?= $o['order_status'] ?>"><?= ucfirst($o['order_status']) ?></span></td>
                            <td><?= date('d M Y', strtotime($o['order_date'])) ?></td>
                            <td><a href="orders.php" class="btn btn-dark btn-sm"><i class="fas fa-edit"></i></a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                    <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;">Low Stock</h3>
                    <a href="products.php" class="btn btn-outline btn-sm">Manage</a>
                </div>
                <div class="admin-card" style="padding:0;overflow:hidden;">
                    <?php while ($lp = $low_stock->fetch_assoc()): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                        <span style="font-size:.875rem;font-weight:500;"><?= clean($lp['title']) ?></span>
                        <span style="font-size:.72rem;font-weight:700;padding:2px 10px;background:<?= $lp['stock']==0?'#fee2e2':'#fef3c7' ?>;color:<?= $lp['stock']==0?'#991b1b':'#92400e' ?>;">
                            <?= $lp['stock'] ?> left
                        </span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
