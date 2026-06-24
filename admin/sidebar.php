<?php $cur = basename($_SERVER['PHP_SELF']); ?>
<aside class="admin-sidebar">
    <div class="admin-brand">
        <h3>LuxeMode Admin</h3>
        <div style="font-size:.78rem;color:#6b7280;margin-top:4px;"><?= clean($_SESSION['fullname']) ?></div>
    </div>
    <nav class="admin-nav">
        <a href="dashboard.php" class="<?= $cur==='dashboard.php'?'active':'' ?>"><i class="fas fa-tachometer-alt fa-fw"></i> Dashboard</a>
        <a href="products.php" class="<?= $cur==='products.php'?'active':'' ?>"><i class="fas fa-tshirt fa-fw"></i> Products</a>
        <a href="add-product.php" class="<?= $cur==='add-product.php'?'active':'' ?>"><i class="fas fa-plus fa-fw"></i> Add Product</a>
        <a href="orders.php" class="<?= $cur==='orders.php'?'active':'' ?>"><i class="fas fa-shopping-bag fa-fw"></i> Orders</a>
        <a href="customers.php" class="<?= $cur==='customers.php'?'active':'' ?>"><i class="fas fa-users fa-fw"></i> Customers</a>
        <a href="reviews.php" class="<?= $cur==='reviews.php'?'active':'' ?>"><i class="fas fa-star fa-fw"></i> Reviews</a>
        <a href="../index.php"><i class="fas fa-store fa-fw"></i> View Store</a>
        <a href="../logout.php" style="color:#dc2626;margin-top:4px;"><i class="fas fa-sign-out-alt fa-fw"></i> Logout</a>
    </nav>
</aside>
