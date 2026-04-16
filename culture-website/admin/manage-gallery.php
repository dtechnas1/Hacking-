<?php
/**
 * Cultural Heritage Website - Manage Gallery
 */
require_once __DIR__ . '/auth.php';

$successMsg = '';
$errorMsg = '';

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM gallery_items WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        $successMsg = 'Gallery item deleted successfully.';
    } else {
        $errorMsg = 'Failed to delete gallery item.';
    }
}

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = isset($_POST['edit_id']) ? (int) $_POST['edit_id'] : 0;
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $categoryLabel = sanitize($_POST['category_label'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $imagePath = sanitize($_POST['image_path'] ?? '');

    // Get category_id based on category_label
    $catTypeMap = ['events' => 1, 'traditional_dress' => 2, 'activities' => 3];
    $categoryId = $catTypeMap[$categoryLabel] ?? 1;

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = ROOT_PATH . 'uploads/gallery/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/gallery/' . $fileName;
        }
    }

    if (empty($title) || empty($categoryLabel)) {
        $errorMsg = 'Title and category are required.';
    } else {
        if ($editId > 0) {
            // Update
            $stmt = $conn->prepare("UPDATE gallery_items SET category_id = ?, title = ?, description = ?, image_path = ?, category_label = ?, is_featured = ? WHERE id = ?");
            $stmt->bind_param("issssii", $categoryId, $title, $description, $imagePath, $categoryLabel, $isFeatured, $editId);
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO gallery_items (category_id, title, description, image_path, category_label, is_featured) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssi", $categoryId, $title, $description, $imagePath, $categoryLabel, $isFeatured);
        }

        if ($stmt->execute()) {
            $successMsg = $editId > 0 ? 'Gallery item updated successfully.' : 'Gallery item added successfully.';
        } else {
            $errorMsg = 'An error occurred. Please try again.';
        }
    }
}

// Get editing item if requested
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM gallery_items WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editItem = $stmt->get_result()->fetch_assoc();
}

// Get all gallery items
$galleryItems = getGalleryItems($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Manage Gallery</title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <h1><i class="fas fa-cog"></i> Admin Panel</h1>
        <nav class="admin-nav">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage-gallery.php" class="active"><i class="fas fa-images"></i> Gallery</a>
            <a href="manage-videos.php"><i class="fas fa-video"></i> Videos</a>
            <a href="manage-ethics.php"><i class="fas fa-book-open"></i> Ethics</a>
            <a href="manage-messages.php"><i class="fas fa-envelope"></i> Messages</a>
            <a href="?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="admin-container">
        <h2><i class="fas fa-images"></i> Manage Gallery</h2>

        <?php if ($successMsg): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $successMsg; ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $errorMsg; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="admin-form">
            <h3><?php echo $editItem ? 'Edit Gallery Item' : 'Add New Gallery Item'; ?></h3>
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
                    <label for="category_label">Category</label>
                    <select id="category_label" name="category_label" style="width:100%; padding:12px 16px; border:2px solid var(--gray-300); border-radius:var(--radius); font-size:1rem;" required>
                        <option value="events" <?php echo ($editItem && $editItem['category_label'] === 'events') ? 'selected' : ''; ?>>Cultural Events</option>
                        <option value="traditional_dress" <?php echo ($editItem && $editItem['category_label'] === 'traditional_dress') ? 'selected' : ''; ?>>Traditional Dress</option>
                        <option value="activities" <?php echo ($editItem && $editItem['category_label'] === 'activities') ? 'selected' : ''; ?>>Activities</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="image">Upload Image</label>
                    <input type="file" id="image" name="image" accept="image/*" style="width:100%; padding:10px; border:2px solid var(--gray-300); border-radius:var(--radius);">
                    <?php if ($editItem && $editItem['image_path']): ?>
                        <small>Current: <?php echo htmlspecialchars($editItem['image_path']); ?></small>
                        <input type="hidden" name="image_path" value="<?php echo htmlspecialchars($editItem['image_path']); ?>">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_featured" value="1" <?php echo ($editItem && $editItem['is_featured']) ? 'checked' : ''; ?>>
                        Featured Item
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $editItem ? 'Update Item' : 'Add Item'; ?>
                </button>
                <?php if ($editItem): ?>
                    <a href="manage-gallery.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Gallery Items Table -->
        <h3 style="margin-top: 30px;">All Gallery Items</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Featured</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($galleryItems)): ?>
                    <?php foreach ($galleryItems as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo htmlspecialchars($item['category_label']); ?></td>
                            <td><?php echo $item['is_featured'] ? '<span class="badge badge-unread">Yes</span>' : 'No'; ?></td>
                            <td><?php echo $item['created_at']; ?></td>
                            <td>
                                <a href="manage-gallery.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <a href="manage-gallery.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No gallery items found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
