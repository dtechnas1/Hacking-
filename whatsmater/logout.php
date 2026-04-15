<?php
require_once __DIR__ . '/config/app.php';

if (isLoggedIn()) {
    // Update online status
    $stmt = $conn->prepare("UPDATE users SET is_online = 0, last_seen = NOW() WHERE id = ?");
    $stmt->bind_param("i", getCurrentUserId());
    $stmt->execute();
}

// Destroy session
session_unset();
session_destroy();

redirect(APP_URL . '/login.php');
