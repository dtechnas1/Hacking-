<?php
$pageTitle = 'News Feed - WhatsMater';
require_once __DIR__ . '/config/app.php';

if (!isLoggedIn()) {
    redirect(APP_URL . '/login.php');
}

// Handle new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_content'])) {
    $content = sanitize($_POST['post_content']);
    $privacy = sanitize($_POST['privacy'] ?? 'public');
    $image = null;

    // Handle image upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['post_image'];
        if (in_array($file['type'], ALLOWED_IMAGE_TYPES) && $file['size'] <= MAX_FILE_SIZE) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $image = 'post_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], UPLOADS_PATH . 'posts/' . $image);
        }
    }

    if (!empty($content) || $image) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image, privacy) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $_SESSION['user_id'], $content, $image, $privacy);
        $stmt->execute();
    }
    redirect(APP_URL . '/index.php');
}

// Handle like
if (isset($_GET['like'])) {
    $postId = (int)$_GET['like'];
    $userId = getCurrentUserId();
    // Toggle like
    $stmt = $conn->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $postId, $userId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $postId, $userId);
    } else {
        $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $postId, $userId);
        // Notify post owner
        $postOwner = $conn->query("SELECT user_id FROM posts WHERE id = $postId")->fetch_assoc();
        if ($postOwner && $postOwner['user_id'] != $userId) {
            $nStmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type, reference_id) VALUES (?, ?, 'like', ?)");
            $nStmt->bind_param("iii", $postOwner['user_id'], $userId, $postId);
            $nStmt->execute();
        }
    }
    $stmt->execute();
    redirect(APP_URL . '/index.php');
}

// Handle comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_content'])) {
    $postId = (int)$_POST['post_id'];
    $commentContent = sanitize($_POST['comment_content']);
    $userId = getCurrentUserId();

    if (!empty($commentContent)) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $postId, $userId, $commentContent);
        $stmt->execute();

        // Notify post owner
        $postOwner = $conn->query("SELECT user_id FROM posts WHERE id = $postId")->fetch_assoc();
        if ($postOwner && $postOwner['user_id'] != $userId) {
            $nStmt = $conn->prepare("INSERT INTO notifications (user_id, from_user_id, type, reference_id) VALUES (?, ?, 'comment', ?)");
            $nStmt->bind_param("iii", $postOwner['user_id'], $userId, $postId);
            $nStmt->execute();
        }
    }
    redirect(APP_URL . '/index.php');
}

// Handle delete post
if (isset($_GET['delete_post'])) {
    $postId = (int)$_GET['delete_post'];
    $userId = getCurrentUserId();
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $postId, $userId);
    $stmt->execute();
    redirect(APP_URL . '/index.php');
}

// Fetch posts (own + friends)
$userId = getCurrentUserId();
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * POSTS_PER_PAGE;

$sql = "SELECT p.*, u.username, u.full_name, u.profile_pic,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count,
        (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as user_liked
        FROM posts p
        JOIN users u ON p.user_id = u.id
        WHERE p.status = 'active' AND (
            p.user_id = ? OR
            p.privacy = 'public' OR
            (p.privacy = 'friends' AND p.user_id IN (
                SELECT CASE WHEN requester_id = ? THEN receiver_id ELSE requester_id END
                FROM friendships WHERE (requester_id = ? OR receiver_id = ?) AND status = 'accepted'
            ))
        )
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiiiii", $userId, $userId, $userId, $userId, $userId, $postsPerPage, $offset);
$postsPerPage = POSTS_PER_PAGE;
$stmt->bind_param("iiiiiii", $userId, $userId, $userId, $userId, $userId, $postsPerPage, $offset);
$stmt->execute();
$posts = $stmt->get_result();

require_once INCLUDES_PATH . 'header.php';
require_once INCLUDES_PATH . 'sidebar.php';
?>

<div class="content-area">
    <!-- Create Post -->
    <div class="card create-post-card">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="create-post-top">
                <img src="<?php echo getProfilePic($currentUser['profile_pic']); ?>" alt="" class="avatar-sm">
                <input type="text" name="post_content" placeholder="What's on your mind, <?php echo htmlspecialchars($currentUser['full_name']); ?>?" class="post-input" required>
            </div>
            <div class="create-post-options">
                <label class="post-option">
                    <i class="fas fa-image" style="color: #45bd62;"></i>
                    <span>Photo</span>
                    <input type="file" name="post_image" accept="image/*" hidden>
                </label>
                <select name="privacy" class="privacy-select">
                    <option value="public">Public</option>
                    <option value="friends">Friends</option>
                    <option value="private">Only Me</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Post</button>
            </div>
        </form>
    </div>

    <!-- Posts Feed -->
    <?php if ($posts->num_rows === 0): ?>
    <div class="card empty-state">
        <i class="fas fa-newspaper"></i>
        <h3>No posts yet</h3>
        <p>Start by creating a post or adding some friends!</p>
    </div>
    <?php endif; ?>

    <?php while ($post = $posts->fetch_assoc()): ?>
    <div class="card post-card">
        <div class="post-header">
            <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo $post['user_id']; ?>" class="post-author">
                <img src="<?php echo getProfilePic($post['profile_pic']); ?>" alt="" class="avatar-sm">
                <div>
                    <strong><?php echo htmlspecialchars($post['full_name']); ?></strong>
                    <span class="post-time"><?php echo timeAgo($post['created_at']); ?> &middot; <i class="fas fa-<?php echo $post['privacy'] === 'public' ? 'globe-americas' : ($post['privacy'] === 'friends' ? 'user-friends' : 'lock'); ?>"></i></span>
                </div>
            </a>
            <?php if ($post['user_id'] == $userId): ?>
            <div class="post-actions-menu">
                <button class="btn-icon dropdown-toggle"><i class="fas fa-ellipsis-h"></i></button>
                <div class="dropdown-menu">
                    <a href="<?php echo APP_URL; ?>/index.php?delete_post=<?php echo $post['id']; ?>" onclick="return confirm('Delete this post?')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($post['content']): ?>
        <div class="post-content">
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        </div>
        <?php endif; ?>

        <?php if ($post['image']): ?>
        <div class="post-image">
            <img src="<?php echo APP_URL; ?>/uploads/posts/<?php echo $post['image']; ?>" alt="Post image">
        </div>
        <?php endif; ?>

        <div class="post-stats">
            <?php if ($post['like_count'] > 0): ?>
            <span><i class="fas fa-thumbs-up text-primary"></i> <?php echo $post['like_count']; ?></span>
            <?php endif; ?>
            <?php if ($post['comment_count'] > 0): ?>
            <span><?php echo $post['comment_count']; ?> comment<?php echo $post['comment_count'] > 1 ? 's' : ''; ?></span>
            <?php endif; ?>
        </div>

        <div class="post-action-bar">
            <a href="<?php echo APP_URL; ?>/index.php?like=<?php echo $post['id']; ?>" class="post-action <?php echo $post['user_liked'] ? 'liked' : ''; ?>">
                <i class="fas fa-thumbs-up"></i> Like
            </a>
            <button class="post-action toggle-comments" data-post="<?php echo $post['id']; ?>">
                <i class="fas fa-comment"></i> Comment
            </button>
        </div>

        <!-- Comments Section -->
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

            <form method="POST" action="" class="comment-form">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <img src="<?php echo getProfilePic($currentUser['profile_pic']); ?>" alt="" class="avatar-xs">
                <input type="text" name="comment_content" placeholder="Write a comment..." required>
                <button type="submit" class="btn-icon"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
    <?php endwhile; ?>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="btn btn-outline">&laquo; Previous</a>
        <?php endif; ?>
        <a href="?page=<?php echo $page + 1; ?>" class="btn btn-outline">Next &raquo;</a>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
