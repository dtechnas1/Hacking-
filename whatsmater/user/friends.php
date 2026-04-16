<?php
$pageTitle = 'Friends - WhatsMater';
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn()) {
    redirect(APP_URL . '/login.php');
}

$userId = getCurrentUserId();

// Handle friend actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $targetId = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'accept') {
        $stmt = $conn->prepare("UPDATE friendships SET status = 'accepted' WHERE requester_id = ? AND receiver_id = ? AND status = 'pending'");
        $stmt->bind_param("ii", $targetId, $userId);
        $stmt->execute();
        $nStmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type) VALUES (?, ?, 'friend_accept')");
        $nStmt->bind_param("ii", $targetId, $userId);
        $nStmt->execute();
    } elseif ($action === 'decline') {
        $stmt = $conn->prepare("UPDATE friendships SET status = 'declined' WHERE requester_id = ? AND receiver_id = ? AND status = 'pending'");
        $stmt->bind_param("ii", $targetId, $userId);
        $stmt->execute();
    } elseif ($action === 'unfriend') {
        $stmt = $conn->prepare("DELETE FROM friendships WHERE (requester_id = ? AND receiver_id = ?) OR (requester_id = ? AND receiver_id = ?)");
        $stmt->bind_param("iiii", $userId, $targetId, $targetId, $userId);
        $stmt->execute();
    }
    redirect(APP_URL . '/user/friends.php');
}

$tab = $_GET['tab'] ?? 'friends';

// Get friends
$stmt = $conn->prepare("SELECT u.id, u.full_name, u.username, u.profile_pic, u.is_online, u.city, u.country
    FROM users u JOIN friendships f ON (u.id = f.requester_id OR u.id = f.receiver_id)
    WHERE u.id != ? AND ((f.requester_id = ? OR f.receiver_id = ?) AND f.status = 'accepted')
    ORDER BY u.full_name ASC");
$stmt->bind_param("iii", $userId, $userId, $userId);
$stmt->execute();
$friendsList = $stmt->get_result();

// Get pending requests
$stmt = $conn->prepare("SELECT u.id, u.full_name, u.username, u.profile_pic, f.created_at as request_date
    FROM users u JOIN friendships f ON u.id = f.requester_id
    WHERE f.receiver_id = ? AND f.status = 'pending'
    ORDER BY f.created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$pendingRequests = $stmt->get_result();

// Get suggested people (not friends, not self)
$stmt = $conn->prepare("SELECT u.id, u.full_name, u.username, u.profile_pic FROM users u
    WHERE u.id != ? AND u.status = 'active' AND u.id NOT IN (
        SELECT CASE WHEN requester_id = ? THEN receiver_id ELSE requester_id END
        FROM friendships WHERE requester_id = ? OR receiver_id = ?
    ) ORDER BY RAND() LIMIT 10");
$stmt->bind_param("iiii", $userId, $userId, $userId, $userId);
$stmt->execute();
$suggestions = $stmt->get_result();

require_once INCLUDES_PATH . 'header.php';
?>

<div class="content-area friends-page">
    <div class="card">
        <div class="tabs">
            <a href="?tab=friends" class="tab <?php echo $tab === 'friends' ? 'active' : ''; ?>">
                <i class="fas fa-user-friends"></i> All Friends (<?php echo $friendsList->num_rows; ?>)
            </a>
            <a href="?tab=requests" class="tab <?php echo $tab === 'requests' ? 'active' : ''; ?>">
                <i class="fas fa-user-clock"></i> Friend Requests (<?php echo $pendingRequests->num_rows; ?>)
            </a>
            <a href="?tab=suggestions" class="tab <?php echo $tab === 'suggestions' ? 'active' : ''; ?>">
                <i class="fas fa-user-plus"></i> Suggestions
            </a>
        </div>
    </div>

    <?php if ($tab === 'friends'): ?>
    <div class="friends-grid-list">
        <?php if ($friendsList->num_rows === 0): ?>
        <div class="card empty-state">
            <i class="fas fa-users"></i>
            <h3>No friends yet</h3>
            <p>Start connecting with people!</p>
        </div>
        <?php endif; ?>
        <?php while ($friend = $friendsList->fetch_assoc()): ?>
        <div class="card friend-card">
            <img src="<?php echo getProfilePic($friend['profile_pic']); ?>" alt="" class="friend-card-img">
            <div class="friend-card-info">
                <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo $friend['id']; ?>">
                    <strong><?php echo htmlspecialchars($friend['full_name']); ?></strong>
                </a>
                <?php if ($friend['city'] || $friend['country']): ?>
                <span class="text-muted"><?php echo htmlspecialchars(($friend['city'] ? $friend['city'] . ', ' : '') . ($friend['country'] ?? '')); ?></span>
                <?php endif; ?>
                <span class="online-status <?php echo $friend['is_online'] ? 'online' : 'offline'; ?>">
                    <?php echo $friend['is_online'] ? 'Online' : 'Offline'; ?>
                </span>
            </div>
            <div class="friend-card-actions">
                <a href="<?php echo APP_URL; ?>/user/messages.php?user=<?php echo $friend['id']; ?>" class="btn btn-outline btn-sm"><i class="fas fa-comment"></i></a>
                <a href="?action=unfriend&id=<?php echo $friend['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Unfriend?')"><i class="fas fa-user-times"></i></a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php elseif ($tab === 'requests'): ?>
    <div class="friends-grid-list">
        <?php if ($pendingRequests->num_rows === 0): ?>
        <div class="card empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No pending requests</h3>
        </div>
        <?php endif; ?>
        <?php while ($req = $pendingRequests->fetch_assoc()): ?>
        <div class="card friend-card">
            <img src="<?php echo getProfilePic($req['profile_pic']); ?>" alt="" class="friend-card-img">
            <div class="friend-card-info">
                <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo $req['id']; ?>">
                    <strong><?php echo htmlspecialchars($req['full_name']); ?></strong>
                </a>
                <span class="text-muted"><?php echo timeAgo($req['request_date']); ?></span>
            </div>
            <div class="friend-card-actions">
                <a href="?action=accept&id=<?php echo $req['id']; ?>" class="btn btn-primary btn-sm">Accept</a>
                <a href="?action=decline&id=<?php echo $req['id']; ?>" class="btn btn-outline btn-sm">Decline</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <?php elseif ($tab === 'suggestions'): ?>
    <div class="friends-grid-list">
        <?php if ($suggestions->num_rows === 0): ?>
        <div class="card empty-state">
            <i class="fas fa-search"></i>
            <h3>No suggestions at the moment</h3>
        </div>
        <?php endif; ?>
        <?php while ($sug = $suggestions->fetch_assoc()): ?>
        <div class="card friend-card">
            <img src="<?php echo getProfilePic($sug['profile_pic']); ?>" alt="" class="friend-card-img">
            <div class="friend-card-info">
                <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo $sug['id']; ?>">
                    <strong><?php echo htmlspecialchars($sug['full_name']); ?></strong>
                </a>
                <span class="text-muted">@<?php echo htmlspecialchars($sug['username']); ?></span>
            </div>
            <div class="friend-card-actions">
                <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo $sug['id']; ?>&action=add_friend" class="btn btn-primary btn-sm"><i class="fas fa-user-plus"></i> Add</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
