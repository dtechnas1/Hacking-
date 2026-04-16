<?php
$pageTitle = 'Messages - WhatsMater';
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn()) {
    redirect(APP_URL . '/login.php');
}

$userId = getCurrentUserId();
$chatUserId = isset($_GET['user']) ? (int)$_GET['user'] : null;

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $chatUserId) {
    $message = sanitize($_POST['message']);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userId, $chatUserId, $message);
        $stmt->execute();

        // Notify
        $nStmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type) VALUES (?, ?, 'message')");
        $nStmt->bind_param("ii", $chatUserId, $userId);
        $nStmt->execute();
    }
    redirect(APP_URL . '/user/messages.php?user=' . $chatUserId);
}

// Get conversations list
$sql = "SELECT u.id, u.full_name, u.profile_pic, u.is_online,
    (SELECT message FROM messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message,
    (SELECT created_at FROM messages WHERE (sender_id = u.id AND receiver_id = ?) OR (sender_id = ? AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_message_time,
    (SELECT COUNT(*) FROM messages WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0) as unread_count
    FROM users u
    WHERE u.id IN (
        SELECT DISTINCT CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END
        FROM messages WHERE sender_id = ? OR receiver_id = ?
    )
    ORDER BY last_message_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiiiiii", $userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId);
$stmt->execute();
$conversations = $stmt->get_result();

// Get chat messages
$chatUser = null;
$chatMessages = null;
if ($chatUserId) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $chatUserId);
    $stmt->execute();
    $chatUser = $stmt->get_result()->fetch_assoc();

    if ($chatUser) {
        // Mark as read
        $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?");
        $stmt->bind_param("ii", $chatUserId, $userId);
        $stmt->execute();

        // Get messages
        $stmt = $conn->prepare("SELECT m.*, u.full_name, u.profile_pic FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC LIMIT 100");
        $stmt->bind_param("iiii", $userId, $chatUserId, $chatUserId, $userId);
        $stmt->execute();
        $chatMessages = $stmt->get_result();
    }
}

require_once INCLUDES_PATH . 'header.php';
?>

<div class="messenger-page">
    <!-- Conversations List -->
    <div class="messenger-sidebar">
        <div class="messenger-sidebar-header">
            <h3>Chats</h3>
        </div>
        <div class="conversation-list">
            <?php if ($conversations->num_rows === 0): ?>
            <div class="empty-conversations">
                <p>No conversations yet</p>
            </div>
            <?php endif; ?>
            <?php while ($convo = $conversations->fetch_assoc()): ?>
            <a href="?user=<?php echo $convo['id']; ?>" class="conversation-item <?php echo $chatUserId == $convo['id'] ? 'active' : ''; ?>">
                <div class="convo-avatar">
                    <img src="<?php echo getProfilePic($convo['profile_pic']); ?>" alt="">
                    <?php if ($convo['is_online']): ?><span class="online-dot"></span><?php endif; ?>
                </div>
                <div class="convo-info">
                    <strong><?php echo htmlspecialchars($convo['full_name']); ?></strong>
                    <p><?php echo htmlspecialchars(substr($convo['last_message'] ?? '', 0, 40)); ?><?php echo strlen($convo['last_message'] ?? '') > 40 ? '...' : ''; ?></p>
                </div>
                <?php if ($convo['unread_count'] > 0): ?>
                <span class="unread-badge"><?php echo $convo['unread_count']; ?></span>
                <?php endif; ?>
            </a>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Chat Area -->
    <div class="messenger-chat">
        <?php if ($chatUser): ?>
        <div class="chat-header">
            <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo $chatUser['id']; ?>" class="chat-user-info">
                <img src="<?php echo getProfilePic($chatUser['profile_pic']); ?>" alt="" class="avatar-sm">
                <div>
                    <strong><?php echo htmlspecialchars($chatUser['full_name']); ?></strong>
                    <span class="<?php echo $chatUser['is_online'] ? 'text-success' : 'text-muted'; ?>">
                        <?php echo $chatUser['is_online'] ? 'Active Now' : 'Offline'; ?>
                    </span>
                </div>
            </a>
        </div>

        <div class="chat-messages" id="chatMessages">
            <?php if ($chatMessages): ?>
            <?php while ($msg = $chatMessages->fetch_assoc()): ?>
            <div class="message <?php echo $msg['sender_id'] == $userId ? 'sent' : 'received'; ?>">
                <?php if ($msg['sender_id'] != $userId): ?>
                <img src="<?php echo getProfilePic($msg['profile_pic']); ?>" alt="" class="avatar-xs">
                <?php endif; ?>
                <div class="message-bubble">
                    <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    <span class="message-time"><?php echo timeAgo($msg['created_at']); ?></span>
                </div>
            </div>
            <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <form method="POST" action="" class="chat-input-form">
            <input type="text" name="message" placeholder="Type a message..." autocomplete="off" required>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
        </form>
        <?php else: ?>
        <div class="chat-empty">
            <i class="fas fa-comments"></i>
            <h3>Select a conversation</h3>
            <p>Choose a friend to start chatting</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Scroll to bottom of chat
const chatMessages = document.getElementById('chatMessages');
if (chatMessages) {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
