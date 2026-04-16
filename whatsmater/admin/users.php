<?php
$pageTitle = 'Manage Users - WhatsMater';
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(APP_URL . '/index.php');
}

$adminId = getCurrentUserId();

// Handle user actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $targetId = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($targetId != $adminId) { // Can't modify self
        switch ($action) {
            case 'activate':
                $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                $stmt->bind_param("i", $targetId);
                $stmt->execute();
                $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Activated user', ?)");
                $detail = "User ID: $targetId";
                $logStmt->bind_param("is", $adminId, $detail);
                $logStmt->execute();
                break;
            case 'suspend':
                $stmt = $conn->prepare("UPDATE users SET status = 'suspended' WHERE id = ?");
                $stmt->bind_param("i", $targetId);
                $stmt->execute();
                $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Suspended user', ?)");
                $detail = "User ID: $targetId";
                $logStmt->bind_param("is", $adminId, $detail);
                $logStmt->execute();
                break;
            case 'ban':
                $stmt = $conn->prepare("UPDATE users SET status = 'banned' WHERE id = ?");
                $stmt->bind_param("i", $targetId);
                $stmt->execute();
                $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Banned user', ?)");
                $detail = "User ID: $targetId";
                $logStmt->bind_param("is", $adminId, $detail);
                $logStmt->execute();
                break;
            case 'make_admin':
                $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
                $stmt->bind_param("i", $targetId);
                $stmt->execute();
                $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Promoted to admin', ?)");
                $detail = "User ID: $targetId";
                $logStmt->bind_param("is", $adminId, $detail);
                $logStmt->execute();
                break;
            case 'remove_admin':
                $stmt = $conn->prepare("UPDATE users SET role = 'user' WHERE id = ?");
                $stmt->bind_param("i", $targetId);
                $stmt->execute();
                $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Removed admin role', ?)");
                $detail = "User ID: $targetId";
                $logStmt->bind_param("is", $adminId, $detail);
                $logStmt->execute();
                break;
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND id != ?");
                $stmt->bind_param("ii", $targetId, $adminId);
                $stmt->execute();
                $logStmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action, details) VALUES (?, 'Deleted user', ?)");
                $detail = "User ID: $targetId";
                $logStmt->bind_param("is", $adminId, $detail);
                $logStmt->execute();
                break;
        }
    }
    redirect(APP_URL . '/admin/users.php');
}

// Search & filter
$search = sanitize($_GET['search'] ?? '');
$filter = sanitize($_GET['filter'] ?? 'all');

$where = "WHERE 1=1";
$params = [];
$types = "";

if ($search) {
    $where .= " AND (u.full_name LIKE ? OR u.username LIKE ? OR u.email LIKE ?)";
    $searchParam = "%$search%";
    $params[] = &$searchParam;
    $params[] = &$searchParam;
    $params[] = &$searchParam;
    $types .= "sss";
}
if ($filter !== 'all') {
    $where .= " AND u.status = ?";
    $params[] = &$filter;
    $types .= "s";
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = USERS_PER_PAGE;
$offset = ($page - 1) * $perPage;

$sql = "SELECT u.*, (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as post_count,
    (SELECT COUNT(*) FROM friendships WHERE (requester_id = u.id OR receiver_id = u.id) AND status = 'accepted') as friend_count
    FROM users u $where ORDER BY u.created_at DESC LIMIT $perPage OFFSET $offset";

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$users = $stmt->get_result();

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
            <a href="<?php echo APP_URL; ?>/admin/users.php" class="admin-nav-item active"><i class="fas fa-users"></i> Users</a>
            <a href="<?php echo APP_URL; ?>/admin/posts.php" class="admin-nav-item"><i class="fas fa-newspaper"></i> Posts</a>
            <a href="<?php echo APP_URL; ?>/admin/reports.php" class="admin-nav-item"><i class="fas fa-flag"></i> Reports <?php if ($pendingReports > 0): ?><span class="admin-badge"><?php echo $pendingReports; ?></span><?php endif; ?></a>
            <a href="<?php echo APP_URL; ?>/admin/settings.php" class="admin-nav-item"><i class="fas fa-cogs"></i> Settings</a>
            <hr>
            <a href="<?php echo APP_URL; ?>/index.php" class="admin-nav-item"><i class="fas fa-arrow-left"></i> Back to Site</a>
        </nav>
    </div>

    <div class="admin-content">
        <h2>Manage Users</h2>

        <!-- Filters -->
        <div class="card admin-card">
            <form method="GET" class="admin-filters">
                <div class="form-group">
                    <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="form-group">
                    <select name="filter">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="active" <?php echo $filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="suspended" <?php echo $filter === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                        <option value="banned" <?php echo $filter === 'banned' ? 'selected' : ''; ?>>Banned</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

        <!-- Users Table -->
        <div class="card admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Posts</th>
                        <th>Friends</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td>
                            <div class="table-user">
                                <img src="<?php echo getProfilePic($u['profile_pic']); ?>" alt="" class="avatar-xs">
                                <div>
                                    <strong><?php echo htmlspecialchars($u['full_name']); ?></strong>
                                    <small>@<?php echo htmlspecialchars($u['username']); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="role-badge role-<?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span></td>
                        <td><span class="status-badge status-<?php echo $u['status']; ?>"><?php echo ucfirst($u['status']); ?></span></td>
                        <td><?php echo $u['post_count']; ?></td>
                        <td><?php echo $u['friend_count']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                        <td>
                            <?php if ($u['id'] != $adminId): ?>
                            <div class="action-btns">
                                <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo $u['id']; ?>" class="btn-icon" title="View"><i class="fas fa-eye"></i></a>
                                <?php if ($u['status'] !== 'active'): ?>
                                <a href="?action=activate&id=<?php echo $u['id']; ?>" class="btn-icon text-success" title="Activate"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <?php if ($u['status'] !== 'suspended'): ?>
                                <a href="?action=suspend&id=<?php echo $u['id']; ?>" class="btn-icon text-warning" title="Suspend"><i class="fas fa-pause"></i></a>
                                <?php endif; ?>
                                <?php if ($u['status'] !== 'banned'): ?>
                                <a href="?action=ban&id=<?php echo $u['id']; ?>" class="btn-icon text-danger" title="Ban"><i class="fas fa-ban"></i></a>
                                <?php endif; ?>
                                <?php if ($u['role'] === 'user'): ?>
                                <a href="?action=make_admin&id=<?php echo $u['id']; ?>" class="btn-icon text-info" title="Make Admin" onclick="return confirm('Make this user an admin?')"><i class="fas fa-crown"></i></a>
                                <?php else: ?>
                                <a href="?action=remove_admin&id=<?php echo $u['id']; ?>" class="btn-icon text-warning" title="Remove Admin" onclick="return confirm('Remove admin role?')"><i class="fas fa-user"></i></a>
                                <?php endif; ?>
                                <a href="?action=delete&id=<?php echo $u['id']; ?>" class="btn-icon text-danger" title="Delete" onclick="return confirm('Delete this user permanently?')"><i class="fas fa-trash"></i></a>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
