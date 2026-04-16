<?php
$pageTitle = 'Home - Cultural Heritage';
require_once __DIR__ . '/config/app.php';

$featuredGallery = getGalleryItems(null, 6, true);
$latestVideos = getVideos(null, 3);

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-overlay">
        <div class="hero-content">
            <h1>Welcome to Our Cultural Heritage</h1>
            <p>Discover the beauty of our traditions, values, and history that have been passed down through generations.</p>
            <a href="<?php echo APP_URL; ?>/gallery.php" class="btn btn-primary btn-lg">Explore Gallery</a>
        </div>
    </div>
</section>

<!-- About Culture Section -->
<section class="section about-section">
    <div class="container">
        <div class="section-header">
            <h2>About Our Culture</h2>
            <div class="section-divider"></div>
        </div>
        <div class="about-grid">
            <div class="about-image">
                <div class="placeholder-image">
                    <i class="fas fa-users"></i>
                    <span>Cultural Image</span>
                </div>
            </div>
            <div class="about-text">
                <h3>A Living Heritage</h3>
                <p>Our culture is a tapestry woven from centuries of tradition, wisdom, and community spirit. From the rhythmic beats of our traditional drums to the intricate patterns of our ceremonial garments, every element tells a story of resilience and beauty.</p>
                <p>We are dedicated to preserving these traditions while embracing the future. Through education, celebration, and community engagement, we ensure that our cultural identity remains vibrant and relevant for generations to come.</p>
                <a href="<?php echo APP_URL; ?>/ethics.php" class="btn btn-outline">Learn About Our Values</a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Gallery Section -->
<?php if (!empty($featuredGallery)): ?>
<section class="section gallery-preview-section">
    <div class="container">
        <div class="section-header">
            <h2>Featured Gallery</h2>
            <div class="section-divider"></div>
            <p>A glimpse into our cultural events, traditional dress, and activities.</p>
        </div>
        <div class="gallery-grid">
            <?php foreach ($featuredGallery as $item): ?>
            <div class="gallery-card">
                <div class="gallery-card-image">
                    <div class="placeholder-image">
                        <i class="fas fa-image"></i>
                        <span><?php echo htmlspecialchars($item['title']); ?></span>
                    </div>
                    <div class="gallery-card-overlay">
                        <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                        <span class="gallery-card-category"><?php echo htmlspecialchars($item['category_name']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="<?php echo APP_URL; ?>/gallery.php" class="btn btn-primary">View Full Gallery</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Videos Section -->
<?php if (!empty($latestVideos)): ?>
<section class="section videos-preview-section">
    <div class="container">
        <div class="section-header">
            <h2>Latest Videos</h2>
            <div class="section-divider"></div>
            <p>Watch cultural dances, interviews, and community programs.</p>
        </div>
        <div class="video-grid">
            <?php foreach ($latestVideos as $video): ?>
            <div class="video-card" data-video-url="<?php echo htmlspecialchars($video['video_url']); ?>">
                <div class="video-card-thumb">
                    <div class="placeholder-image">
                        <i class="fas fa-play-circle"></i>
                        <span><?php echo htmlspecialchars($video['title']); ?></span>
                    </div>
                    <div class="play-overlay"><i class="fas fa-play"></i></div>
                </div>
                <div class="video-card-info">
                    <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                    <p><?php echo htmlspecialchars(substr($video['description'], 0, 100)); ?>...</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-cta">
            <a href="<?php echo APP_URL; ?>/videos.php" class="btn btn-primary">View All Videos</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Values Teaser -->
<section class="section values-teaser">
    <div class="container">
        <div class="values-teaser-content">
            <h2>Our Values Define Us</h2>
            <p>Discover the traditions, moral teachings, and history that form the foundation of our cultural identity.</p>
            <a href="<?php echo APP_URL; ?>/ethics.php" class="btn btn-primary btn-lg">Explore Our Values</a>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
