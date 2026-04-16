<?php
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn()) {
    redirect(APP_URL . '/login.php');
}

$profileId = (int)($_GET['id'] ?? getCurrentUserId());
$currentUserId = getCurrentUserId();

// Get profile user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$profileUser = $stmt->get_result()->fetch_assoc();

if (!$profileUser) {
    redirect(APP_URL . '/index.php');
}

$pageTitle = htmlspecialchars($profileUser['full_name']) . ' - WhatsMater';

// Check friendship status
$friendStatus = null;
if ($profileId != $currentUserId) {
    $stmt = $conn->prepare("SELECT * FROM friendships WHERE (requester_id = ? AND receiver_id = ?) OR (requester_id = ? AND receiver_id = ?)");
    $stmt->bind_param("iiii", $currentUserId, $profileId, $profileId, $currentUserId);
    $stmt->execute();
    $friendship = $stmt->get_result()->fetch_assoc();
    if ($friendship) {
        $friendStatus = $friendship['status'];
        if ($friendStatus === 'pending' && $friendship['receiver_id'] == $currentUserId) {
            $friendStatus = 'received';
        }
    }
}

// Handle friend actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'add_friend' && !$friendStatus) {
        $stmt = $conn->prepare("INSERT INTO friendships (requester_id, receiver_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $currentUserId, $profileId);
        $stmt->execute();
        // Notify
        $nStmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type) VALUES (?, ?, 'friend_request')");
        $nStmt->bind_param("ii", $profileId, $currentUserId);
        $nStmt->execute();
    } elseif ($action === 'accept_friend' && $friendStatus === 'received') {
        $stmt = $conn->prepare("UPDATE friendships SET status = 'accepted' WHERE requester_id = ? AND receiver_id = ?");
        $stmt->bind_param("ii", $profileId, $currentUserId);
        $stmt->execute();
        $nStmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type) VALUES (?, ?, 'friend_accept')");
        $nStmt->bind_param("ii", $profileId, $currentUserId);
        $nStmt->execute();
    } elseif ($action === 'unfriend') {
        $stmt = $conn->prepare("DELETE FROM friendships WHERE (requester_id = ? AND receiver_id = ?) OR (requester_id = ? AND receiver_id = ?)");
        $stmt->bind_param("iiii", $currentUserId, $profileId, $profileId, $currentUserId);
        $stmt->execute();
    }
    redirect(APP_URL . '/user/profile.php?id=' . $profileId);
}

// Get friend count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM friendships WHERE (requester_id = ? OR receiver_id = ?) AND status = 'accepted'");
$stmt->bind_param("ii", $profileId, $profileId);
$stmt->execute();
$friendCount = $stmt->get_result()->fetch_assoc()['count'];

// Get posts count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM posts WHERE user_id = ? AND status = 'active'");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$postCount = $stmt->get_result()->fetch_assoc()['count'];

// Get profile posts
$stmt = $conn->prepare("SELECT p.*, u.username, u.full_name, u.profile_pic,
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
    (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked
    FROM posts p JOIN users u ON p.user_id = u.id
    WHERE p.user_id = ? AND p.status = 'active'
    ORDER BY p.created_at DESC LIMIT 20");
$stmt->bind_param("ii", $currentUserId, $profileId);
$stmt->execute();
$posts = $stmt->get_result();

// Get friends list (for sidebar)
$stmt = $conn->prepare("SELECT u.id, u.full_name, u.profile_pic, u.is_online FROM users u
    JOIN friendships f ON (u.id = f.requester_id OR u.id = f.receiver_id)
    WHERE u.id != ? AND ((f.requester_id = ? OR f.receiver_id = ?) AND f.status = 'accepted')
    LIMIT 9");
$stmt->bind_param("iii", $profileId, $profileId, $profileId);
$stmt->execute();
$friends = $stmt->get_result();

require_once INCLUDES_PATH . 'header.php';
?>

<div class="profile-page">
    <!-- Cover & Profile Section -->
    <div class="profile-cover" style="background: linear-gradient(135deg, #1877f2, #42b72a);">
        <div class="profile-info-bar">
            <div class="profile-avatar-section">
                <img src="<?php echo getProfilePic($profileUser['profile_pic']); ?>" alt="" class="profile-avatar-lg">
            </div>
            <div class="profile-name-section">
                <h1><?php echo htmlspecialchars($profileUser['full_name']); ?></h1>
                <span class="friend-count"><?php echo $friendCount; ?> friend<?php echo $friendCount != 1 ? 's' : ''; ?></span>
            </div>
            <div class="profile-actions">
                <?php if ($profileId == $currentUserId): ?>
                    <a href="<?php echo APP_URL; ?>/user/settings.php" class="btn btn-outline"><i class="fas fa-pen"></i> Edit Profile</a>
                <?php else: ?>
                    <?php if (!$friendStatus): ?>
                        <a href="?id=<?php echo $profileId; ?>&action=add_friend" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Friend</a>
                    <?php elseif ($friendStatus === 'pending'): ?>
                        <button class="btn btn-outline" disabled><i class="fas fa-clock"></i> Request Sent</button>
                    <?php elseif ($friendStatus === 'received'): ?>
                        <a href="?id=<?php echo $profileId; ?>&action=accept_friend" class="btn btn-primary"><i class="fas fa-check"></i> Accept Request</a>
                    <?php elseif ($friendStatus === 'accepted'): ?>
                        <a href="?id=<?php echo $profileId; ?>&action=unfriend" class="btn btn-outline" onclick="return confirm('Unfriend this person?')"><i class="fas fa-user-check"></i> Friends</a>
                    <?php endif; ?>
                    <a href="<?php echo APP_URL; ?>/user/messages.php?user=<?php echo $profileId; ?>" class="btn btn-outline"><i class="fas fa-comment"></i> Message</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="profile-body">
        <!-- Left Column: About & Friends -->
        <div class="profile-sidebar">
            <div class="card">
                <h3>About</h3>
                <?php if ($profileUser['bio']): ?>
                <p class="bio-text"><?php echo nl2br(htmlspecialchars($profileUser['bio'])); ?></p>
                <?php endif; ?>
                <div class="about-items">
                    <?php if ($profileUser['city'] || $profileUser['country']): ?>
                    <div class="about-item"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars(($profileUser['city'] ? $profileUser['city'] . ', ' : '') . ($profileUser['country'] ?? '')); ?></div>
                    <?php endif; ?>
                    <?php if ($profileUser['website']): ?>
                    <div class="about-item"><i class="fas fa-link"></i> <a href="<?php echo htmlspecialchars($profileUser['website']); ?>" target="_blank"><?php echo htmlspecialchars($profileUser['website']); ?></a></div>
                    <?php endif; ?>
                    <?php if ($profileUser['gender']): ?>
                    <div class="about-item"><i class="fas fa-venus-mars"></i> <?php echo ucfirst($profileUser['gender']); ?></div>
                    <?php endif; ?>
                    <div class="about-item"><i class="fas fa-calendar"></i> Joined <?php echo date('F Y', strtotime($profileUser['created_at'])); ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header-flex">
                    <h3>Friends</h3>
                    <span><?php echo $friendCount; ?></span>
                </div>
                <div class="friends-grid">
                    <?php while ($friend = $friends->fetch_assoc()): ?>
                    <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo $friend['id']; ?>" class="friend-grid-item">
                        <img src="<?php echo getProfilePic($friend['profile_pic']); ?>" alt="">
                        <span><?php echo htmlspecialchars($friend['full_name']); ?></span>
                    </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Right Column: Posts -->
        <div class="profile-content">
            <?php if ($profileId == $currentUserId): ?>
            <div class="card create-post-card">
                <form method="POST" action="<?php echo APP_URL; ?>/index.php" enctype="multipart/form-data">
                    <div class="create-post-top">
                        <img src="<?php echo getProfilePic($currentUser['profile_pic']); ?>" alt="" class="avatar-sm">
                        <input type="text" name="post_content" placeholder="What's on your mind?" class="post-input" required>
                    </div>
                    <div class="create-post-options">
                        <label class="post-option">
                            <i class="fas fa-image" style="color: #45bd62;"></i> <span>Photo</span>
                            <input type="file" name="post_image" accept="image/*" hidden>
                        </label>
                        <button type="submit" class="btn btn-primary btn-sm">Post</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <?php if ($posts->num_rows === 0): ?>
            <div class="card empty-state">
                <i class="fas fa-pen-fancy"></i>
                <h3>No posts yet</h3>
            </div>
            <?php endif; ?>

            <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="card post-card">
                <div class="post-header">
                    <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo $post['user_id']; ?>" class="post-author">
                        <img src="<?php echo getProfilePic($post['profile_pic']); ?>" alt="" class="avatar-sm">
                        <div>
                            <strong><?php echo htmlspecialchars($post['full_name']); ?></strong>
                            <span class="post-time"><?php echo timeAgo($post['created_at']); ?></span>
                        </div>
                    </a>
                </div>
                <?php if ($post['content']): ?>
                <div class="post-content"><p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p></div>
                <?php endif; ?>
                <?php if ($post['image']): ?>
                <div class="post-image"><img src="<?php echo APP_URL; ?>/uploads/posts/<?php echo $post['image']; ?>" alt=""></div>
                <?php endif; ?>
                <div class="post-stats">
                    <?php if ($post['like_count'] > 0): ?><span><i class="fas fa-thumbs-up text-primary"></i> <?php echo $post['like_count']; ?></span><?php endif; ?>
                    <?php if ($post['comment_count'] > 0): ?><span><?php echo $post['comment_count']; ?> comment<?php echo $post['comment_count'] > 1 ? 's' : ''; ?></span><?php endif; ?>
                </div>
                <div class="post-action-bar">
                    <a href="<?php echo APP_URL; ?>/index.php?like=<?php echo $post['id']; ?>" class="post-action <?php echo $post['user_liked'] ? 'liked' : ''; ?>">
                        <i class="fas fa-thumbs-up"></i> Like
                    </a>
                    <button class="post-action toggle-comments" data-post="<?php echo $post['id']; ?>">
                        <i class="fas fa-comment"></i> Comment
                    </button>
                </div>
                <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display:none;">
                    <?php
                    $cStmt = $conn->prepare("SELECT c.*, u.full_name, u.profile_pic FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC LIMIT 10");
                    $cStmt->bind_param("i", $post['id']);
                    $cStmt->execute();
                    $comments = $cStmt->get_result();
                    while ($comment = $comments->fetch_assoc()):
                    ?>
                    <div class="comment">
                        <img src="<?php echo getProfilePic($comment['profile_pic']); ?>" alt="" class="avatar-xs">
                        <div class="comment-body">
                            <strong><?php echo htmlspecialchars($comment['full_name']); ?></strong>
                            <p><?php echo htmlspecialchars($comment['content']); ?></p>
                            <span class="comment-time"><?php echo timeAgo($comment['created_at']); ?></span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <form method="POST" action="<?php echo APP_URL; ?>/index.php" class="comment-form">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <img src="<?php echo getProfilePic($currentUser['profile_pic']); ?>" alt="" class="avatar-xs">
                        <input type="text" name="comment_content" placeholder="Write a comment..." required>
                        <button type="submit" class="btn-icon"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
