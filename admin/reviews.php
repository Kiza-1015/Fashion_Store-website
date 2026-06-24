<?php
$page_title = 'Reviews - LuxeMode Admin';
require_once '../includes/header.php';
require_admin();

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM reviews WHERE review_id=" . (int)$_GET['delete']);
    header('Location: reviews.php?deleted=1'); exit;
}

$reviews = $conn->query("SELECT r.*, c.fullname, p.title AS product FROM reviews r JOIN customers c ON r.customer_id=c.customer_id JOIN products p ON r.product_id=p.product_id ORDER BY r.created_at DESC");
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header-bar"><h2>Product Reviews</h2></div>
        <?php if (!empty($_GET['deleted'])): ?><div class="alert alert-success">Review deleted.</div><?php endif; ?>
        <table class="admin-table">
            <thead><tr><th>Product</th><th>Customer</th><th>Rating</th><th>Comment</th><th>Date</th><th></th></tr></thead>
            <tbody>
                <?php while ($r = $reviews->fetch_assoc()): ?>
                <tr>
                    <td style="font-weight:600;max-width:160px;"><?= clean($r['product']) ?></td>
                    <td><?= clean($r['fullname']) ?></td>
                    <td>
                        <div style="display:flex;gap:2px;">
                            <?php for($i=1;$i<=5;$i++): ?>
                            <i class="fas fa-star" style="color:<?= $i<=$r['rating']?'#f59e0b':'#d1d5db' ?>;font-size:.85rem;"></i>
                            <?php endfor; ?>
                        </div>
                    </td>
                    <td style="max-width:200px;font-size:.85rem;color:#374151;"><?= clean(substr($r['comment'],0,80)) ?>...</td>
                    <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                    <td><a href="?delete=<?= $r['review_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete review?')"><i class="fas fa-trash"></i></a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
