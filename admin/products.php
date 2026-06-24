<?php
$page_title = 'Products - LuxeMode Admin';
require_once '../includes/header.php';
require_admin();

if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM products WHERE product_id=$did");
    header('Location: products.php?deleted=1'); exit;
}

$cat = clean($_GET['cat'] ?? '');
$where = $cat && in_array($cat, ['mens','womens','kids','accessories']) ? "WHERE category='$cat'" : '';
$products = $conn->query("SELECT * FROM products $where ORDER BY product_id DESC");
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header-bar">
            <h2>Products</h2>
            <a href="add-product.php" class="btn btn-dark btn-sm"><i class="fas fa-plus"></i> Add Product</a>
        </div>
        <?php if (!empty($_GET['deleted'])): ?><div class="alert alert-success">Product deleted.</div><?php endif; ?>
        <?php if (!empty($_GET['saved'])): ?><div class="alert alert-success">Product saved.</div><?php endif; ?>

        <div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;">
            <?php foreach ([''=>'All','mens'=>"Men's",'womens'=>"Women's",'accessories'=>'Accessories'] as $val=>$lbl): ?>
            <a href="?cat=<?= $val ?>" class="btn <?= $cat===$val?'btn-dark':'btn-outline' ?> btn-sm"><?= $lbl ?></a>
            <?php endforeach; ?>
        </div>

        <table class="admin-table">
            <thead><tr><th>ID</th><th>Image</th><th>Title</th><th>Category</th><th>Price</th><th>Sale</th><th>Stock</th><th>Actions</th></tr></thead>
            <tbody>
                <?php while ($p = $products->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['product_id'] ?></td>
                    <td>
                        <?php if ($p['image'] && file_exists("../uploads/{$p['image']}")): ?>
                        <img src="../uploads/<?= clean($p['image']) ?>" style="width:48px;height:60px;object-fit:cover;">
                        <?php else: ?>
                        <div style="width:48px;height:60px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;"><i class="fas fa-tshirt" style="color:#d1d5db;"></i></div>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:600;max-width:200px;"><?= clean($p['title']) ?></td>
                    <td><?= ucfirst($p['category']) ?></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td><?= $p['sale_price'] ? '$'.number_format($p['sale_price'],2) : '<span style="color:#9ca3af;">—</span>' ?></td>
                    <td>
                        <span style="font-size:.75rem;font-weight:700;padding:2px 10px;background:<?= $p['stock']==0?'#fee2e2':($p['stock']<=5?'#fef3c7':'#d1fae5') ?>;color:<?= $p['stock']==0?'#991b1b':($p['stock']<=5?'#92400e':'#065f46') ?>;">
                            <?= $p['stock'] ?>
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="edit-product.php?id=<?= $p['product_id'] ?>" class="btn btn-dark btn-sm"><i class="fas fa-edit"></i></a>
                            <a href="products.php?delete=<?= $p['product_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
