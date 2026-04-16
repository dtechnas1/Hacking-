<?php
$pageTitle = 'Admin Dashboard - Cultural Heritage';
require_once __DIR__ . '/../config/app.php';

if (!isAdminLoggedIn()) {
    redirect(APP_URL . '/admin/login.php');
}

// Get counts for dashboard
$galleryCount = $conn->query("SELECT COUNT(*) as count FROM gallery_items")->fetch_assoc()['count'];
$videoCount = $conn->query("SELECT COUNT(*) as count FROM videos")->fetch_assoc()['count'];
$ethicsCount = $conn->query("SELECT COUNT(*) as count FROM ethics_content")->fetch_assoc()['count'];
$messageCount = $conn->query("SELECT COUNT(*) as count FROM contact_messages")->fetch_assoc()['count'];
$unreadCount = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")->fetch_assoc()['count'];

// Get recent messages
$recentMessages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body class="admin-body">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <i class="fas fa-landmark"></i>
            <h2><?php echo APP_NAME; ?></h2>
            <span>Admin Panel</span>
        </div>
        <nav class="admin-nav">
            <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="admin-nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?php echo APP_URL; ?>/admin/gallery.php" class="admin-nav-link"><i class="fas fa-images"></i> Gallery</a>
            <a href="<?php echo APP_URL; ?>/admin/videos.php" class="admin-nav-link"><i class="fas fa-video"></i> Videos</a>
            <a href="<?php echo APP_URL; ?>/admin/ethics.php" class="admin-nav-link"><i class="fas fa-scroll"></i> Ethics Content</a>
            <a href="<?php echo APP_URL; ?>/admin/messages.php" class="admin-nav-link"><i class="fas fa-envelope"></i> Messages <?php if ($unreadCount > 0): ?><span class="badge"><?php echo $unreadCount; ?></span><?php endif; ?></a>
            <hr>
            <a href="<?php echo APP_URL; ?>/index.php" class="admin-nav-link"><i class="fas fa-globe"></i> View Website</a>
            <a href="<?php echo APP_URL; ?>/admin/logout.php" class="admin-nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <!-- Admin Main Content -->
    <div class="admin-main">
        <header class="admin-header">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <div class="admin-user">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            </div>
        </header>

        <div class="admin-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-card-primary">
                    <div class="stat-card-icon"><i class="fas fa-images"></i></div>
                    <div class="stat-card-info">
                        <h3><?php echo $galleryCount; ?></h3>
                        <p>Gallery Items</p>
                    </div>
                </div>
                <div class="stat-card stat-card-success">
                    <div class="stat-card-icon"><i class="fas fa-video"></i></div>
                    <div class="stat-card-info">
                        <h3><?php echo $videoCount; ?></h3>
                        <p>Videos</p>
                    </div>
                </div>
                <div class="stat-card stat-card-warning">
                    <div class="stat-card-icon"><i class="fas fa-scroll"></i></div>
                    <div class="stat-card-info">
                        <h3><?php echo $ethicsCount; ?></h3>
                        <p>Ethics Articles</p>
                    </div>
                </div>
                <div class="stat-card stat-card-danger">
                    <div class="stat-card-icon"><i class="fas fa-envelope"></i></div>
                    <div class="stat-card-info">
                        <h3><?php echo $messageCount; ?></h3>
                        <p>Messages (<?php echo $unreadCount; ?> unread)</p>
                    </div>
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2><i class="fas fa-envelope"></i> Recent Messages</h2>
                    <a href="<?php echo APP_URL; ?>/admin/messages.php" class="btn btn-sm btn-outline">View All</a>
                </div>
                <div class="admin-card-body">
                    <?php if (empty($recentMessages)): ?>
                    <p class="text-muted">No messages yet.</p>
                    <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentMessages as $msg): ?>
                            <tr class="<?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                <td><?php echo htmlspecialchars($msg['subject'] ?: 'No subject'); ?></td>
                                <td><?php echo date('M j, Y', strtotime($msg['created_at'])); ?></td>
                                <td><span class="status-badge <?php echo $msg['is_read'] ? 'status-read' : 'status-unread'; ?>"><?php echo $msg['is_read'] ? 'Read' : 'Unread'; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
</body>
</html>
