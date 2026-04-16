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
    <title><?php echo $pageTitle ?? APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="<?php echo APP_URL; ?>/index.php" class="nav-logo">
                <i class="fas fa-landmark"></i>
                <span><?php echo APP_NAME; ?></span>
            </a>

            <button class="nav-hamburger" id="navHamburger" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo APP_URL; ?>/index.php" class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?php echo APP_URL; ?>/gallery.php" class="nav-link <?php echo $currentPage === 'gallery.php' ? 'active' : ''; ?>"><i class="fas fa-camera"></i> Gallery</a></li>
                <li><a href="<?php echo APP_URL; ?>/videos.php" class="nav-link <?php echo $currentPage === 'videos.php' ? 'active' : ''; ?>"><i class="fas fa-video"></i> Videos</a></li>
                <li><a href="<?php echo APP_URL; ?>/ethics.php" class="nav-link <?php echo $currentPage === 'ethics.php' ? 'active' : ''; ?>"><i class="fas fa-scroll"></i> Ethics & Values</a></li>
                <li><a href="<?php echo APP_URL; ?>/contact.php" class="nav-link <?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
