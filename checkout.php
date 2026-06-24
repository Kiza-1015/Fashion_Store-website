<?php
$page_title = 'Checkout - LuxeMode';
require_once 'includes/header.php';
require_login();

$cid = $_SESSION['customer_id'];
$res = $conn->query("SELECT c.cart_id, c.quantity, c.size, p.* FROM cart c JOIN products p ON c.product_id=p.product_id WHERE c.customer_id=$cid");
$items = []; $subtotal = 0;
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
    $subtotal += ($row['sale_price'] ?? $row['price']) * $row['quantity'];
}
if (empty($items)) { header('Location: cart.php'); exit; }

$shipping = $subtotal > 75 ? 0 : 7.99;
$total    = $subtotal + $shipping;
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ship_name    = clean($_POST['ship_name'] ?? '');
    $ship_address = clean($_POST['ship_address'] ?? '');
    $ship_phone   = clean($_POST['ship_phone'] ?? '');
    $note         = clean($_POST['note'] ?? '');

    if (!$ship_name || !$ship_address || !$ship_phone) {
        $error = 'Please fill in all required shipping fields.';
    } else {
        $conn->begin_transaction();
        try {
            $addr = "$ship_name, $ship_address | Ph: $ship_phone" . ($note ? " | Note: $note" : '');
            $stmt = $conn->prepare("INSERT INTO orders (customer_id, amount, shipping_name, shipping_address, shipping_phone) VALUES (?,?,?,?,?)");
            $stmt->bind_param('idsss', $cid, $total, $ship_name, $addr, $ship_phone);
            $stmt->execute();
            $order_id = $conn->insert_id;

            foreach ($items as $item) {
                $price = $item['sale_price'] ?? $item['price'];
                $ist   = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size) VALUES (?,?,?,?,?)");
                $ist->bind_param('iiids', $order_id, $item['product_id'], $item['quantity'], $price, $item['size']);
                $ist->execute();
                $conn->query("UPDATE products SET stock=stock-{$item['quantity']} WHERE product_id={$item['product_id']}");
            }
            $conn->query("DELETE FROM cart WHERE customer_id=$cid");
            $conn->commit();
            header("Location: orders.php?placed=$order_id"); exit;
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Order failed. Please try again.';
        }
    }
}
$customer = $conn->query("SELECT * FROM customers WHERE customer_id=$cid")->fetch_assoc();
?>
<div class="page-banner"><div class="container"><h1>Checkout</h1></div></div>

<section class="checkout-page">
    <div class="container">
        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
        <div class="checkout-layout">
            <form method="POST" id="checkoutForm">
                <div class="checkout-card" style="margin-bottom:20px;">
                    <h3>Shipping Information</h3>
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="ship_name" value="<?= clean($customer['fullname']) ?>">
                        <div class="field-error" data-err="ship_name"></div>
                    </div>
                    <div class="form-group">
                        <label>Shipping Address *</label>
                        <textarea name="ship_address" rows="2" placeholder="Street address, city, postal code..."><?= clean($customer['address'] ?? '') ?></textarea>
                        <div class="field-error" data-err="ship_address"></div>
                    </div>
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="text" name="ship_phone" value="<?= clean($customer['phone'] ?? '') ?>">
                        <div class="field-error" data-err="ship_phone"></div>
                    </div>
                    <div class="form-group">
                        <label>Order Note (optional)</label>
                        <textarea name="note" rows="2" placeholder="Any special instructions?"></textarea>
                    </div>
                </div>
                <div class="checkout-card">
                    <h3>Payment Method</h3>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <label style="display:flex;align-items:center;gap:12px;padding:14px;border:1.5px solid #1a1a1a;cursor:pointer;">
                            <input type="radio" name="payment" value="cod" checked>
                            <i class="fas fa-money-bill-wave" style="color:#c9a96e;font-size:1.1rem;"></i>
                            <span><strong>Cash on Delivery</strong></span>
                        </label>
                        <label style="display:flex;align-items:center;gap:12px;padding:14px;border:1.5px solid #e5e7eb;cursor:pointer;">
                            <input type="radio" name="payment" value="card">
                            <i class="fab fa-cc-visa" style="font-size:1.3rem;color:#1a1a6b;"></i>
                            <span><strong>Card Payment</strong> <small style="color:#6b7280;">(demo)</small></span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-dark btn-lg btn-block" style="margin-top:20px;">
                    <i class="fas fa-check-circle"></i> Place Order — $<?= number_format($total, 2) ?>
                </button>
            </form>

            <div>
                <div class="checkout-card">
                    <h3>Your Order</h3>
                    <?php foreach ($items as $item):
                        $price = $item['sale_price'] ?? $item['price'];
                    ?>
                    <div class="order-line">
                        <div>
                            <div style="font-weight:600;font-size:.9rem;"><?= htmlspecialchars($item['title']) ?></div>
                            <div style="font-size:.78rem;color:#6b7280;">Qty: <?= $item['quantity'] ?><?= $item['size']?' | Size: '.$item['size']:'' ?></div>
                        </div>
                        <strong>$<?= number_format($price * $item['quantity'], 2) ?></strong>
                    </div>
                    <?php endforeach; ?>
                    <div class="summary-line" style="padding:8px 0;"><span>Subtotal</span><span>$<?= number_format($subtotal, 2) ?></span></div>
                    <div class="summary-line" style="padding:8px 0;"><span>Shipping</span><span><?= $shipping===0?'<span style="color:#16a34a;">Free</span>':'$'.number_format($shipping,2) ?></span></div>
                    <div class="summary-line total" style="font-size:1.05rem;"><span>Total</span><span>$<?= number_format($total, 2) ?></span></div>
                </div>
                <div style="margin-top:14px;padding:12px;background:#f9f9f9;font-size:.8rem;color:#6b7280;text-align:center;">
                    <i class="fas fa-shield-alt"></i> Secure 256-bit encrypted checkout
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
