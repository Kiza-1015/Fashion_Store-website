<?php
$page_title = 'My Profile - LuxeMode';
require_once 'includes/header.php';
require_login();

$cid      = $_SESSION['customer_id'];
$customer = $conn->query("SELECT * FROM customers WHERE customer_id=$cid")->fetch_assoc();
$success  = '';
$error    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = clean($_POST['fullname'] ?? '');
    $phone    = clean($_POST['phone'] ?? '');
    $address  = clean($_POST['address'] ?? '');

    if (!$fullname) { $error = 'Name is required.'; }
    else {
        // Password change
        if (!empty($_POST['new_password'])) {
            if (!password_verify($_POST['current_password'] ?? '', $customer['password'])) {
                $error = 'Current password is incorrect.';
            } elseif (strlen($_POST['new_password']) < 6) {
                $error = 'New password must be at least 6 characters.';
            } else {
                $hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $conn->prepare("UPDATE customers SET fullname=?,phone=?,address=?,password=? WHERE customer_id=?")->bind_param('ssssi', $fullname, $phone, $address, $hash, $cid) && $conn->prepare("UPDATE customers SET fullname=?,phone=?,address=?,password=? WHERE customer_id=?")->execute();
            }
        }
        if (!$error) {
            $stmt = $conn->prepare("UPDATE customers SET fullname=?, phone=?, address=? WHERE customer_id=?");
            $stmt->bind_param('sssi', $fullname, $phone, $address, $cid);
            $stmt->execute();
            $_SESSION['fullname'] = $fullname;
            $customer['fullname'] = $fullname;
            $customer['phone']    = $phone;
            $customer['address']  = $address;
            $success = 'Profile updated successfully.';
        }
    }
}
$order_count = $conn->query("SELECT COUNT(*) FROM orders WHERE customer_id=$cid")->fetch_row()[0];
$wish_count  = $conn->query("SELECT COUNT(*) FROM wishlist WHERE customer_id=$cid")->fetch_row()[0];
?>
<div class="page-banner"><div class="container"><h1>My Account</h1></div></div>

<section class="profile-page">
    <div class="container">
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>

        <div class="profile-layout">
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <div class="avatar"><?= strtoupper(substr($customer['fullname'], 0, 1)) ?></div>
                    <h3><?= clean($customer['fullname']) ?></h3>
                    <p style="font-size:.8rem;color:#6b7280;margin-top:4px;"><?= clean($customer['email']) ?></p>
                </div>
                <nav class="profile-nav">
                    <a href="profile.php" class="active"><i class="fas fa-user fa-fw"></i> Profile Settings</a>
                    <a href="orders.php"><i class="fas fa-box fa-fw"></i> My Orders <span style="float:right;background:#e5e7eb;padding:1px 8px;border-radius:10px;font-size:.75rem;"><?= $order_count ?></span></a>
                    <a href="wishlist.php"><i class="fas fa-heart fa-fw"></i> Wishlist <span style="float:right;background:#e5e7eb;padding:1px 8px;border-radius:10px;font-size:.75rem;"><?= $wish_count ?></span></a>
                    <a href="logout.php" style="color:#dc2626;"><i class="fas fa-sign-out-alt fa-fw"></i> Logout</a>
                </nav>
            </div>

            <div class="profile-content">
                <h2>Profile Information</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="fullname" value="<?= clean($customer['fullname']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Email (read-only)</label>
                            <input type="email" value="<?= clean($customer['email']) ?>" disabled style="background:#f9f9f9;color:#6b7280;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" placeholder="+94 77 123 4567" value="<?= clean($customer['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Default Shipping Address</label>
                        <textarea name="address" rows="3" placeholder="Street, City, Postal Code"><?= clean($customer['address'] ?? '') ?></textarea>
                    </div>

                    <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;margin:24px 0 16px;padding-top:20px;border-top:1px solid #e5e7eb;">Change Password</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" placeholder="Leave blank to keep current">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" placeholder="Min. 6 characters">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-dark">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
