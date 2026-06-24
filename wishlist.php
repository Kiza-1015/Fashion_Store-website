<?php
$page_title = 'Wishlist - LuxeMode';
require_once 'includes/header.php';

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    if (!is_logged_in()) { header('Location: login.php?redirect=' . urlencode("product-detail.php?id=" . (int)($_GET['id'] ?? 0))); exit; }
    $pid = (int)($_GET['id'] ?? 0);
    $cid = $_SESSION['customer_id'];
    $conn->query("INSERT IGNORE INTO wishlist (customer_id, product_id) VALUES ($cid, $pid)");
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'wishlist.php') . '&wishlisted=1'); exit;
}

if ($action === 'remove') {
    if (is_logged_in()) {
        $pid = (int)($_GET['id'] ?? 0);
        $cid = $_SESSION['customer_id'];
        $conn->query("DELETE FROM wishlist WHERE customer_id=$cid AND product_id=$pid");
    }
    header('Location: wishlist.php'); exit;
}

$items = [];
if (is_logged_in()) {
    $cid = $_SESSION['customer_id'];
    $res = $conn->query("SELECT p.* FROM wishlist w JOIN products p ON w.product_id=p.product_id WHERE w.customer_id=$cid ORDER BY w.added_at DESC");
    while ($row = $res->fetch_assoc()) $items[] = $row;
}
?>
<div class="page-banner"><div class="container"><h1>Your Wishlist</h1></div></div>

<section class="wishlist-page">
    <div class="container">
        <?php if (!is_logged_in()): ?>
        <div class="alert alert-info">Please <a href="login.php" style="font-weight:700;">sign in</a> to view your wishlist.</div>
        <?php elseif (empty($items)): ?>
        <div style="text-align:center;padding:60px 20px;">
            <i class="fas fa-heart" style="font-size:3.5rem;color:#d1d5db;display:block;margin-bottom:16px;"></i>
            <h3 style="font-family:'Playfair Display',serif;">Your wishlist is empty</h3>
            <p style="color:#6b7280;margin:8px 0 24px;">Save items you love for later.</p>
            <a href="products.php" class="btn btn-dark">Start Browsing</a>
        </div>
        <?php else: ?>
        <div class="product-grid">
            <?php foreach ($items as $p): ?>
            <div class="product-card">
                <a href="product-detail.php?id=<?= $p['product_id'] ?>">
                    <div class="product-img-wrap">
                        <?php if ($p['image'] && file_exists("uploads/{$p['image']}")): ?>
                        <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                        <?php else: ?>
                        <div class="product-placeholder"><i class="fas fa-tshirt"></i></div>
                        <?php endif; ?>
                        <?php if ($p['sale_price']): ?><span class="product-tag tag-sale">Sale</span><?php endif; ?>
                    </div>
                </a>
                <div class="product-actions">
                    <a href="cart.php?action=add&id=<?= $p['product_id'] ?>" class="action-cart">Add to Bag</a>
                    <a href="wishlist.php?action=remove&id=<?= $p['product_id'] ?>" class="action-wish" style="color:#dc2626;" onclick="return confirm('Remove from wishlist?')"><i class="fas fa-heart"></i></a>
                </div>
                <div class="product-info">
                    <div class="category"><?= ucfirst($p['category']) ?></div>
                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <div class="price">
                        <?php if ($p['sale_price']): ?>
                        <span class="sale-price">$<?= number_format($p['sale_price'], 2) ?></span>
                        <span class="original">$<?= number_format($p['price'], 2) ?></span>
                        <?php else: ?>
                        <span class="current">$<?= number_format($p['price'], 2) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
