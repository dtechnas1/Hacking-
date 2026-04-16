<?php
/**
 * Cultural Heritage Website - Application Configuration
 */

// Application settings
define('APP_NAME', 'Cultural Heritage');
define('APP_URL', 'http://localhost/culture-website');

// Directory paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');

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

/**
 * Get gallery items from the database.
 *
 * @param mysqli $conn     Database connection
 * @param string|null $category  Filter by category_label (events, traditional_dress, activities)
 * @param int|null $limit  Maximum number of results
 * @return array
 */
function getGalleryItems($conn, $category = null, $limit = null) {
    $sql = "SELECT g.*, c.name AS category_name FROM gallery_items g
            LEFT JOIN categories c ON g.category_id = c.id";
    $params = [];
    $types = '';

    if ($category) {
        $sql .= " WHERE g.category_label = ?";
        $params[] = $category;
        $types .= 's';
    }

    $sql .= " ORDER BY g.created_at DESC";

    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
        $types .= 'i';
    }

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    return $items;
}

/**
 * Get videos from the database.
 *
 * @param mysqli $conn     Database connection
 * @param string|null $category  Filter by category_label (dance, interviews, programs)
 * @param int|null $limit  Maximum number of results
 * @return array
 */
function getVideos($conn, $category = null, $limit = null) {
    $sql = "SELECT v.*, c.name AS category_name FROM videos v
            LEFT JOIN categories c ON v.category_id = c.id";
    $params = [];
    $types = '';

    if ($category) {
        $sql .= " WHERE v.category_label = ?";
        $params[] = $category;
        $types .= 's';
    }

    $sql .= " ORDER BY v.created_at DESC";

    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
        $types .= 'i';
    }

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    return $items;
}

/**
 * Get ethics content from the database.
 *
 * @param mysqli $conn        Database connection
 * @param string|null $section  Filter by section (traditions, moral_teachings, history)
 * @return array
 */
function getEthicsContent($conn, $section = null) {
    $sql = "SELECT * FROM ethics_content";
    $params = [];
    $types = '';

    if ($section) {
        $sql .= " WHERE section = ?";
        $params[] = $section;
        $types .= 's';
    }

    $sql .= " ORDER BY sort_order ASC";

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    return $items;
}
