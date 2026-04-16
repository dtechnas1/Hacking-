<?php
if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/app.php';
}
$currentUser = getCurrentUser();
$unreadNotifs = isLoggedIn() ? getUnreadNotificationCount(getCurrentUserId()) : 0;
$unreadMsgs = isLoggedIn() ? getUnreadMessageCount(getCurrentUserId()) : 0;
$friendReqs = isLoggedIn() ? getFriendRequestCount(getCurrentUserId()) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="top-nav">
        <div class="nav-container">
            <div class="nav-left">
                <a href="<?php echo APP_URL; ?>/index.php" class="logo"><?php echo APP_NAME; ?></a>
                <?php if (isLoggedIn()): ?>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search WhatsMater" autocomplete="off">
                    <div id="searchResults" class="search-results"></div>
                </div>
                <?php endif; ?>
            </div>

            <?php if (isLoggedIn()): ?>
            <div class="nav-center">
                <a href="<?php echo APP_URL; ?>/index.php" class="nav-icon <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" title="Home">
                    <i class="fas fa-home"></i>
                </a>
                <a href="<?php echo APP_URL; ?>/user/friends.php" class="nav-icon <?php echo basename($_SERVER['PHP_SELF']) == 'friends.php' ? 'active' : ''; ?>" title="Friends">
                    <i class="fas fa-user-friends"></i>
                    <?php if ($friendReqs > 0): ?><span class="badge"><?php echo $friendReqs; ?></span><?php endif; ?>
                </a>
                <a href="<?php echo APP_URL; ?>/user/messages.php" class="nav-icon <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" title="Messages">
                    <i class="fas fa-comment-dots"></i>
                    <?php if ($unreadMsgs > 0): ?><span class="badge"><?php echo $unreadMsgs; ?></span><?php endif; ?>
                </a>
                <a href="<?php echo APP_URL; ?>/user/notifications.php" class="nav-icon <?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <?php if ($unreadNotifs > 0): ?><span class="badge"><?php echo $unreadNotifs; ?></span><?php endif; ?>
                </a>
            </div>

            <div class="nav-right">
                <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo getCurrentUserId(); ?>" class="nav-profile">
                    <img src="<?php echo getProfilePic($currentUser['profile_pic'] ?? ''); ?>" alt="Profile">
                    <span><?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?></span>
                </a>
                <div class="nav-dropdown">
                    <button class="nav-icon dropdown-toggle" id="menuToggle">
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo getCurrentUserId(); ?>">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                        <a href="<?php echo APP_URL; ?>/user/settings.php">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <?php if (isAdmin()): ?>
                        <a href="<?php echo APP_URL; ?>/admin/dashboard.php">
                            <i class="fas fa-shield-alt"></i> Admin Panel
                        </a>
                        <?php endif; ?>
                        <hr>
                        <a href="<?php echo APP_URL; ?>/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="nav-right">
                <a href="<?php echo APP_URL; ?>/login.php" class="btn btn-primary btn-sm">Login</a>
                <a href="<?php echo APP_URL; ?>/register.php" class="btn btn-success btn-sm">Sign Up</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>
    <div class="main-wrapper">
