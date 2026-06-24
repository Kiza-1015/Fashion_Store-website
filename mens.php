<?php
$page_title = "Men's Collection - LuxeMode";
require_once 'includes/header.php';

$sort    = clean($_GET['sort'] ?? 'newest');
$page    = max(1, (int)($_GET['page'] ?? 1));
$per     = 8;
$offset  = ($page - 1) * $per;

$sale_filter = isset($_GET['sale']) ? "AND sale_price IS NOT NULL" : '';
$sort_map = ['newest'=>'product_id DESC','price_asc'=>'price ASC','price_desc'=>'price DESC','name'=>'title ASC'];
$order = $sort_map[$sort] ?? 'product_id DESC';

$total = $conn->query("SELECT COUNT(*) FROM products WHERE category='mens' $sale_filter")->fetch_row()[0];
$pages = ceil($total / $per);
$products = $conn->query("SELECT * FROM products WHERE category='mens' $sale_filter ORDER BY $order LIMIT $per OFFSET $offset");
?>
<div class="page-banner">
    <div class="container">
        <p class="eyebrow" style="font-size:.75rem;letter-spacing:.15em;text-transform:uppercase;color:#c9a96e;font-weight:600;margin-bottom:8px;">Shop</p>
        <h1>Men's Collection</h1>
        <p><?= $total ?> styles available</p>
    </div>
</div>

<div class="container" style="padding:30px 24px;">
    <div class="filter-wrap">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;width:100%;">
            <div class="filter-group">
                <label>Sort By</label>
                <select name="sort">
                    <option value="newest" <?= $sort==='newest'?'selected':'' ?>>Newest</option>
                    <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: High to Low</option>
                    <option value="name" <?= $sort==='name'?'selected':'' ?>>Name A-Z</option>
                </select>
            </div>
            <div class="filter-group" style="flex-direction:row;align-items:center;gap:8px;min-width:auto;">
                <input type="checkbox" name="sale" id="sale_check" value="1" <?= isset($_GET['sale'])?'checked':'' ?> style="width:auto;">
                <label for="sale_check" style="text-transform:none;font-size:.875rem;">Sale only</label>
            </div>
            <button type="submit" class="btn btn-dark btn-sm">Apply</button>
            <a href="mens.php" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>

    <?php if ($products->num_rows === 0): ?>
    <div style="text-align:center;padding:60px 0;">
        <i class="fas fa-box-open" style="font-size:3rem;color:#d1d5db;"></i>
        <p style="margin-top:12px;color:#6b7280;">No products found.</p>
    </div>
    <?php else: ?>
    <div class="product-grid">
        <?php while ($p = $products->fetch_assoc()):
            include_once 'includes/auth.php'; ?>
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
                <a href="wishlist.php?action=add&id=<?= $p['product_id'] ?>" class="action-wish"><i class="fas fa-heart"></i></a>
            </div>
            <div class="product-info">
                <h3><?= htmlspecialchars($p['title']) ?></h3>
                <div class="price">
                    <?php if ($p['sale_price']): ?>
                    <span class="sale-price">$<?= number_format($p['sale_price'], 2) ?></span>
                    <span class="original">$<?= number_format($p['price'], 2) ?></span>
                    <?php else: ?>
                    <span class="current">$<?= number_format($p['price'], 2) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($p['size']): ?><div class="sizes">Sizes: <?= htmlspecialchars($p['size']) ?></div><?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php if ($pages > 1): ?>
    <div style="display:flex;gap:8px;justify-content:center;margin-top:36px;">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?sort=<?= $sort ?>&page=<?= $i ?><?= isset($_GET['sale'])?'&sale=1':'' ?>"
           style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border:1.5px solid <?= $i===$page?'#1a1a1a':'#e5e7eb' ?>;background:<?= $i===$page?'#1a1a1a':'#fff' ?>;color:<?= $i===$page?'#fff':'#1a1a1a' ?>;font-size:.875rem;font-weight:600;">
            <?= $i ?>
        </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
