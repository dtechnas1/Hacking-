<?php
/**
 * WhatsMater - Application Configuration
 */

// Application settings
define('APP_NAME', 'WhatsMater');
define('APP_URL', 'http://localhost/whatsmater');
define('APP_VERSION', '1.0.0');

// Directory paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');

// Upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Pagination
define('POSTS_PER_PAGE', 10);
define('USERS_PER_PAGE', 20);
define('MESSAGES_PER_PAGE', 50);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database
require_once __DIR__ . '/database.php';

/**
 * Helper Functions
 */

// Sanitize input
function sanitize($data) {
    global $conn;
    return htmlspecialchars(mysqli_real_escape_string($conn, trim($data)));
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user data
function getCurrentUser() {
    global $conn;
    if (!isLoggedIn()) return null;
    $id = getCurrentUserId();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Format time ago
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

// Get user profile picture URL
function getProfilePic($pic) {
    if ($pic && $pic !== 'default.png' && file_exists(UPLOADS_PATH . 'profiles/' . $pic)) {
        return APP_URL . '/uploads/profiles/' . $pic;
    }
    return APP_URL . '/assets/images/default-avatar.svg';
}

// Get unread notification count
function getUnreadNotificationCount($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'];
}

// Get unread message count
function getUnreadMessageCount($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'];
}

// Get friend request count
function getFriendRequestCount($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM friendships WHERE receiver_id = ? AND status = 'pending'");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'];
}
