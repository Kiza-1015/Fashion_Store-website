<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'fashion_store');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="background:#fee;color:#c00;padding:20px;font-family:sans-serif;">
        <strong>Database Connection Failed:</strong> ' . htmlspecialchars($conn->connect_error) . '
        <br><small>Check your database settings in includes/db.php</small>
    </div>');
}

$conn->set_charset('utf8mb4');
