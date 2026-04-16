<?php
/**
 * Cultural Heritage Website - Manage Ethics Content
 */
require_once __DIR__ . '/auth.php';

$successMsg = '';
$errorMsg = '';

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM ethics_content WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $successMsg = 'Ethics content deleted successfully.';
    } else {
        $errorMsg = 'Failed to delete ethics content.';
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : 0;
    $title = sanitize($_POST['title'] ?? '');
    $body = sanitize($_POST['body'] ?? '');
    $section = sanitize($_POST['section'] ?? '');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    if (empty($title) || empty($body) || empty($section)) {
        $errorMsg = 'Title, body, and section are required.';
    } else {
        if ($editId > 0) {
            $stmt = $conn->prepare("UPDATE ethics_content SET title = ?, body = ?, section = ?, sort_order = ? WHERE id = ?");
            $stmt->bind_param("sssii", $title, $body, $section, $sortOrder, $editId);
        } else {
            $stmt = $conn->prepare("INSERT INTO ethics_content (title, body, section, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $title, $body, $section, $sortOrder);
        }

        if ($stmt->execute()) {
            $successMsg = $editId > 0 ? 'Ethics content updated successfully.' : 'Ethics content added successfully.';
        } else {
            $errorMsg = 'An error occurred. Please try again.';
        }
    }
}

// Get editing item
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM ethics_content WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editItem = $stmt->get_result()->fetch_assoc();
}

// Get all ethics content
$ethicsItems = getEthicsContent($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Manage Ethics</title>
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
            <a href="manage-ethics.php" class="active"><i class="fas fa-book-open"></i> Ethics</a>
            <a href="manage-messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="admin-container">
        <h2><i class="fas fa-book-open"></i> Manage Ethics Content</h2>

        <?php if ($successMsg): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $successMsg; ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="admin-form">
            <h3><?php echo $editItem ? 'Edit Ethics Content' : 'Add New Ethics Content'; ?></h3>
            <form method="POST">
                <?php if ($editItem): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editItem['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo $editItem ? htmlspecialchars($editItem['title']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="body">Body Content</label>
                    <textarea id="body" name="body" rows="6" required><?php echo $editItem ? htmlspecialchars($editItem['body']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="section">Section</label>
                    <select id="section" name="section" style="width:100%; padding:12px 16px; border:2px solid var(--gray-300); border-radius:var(--radius); font-size:1rem;" required>
                        <option value="traditions" <?php echo ($editItem && $editItem['section'] === 'traditions') ? 'selected' : ''; ?>>Traditions</option>
                        <option value="moral_teachings" <?php echo ($editItem && $editItem['section'] === 'moral_teachings') ? 'selected' : ''; ?>>Moral Teachings</option>
                        <option value="history" <?php echo ($editItem && $editItem['section'] === 'history') ? 'selected' : ''; ?>>History</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sort_order">Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" value="<?php echo $editItem ? $editItem['sort_order'] : 0; ?>" min="0">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $editItem ? 'Update Content' : 'Add Content'; ?>
                </button>
                <?php if ($editItem): ?>
                    <a href="manage-ethics.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Ethics Content Table -->
        <h3 style="margin-top: 30px;">All Ethics Content</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Section</th>
                    <th>Sort Order</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ethicsItems)): ?>
                    <?php foreach ($ethicsItems as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo htmlspecialchars($item['section']); ?></td>
                            <td><?php echo $item['sort_order']; ?></td>
                            <td><?php echo $item['created_at']; ?></td>
                            <td>
                                <a href="manage-ethics.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="manage-ethics.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this content?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No ethics content found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
