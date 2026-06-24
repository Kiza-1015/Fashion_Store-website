<?php
$page_title = 'Edit Product - LuxeMode Admin';
require_once '../includes/header.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$p  = $conn->query("SELECT * FROM products WHERE product_id=$id")->fetch_assoc();
if (!$p) { header('Location: products.php'); exit; }
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = clean($_POST['title'] ?? '');
    $desc       = clean($_POST['description'] ?? '');
    $price      = (float)($_POST['price'] ?? 0);
    $sale_price = $_POST['sale_price'] !== '' ? (float)$_POST['sale_price'] : null;
    $category   = clean($_POST['category'] ?? '');
    $size       = clean($_POST['size'] ?? '');
    $color      = clean($_POST['color'] ?? '');
    $stock      = (int)($_POST['stock'] ?? 0);
    $image      = $p['image'];

    if (!$title || $price <= 0) { $error = 'Title and price are required.'; }
    else {
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) { $error = 'Invalid image type.'; }
            else {
                if ($image && file_exists("../uploads/$image")) unlink("../uploads/$image");
                $image = uniqid('fashion_') . ".$ext";
                move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$image");
            }
        }
        if (!$error) {
            $stmt = $conn->prepare("UPDATE products SET title=?,description=?,price=?,sale_price=?,image=?,size=?,color=?,category=?,stock=? WHERE product_id=?");
            $stmt->bind_param('ssddssssi i', $title,$desc,$price,$sale_price,$image,$size,$color,$category,$stock,$id);
            // fix spacing issue
            $stmt = $conn->prepare("UPDATE products SET title=?,description=?,price=?,sale_price=?,image=?,size=?,color=?,category=?,stock=? WHERE product_id=?");
            $stmt->bind_param('ssddssssii', $title,$desc,$price,$sale_price,$image,$size,$color,$category,$stock,$id);
            $stmt->execute();
            header('Location: products.php?saved=1'); exit;
        }
    }
}
?>
<div class="admin-layout">
    <?php include 'sidebar.php'; ?>
    <div class="admin-main">
        <div class="admin-header-bar">
            <h2>Edit Product</h2>
            <a href="products.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
        <div class="admin-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Title *</label>
                        <input type="text" name="title" value="<?= clean($p['title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category">
                            <?php foreach(['mens'=>"Men's",'womens'=>"Women's",'kids'=>'Kids','accessories'=>'Accessories'] as $v=>$l): ?>
                            <option value="<?= $v ?>" <?= $p['category']===$v?'selected':'' ?>><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"><?= clean($p['description']) ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" name="price" step="0.01" value="<?= $p['price'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Sale Price ($)</label>
                        <input type="number" name="sale_price" step="0.01" value="<?= $p['sale_price'] ?? '' ?>" placeholder="Leave blank if no sale">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Sizes</label>
                        <input type="text" name="size" value="<?= clean($p['size']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Colors</label>
                        <input type="text" name="color" value="<?= clean($p['color']) ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" value="<?= $p['stock'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <?php if ($p['image'] && file_exists("../uploads/{$p['image']}")): ?>
                        <img src="../uploads/<?= clean($p['image']) ?>" style="max-height:80px;margin-bottom:8px;display:block;">
                        <?php endif; ?>
                        <input type="file" name="image" accept="image/*">
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:8px;">
                    <button type="submit" class="btn btn-dark btn-lg"><i class="fas fa-save"></i> Update Product</button>
                    <a href="products.php" class="btn btn-outline btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
