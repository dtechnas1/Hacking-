<?php
$pageTitle = 'Manage Videos - Cultural Heritage';
require_once __DIR__ . '/../config/app.php';

if (!isAdminLoggedIn()) {
    redirect(APP_URL . '/admin/login.php');
}

$success = '';
$error = '';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Video deleted successfully.';
    } else {
        $error = 'Failed to delete video.';
    }
    $stmt->close();
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $video_url = sanitize($_POST['video_url'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $category_label = sanitize($_POST['category_label'] ?? 'dance');
    $thumbnail = sanitize($_POST['thumbnail'] ?? 'placeholder.jpg');
    $edit_id = (int)($_POST['edit_id'] ?? 0);

    if (empty($title) || empty($video_url)) {
        $error = 'Title and Video URL are required.';
    } else {
        if ($edit_id > 0) {
            $stmt = $conn->prepare("UPDATE videos SET title = ?, description = ?, video_url = ?, category_id = ?, category_label = ?, thumbnail = ? WHERE id = ?");
            $stmt->bind_param("sssissi", $title, $description, $video_url, $category_id, $category_label, $thumbnail, $edit_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO videos (title, description, video_url, category_id, category_label, thumbnail) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiss", $title, $description, $video_url, $category_id, $category_label, $thumbnail);
        }

        if ($stmt->execute()) {
            $success = $edit_id > 0 ? 'Video updated successfully.' : 'Video added successfully.';
        } else {
            $error = 'Failed to save video.';
        }
        $stmt->close();
    }
}

// Get all videos
$videos = $conn->query("SELECT v.*, c.name as category_name FROM videos v LEFT JOIN categories c ON v.category_id = c.id ORDER BY v.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Get video categories
$categories = $conn->query("SELECT * FROM categories WHERE type = 'video' ORDER BY name")->fetch_all(MYSQLI_ASSOC);

// Get item for editing
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM videos WHERE id = ?");
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
            <a href="<?php echo APP_URL; ?>/admin/videos.php" class="admin-nav-link active"><i class="fas fa-video"></i> Videos</a>
            <a href="<?php echo APP_URL; ?>/admin/ethics.php" class="admin-nav-link"><i class="fas fa-scroll"></i> Ethics Content</a>
            <a href="<?php echo APP_URL; ?>/admin/messages.php" class="admin-nav-link"><i class="fas fa-envelope"></i> Messages <?php if ($unreadCount > 0): ?><span class="badge"><?php echo $unreadCount; ?></span><?php endif; ?></a>
            <hr>
            <a href="<?php echo APP_URL; ?>/index.php" class="admin-nav-link"><i class="fas fa-globe"></i> View Website</a>
            <a href="<?php echo APP_URL; ?>/admin/logout.php" class="admin-nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-header">
            <h1><i class="fas fa-video"></i> Manage Videos</h1>
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
                    <h2><?php echo $editItem ? 'Edit Video' : 'Add New Video'; ?></h2>
                </div>
                <div class="admin-card-body">
                    <form method="POST" action="<?php echo APP_URL; ?>/admin/videos.php">
                        <?php if ($editItem): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editItem['id']; ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="title">Title <span class="required">*</span></label>
                                <input type="text" id="title" name="title" required value="<?php echo $editItem ? htmlspecialchars($editItem['title']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label for="video_url">Video URL (embed) <span class="required">*</span></label>
                                <input type="url" id="video_url" name="video_url" required placeholder="https://www.youtube.com/embed/..." value="<?php echo $editItem ? htmlspecialchars($editItem['video_url']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3"><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select id="category_id" name="category_id">
                                    <option value="0">-- Select Category --</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($editItem && $editItem['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="category_label">Category Label</label>
                                <select id="category_label" name="category_label">
                                    <option value="dance" <?php echo ($editItem && $editItem['category_label'] === 'dance') ? 'selected' : ''; ?>>Dance</option>
                                    <option value="interviews" <?php echo ($editItem && $editItem['category_label'] === 'interviews') ? 'selected' : ''; ?>>Interviews</option>
                                    <option value="programs" <?php echo ($editItem && $editItem['category_label'] === 'programs') ? 'selected' : ''; ?>>Programs</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="thumbnail">Thumbnail Filename</label>
                            <input type="text" id="thumbnail" name="thumbnail" value="<?php echo $editItem ? htmlspecialchars($editItem['thumbnail']) : 'placeholder.jpg'; ?>">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $editItem ? 'Update' : 'Add'; ?> Video</button>
                            <?php if ($editItem): ?>
                            <a href="<?php echo APP_URL; ?>/admin/videos.php" class="btn btn-outline">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Videos Table -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h2>All Videos (<?php echo count($videos); ?>)</h2>
                </div>
                <div class="admin-card-body">
                    <?php if (empty($videos)): ?>
                    <p class="text-muted">No videos found.</p>
                    <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Label</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($videos as $video): ?>
                            <tr>
                                <td><?php echo $video['id']; ?></td>
                                <td><?php echo htmlspecialchars($video['title']); ?></td>
                                <td><?php echo htmlspecialchars($video['category_name'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($video['category_label']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($video['created_at'])); ?></td>
                                <td class="actions-cell">
                                    <a href="<?php echo APP_URL; ?>/admin/videos.php?edit=<?php echo $video['id']; ?>" class="btn btn-sm btn-outline" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo APP_URL; ?>/admin/videos.php?delete=<?php echo $video['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this video?')"><i class="fas fa-trash"></i></a>
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
