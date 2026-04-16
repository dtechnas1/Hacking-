<?php
if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/app.php';
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - <?php echo ucfirst(str_replace('.php', '', $currentPage)); ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="<?php echo APP_URL; ?>/index.php" class="nav-logo">
                <i class="fas fa-landmark"></i>
                <?php echo APP_NAME; ?>
            </a>

            <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>

            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo APP_URL; ?>/index.php" class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Home
                </a></li>
                <li><a href="<?php echo APP_URL; ?>/gallery.php" class="nav-link <?php echo $currentPage === 'gallery.php' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i> Gallery
                </a></li>
                <li><a href="<?php echo APP_URL; ?>/videos.php" class="nav-link <?php echo $currentPage === 'videos.php' ? 'active' : ''; ?>">
                    <i class="fas fa-video"></i> Videos
                </a></li>
                <li><a href="<?php echo APP_URL; ?>/ethics.php" class="nav-link <?php echo $currentPage === 'ethics.php' ? 'active' : ''; ?>">
                    <i class="fas fa-book-open"></i> Ethics
                </a></li>
                <li><a href="<?php echo APP_URL; ?>/contact.php" class="nav-link <?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Contact
                </a></li>
            </ul>
        </div>
    </nav>
