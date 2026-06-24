<?php
$page_title = 'Add Product - LuxeMode Admin';
require_once '../includes/header.php';
require_admin();
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
    $image      = '';

    if (!$title || $price <= 0 || !$category) { $error = 'Title, price, and category are required.'; }
    else {
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) { $error = 'Image must be JPG/PNG/WEBP.'; }
            else { $image = uniqid('fashion_').".$ext"; move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/$image"); }
        }
        if (!$error) {
            $stmt = $conn->prepare("INSERT INTO products (title, description, price, sale_price, image, size, color, category, stock) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('ssddssssi', $title, $desc, $price, $sale_price, $image, $size, $color, $category, $stock);
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
            <h2>Add New Product</h2>
            <a href="products.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

        <div class="admin-card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Product Title *</label>
                        <input type="text" name="title" placeholder="e.g. Classic Oxford Shirt">
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category">
                            <option value="">-- Select --</option>
                            <option value="mens">Men's</option>
                            <option value="womens">Women's</option>
                            <option value="kids">Kids</option>
                            <option value="accessories">Accessories</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="Product details, fabric, care instructions..."></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" name="price" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Sale Price ($) <small style="font-weight:400;text-transform:none;color:#6b7280;">optional</small></label>
                        <input type="number" name="sale_price" step="0.01" min="0" placeholder="Leave blank if no sale">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Available Sizes <small style="font-weight:400;text-transform:none;color:#6b7280;">comma-separated</small></label>
                        <input type="text" name="size" placeholder="S,M,L,XL  or  28,30,32">
                    </div>
                    <div class="form-group">
                        <label>Colors <small style="font-weight:400;text-transform:none;color:#6b7280;">comma-separated</small></label>
                        <input type="text" name="color" placeholder="Black,White,Navy">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock" min="0" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:8px;">
                    <button type="submit" class="btn btn-dark btn-lg"><i class="fas fa-save"></i> Save Product</button>
                    <a href="products.php" class="btn btn-outline btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>
