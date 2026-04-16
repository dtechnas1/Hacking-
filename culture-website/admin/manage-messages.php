<?php
/**
 * Cultural Heritage Website - Manage Contact Messages
 */
require_once __DIR__ . '/auth.php';

$successMsg = '';

// Handle mark as read
if (isset($_GET['read'])) {
    $readId = (int) $_GET['read'];
    $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $readId);
    if ($stmt->execute()) {
        $successMsg = 'Message marked as read.';
    }
}

// Handle mark as unread
if (isset($_GET['unread'])) {
    $unreadId = (int) $_GET['unread'];
    $stmt = $conn->prepare("UPDATE contact_messages SET is_read = 0 WHERE id = ?");
    $stmt->bind_param("i", $unreadId);
    if ($stmt->execute()) {
        $successMsg = 'Message marked as unread.';
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $successMsg = 'Message deleted successfully.';
    }
}

// Get all messages
$messages = [];
$stmt = $conn->prepare("SELECT * FROM contact_messages ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Manage Messages</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <h1><i class="fas fa-cog"></i> Admin Panel</h1>
        <nav class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage-gallery.php"><i class="fas fa-images"></i> Gallery</a>
            <a href="manage-videos.php"><i class="fas fa-video"></i> Videos</a>
            <a href="manage-ethics.php"><i class="fas fa-book-open"></i> Ethics</a>
            <a href="manage-messages.php" class="active"><i class="fas fa-envelope"></i> Messages</a>
            <a href="?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="admin-container">
        <h2><i class="fas fa-envelope"></i> Contact Messages</h2>

        <?php if ($successMsg): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $successMsg; ?></div>
        <?php endif; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $msg): ?>
                        <tr style="<?php echo !$msg['is_read'] ? 'font-weight: 600; background-color: #FFF8DC;' : ''; ?>">
                            <td><?php echo $msg['id']; ?></td>
                            <td>
                                <?php if ($msg['is_read']): ?>
                                    <span class="badge badge-read">Read</span>
                                <?php else: ?>
                                    <span class="badge badge-unread">Unread</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" style="color: var(--primary);"><?php echo htmlspecialchars($msg['email']); ?></a></td>
                            <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                            <td><?php echo htmlspecialchars(substr($msg['message'], 0, 100)); ?><?php echo strlen($msg['message']) > 100 ? '...' : ''; ?></td>
                            <td><?php echo $msg['created_at']; ?></td>
                            <td>
                                <?php if (!$msg['is_read']): ?>
                                    <a href="manage-messages.php?read=<?php echo $msg['id']; ?>" class="btn btn-sm btn-success" title="Mark as Read"><i class="fas fa-check"></i></a>
                                <?php else: ?>
                                    <a href="manage-messages.php?unread=<?php echo $msg['id']; ?>" class="btn btn-sm btn-secondary" title="Mark as Unread"><i class="fas fa-undo"></i></a>
                                <?php endif; ?>
                                <a href="manage-messages.php?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this message?');" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" style="text-align:center;">No messages found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
