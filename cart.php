<?php
$page_title = 'Shopping Bag - LuxeMode';
require_once 'includes/header.php';

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    if (!is_logged_in()) { header('Location: login.php?redirect=' . urlencode("cart.php?action=add&id=" . (int)($_GET['id'] ?? 0))); exit; }
    $pid = (int)($_GET['id'] ?? 0);
    $cid = $_SESSION['customer_id'];
    $stock = $conn->query("SELECT stock FROM products WHERE product_id=$pid")->fetch_row()[0] ?? 0;
    if ($stock > 0) {
        $stmt = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (?,?,1) ON DUPLICATE KEY UPDATE quantity=LEAST(quantity+1,?)");
        $stmt->bind_param('iii', $cid, $pid, $stock);
        $stmt->execute();
    }
    header('Location: cart.php?added=1'); exit;
}

if ($action === 'remove') {
    if (is_logged_in()) {
        $cid = $_SESSION['customer_id'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND customer_id=?");
        $cid2 = (int)($_GET['cid'] ?? 0);
        $stmt->bind_param('ii', $cid2, $cid);
        $stmt->execute();
    }
    header('Location: cart.php'); exit;
}

if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    $cid = $_SESSION['customer_id'];
    foreach ($_POST['qty'] ?? [] as $cart_id => $qty) {
        $cart_id = (int)$cart_id; $qty = (int)$qty;
        if ($qty < 1) $conn->query("DELETE FROM cart WHERE cart_id=$cart_id AND customer_id=$cid");
        else $conn->query("UPDATE cart SET quantity=$qty WHERE cart_id=$cart_id AND customer_id=$cid");
    }
    header('Location: cart.php?updated=1'); exit;
}

$items = []; $subtotal = 0;
if (is_logged_in()) {
    $cid = $_SESSION['customer_id'];
    $res = $conn->query("SELECT c.cart_id, c.quantity, c.size, p.* FROM cart c JOIN products p ON c.product_id=p.product_id WHERE c.customer_id=$cid");
    while ($row = $res->fetch_assoc()) {
        $items[] = $row;
        $price = $row['sale_price'] ?? $row['price'];
        $subtotal += $price * $row['quantity'];
    }
}
$shipping = $subtotal > 75 ? 0 : ($subtotal > 0 ? 7.99 : 0);
$total = $subtotal + $shipping;
?>

<div class="page-banner">
    <div class="container"><h1>Your Shopping Bag</h1></div>
</div>

<section class="cart-page">
    <div class="container">
        <?php if (!is_logged_in()): ?>
        <div class="alert alert-info">Please <a href="login.php" style="font-weight:700;">sign in</a> to view your bag.</div>
        <?php elseif (empty($items)): ?>
        <div class="empty-bag">
            <i class="fas fa-shopping-bag"></i>
            <h3 style="font-family:'Playfair Display',serif;font-size:1.5rem;margin-bottom:8px;">Your bag is empty</h3>
            <p style="color:#6b7280;margin-bottom:24px;">Discover our collections and find your next favourite piece.</p>
            <a href="products.php" class="btn btn-dark btn-lg">Start Shopping</a>
        </div>
        <?php else: ?>
        <?php if (!empty($_GET['added'])): ?><div class="alert alert-success"><i class="fas fa-check"></i> Item added to your bag.</div><?php endif; ?>
        <?php if (!empty($_GET['updated'])): ?><div class="alert alert-success"><i class="fas fa-check"></i> Bag updated.</div><?php endif; ?>

        <div class="cart-layout">
            <div>
                <div class="cart-items">
                    <div class="cart-head">
                        <div>Product</div><div>Price</div><div>Qty</div><div>Total</div><div></div>
                    </div>
                    <form method="POST" action="cart.php?action=update">
                        <?php foreach ($items as $item):
                            $price = $item['sale_price'] ?? $item['price'];
                        ?>
                        <div class="cart-row">
                            <div class="cart-product">
                                <?php if ($item['image'] && file_exists("uploads/{$item['image']}")): ?>
                                <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                                <?php else: ?>
                                <div style="width:70px;height:90px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;"><i class="fas fa-tshirt" style="color:#d1d5db;font-size:1.5rem;"></i></div>
                                <?php endif; ?>
                                <div>
                                    <div class="name"><a href="product-detail.php?id=<?= $item['product_id'] ?>"><?= htmlspecialchars($item['title']) ?></a></div>
                                    <?php if ($item['size']): ?><div class="meta">Size: <?= htmlspecialchars($item['size']) ?></div><?php endif; ?>
                                </div>
                            </div>
                            <div style="font-weight:600;">$<?= number_format($price, 2) ?></div>
                            <div>
                                <div class="qty-input">
                                    <button type="button" onclick="this.nextElementSibling.stepDown();this.nextElementSibling.dispatchEvent(new Event('change'))">-</button>
                                    <input type="number" name="qty[<?= $item['cart_id'] ?>]" value="<?= $item['quantity'] ?>" min="0" max="<?= $item['stock'] ?>">
                                    <button type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                                </div>
                            </div>
                            <div style="font-weight:700;">$<?= number_format($price * $item['quantity'], 2) ?></div>
                            <div>
                                <a href="cart.php?action=remove&cid=<?= $item['cart_id'] ?>" style="color:#dc2626;font-size:1rem;" onclick="return confirm('Remove?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div style="padding:14px 20px;display:flex;gap:10px;border-top:1px solid #e5e7eb;">
                            <button type="submit" class="btn btn-outline btn-sm">Update Bag</button>
                            <a href="products.php" class="btn btn-outline btn-sm">Continue Shopping</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-line"><span>Subtotal</span><span>$<?= number_format($subtotal, 2) ?></span></div>
                <div class="summary-line"><span>Shipping</span><span><?= $shipping===0 ? '<span style="color:#16a34a;">Free</span>' : '$' . number_format($shipping, 2) ?></span></div>
                <?php if ($shipping > 0): ?><div style="font-size:.78rem;color:#6b7280;margin-top:-6px;">Free shipping on orders over $75</div><?php endif; ?>
                <div class="summary-line total"><span>Total</span><span>$<?= number_format($total, 2) ?></span></div>
                <a href="checkout.php" class="btn btn-dark btn-block btn-lg" style="margin-top:20px;">
                    <i class="fas fa-lock"></i> Proceed to Checkout
                </a>
                <div style="margin-top:14px;text-align:center;font-size:.78rem;color:#9ca3af;">
                    <i class="fas fa-lock"></i> Secure, encrypted checkout
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
