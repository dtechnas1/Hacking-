<?php
$pageTitle = 'Notifications - WhatsMater';
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn()) {
    redirect(APP_URL . '/login.php');
}

$userId = getCurrentUserId();

// Mark all as read
if (isset($_GET['mark_read'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    redirect(APP_URL . '/user/notifications.php');
}

// Get notifications
$stmt = $conn->prepare("SELECT n.*, u.full_name, u.profile_pic
    FROM notifications n
    JOIN users u ON n.from_user_id = u.id
    WHERE n.user_id = ?
    ORDER BY n.created_at DESC
    LIMIT 50");
$stmt->bind_param("i", $userId);
$stmt->execute();
$notifications = $stmt->get_result();

require_once INCLUDES_PATH . 'header.php';
?>

<div class="content-area notifications-page">
    <div class="card">
        <div class="card-header-flex">
            <h2>Notifications</h2>
            <a href="?mark_read=1" class="btn btn-outline btn-sm">Mark all as read</a>
        </div>

        <div class="notification-list">
            <?php if ($notifications->num_rows === 0): ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No notifications</h3>
                <p>You're all caught up!</p>
            </div>
            <?php endif; ?>

            <?php while ($notif = $notifications->fetch_assoc()):
                $link = '#';
                $icon = '';
                $text = '';

                switch ($notif['type']) {
                    case 'like':
                        $icon = 'fas fa-thumbs-up text-primary';
                        $text = htmlspecialchars($notif['full_name']) . ' liked your post.';
                        $link = APP_URL . '/index.php';
                        break;
                    case 'comment':
                        $icon = 'fas fa-comment text-success';
                        $text = htmlspecialchars($notif['full_name']) . ' commented on your post.';
                        $link = APP_URL . '/index.php';
                        break;
                    case 'friend_request':
                        $icon = 'fas fa-user-plus text-info';
                        $text = htmlspecialchars($notif['full_name']) . ' sent you a friend request.';
                        $link = APP_URL . '/user/friends.php?tab=requests';
                        break;
                    case 'friend_accept':
                        $icon = 'fas fa-user-check text-success';
                        $text = htmlspecialchars($notif['full_name']) . ' accepted your friend request.';
                        $link = APP_URL . '/user/profile.php?id=' . $notif['from_user_id'];
                        break;
                    case 'message':
                        $icon = 'fas fa-envelope text-primary';
                        $text = htmlspecialchars($notif['full_name']) . ' sent you a message.';
                        $link = APP_URL . '/user/messages.php?user=' . $notif['from_user_id'];
                        break;
                }
            ?>
            <a href="<?php echo $link; ?>" class="notification-item <?php echo !$notif['is_read'] ? 'unread' : ''; ?>">
                <img src="<?php echo getProfilePic($notif['profile_pic']); ?>" alt="" class="avatar-sm">
                <div class="notification-content">
                    <i class="<?php echo $icon; ?> notification-icon"></i>
                    <p><?php echo $text; ?></p>
                    <span class="notification-time"><?php echo timeAgo($notif['created_at']); ?></span>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
