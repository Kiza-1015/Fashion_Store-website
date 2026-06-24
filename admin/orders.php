<?php
$page_title = 'Orders - LuxeMode Admin';
require_once '../includes/header.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $oid    = (int)$_POST['order_id'];
    $status = clean($_POST['status']);
    $valid  = ['pending','confirmed','shipped','delivered','cancelled'];
    if (in_array($status, $valid)) $conn->query("UPDATE orders SET order_status='$status' WHERE order_id=$oid");
    header('Location: orders.php?updated=1'); exit;
}

$orders = $conn->query("SELECT o.*, c.fullname FROM orders o JOIN customers c ON o.customer_id=c.customer_id ORDER BY o.order_date DESC");
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header-bar"><h2>Orders</h2></div>
        <?php if (!empty($_GET['updated'])): ?><div class="alert alert-success">Status updated.</div><?php endif; ?>

        <table class="admin-table">
            <thead><tr><th>#</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th><th>Shipping</th><th>Update Status</th></tr></thead>
            <tbody>
                <?php while ($o = $orders->fetch_assoc()): ?>
                <tr>
                    <td><strong>#<?= $o['order_id'] ?></strong></td>
                    <td><?= clean($o['fullname']) ?></td>
                    <td><strong>$<?= number_format($o['amount'], 2) ?></strong></td>
                    <td><span class="order-status status-<?= $o['order_status'] ?>"><?= ucfirst($o['order_status']) ?></span></td>
                    <td><?= date('d M Y', strtotime($o['order_date'])) ?></td>
                    <td style="font-size:.78rem;color:#6b7280;max-width:160px;"><?= clean(substr($o['shipping_address'] ?? '', 0, 50)) ?>...</td>
                    <td>
                        <form method="POST" style="display:flex;gap:6px;align-items:center;">
                            <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
                            <select name="status" style="padding:5px 8px;border:1px solid #e5e7eb;font-size:.8rem;">
                                <?php foreach(['pending','confirmed','shipped','delivered','cancelled'] as $s): ?>
                                <option value="<?= $s ?>" <?= $o['order_status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-dark btn-sm"><i class="fas fa-check"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
