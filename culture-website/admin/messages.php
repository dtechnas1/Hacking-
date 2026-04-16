<?php
$pageTitle = 'Manage Messages - Cultural Heritage';
require_once __DIR__ . '/../config/app.php';

if (!isAdminLoggedIn()) {
    redirect(APP_URL . '/admin/login.php');
}

$success = '';
$error = '';

// Handle mark as read
if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Message marked as read.';
    }
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Message deleted successfully.';
    } else {
        $error = 'Failed to delete message.';
    }
    $stmt->close();
}

// Get all messages
$messages = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$unreadCount = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")->fetch_assoc()['count'];

// Get single message for viewing
$viewMessage = null;
if (isset($_GET['view'])) {
    $viewId = (int)$_GET['view'];
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $viewMessage = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Mark as read
    if ($viewMessage && !$viewMessage['is_read']) {
        $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $viewId);
        $stmt->execute();
        $stmt->close();
        $viewMessage['is_read'] = 1;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body class="admin-body">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <i class="fas fa-landmark"></i>
            <h2><?php echo APP_NAME; ?></h2>
            <span>Admin Panel</span>
        </div>
        <nav class="admin-nav">
            <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="admin-nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="<?php echo APP_URL; ?>/admin/gallery.php" class="admin-nav-link"><i class="fas fa-images"></i> Gallery</a>
            <a href="<?php echo APP_URL; ?>/admin/videos.php" class="admin-nav-link"><i class="fas fa-video"></i> Videos</a>
            <a href="<?php echo APP_URL; ?>/admin/ethics.php" class="admin-nav-link"><i class="fas fa-scroll"></i> Ethics Content</a>
            <a href="<?php echo APP_URL; ?>/admin/messages.php" class="admin-nav-link active"><i class="fas fa-envelope"></i> Messages <?php if ($unreadCount > 0): ?><span class="badge"><?php echo $unreadCount; ?></span><?php endif; ?></a>
            <hr>
            <a href="<?php echo APP_URL; ?>/index.php" class="admin-nav-link"><i class="fas fa-globe"></i> View Website</a>
            <a href="<?php echo APP_URL; ?>/admin/logout.php" class="admin-nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-header">
            <h1><i class="fas fa-envelope"></i> Messages</h1>
            <div class="admin-user">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            </div>
        </header>

        <div class="admin-content">
            <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($viewMessage): ?>
            <!-- View Single Message -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>Message from <?php echo htmlspecialchars($viewMessage['name']); ?></h2>
                    <a href="<?php echo APP_URL; ?>/admin/messages.php" class="btn btn-sm btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
                <div class="admin-card-body">
                    <div class="message-detail">
                        <div class="message-meta">
                            <p><strong>From:</strong> <?php echo htmlspecialchars($viewMessage['name']); ?> (<?php echo htmlspecialchars($viewMessage['email']); ?>)</p>
                            <p><strong>Subject:</strong> <?php echo htmlspecialchars($viewMessage['subject'] ?: 'No subject'); ?></p>
                            <p><strong>Date:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($viewMessage['created_at'])); ?></p>
                        </div>
                        <div class="message-body">
                            <p><?php echo nl2br(htmlspecialchars($viewMessage['message'])); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- Messages Table -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>All Messages (<?php echo count($messages); ?>) — <?php echo $unreadCount; ?> unread</h2>
                </div>
                <div class="admin-card-body">
                    <?php if (empty($messages)): ?>
                    <p class="text-muted">No messages yet.</p>
                    <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                            <tr class="<?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                                <td><?php echo $msg['id']; ?></td>
                                <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                <td><?php echo htmlspecialchars($msg['subject'] ?: 'No subject'); ?></td>
                                <td><?php echo date('M j, Y', strtotime($msg['created_at'])); ?></td>
                                <td><span class="status-badge <?php echo $msg['is_read'] ? 'status-read' : 'status-unread'; ?>"><?php echo $msg['is_read'] ? 'Read' : 'Unread'; ?></span></td>
                                <td class="actions-cell">
                                    <a href="<?php echo APP_URL; ?>/admin/messages.php?view=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline" title="View"><i class="fas fa-eye"></i></a>
                                    <?php if (!$msg['is_read']): ?>
                                    <a href="<?php echo APP_URL; ?>/admin/messages.php?read=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline" title="Mark Read"><i class="fas fa-check"></i></a>
                                    <?php endif; ?>
                                    <a href="<?php echo APP_URL; ?>/admin/messages.php?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this message?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
</body>
</html>
