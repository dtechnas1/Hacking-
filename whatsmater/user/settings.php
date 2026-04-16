<?php
$pageTitle = 'Settings - WhatsMater';
require_once __DIR__ . '/../config/app.php';

if (!isLoggedIn()) {
    redirect(APP_URL . '/login.php');
}

$userId = getCurrentUserId();
$user = getCurrentUser();
$errors = [];
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section = $_POST['section'] ?? '';

    if ($section === 'profile') {
        $full_name = sanitize($_POST['full_name'] ?? '');
        $bio = sanitize($_POST['bio'] ?? '');
        $gender = sanitize($_POST['gender'] ?? '');
        $dob = sanitize($_POST['dob'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $city = sanitize($_POST['city'] ?? '');
        $country = sanitize($_POST['country'] ?? '');
        $website = sanitize($_POST['website'] ?? '');

        if (empty($full_name)) {
            $errors[] = 'Full name is required.';
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name=?, bio=?, gender=?, date_of_birth=?, phone=?, city=?, country=?, website=? WHERE id=?");
            $dob_val = !empty($dob) ? $dob : null;
            $gender_val = !empty($gender) ? $gender : null;
            $stmt->bind_param("ssssssssi", $full_name, $bio, $gender_val, $dob_val, $phone, $city, $country, $website, $userId);
            if ($stmt->execute()) {
                $success = 'Profile updated successfully!';
                $user = getCurrentUser(); // Refresh
            } else {
                $errors[] = 'Update failed.';
            }
        }
    } elseif ($section === 'password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'New password must be at least 6 characters.';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed, $userId);
            if ($stmt->execute()) {
                $success = 'Password changed successfully!';
            } else {
                $errors[] = 'Password update failed.';
            }
        }
    } elseif ($section === 'avatar') {
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_pic'];
            if (in_array($file['type'], ALLOWED_IMAGE_TYPES) && $file['size'] <= MAX_FILE_SIZE) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], UPLOADS_PATH . 'profiles/' . $filename)) {
                    $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                    $stmt->bind_param("si", $filename, $userId);
                    $stmt->execute();
                    $_SESSION['profile_pic'] = $filename;
                    $success = 'Profile picture updated!';
                    $user = getCurrentUser();
                }
            } else {
                $errors[] = 'Invalid file. Max 5MB, JPEG/PNG/GIF only.';
            }
        }
    }
}

require_once INCLUDES_PATH . 'header.php';
?>

<div class="content-area settings-page">
    <div class="settings-container">
        <h2><i class="fas fa-cog"></i> Settings</h2>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?><p><?php echo $e; ?></p><?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success"><p><?php echo $success; ?></p></div>
        <?php endif; ?>

        <!-- Profile Picture -->
        <div class="card settings-card">
            <h3>Profile Picture</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="section" value="avatar">
                <div class="avatar-upload">
                    <img src="<?php echo getProfilePic($user['profile_pic']); ?>" alt="" class="avatar-lg">
                    <div>
                        <input type="file" name="profile_pic" accept="image/*" required>
                        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Profile Info -->
        <div class="card settings-card">
            <h3>Profile Information</h3>
            <form method="POST">
                <input type="hidden" name="section" value="profile">
                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" rows="3" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender">
                            <option value="">Select</option>
                            <option value="male" <?php echo ($user['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($user['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo ($user['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Website</label>
                    <input type="url" name="website" value="<?php echo htmlspecialchars($user['website'] ?? ''); ?>" placeholder="https://">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="card settings-card">
            <h3>Change Password</h3>
            <form method="POST">
                <input type="hidden" name="section" value="password">
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>

        <!-- Delete Account -->
        <div class="card settings-card">
            <h3>Danger Zone</h3>
            <p class="text-muted">Once you delete your account, there is no going back.</p>
            <a href="#" class="btn btn-danger" onclick="alert('Please contact admin to delete your account.')">Delete My Account</a>
        </div>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
