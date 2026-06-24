<?php
$page_title = 'All Products - LuxeMode';
require_once 'includes/header.php';

$search   = clean($_GET['search'] ?? '');
$category = clean($_GET['category'] ?? '');
$sort     = clean($_GET['sort'] ?? 'newest');
$sale     = isset($_GET['sale']);
$page     = max(1, (int)($_GET['page'] ?? 1));
$per      = 8;
$offset   = ($page - 1) * $per;

$where = "WHERE 1=1";
$params = []; $types = '';

if ($search) { $where .= " AND title LIKE ?"; $s = "%$search%"; $params[] = $s; $types .= 's'; }
if ($category && in_array($category, ['mens','womens','kids','accessories'])) { $where .= " AND category=?"; $params[] = $category; $types .= 's'; }
if ($sale) $where .= " AND sale_price IS NOT NULL";

$sort_map = ['newest'=>'product_id DESC','price_asc'=>'price ASC','price_desc'=>'price DESC','name'=>'title ASC'];
$order = $sort_map[$sort] ?? 'product_id DESC';

$ct_stmt = $conn->prepare("SELECT COUNT(*) FROM products $where");
if ($params) $ct_stmt->bind_param($types, ...$params);
$ct_stmt->execute();
$total = $ct_stmt->get_result()->fetch_row()[0];
$pages = ceil($total / $per);

$params[] = $per; $params[] = $offset; $types .= 'ii';
$stmt = $conn->prepare("SELECT * FROM products $where ORDER BY $order LIMIT ? OFFSET ?");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();
?>
<div class="page-banner">
    <div class="container">
        <h1><?= $search ? "Results for \"$search\"" : ($sale ? 'Sale Items' : 'All Products') ?></h1>
        <p><?= $total ?> item<?= $total!==1?'s':'' ?> found</p>
    </div>
</div>
<div class="container" style="padding:30px 24px;">
    <div class="filter-wrap">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;width:100%;">
            <div class="filter-group" style="flex:1;min-width:180px;">
                <label>Search</label>
                <input type="text" name="search" placeholder="Search styles..." value="<?= $search ?>">
            </div>
            <div class="filter-group">
                <label>Category</label>
                <select name="category">
                    <option value="">All</option>
                    <?php foreach(['mens'=>"Men's",'womens'=>"Women's",'accessories'=>'Accessories'] as $val=>$lbl): ?>
                    <option value="<?= $val ?>" <?= $category===$val?'selected':'' ?>><?= $lbl ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
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
                <input type="checkbox" name="sale" id="sale_check" <?= $sale?'checked':'' ?> style="width:auto;">
                <label for="sale_check" style="text-transform:none;font-size:.875rem;">Sale only</label>
            </div>
            <button type="submit" class="btn btn-dark btn-sm">Filter</button>
            <a href="products.php" class="btn btn-outline btn-sm">Reset</a>
        </form>
    </div>

    <?php if ($products->num_rows === 0): ?>
    <div style="text-align:center;padding:60px 0;">
        <i class="fas fa-search" style="font-size:3rem;color:#d1d5db;"></i>
        <p style="margin-top:12px;color:#6b7280;">No products found.</p>
        <a href="products.php" class="btn btn-dark btn-sm" style="margin-top:16px;">View All Products</a>
    </div>
    <?php else: ?>
    <div class="product-grid">
        <?php while ($p = $products->fetch_assoc()): ?>
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
        <?php endwhile; ?>
    </div>
    <?php if ($pages > 1): ?>
    <div style="display:flex;gap:8px;justify-content:center;margin-top:36px;">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>&sort=<?= $sort ?>&page=<?= $i ?><?= $sale?'&sale=1':'' ?>"
           style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border:1.5px solid <?= $i===$page?'#1a1a1a':'#e5e7eb' ?>;background:<?= $i===$page?'#1a1a1a':'#fff' ?>;color:<?= $i===$page?'#fff':'#1a1a1a' ?>;font-size:.875rem;font-weight:600;">
            <?= $i ?>
        </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
