<?php
/**
 * Cultural Heritage Website - Manage Videos
 */
require_once __DIR__ . '/auth.php';

$successMsg = '';
$errorMsg = '';

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $successMsg = 'Video deleted successfully.';
    } else {
        $errorMsg = 'Failed to delete video.';
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : 0;
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $videoUrl = sanitize($_POST['video_url'] ?? '');
    $categoryLabel = sanitize($_POST['category_label'] ?? '');
    $thumbnail = sanitize($_POST['thumbnail'] ?? '');

    // Get category_id based on category_label
    $catTypeMap = ['dance' => 4, 'interviews' => 5, 'programs' => 6];
    $categoryId = $catTypeMap[$categoryLabel] ?? 4;

    // Handle thumbnail upload
    if (isset($_FILES['thumbnail_file']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = ROOT_PATH . 'uploads/videos/';
        $fileName = time() . '_' . basename($_FILES['thumbnail_file']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['thumbnail_file']['tmp_name'], $targetPath)) {
            $thumbnail = 'uploads/videos/' . $fileName;
        }
    }

    if (empty($title) || empty($videoUrl) || empty($categoryLabel)) {
        $errorMsg = 'Title, video URL, and category are required.';
    } else {
        if ($editId > 0) {
            $stmt = $conn->prepare("UPDATE videos SET category_id = ?, title = ?, description = ?, video_url = ?, thumbnail = ?, category_label = ? WHERE id = ?");
            $stmt->bind_param("isssssi", $categoryId, $title, $description, $videoUrl, $thumbnail, $categoryLabel, $editId);
        } else {
            $stmt = $conn->prepare("INSERT INTO videos (category_id, title, description, video_url, thumbnail, category_label) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $categoryId, $title, $description, $videoUrl, $thumbnail, $categoryLabel);
        }

        if ($stmt->execute()) {
            $successMsg = $editId > 0 ? 'Video updated successfully.' : 'Video added successfully.';
        } else {
            $errorMsg = 'An error occurred. Please try again.';
        }
    }
}

// Get editing item
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editItem = $stmt->get_result()->fetch_assoc();
}

// Get all videos
$videoItems = getVideos($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Manage Videos</title>
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
            <a href="manage-videos.php" class="active"><i class="fas fa-video"></i> Videos</a>
            <a href="manage-ethics.php"><i class="fas fa-book-open"></i> Ethics</a>
            <a href="manage-messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="admin-container">
        <h2><i class="fas fa-video"></i> Manage Videos</h2>

        <?php if ($successMsg): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $successMsg; ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="admin-form">
            <h3><?php echo $editItem ? 'Edit Video' : 'Add New Video'; ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editItem): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editItem['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" value="<?php echo $editItem ? htmlspecialchars($editItem['title']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"><?php echo $editItem ? htmlspecialchars($editItem['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="video_url">Video URL (YouTube Embed URL)</label>
                    <input type="url" id="video_url" name="video_url" value="<?php echo $editItem ? htmlspecialchars($editItem['video_url']) : ''; ?>" placeholder="https://www.youtube.com/embed/..." required>
                </div>

                <div class="form-group">
                    <label for="category_label">Category</label>
                    <select id="category_label" name="category_label" style="width:100%; padding:12px 16px; border:2px solid var(--gray-300); border-radius:var(--radius); font-size:1rem;" required>
                        <option value="dance" <?php echo ($editItem && $editItem['category_label'] === 'dance') ? 'selected' : ''; ?>>Cultural Dance</option>
                        <option value="interviews" <?php echo ($editItem && $editItem['category_label'] === 'interviews') ? 'selected' : ''; ?>>Interviews</option>
                        <option value="programs" <?php echo ($editItem && $editItem['category_label'] === 'programs') ? 'selected' : ''; ?>>Programs</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="thumbnail_file">Upload Thumbnail</label>
                    <input type="file" id="thumbnail_file" name="thumbnail_file" accept="image/*" style="width:100%; padding:10px; border:2px solid var(--gray-300); border-radius:var(--radius);">
                    <?php if ($editItem && $editItem['thumbnail']): ?>
                        <small>Current: <?php echo htmlspecialchars($editItem['thumbnail']); ?></small>
                        <input type="hidden" name="thumbnail" value="<?php echo htmlspecialchars($editItem['thumbnail']); ?>">
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $editItem ? 'Update Video' : 'Add Video'; ?>
                </button>
                <?php if ($editItem): ?>
                    <a href="manage-videos.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Videos Table -->
        <h3 style="margin-top: 30px;">All Videos</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Video URL</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($videoItems)): ?>
                    <?php foreach ($videoItems as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo htmlspecialchars($item['category_label']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($item['video_url']); ?>" target="_blank" style="color: var(--primary);">View</a></td>
                            <td><?php echo $item['created_at']; ?></td>
                            <td>
                                <a href="manage-videos.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="manage-videos.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this video?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No videos found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
