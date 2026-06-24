<?php
$page_title = 'Login - LuxeMode';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM customers WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['customer_id'] = $user['customer_id'];
        $_SESSION['fullname']    = $user['fullname'];
        $_SESSION['email']       = $user['email'];
        $_SESSION['role']        = $user['role'];
        $redirect = $_GET['redirect'] ?? ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php');
        header('Location: ' . $redirect); exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
require_once 'includes/header.php';
?>
<div class="page-wrapper">
    <div class="form-card">
        <h2>Welcome Back</h2>
        <p>Sign in to your LuxeMode account</p>
        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
        <?php if (!empty($_GET['registered'])): ?><div class="alert alert-success">Account created! Please log in.</div><?php endif; ?>
        <form method="POST" id="loginForm">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="your@email.com" value="<?= clean($_POST['email'] ?? '') ?>">
                <div class="field-error" data-err="email"></div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Your password">
                <div class="field-error" data-err="password"></div>
            </div>
            <button type="submit" class="btn btn-dark btn-block" style="margin-top:10px;padding:14px;">Sign In</button>
        </form>
        <p style="text-align:center;margin-top:20px;font-size:.875rem;color:#6b7280;">
            New to LuxeMode? <a href="register.php" style="color:#1a1a1a;font-weight:700;">Create account</a>
        </p>
        <div style="margin-top:14px;padding:12px;background:#f9f9f9;text-align:center;font-size:.8rem;color:#6b7280;">
            <strong>Demo Admin:</strong> admin@fashionstore.com / password
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
