<?php
$page_title = 'LuxeMode - Fashion & Style';
require_once 'includes/header.php';

$new_mens    = $conn->query("SELECT * FROM products WHERE category='mens'   ORDER BY product_id DESC LIMIT 4");
$new_womens  = $conn->query("SELECT * FROM products WHERE category='womens' ORDER BY product_id DESC LIMIT 4");
$sale_items  = $conn->query("SELECT * FROM products WHERE sale_price IS NOT NULL ORDER BY product_id DESC LIMIT 4");

function product_card($p, $base = '/fashion-store') { ?>
<div class="product-card">
    <a href="<?= $base ?>/product-detail.php?id=<?= $p['product_id'] ?>">
        <div class="product-img-wrap">
            <?php if ($p['image'] && file_exists(__DIR__ . "/uploads/{$p['image']}")): ?>
            <img src="<?= $base ?>/uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
            <?php else: ?>
            <div class="product-placeholder"><i class="fas fa-tshirt"></i></div>
            <?php endif; ?>
            <?php if ($p['sale_price']): ?><span class="product-tag tag-sale">Sale</span><?php endif; ?>
        </div>
    </a>
    <div class="product-actions">
        <a href="<?= $base ?>/cart.php?action=add&id=<?= $p['product_id'] ?>" class="action-cart">Add to Bag</a>
        <a href="<?= $base ?>/wishlist.php?action=add&id=<?= $p['product_id'] ?>" class="action-wish"><i class="fas fa-heart"></i></a>
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
        <?php if ($p['size']): ?><div class="sizes">Sizes: <?= htmlspecialchars($p['size']) ?></div><?php endif; ?>
    </div>
</div>
<?php } ?>

<!-- HERO SPLIT -->
<section class="hero">
    <div class="hero-split">
        <div class="hero-panel dark">
            <div class="hero-eyebrow">New Season Collection</div>
            <h1>Men's Essentials, Refined</h1>
            <p>Discover our curated menswear collection — where classic craftsmanship meets contemporary style.</p>
            <a href="mens.php" class="btn btn-gold btn-lg">Shop Men</a>
        </div>
        <div class="hero-panel light">
            <div class="hero-eyebrow">Spring / Summer 2025</div>
            <h1>Effortless Women's Style</h1>
            <p>From casual everyday looks to elegant eveningwear. Find your signature style with LuxeMode.</p>
            <a href="womens.php" class="btn btn-dark btn-lg">Shop Women</a>
        </div>
    </div>
</section>

<!-- CATEGORY CARDS -->
<section class="category-strip">
    <div class="container">
        <div class="section-header center" style="margin-bottom:32px;">
            <div class="eyebrow">Explore</div>
            <h2>Shop by Category</h2>
        </div>
        <div class="cat-grid">
            <a href="mens.php" class="cat-solid">
                <i class="fas fa-male" style="font-size:2.5rem;margin-bottom:12px;"></i>
                <h3>Men</h3>
                <p>Jackets, shirts, trousers & more</p>
                <span class="btn btn-outline btn-sm" style="color:#fff;border-color:rgba(255,255,255,.4);">Explore</span>
            </a>
            <a href="womens.php" class="cat-solid">
                <i class="fas fa-female" style="font-size:2.5rem;margin-bottom:12px;"></i>
                <h3>Women</h3>
                <p>Dresses, tops, knitwear & more</p>
                <span class="btn btn-outline btn-sm">Explore</span>
            </a>
            <a href="products.php?category=accessories" class="cat-solid">
                <i class="fas fa-gem" style="font-size:2.5rem;margin-bottom:12px;"></i>
                <h3>Accessories</h3>
                <p>Bags, belts, scarves & more</p>
                <span class="btn btn-outline btn-sm">Explore</span>
            </a>
            <a href="products.php?sale=1" class="cat-solid">
                <i class="fas fa-tag" style="font-size:2.5rem;margin-bottom:12px;"></i>
                <h3>Sale</h3>
                <p>Up to 40% off selected items</p>
                <span class="btn btn-outline btn-sm">Shop Sale</span>
            </a>
        </div>
    </div>
</section>

<!-- MEN'S PICKS -->
<section class="products-section" style="background:#fff;">
    <div class="container">
        <div class="section-header" style="display:flex;justify-content:space-between;align-items:flex-end;">
            <div>
                <div class="eyebrow">For Him</div>
                <h2>Men's New Arrivals</h2>
            </div>
            <a href="mens.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="product-grid">
            <?php while ($p = $new_mens->fetch_assoc()): product_card($p); endwhile; ?>
        </div>
    </div>
</section>

<!-- PROMO BANNER -->
<div class="promo-banner">
    <div class="eyebrow">Limited Time</div>
    <h2>End of Season Sale</h2>
    <p>Up to 40% off on selected styles. Don't miss out.</p>
    <a href="products.php?sale=1" class="btn btn-outline btn-lg" style="color:#fff;border-color:rgba(255,255,255,.6);">Shop the Sale</a>
</div>

<!-- WOMEN'S PICKS -->
<section class="products-section" style="background:#fff;">
    <div class="container">
        <div class="section-header" style="display:flex;justify-content:space-between;align-items:flex-end;">
            <div>
                <div class="eyebrow">For Her</div>
                <h2>Women's New Arrivals</h2>
            </div>
            <a href="womens.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="product-grid">
            <?php while ($p = $new_womens->fetch_assoc()): product_card($p); endwhile; ?>
        </div>
    </div>
</section>

<!-- FEATURES STRIP -->
<div style="background:#111;color:#9ca3af;padding:40px 0;">
    <div class="container">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
            <?php foreach([
                ['fas fa-shipping-fast','Free Shipping','On all orders over $75'],
                ['fas fa-undo','Easy Returns','30-day free returns'],
                ['fas fa-lock','Secure Payment','256-bit SSL encryption'],
                ['fas fa-star','Quality Guarantee','Premium materials only']
            ] as [$icon,$title,$desc]): ?>
            <div style="text-align:center;">
                <i class="<?= $icon ?>" style="font-size:1.8rem;color:#c9a96e;margin-bottom:10px;display:block;"></i>
                <h4 style="color:#fff;font-size:.9rem;margin-bottom:4px;"><?= $title ?></h4>
                <p style="font-size:.8rem;"><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
