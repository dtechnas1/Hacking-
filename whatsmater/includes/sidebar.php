<?php if (isLoggedIn()): ?>
<aside class="sidebar-left">
    <div class="sidebar-menu">
        <a href="<?php echo APP_URL; ?>/user/profile.php?id=<?php echo getCurrentUserId(); ?>" class="sidebar-item">
            <img src="<?php echo getProfilePic($currentUser['profile_pic'] ?? ''); ?>" alt="" class="sidebar-avatar">
            <span><?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?></span>
        </a>
        <a href="<?php echo APP_URL; ?>/index.php" class="sidebar-item">
            <i class="fas fa-newspaper"></i> <span>News Feed</span>
        </a>
        <a href="<?php echo APP_URL; ?>/user/messages.php" class="sidebar-item">
            <i class="fas fa-comment-dots"></i> <span>Messenger</span>
            <?php if ($unreadMsgs > 0): ?><span class="sidebar-badge"><?php echo $unreadMsgs; ?></span><?php endif; ?>
        </a>
        <a href="<?php echo APP_URL; ?>/user/friends.php" class="sidebar-item">
            <i class="fas fa-user-friends"></i> <span>Friends</span>
            <?php if ($friendReqs > 0): ?><span class="sidebar-badge"><?php echo $friendReqs; ?></span><?php endif; ?>
        </a>
        <a href="<?php echo APP_URL; ?>/user/notifications.php" class="sidebar-item">
            <i class="fas fa-bell"></i> <span>Notifications</span>
            <?php if ($unreadNotifs > 0): ?><span class="sidebar-badge"><?php echo $unreadNotifs; ?></span><?php endif; ?>
        </a>
        <a href="<?php echo APP_URL; ?>/user/settings.php" class="sidebar-item">
            <i class="fas fa-cog"></i> <span>Settings</span>
        </a>
        <?php if (isAdmin()): ?>
        <hr>
        <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="sidebar-item">
            <i class="fas fa-shield-alt"></i> <span>Admin Panel</span>
        </a>
        <?php endif; ?>
    </div>
</aside>
<?php endif; ?>
