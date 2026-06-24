<?php
$page_title = 'Contact Us - LuxeMode';
require_once 'includes/header.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = clean($_POST['name'] ?? '');
    $email   = clean($_POST['email'] ?? '');
    $subject = clean($_POST['subject'] ?? '');
    $message = clean($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        $error = 'Name, email, and message are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // In a real app, send email or store in DB
        $success = 'Thank you for reaching out! We\'ll get back to you within 24 hours.';
    }
}
?>
<div class="page-banner"><div class="container"><h1>Contact Us</h1><p>We'd love to hear from you</p></div></div>

<section style="padding:60px 0;">
    <div class="container">
        <?php if ($success): ?><div class="alert alert-success" style="margin-bottom:24px;"><?= $success ?></div><?php endif; ?>

        <div class="contact-grid">
            <div class="contact-info-col">
                <h2>Get in Touch</h2>
                <p>Have a question about sizing, an order, or our collections? Our style experts are here to help.</p>
                <div class="contact-item">
                    <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div><h4>Store Address</h4><p>45 Fashion Avenue, Colombo 07, Sri Lanka</p></div>
                </div>
                <div class="contact-item">
                    <div class="icon"><i class="fas fa-phone"></i></div>
                    <div><h4>Phone</h4><p>+94 11 456 7890</p><p>Mon-Sat, 10am–7pm</p></div>
                </div>
                <div class="contact-item">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <div><h4>Email</h4><p>hello@luxemode.lk</p></div>
                </div>
                <div class="contact-item">
                    <div class="icon"><i class="fab fa-instagram"></i></div>
                    <div><h4>Instagram</h4><p>@luxemode_official</p></div>
                </div>
            </div>

            <div style="background:#fff;padding:40px;border:1px solid #e5e7eb;">
                <h3 style="font-family:'Playfair Display',serif;font-size:1.4rem;margin-bottom:24px;">Send a Message</h3>
                <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" name="name" placeholder="Jane Doe" value="<?= clean($_POST['name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" placeholder="your@email.com" value="<?= clean($_POST['email'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" placeholder="Order inquiry, Sizing help, etc." value="<?= clean($_POST['subject'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" rows="5" placeholder="Tell us how we can help..."><?= clean($_POST['message'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark btn-lg" style="width:100%;justify-content:center;">
                        Send Message <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
