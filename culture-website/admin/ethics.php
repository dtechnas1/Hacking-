<?php
$pageTitle = 'Manage Ethics Content - Cultural Heritage';
require_once __DIR__ . '/../config/app.php';

if (!isAdminLoggedIn()) {
    redirect(APP_URL . '/admin/login.php');
}

$success = '';
$error = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM ethics_content WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Ethics content deleted successfully.';
    } else {
        $error = 'Failed to delete ethics content.';
    }
    $stmt->close();
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $body = sanitize($_POST['body'] ?? '');
    $section = sanitize($_POST['section'] ?? 'traditions');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $edit_id = (int)($_POST['edit_id'] ?? 0);

    if (empty($title) || empty($body)) {
        $error = 'Title and body are required.';
    } else {
        if ($edit_id > 0) {
            $stmt = $conn->prepare("UPDATE ethics_content SET title = ?, body = ?, section = ?, sort_order = ? WHERE id = ?");
            $stmt->bind_param("sssii", $title, $body, $section, $sort_order, $edit_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO ethics_content (title, body, section, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $title, $body, $section, $sort_order);
        }

        if ($stmt->execute()) {
            $success = $edit_id > 0 ? 'Ethics content updated successfully.' : 'Ethics content added successfully.';
        } else {
            $error = 'Failed to save ethics content.';
        }
        $stmt->close();
    }
}

// Get all ethics content
$ethicsItems = $conn->query("SELECT * FROM ethics_content ORDER BY section, sort_order ASC")->fetch_all(MYSQLI_ASSOC);

// Get item for editing
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM ethics_content WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editItem = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$unreadCount = $conn->query("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")->fetch_assoc()['count'];
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
            <a href="<?php echo APP_URL; ?>/admin/ethics.php" class="admin-nav-link active"><i class="fas fa-scroll"></i> Ethics Content</a>
            <a href="<?php echo APP_URL; ?>/admin/messages.php" class="admin-nav-link"><i class="fas fa-envelope"></i> Messages <?php if ($unreadCount > 0): ?><span class="badge"><?php echo $unreadCount; ?></span><?php endif; ?></a>
            <hr>
            <a href="<?php echo APP_URL; ?>/index.php" class="admin-nav-link"><i class="fas fa-globe"></i> View Website</a>
            <a href="<?php echo APP_URL; ?>/admin/logout.php" class="admin-nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-header">
            <h1><i class="fas fa-scroll"></i> Manage Ethics Content</h1>
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

            <!-- Add/Edit Form -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2><?php echo $editItem ? 'Edit Ethics Content' : 'Add New Ethics Content'; ?></h2>
                </div>
                <div class="admin-card-body">
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/ethics.php">
                        <?php if ($editItem): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editItem['id']; ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="title">Title <span class="required">*</span></label>
                                <input type="text" id="title" name="title" required value="<?php echo $editItem ? htmlspecialchars($editItem['title']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="section">Section</label>
                                <select id="section" name="section">
                                    <option value="traditions" <?php echo ($editItem && $editItem['section'] === 'traditions') ? 'selected' : ''; ?>>Traditions</option>
                                    <option value="moral_teachings" <?php echo ($editItem && $editItem['section'] === 'moral_teachings') ? 'selected' : ''; ?>>Moral Teachings</option>
                                    <option value="history" <?php echo ($editItem && $editItem['section'] === 'history') ? 'selected' : ''; ?>>History</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="body">Body <span class="required">*</span></label>
                            <textarea id="body" name="body" rows="6" required><?php echo $editItem ? htmlspecialchars($editItem['body']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="sort_order">Sort Order</label>
                            <input type="number" id="sort_order" name="sort_order" value="<?php echo $editItem ? $editItem['sort_order'] : 0; ?>" min="0">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $editItem ? 'Update' : 'Add'; ?> Content</button>
                            <?php if ($editItem): ?>
                            <a href="<?php echo APP_URL; ?>/admin/ethics.php" class="btn btn-outline">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ethics Content Table -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>All Ethics Content (<?php echo count($ethicsItems); ?>)</h2>
                </div>
                <div class="admin-card-body">
                    <?php if (empty($ethicsItems)): ?>
                    <p class="text-muted">No ethics content found.</p>
                    <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Section</th>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ethicsItems as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo ucwords(str_replace('_', ' ', $item['section'])); ?></td>
                                <td><?php echo $item['sort_order']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($item['created_at'])); ?></td>
                                <td class="actions-cell">
                                    <a href="<?php echo APP_URL; ?>/admin/ethics.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo APP_URL; ?>/admin/ethics.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this content?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
</body>
</html>
