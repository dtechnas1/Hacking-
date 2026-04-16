<?php
$pageTitle = 'Manage Posts - WhatsMater';
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(APP_URL . '/index.php');
}

$adminId = getCurrentUserId();

// Handle post actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $postId = (int)$_GET['id'];
    $action = $_GET['action'];

    switch ($action) {
        case 'hide':
            $stmt = $conn->prepare("UPDATE posts SET status = 'hidden' WHERE id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Hid post', ?)");
            $detail = "Post ID: $postId";
            $logStmt->bind_param("is", $adminId, $detail);
            $logStmt->execute();
            break;
        case 'restore':
            $stmt = $conn->prepare("UPDATE posts SET status = 'active' WHERE id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Restored post', ?)");
            $detail = "Post ID: $postId";
            $logStmt->bind_param("is", $adminId, $detail);
            $logStmt->execute();
            break;
        case 'delete':
            $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Deleted post', ?)");
            $detail = "Post ID: $postId";
            $logStmt->bind_param("is", $adminId, $detail);
            $logStmt->execute();
            break;
    }
    redirect(APP_URL . '/admin/posts.php');
}

// Get posts
$filter = sanitize($_GET['filter'] ?? 'all');
$where = "";
if ($filter === 'active') $where = "WHERE p.status = 'active'";
elseif ($filter === 'hidden') $where = "WHERE p.status = 'hidden'";

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$posts = $conn->query("SELECT p.*, u.full_name, u.username, u.profile_pic,
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
    (SELECT COUNT(*) FROM reports WHERE reported_post_id = p.id AND status = 'pending') as report_count
    FROM posts p JOIN users u ON p.user_id = u.id $where
    ORDER BY p.created_at DESC LIMIT $perPage OFFSET $offset");

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
            <a href="<?php echo APP_URL; ?>/admin/posts.php" class="admin-nav-item active"><i class="fas fa-newspaper"></i> Posts</a>
            <a href="<?php echo APP_URL; ?>/admin/reports.php" class="admin-nav-item"><i class="fas fa-flag"></i> Reports <?php if ($pendingReports > 0): ?><span class="admin-badge"><?php echo $pendingReports; ?></span><?php endif; ?></a>
            <a href="<?php echo APP_URL; ?>/admin/settings.php" class="admin-nav-item"><i class="fas fa-cogs"></i> Settings</a>
            <hr>
            <a href="<?php echo APP_URL; ?>/index.php" class="admin-nav-item"><i class="fas fa-arrow-left"></i> Back to Site</a>
        </nav>
    </div>

    <div class="admin-content">
        <h2>Manage Posts</h2>

        <div class="card admin-card">
            <div class="admin-filters">
                <a href="?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">All</a>
                <a href="?filter=active" class="btn <?php echo $filter === 'active' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Active</a>
                <a href="?filter=hidden" class="btn <?php echo $filter === 'hidden' ? 'btn-primary' : 'btn-outline'; ?> btn-sm">Hidden</a>
            </div>
        </div>

        <div class="card admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Author</th>
                        <th>Content</th>
                        <th>Likes</th>
                        <th>Comments</th>
                        <th>Reports</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($p = $posts->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td>
                            <div class="table-user">
                                <img src="<?php echo getProfilePic($p['profile_pic']); ?>" alt="" class="avatar-xs">
                                <span><?php echo htmlspecialchars($p['full_name']); ?></span>
                            </div>
                        </td>
                        <td class="post-content-cell"><?php echo htmlspecialchars(substr($p['content'] ?? '', 0, 80)); ?><?php echo strlen($p['content'] ?? '') > 80 ? '...' : ''; ?></td>
                        <td><?php echo $p['like_count']; ?></td>
                        <td><?php echo $p['comment_count']; ?></td>
                        <td><?php echo $p['report_count'] > 0 ? '<span class="text-danger">' . $p['report_count'] . '</span>' : '0'; ?></td>
                        <td><span class="status-badge status-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                        <td><?php echo timeAgo($p['created_at']); ?></td>
                        <td>
                            <div class="action-btns">
                                <?php if ($p['status'] === 'active'): ?>
                                <a href="?action=hide&id=<?php echo $p['id']; ?>" class="btn-icon text-warning" title="Hide"><i class="fas fa-eye-slash"></i></a>
                                <?php else: ?>
                                <a href="?action=restore&id=<?php echo $p['id']; ?>" class="btn-icon text-success" title="Restore"><i class="fas fa-eye"></i></a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?php echo $p['id']; ?>" class="btn-icon text-danger" title="Delete" onclick="return confirm('Delete this post permanently?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
