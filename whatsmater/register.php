<?php
$pageTitle = 'Sign Up - WhatsMater';
require_once __DIR__ . '/config/app.php';

if (isLoggedIn()) {
    redirect(APP_URL . '/index.php');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $gender = sanitize($_POST['gender'] ?? '');
    $dob = sanitize($_POST['dob'] ?? '');

    // Validation
    if (empty($full_name)) $errors[] = 'Full name is required.';
    if (empty($username)) $errors[] = 'Username is required.';
    if (strlen($username) < 3) $errors[] = 'Username must be at least 3 characters.';
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) $errors[] = 'Username can only contain letters, numbers, and underscores.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        // Check if username or email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = 'Username or email already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, gender, date_of_birth) VALUES (?, ?, ?, ?, ?, ?)");
            $dob_val = !empty($dob) ? $dob : null;
            $gender_val = !empty($gender) ? $gender : null;
            $stmt->bind_param("ssssss", $full_name, $username, $email, $hashed_password, $gender_val, $dob_val);

            if ($stmt->execute()) {
                $success = 'Account created successfully! You can now login.';
            } else {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-left">
            <h1 class="auth-logo"><?php echo APP_NAME; ?></h1>
            <p class="auth-tagline">Connect with friends and the world around you on WhatsMater.</p>
        </div>
        <div class="auth-right">
            <div class="auth-card">
                <h2>Create a new account</h2>
                <p class="auth-subtitle">It's quick and easy.</p>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <p><?php echo $success; ?></p>
                    <a href="<?php echo APP_URL; ?>/login.php">Click here to login</a>
                </div>
                <?php endif; ?>

                <form method="POST" action="" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="full_name" placeholder="Full Name" value="<?php echo htmlspecialchars($full_name ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="New Password" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" value="<?php echo htmlspecialchars($dob ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Select</option>
                                <option value="male" <?php echo (($gender ?? '') === 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo (($gender ?? '') === 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo (($gender ?? '') === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <p class="auth-terms">By clicking Sign Up, you agree to our Terms, Privacy Policy and Cookies Policy.</p>
                    <button type="submit" class="btn btn-success btn-block">Sign Up</button>
                </form>
                <div class="auth-switch">
                    <a href="<?php echo APP_URL; ?>/login.php">Already have an account?</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
