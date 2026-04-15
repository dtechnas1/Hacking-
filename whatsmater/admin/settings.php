<?php
$pageTitle = 'Admin Settings - WhatsMater';
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(APP_URL . '/index.php');
}

$pendingReports = $conn->query("SELECT COUNT(*) as c FROM reports WHERE status = 'pending'")->fetch_assoc()['c'];

// Stats for overview
$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$totalPosts = $conn->query("SELECT COUNT(*) as c FROM posts")->fetch_assoc()['c'];
$totalMessages = $conn->query("SELECT COUNT(*) as c FROM messages")->fetch_assoc()['c'];
$totalComments = $conn->query("SELECT COUNT(*) as c FROM comments")->fetch_assoc()['c'];
$totalLikes = $conn->query("SELECT COUNT(*) as c FROM likes")->fetch_assoc()['c'];

require_once INCLUDES_PATH . 'header.php';
?>

<div class="admin-page">
    <div class="admin-sidebar">
        <div class="admin-sidebar-header">
            <i class="fas fa-shield-alt"></i>
            <h3>Admin Panel</h3>
        </div>
        <nav class="admin-nav">
            <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="admin-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?php echo APP_URL; ?>/admin/users.php" class="admin-nav-item"><i class="fas fa-users"></i> Users</a>
            <a href="<?php echo APP_URL; ?>/admin/posts.php" class="admin-nav-item"><i class="fas fa-newspaper"></i> Posts</a>
            <a href="<?php echo APP_URL; ?>/admin/reports.php" class="admin-nav-item"><i class="fas fa-flag"></i> Reports <?php if ($pendingReports > 0): ?><span class="admin-badge"><?php echo $pendingReports; ?></span><?php endif; ?></a>
            <a href="<?php echo APP_URL; ?>/admin/settings.php" class="admin-nav-item active"><i class="fas fa-cogs"></i> Settings</a>
            <hr>
            <a href="<?php echo APP_URL; ?>/index.php" class="admin-nav-item"><i class="fas fa-arrow-left"></i> Back to Site</a>
        </nav>
    </div>

    <div class="admin-content">
        <h2>Settings & System Info</h2>

        <!-- Application Info -->
        <div class="card admin-card">
            <h3><i class="fas fa-info-circle"></i> Application Info</h3>
            <div class="settings-info-grid">
                <div class="info-item">
                    <label>Application Name</label>
                    <span><?php echo APP_NAME; ?></span>
                </div>
                <div class="info-item">
                    <label>Version</label>
                    <span><?php echo APP_VERSION; ?></span>
                </div>
                <div class="info-item">
                    <label>PHP Version</label>
                    <span><?php echo phpversion(); ?></span>
                </div>
                <div class="info-item">
                    <label>Server Software</label>
                    <span><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></span>
                </div>
            </div>
        </div>

        <!-- Database Stats -->
        <div class="card admin-card">
            <h3><i class="fas fa-database"></i> Database Statistics</h3>
            <div class="settings-info-grid">
                <div class="info-item">
                    <label>Total Users</label>
                    <span class="text-primary"><?php echo number_format($totalUsers); ?></span>
                </div>
                <div class="info-item">
                    <label>Total Posts</label>
                    <span class="text-primary"><?php echo number_format($totalPosts); ?></span>
                </div>
                <div class="info-item">
                    <label>Total Messages</label>
                    <span class="text-primary"><?php echo number_format($totalMessages); ?></span>
                </div>
                <div class="info-item">
                    <label>Total Comments</label>
                    <span class="text-primary"><?php echo number_format($totalComments); ?></span>
                </div>
                <div class="info-item">
                    <label>Total Likes</label>
                    <span class="text-primary"><?php echo number_format($totalLikes); ?></span>
                </div>
                <div class="info-item">
                    <label>Pending Reports</label>
                    <span class="<?php echo $pendingReports > 0 ? 'text-danger' : 'text-success'; ?>"><?php echo $pendingReports; ?></span>
                </div>
            </div>
        </div>

        <!-- Configuration Reference -->
        <div class="card admin-card">
            <h3><i class="fas fa-cog"></i> Configuration Reference</h3>
            <p class="text-muted">Edit <code>config/database.php</code> and <code>config/app.php</code> to change application settings.</p>
            <div class="settings-info-grid">
                <div class="info-item">
                    <label>Database Host</label>
                    <span><code><?php echo DB_HOST; ?></code></span>
                </div>
                <div class="info-item">
                    <label>Database Name</label>
                    <span><code><?php echo DB_NAME; ?></code></span>
                </div>
                <div class="info-item">
                    <label>Max Upload Size</label>
                    <span><?php echo (MAX_FILE_SIZE / 1024 / 1024) . ' MB'; ?></span>
                </div>
                <div class="info-item">
                    <label>Posts Per Page</label>
                    <span><?php echo POSTS_PER_PAGE; ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card admin-card">
            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            <div class="quick-actions">
                <a href="<?php echo APP_URL; ?>/admin/users.php" class="btn btn-primary btn-sm"><i class="fas fa-users"></i> Manage Users</a>
                <a href="<?php echo APP_URL; ?>/admin/posts.php" class="btn btn-primary btn-sm"><i class="fas fa-newspaper"></i> Manage Posts</a>
                <a href="<?php echo APP_URL; ?>/admin/reports.php" class="btn btn-primary btn-sm"><i class="fas fa-flag"></i> View Reports</a>
                <a href="<?php echo APP_URL; ?>/index.php" class="btn btn-outline btn-sm"><i class="fas fa-home"></i> Go to Site</a>
            </div>
        </div>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
