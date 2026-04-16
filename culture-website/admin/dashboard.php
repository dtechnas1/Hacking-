<?php
/**
 * Cultural Heritage Website - Admin Dashboard
 */
require_once __DIR__ . '/auth.php';

// Get counts from each table
$galleryCount = 0;
$videoCount = 0;
$ethicsCount = 0;
$messageCount = 0;
$unreadCount = 0;

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM gallery_items");
$stmt->execute();
$galleryCount = $stmt->get_result()->fetch_assoc()['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM videos");
$stmt->execute();
$videoCount = $stmt->get_result()->fetch_assoc()['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM ethics_content");
$stmt->execute();
$ethicsCount = $stmt->get_result()->fetch_assoc()['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM contact_messages");
$stmt->execute();
$messageCount = $stmt->get_result()->fetch_assoc()['count'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0");
$stmt->execute();
$unreadCount = $stmt->get_result()->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Admin Dashboard</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <h1><i class="fas fa-cog"></i> Admin Panel</h1>
        <nav class="admin-nav">
            <a href="dashboard.php" class="<?php echo $adminPage === 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage-gallery.php" class="<?php echo $adminPage === 'manage-gallery.php' ? 'active' : ''; ?>"><i class="fas fa-images"></i> Gallery</a>
            <a href="manage-videos.php" class="<?php echo $adminPage === 'manage-videos.php' ? 'active' : ''; ?>"><i class="fas fa-video"></i> Videos</a>
            <a href="manage-ethics.php" class="<?php echo $adminPage === 'manage-ethics.php' ? 'active' : ''; ?>"><i class="fas fa-book-open"></i> Ethics</a>
            <a href="manage-messages.php" class="<?php echo $adminPage === 'manage-messages.php' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> Messages</a>
            <a href="?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="admin-container">
        <h2>Dashboard Overview</h2>

        <div class="stat-cards">
            <div class="stat-card">
                <h3><?php echo $galleryCount; ?></h3>
                <p><i class="fas fa-images"></i> Gallery Items</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $videoCount; ?></h3>
                <p><i class="fas fa-video"></i> Videos</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $ethicsCount; ?></h3>
                <p><i class="fas fa-book-open"></i> Ethics Content</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $messageCount; ?></h3>
                <p><i class="fas fa-envelope"></i> Messages</p>
            </div>

            <div class="stat-card">
                <h3><?php echo $unreadCount; ?></h3>
                <p><i class="fas fa-bell"></i> Unread Messages</p>
            </div>
        </div>

        <h3>Quick Actions</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 15px;">
            <a href="manage-gallery.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Gallery Item</a>
            <a href="manage-videos.php" class="btn btn-secondary"><i class="fas fa-plus"></i> Add Video</a>
            <a href="manage-ethics.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Ethics Content</a>
            <a href="manage-messages.php" class="btn btn-secondary"><i class="fas fa-inbox"></i> View Messages</a>
            <a href="<?php echo APP_URL; ?>/index.php" class="btn btn-outline" style="border-color: var(--primary); color: var(--primary);"><i class="fas fa-external-link-alt"></i> View Website</a>
        </div>
    </div>
</body>
</html>
