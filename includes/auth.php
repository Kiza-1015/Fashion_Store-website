<?php
function is_logged_in() { return isset($_SESSION['customer_id']); }
function is_admin()     { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }

function require_login() {
    if (!is_logged_in()) {
        header('Location: /fashion-store/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) { header('Location: /fashion-store/index.php'); exit; }
}

function clean($v) { return htmlspecialchars(strip_tags(trim($v))); }

function render_stars($rating) {
    $out = '';
    for ($i = 1; $i <= 5; $i++) {
        $out .= '<i class="fas fa-star' . ($i <= $rating ? '' : '-o') . '" style="color:' . ($i <= $rating ? '#f59e0b' : '#d1d5db') . ';"></i>';
    }
    return $out;
}
