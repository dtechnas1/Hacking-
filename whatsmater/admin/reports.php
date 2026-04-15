<?php
$pageTitle = 'Reports - WhatsMater';
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(APP_URL . '/index.php');
}

$adminId = getCurrentUserId();

// Handle report actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $reportId = (int)$_GET['id'];
    $action = $_GET['action'];

    switch ($action) {
        case 'resolve':
            $stmt = $conn->prepare("UPDATE reports SET status = 'resolved', admin_note = 'Resolved by admin' WHERE id = ?");
            $stmt->bind_param("i", $reportId);
            $stmt->execute();
            $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Resolved report', ?)");
            $detail = "Report ID: $reportId";
            $logStmt->bind_param("is", $adminId, $detail);
            $logStmt->execute();
            break;
        case 'dismiss':
            $stmt = $conn->prepare("UPDATE reports SET status = 'dismissed', admin_note = 'Dismissed by admin' WHERE id = ?");
            $stmt->bind_param("i", $reportId);
            $stmt->execute();
            $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Dismissed report', ?)");
            $detail = "Report ID: $reportId";
            $logStmt->bind_param("is", $adminId, $detail);
            $logStmt->execute();
            break;
    }
    redirect(APP_URL . '/admin/reports.php');
}

$filter = sanitize($_GET['filter'] ?? 'pending');
$where = "";
if ($filter === 'pending') $where = "WHERE r.status = 'pending'";
elseif ($filter === 'resolved') $where = "WHERE r.status = 'resolved'";
elseif ($filter === 'dismissed') $where = "WHERE r.status = 'dismissed'";

$reports = $conn->query("SELECT r.*, 
    reporter.full_name as reporter_name, reporter.profile_pic as reporter_pic,
    reported_user.full_name as reported_user_name,
    p.content as post_content
    FROM reports r
    JOIN users reporter ON r.reporter_id = reporter.id
    LEFT JOIN users reported_user ON r.reported_user_id = reported_user.id
    LEFT JOIN posts p ON r.reported_post_id = p.id
    $where ORDER BY r.created_at DESC LIMIT 50");

$pendingReports = $conn->query("SELECT COUNT(*) as c FROM reports WHERE status = 'pending'")->fetch_assoc()['c'];

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
            <a href="<?php echo APP_URL; ?>/admin/reports.php" class="admin-nav-item active"><i class="fas fa-flag"></i> Reports <?php if ($pendingReports > 0): ?><span class="admin-badge"><?php echo $pendingReports; ?></span><?php endif; ?></a>
            <a href="<?php echo APP_URL; ?>/admin/settings.php" class="admin-nav-item"><i class="fas fa-cogs"></i> Settings</a>
            <hr>
            <a href="<?php echo APP_URL; ?>/index.php" class="admin-nav-item"><i class="fas fa-arrow-left"></i> Back to Site</a>
        </nav>
    </div>

    <div class="admin-content">
        <h2>Reports</h2>

        <div class="card admin-card">
            <div class="admin-filters">
                <a href="?filter=pending" class="btn <?php echo $filter === 'pending' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Pending</a>
                <a href="?filter=resolved" class="btn <?php echo $filter === 'resolved' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Resolved</a>
                <a href="?filter=dismissed" class="btn <?php echo $filter === 'dismissed' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Dismissed</a>
                <a href="?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">All</a>
            </div>
        </div>

        <div class="card admin-card">
            <?php if ($reports->num_rows === 0): ?>
            <div class="empty-state" style="padding:40px;">
                <i class="fas fa-check-circle" style="font-size:48px;color:#42b72a;"></i>
                <h3>No reports</h3>
                <p>All clear!</p>
            </div>
            <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reporter</th>
                        <th>Reported User</th>
                        <th>Post</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $reports->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $r['id']; ?></td>
                        <td>
                            <div class="table-user">
                                <img src="<?php echo getProfilePic($r['reporter_pic']); ?>" alt="" class="avatar-xs">
                                <span><?php echo htmlspecialchars($r['reporter_name']); ?></span>
                            </div>
                        </td>
                        <td><?php echo $r['reported_user_name'] ? htmlspecialchars($r['reported_user_name']) : '-'; ?></td>
                        <td><?php echo $r['post_content'] ? htmlspecialchars(substr($r['post_content'], 0, 40)) . '...' : '-'; ?></td>
                        <td><?php echo htmlspecialchars(substr($r['reason'], 0, 60)); ?></td>
                        <td><span class="status-badge status-<?php echo $r['status']; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                        <td><?php echo timeAgo($r['created_at']); ?></td>
                        <td>
                            <?php if ($r['status'] === 'pending'): ?>
                            <div class="action-btns">
                                <a href="?action=resolve&id=<?php echo $r['id']; ?>" class="btn-icon text-success" title="Resolve"><i class="fas fa-check"></i></a>
                                <a href="?action=dismiss&id=<?php echo $r['id']; ?>" class="btn-icon text-warning" title="Dismiss"><i class="fas fa-times"></i></a>
                            </div>
                            <?php else: ?>
                            <span class="text-muted"><?php echo ucfirst($r['status']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
