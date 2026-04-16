<?php
$pageTitle = 'Admin Dashboard - WhatsMater';
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(APP_URL . '/index.php');
}

$userId = getCurrentUserId();

// Stats
$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$activeUsers = $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'active'")->fetch_assoc()['c'];
$totalPosts = $conn->query("SELECT COUNT(*) as c FROM posts WHERE status = 'active'")->fetch_assoc()['c'];
$totalMessages = $conn->query("SELECT COUNT(*) as c FROM messages")->fetch_assoc()['c'];
$pendingReports = $conn->query("SELECT COUNT(*) as c FROM reports WHERE status = 'pending'")->fetch_assoc()['c'];
$onlineUsers = $conn->query("SELECT COUNT(*) as c FROM users WHERE is_online = 1")->fetch_assoc()['c'];
$newUsersToday = $conn->query("SELECT COUNT(*) as c FROM users WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['c'];
$newPostsToday = $conn->query("SELECT COUNT(*) as c FROM posts WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['c'];

// Recent users
$recentUsers = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

// Recent reports
$recentReports = $conn->query("SELECT r.*, u.full_name as reporter_name FROM reports r JOIN users u ON r.reporter_id = u.id ORDER BY r.created_at DESC LIMIT 5");

// Recent activity log
$recentLogs = $conn->query("SELECT al.*, u.full_name FROM admin_logs al JOIN users u ON al.admin_id = u.id ORDER BY al.created_at DESC LIMIT 10");

require_once INCLUDES_PATH . 'header.php';
?>

<div class="admin-page">
    <div class="admin-sidebar">
        <div class="admin-sidebar-header">
            <i class="fas fa-shield-alt"></i>
            <h3>Admin Panel</h3>
        </div>
        <nav class="admin-nav">
            <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="admin-nav-item active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="<?php echo APP_URL; ?>/admin/users.php" class="admin-nav-item">
                <i class="fas fa-users"></i> Users
            </a>
            <a href="<?php echo APP_URL; ?>/admin/posts.php" class="admin-nav-item">
                <i class="fas fa-newspaper"></i> Posts
            </a>
            <a href="<?php echo APP_URL; ?>/admin/reports.php" class="admin-nav-item">
                <i class="fas fa-flag"></i> Reports
                <?php if ($pendingReports > 0): ?><span class="admin-badge"><?php echo $pendingReports; ?></span><?php endif; ?>
            </a>
            <a href="<?php echo APP_URL; ?>/admin/settings.php" class="admin-nav-item">
                <i class="fas fa-cogs"></i> Settings
            </a>
            <hr>
            <a href="<?php echo APP_URL; ?>/index.php" class="admin-nav-item">
                <i class="fas fa-arrow-left"></i> Back to Site
            </a>
        </nav>
    </div>

    <div class="admin-content">
        <h2>Dashboard</h2>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-blue">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalUsers; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="stat-card stat-green">
                <div class="stat-icon"><i class="fas fa-circle"></i></div>
                <div class="stat-info">
                    <h3><?php echo $onlineUsers; ?></h3>
                    <p>Online Now</p>
                </div>
            </div>
            <div class="stat-card stat-purple">
                <div class="stat-icon"><i class="fas fa-newspaper"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalPosts; ?></h3>
                    <p>Total Posts</p>
                </div>
            </div>
            <div class="stat-card stat-orange">
                <div class="stat-icon"><i class="fas fa-comment-dots"></i></div>
                <div class="stat-info">
                    <h3><?php echo $totalMessages; ?></h3>
                    <p>Messages</p>
                </div>
            </div>
            <div class="stat-card stat-red">
                <div class="stat-icon"><i class="fas fa-flag"></i></div>
                <div class="stat-info">
                    <h3><?php echo $pendingReports; ?></h3>
                    <p>Pending Reports</p>
                </div>
            </div>
            <div class="stat-card stat-teal">
                <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
                <div class="stat-info">
                    <h3><?php echo $newUsersToday; ?></h3>
                    <p>New Today</p>
                </div>
            </div>
        </div>

        <div class="admin-grid">
            <!-- Recent Users -->
            <div class="card admin-card">
                <div class="card-header-flex">
                    <h3>Recent Users</h3>
                    <a href="<?php echo APP_URL; ?>/admin/users.php" class="btn btn-outline btn-sm">View All</a>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr><th>User</th><th>Email</th><th>Status</th><th>Joined</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($u = $recentUsers->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="table-user">
                                    <img src="<?php echo getProfilePic($u['profile_pic']); ?>" alt="" class="avatar-xs">
                                    <span><?php echo htmlspecialchars($u['full_name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><span class="status-badge status-<?php echo $u['status']; ?>"><?php echo ucfirst($u['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Reports -->
            <div class="card admin-card">
                <div class="card-header-flex">
                    <h3>Recent Reports</h3>
                    <a href="<?php echo APP_URL; ?>/admin/reports.php" class="btn btn-outline btn-sm">View All</a>
                </div>
                <?php if ($recentReports->num_rows === 0): ?>
                <p class="text-muted" style="padding: 20px;">No reports yet.</p>
                <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr><th>Reporter</th><th>Reason</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($r = $recentReports->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['reporter_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($r['reason'], 0, 50)); ?>...</td>
                            <td><span class="status-badge status-<?php echo $r['status']; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                            <td><?php echo timeAgo($r['created_at']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Admin Activity Log -->
        <div class="card admin-card">
            <h3>Admin Activity Log</h3>
            <?php if ($recentLogs->num_rows === 0): ?>
            <p class="text-muted" style="padding: 20px;">No activity yet.</p>
            <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr><th>Admin</th><th>Action</th><th>Details</th><th>Time</th></tr>
                </thead>
                <tbody>
                    <?php while ($log = $recentLogs->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                        <td><?php echo htmlspecialchars($log['details'] ?? ''); ?></td>
                        <td><?php echo timeAgo($log['created_at']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
