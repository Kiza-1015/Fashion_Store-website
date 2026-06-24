<?php
$page_title = 'Product - LuxeMode';
require_once 'includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$p  = $conn->query("SELECT * FROM products WHERE product_id=$id")->fetch_assoc();
if (!$p) { header('Location: products.php'); exit; }
$page_title = htmlspecialchars($p['title']) . ' - LuxeMode';

// Reviews
$reviews  = $conn->query("SELECT r.*, c.fullname FROM reviews r JOIN customers c ON r.customer_id=c.customer_id WHERE r.product_id=$id ORDER BY r.created_at DESC");
$avg_stmt = $conn->query("SELECT AVG(rating) AS avg, COUNT(*) AS cnt FROM reviews WHERE product_id=$id");
$avg_data = $avg_stmt->fetch_assoc();
$avg_rating = round($avg_data['avg'] ?? 0, 1);
$review_count = $avg_data['cnt'];

// Wishlist check
$in_wishlist = false;
if (is_logged_in()) {
    $cid = $_SESSION['customer_id'];
    $in_wishlist = (bool)$conn->query("SELECT 1 FROM wishlist WHERE customer_id=$cid AND product_id=$id")->num_rows;
}

$success = '';
$error   = '';

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_cart'])) {
    if (!is_logged_in()) { header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI'])); exit; }
    $qty  = max(1, (int)($_POST['qty'] ?? 1));
    $size = clean($_POST['selected_size'] ?? '');
    $cid  = $_SESSION['customer_id'];
    $stmt = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity, size) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE quantity=quantity+?");
    $stmt->bind_param('iiisi', $cid, $id, $qty, $size, $qty);
    $stmt->execute();
    $success = 'Added to your bag!';
}

// Submit review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!is_logged_in()) { header('Location: login.php'); exit; }
    $rating  = (int)($_POST['rating'] ?? 5);
    $comment = clean($_POST['comment'] ?? '');
    $cid     = $_SESSION['customer_id'];
    if (!$comment) { $error = 'Please write a comment.'; }
    elseif ($rating < 1 || $rating > 5) { $error = 'Invalid rating.'; }
    else {
        $stmt = $conn->prepare("INSERT INTO reviews (customer_id, product_id, comment, rating) VALUES (?,?,?,?)");
        $stmt->bind_param('iisi', $cid, $id, $comment, $rating);
        $stmt->execute();
        header('Location: product-detail.php?id=' . $id . '&reviewed=1'); exit;
    }
}

$sizes = $p['size'] ? array_map('trim', explode(',', $p['size'])) : [];
$display_price = $p['sale_price'] ?? $p['price'];
?>

<div class="container">
    <div class="breadcrumb">
        <ol>
            <li><a href="index.php">Home</a></li>
            <li><a href="<?= $p['category'] ?>.php"><?= ucfirst($p['category']) ?></a></li>
            <li><?= htmlspecialchars($p['title']) ?></li>
        </ol>
    </div>
</div>

<section class="detail-section">
    <div class="container">
        <?php if ($success): ?><div class="alert alert-success" style="margin-bottom:20px;"><?= $success ?> <a href="cart.php" style="font-weight:700;">View Bag</a></div><?php endif; ?>
        <?php if (!empty($_GET['reviewed'])): ?><div class="alert alert-success" style="margin-bottom:20px;">Review submitted. Thank you!</div><?php endif; ?>

        <div class="detail-layout">
            <div class="gallery-main">
                <?php if ($p['image'] && file_exists("uploads/{$p['image']}")): ?>
                <img src="uploads/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                <?php else: ?>
                <div style="font-size:8rem;color:#d1d5db;"><i class="fas fa-tshirt"></i></div>
                <?php endif; ?>
            </div>

            <div class="detail-info">
                <div class="eyebrow"><?= ucfirst($p['category']) ?></div>
                <h1><?= htmlspecialchars($p['title']) ?></h1>

                <?php if ($review_count > 0): ?>
                <div class="detail-rating">
                    <?php for ($i=1;$i<=5;$i++): ?>
                    <i class="fas fa-star" style="color:<?= $i<=$avg_rating?'#f59e0b':'#d1d5db' ?>;"></i>
                    <?php endfor; ?>
                    <span><?= $avg_rating ?> (<?= $review_count ?> review<?= $review_count!==1?'s':'' ?>)</span>
                </div>
                <?php endif; ?>

                <div class="detail-price">
                    <?php if ($p['sale_price']): ?>
                    <span class="price-sale">$<?= number_format($p['sale_price'], 2) ?></span>
                    <span class="price-was">$<?= number_format($p['price'], 2) ?></span>
                    <span style="background:#dc2626;color:#fff;padding:3px 10px;font-size:.75rem;font-weight:700;">
                        <?= round((1 - $p['sale_price']/$p['price'])*100) ?>% OFF
                    </span>
                    <?php else: ?>
                    <span class="price-now">$<?= number_format($p['price'], 2) ?></span>
                    <?php endif; ?>
                </div>

                <p class="detail-desc"><?= nl2br(htmlspecialchars($p['description'])) ?></p>

                <?php if (!empty($sizes)): ?>
                <div class="size-label">Select Size</div>
                <div class="size-grid" style="margin-bottom:16px;">
                    <?php foreach ($sizes as $sz): ?>
                    <button type="button" class="size-btn" data-size="<?= htmlspecialchars($sz) ?>"><?= htmlspecialchars($sz) ?></button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="selected_size" id="selected_size" value="">
                    <div class="qty-row" style="margin-bottom:20px;">
                        <button type="button" onclick="qtyChange(-1)">−</button>
                        <input type="number" id="qty" name="qty" value="1" min="1" max="<?= $p['stock'] ?>">
                        <button type="button" onclick="qtyChange(1)">+</button>
                    </div>
                    <div class="action-row">
                        <?php if ($p['stock'] > 0): ?>
                        <button type="submit" name="add_cart" class="btn btn-dark btn-lg"><i class="fas fa-shopping-bag"></i> Add to Bag</button>
                        <?php else: ?>
                        <button class="btn btn-lg" style="background:#e5e7eb;cursor:not-allowed;" disabled>Out of Stock</button>
                        <?php endif; ?>
                        <a href="wishlist.php?action=<?= $in_wishlist?'remove':'add' ?>&id=<?= $p['product_id'] ?>"
                           class="btn <?= $in_wishlist?'btn-gold':'btn-outline' ?>">
                            <i class="fas fa-heart"></i> <?= $in_wishlist ? 'Wishlisted' : 'Wishlist' ?>
                        </a>
                    </div>
                </form>

                <div class="product-meta">
                    <span><strong>Availability:</strong>
                        <?= $p['stock'] > 0 ? "<span style='color:#16a34a;'>In Stock ({$p['stock']})</span>" : "<span style='color:#dc2626;'>Out of Stock</span>" ?>
                    </span>
                    <?php if ($p['color']): ?><span><strong>Colors:</strong> <?= htmlspecialchars($p['color']) ?></span><?php endif; ?>
                    <span><strong>SKU:</strong> LM-<?= str_pad($p['product_id'], 5, '0', STR_PAD_LEFT) ?></span>
                </div>
            </div>
        </div>

        <!-- REVIEWS -->
        <div class="reviews-section">
            <h2 style="font-family:'Playfair Display',serif;font-size:1.5rem;margin-bottom:24px;padding-bottom:12px;border-bottom:1px solid #e5e7eb;">
                Customer Reviews
            </h2>
            <?php if ($reviews->num_rows === 0): ?>
            <p style="color:#6b7280;">No reviews yet. Be the first to review this product!</p>
            <?php else: ?>
            <?php while ($rv = $reviews->fetch_assoc()): ?>
            <div class="review-card">
                <div class="review-header">
                    <div>
                        <div class="review-author"><?= htmlspecialchars($rv['fullname']) ?></div>
                        <div class="rating-stars">
                            <?php for ($i=1;$i<=5;$i++): ?>
                            <i class="fas fa-star" style="color:<?= $i<=$rv['rating']?'#f59e0b':'#d1d5db' ?>;"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="review-date"><?= date('d M Y', strtotime($rv['created_at'])) ?></div>
                </div>
                <p class="review-text"><?= htmlspecialchars($rv['comment']) ?></p>
            </div>
            <?php endwhile; ?>
            <?php endif; ?>

            <?php if (is_logged_in()): ?>
            <div style="margin-top:32px;padding:24px;background:#f9f9f9;border:1px solid #e5e7eb;">
                <h3 style="font-size:1rem;font-weight:700;margin-bottom:16px;">Write a Review</h3>
                <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Rating</label>
                        <select name="rating" style="width:auto;padding:9px 14px;border:1.5px solid #e5e7eb;">
                            <?php for ($i=5;$i>=1;$i--): ?>
                            <option value="<?= $i ?>"><?= $i ?> Star<?= $i>1?'s':'' ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Your Review</label>
                        <textarea name="comment" rows="4" placeholder="Share your thoughts about this product..."></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="btn btn-dark">Submit Review</button>
                </form>
            </div>
            <?php else: ?>
            <div style="margin-top:24px;padding:16px;background:#f9f9f9;text-align:center;border:1px solid #e5e7eb;">
                <a href="login.php" style="font-weight:700;color:#1a1a1a;">Login</a> to write a review.
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
