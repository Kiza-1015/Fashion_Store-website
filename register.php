<?php
$page_title = 'Register - LuxeMode';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
if (is_logged_in()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = clean($_POST['fullname'] ?? '');
    $email    = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if (!$fullname || !$email || !$password) { $error = 'All fields are required.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Invalid email.'; }
    elseif (strlen($password) < 6) { $error = 'Password must be at least 6 characters.'; }
    elseif ($password !== $confirm) { $error = 'Passwords do not match.'; }
    else {
        $chk = $conn->prepare("SELECT customer_id FROM customers WHERE email=?");
        $chk->bind_param('s', $email);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) { $error = 'Email already registered.'; }
        else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $conn->prepare("INSERT INTO customers (fullname, email, password) VALUES (?,?,?)");
            $ins->bind_param('sss', $fullname, $email, $hash);
            $ins->execute();
            header('Location: login.php?registered=1'); exit;
        }
    }
}
require_once 'includes/header.php';
?>
<div class="page-wrapper">
    <div class="form-card">
        <h2>Join LuxeMode</h2>
        <p>Create your account and start shopping</p>
        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
        <form method="POST" id="registerForm">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" placeholder="Jane Doe" value="<?= clean($_POST['fullname'] ?? '') ?>">
                <div class="field-error" data-err="fullname"></div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="your@email.com" value="<?= clean($_POST['email'] ?? '') ?>">
                <div class="field-error" data-err="email"></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min. 6 chars">
                    <div class="field-error" data-err="password"></div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm" placeholder="Repeat">
                    <div class="field-error" data-err="confirm"></div>
                </div>
            </div>
            <button type="submit" class="btn btn-dark btn-block" style="margin-top:10px;padding:14px;">Create Account</button>
        </form>
        <p style="text-align:center;margin-top:20px;font-size:.875rem;color:#6b7280;">
            Already a member? <a href="login.php" style="color:#1a1a1a;font-weight:700;">Sign in</a>
        </p>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>
