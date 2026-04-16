<?php
/**
 * Cultural Heritage Website - Application Configuration
 */

define('APP_NAME', 'Cultural Heritage');
define('APP_URL', 'http://localhost/culture-website');
define('APP_VERSION', '1.0.0');

define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');

define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ITEMS_PER_PAGE', 12);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

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

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Get gallery items
function getGalleryItems($category = null, $limit = null, $featuredOnly = false) {
    global $conn;
    $sql = "SELECT g.*, c.name as category_name FROM gallery_items g LEFT JOIN categories c ON g.category_id = c.id WHERE 1=1";
    $params = [];
    $types = "";

    if ($category) {
        $sql .= " AND g.category_label = ?";
        $params[] = $category;
        $types .= "s";
    }
    if ($featuredOnly) {
        $sql .= " AND g.is_featured = 1";
    }
    $sql .= " ORDER BY g.created_at DESC";
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
        $types .= "i";
    }

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get videos
function getVideos($category = null, $limit = null) {
    global $conn;
    $sql = "SELECT v.*, c.name as category_name FROM videos v LEFT JOIN categories c ON v.category_id = c.id WHERE 1=1";
    $params = [];
    $types = "";

    if ($category) {
        $sql .= " AND v.category_label = ?";
        $params[] = $category;
        $types .= "s";
    }
    $sql .= " ORDER BY v.created_at DESC";
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
        $types .= "i";
    }

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get ethics content
function getEthicsContent($section = null) {
    global $conn;
    $sql = "SELECT * FROM ethics_content WHERE 1=1";
    $params = [];
    $types = "";

    if ($section) {
        $sql .= " AND section = ?";
        $params[] = $section;
        $types .= "s";
    }
    $sql .= " ORDER BY sort_order ASC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
