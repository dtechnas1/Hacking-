<?php
$pageTitle = 'Login - WhatsMater';
require_once __DIR__ . '/config/app.php';

if (isLoggedIn()) {
    redirect(APP_URL . '/index.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = sanitize($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $errors[] = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'");
        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['profile_pic'] = $user['profile_pic'];

                // Update online status
                $stmt = $conn->prepare("UPDATE users SET is_online = 1, last_seen = NOW() WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    redirect(APP_URL . '/admin/dashboard.php');
                } else {
                    redirect(APP_URL . '/index.php');
                }
            } else {
                $errors[] = 'Invalid password.';
            }
        } else {
            $errors[] = 'Account not found or suspended.';
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
            <p class="auth-tagline">WhatsMater helps you connect and share with the people in your life.</p>
        </div>
        <div class="auth-right">
            <div class="auth-card">
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <input type="text" name="login" placeholder="Email or Username" value="<?php echo htmlspecialchars($login ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Log In</button>
                    <div class="auth-links">
                        <a href="#">Forgot Password?</a>
                    </div>
                </form>
                <hr>
                <div class="auth-switch">
                    <a href="<?php echo APP_URL; ?>/register.php" class="btn btn-success">Create New Account</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
