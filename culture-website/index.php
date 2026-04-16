<?php
/**
 * Cultural Heritage Website - Home Page
 */
require_once __DIR__ . '/config/app.php';

// Get featured gallery items
$featuredItems = [];
$stmt = $conn->prepare("SELECT g.*, c.name AS category_name FROM gallery_items g LEFT JOIN categories c ON g.category_id = c.id WHERE g.is_featured = 1 ORDER BY g.created_at DESC LIMIT 6");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $featuredItems[] = $row;
}

// Get latest videos
$latestVideos = getVideos($conn, null, 3);

include INCLUDES_PATH . 'header.php';
?>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-overlay">
            <div class="hero-content">
                <h1>Welcome to Our Cultural Heritage</h1>
                <p>Discover the rich traditions, values, and history that define our community. Explore centuries of wisdom, artistry, and cultural expression passed down through generations.</p>
                <a href="<?php echo APP_URL; ?>/gallery.php" class="btn btn-primary">Explore Gallery</a>
                <a href="<?php echo APP_URL; ?>/ethics.php" class="btn btn-outline">Our Values</a>
            </div>
        </div>
    </section>

    <!-- About Our Culture Section -->
    <section class="section about-section" id="about">
        <div class="container">
            <div class="section-title">
                <h2>About Our Culture</h2>
                <div class="title-underline"></div>
            </div>
            <div class="about-grid">
                <div class="about-image">
                    <div class="image-placeholder">
                        <i class="fas fa-users fa-3x"></i>
                        <p>Cultural Heritage Image</p>
                    </div>
                </div>
                <div class="about-text">
                    <h3>A Living Legacy</h3>
                    <p>Our cultural heritage is a tapestry woven from centuries of traditions, stories, and shared values. It represents the collective wisdom of our ancestors and continues to guide us in the modern world.</p>
                    <p>From vibrant festivals and traditional ceremonies to the everyday practices that bind our community together, our culture is both a source of pride and a compass for the future.</p>
                    <p>We invite you to explore the many facets of our heritage through our gallery, videos, and writings on ethics and values. Each piece tells a story of resilience, creativity, and the enduring human spirit.</p>
                    <a href="<?php echo APP_URL; ?>/ethics.php" class="btn btn-secondary">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Gallery Section -->
    <section class="section gallery-preview-section">
        <div class="container">
            <div class="section-title">
                <h2>Featured Gallery</h2>
                <div class="title-underline"></div>
            </div>
            <div class="gallery-grid">
                <?php if (!empty($featuredItems)): ?>
                    <?php foreach ($featuredItems as $item): ?>
                        <div class="gallery-card">
                            <div class="gallery-image">
                                <div class="image-placeholder">
                                    <i class="fas fa-image fa-2x"></i>
                                    <p><?php echo htmlspecialchars($item['title']); ?></p>
                                </div>
                                <div class="gallery-overlay">
                                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <p><?php echo htmlspecialchars(substr($item['description'], 0, 80)); ?>...</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-content">No featured gallery items yet.</p>
                <?php endif; ?>
            </div>
            <div class="section-cta">
                <a href="<?php echo APP_URL; ?>/gallery.php" class="btn btn-primary">View Full Gallery</a>
            </div>
        </div>
    </section>

    <!-- Latest Videos Section -->
    <section class="section videos-preview-section">
        <div class="container">
            <div class="section-title">
                <h2>Latest Videos</h2>
                <div class="title-underline"></div>
            </div>
            <div class="video-grid">
                <?php if (!empty($latestVideos)): ?>
                    <?php foreach ($latestVideos as $video): ?>
                        <div class="video-card" data-video-url="<?php echo htmlspecialchars($video['video_url']); ?>">
                            <div class="video-thumbnail">
                                <div class="image-placeholder">
                                    <i class="fas fa-film fa-2x"></i>
                                    <p><?php echo htmlspecialchars($video['title']); ?></p>
                                </div>
                                <div class="play-icon">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                            </div>
                            <div class="video-info">
                                <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                                <p><?php echo htmlspecialchars(substr($video['description'], 0, 100)); ?>...</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-content">No videos available yet.</p>
                <?php endif; ?>
            </div>
            <div class="section-cta">
                <a href="<?php echo APP_URL; ?>/videos.php" class="btn btn-primary">View All Videos</a>
            </div>
        </div>
    </section>

    <!-- Our Values Teaser Section -->
    <section class="section values-teaser-section">
        <div class="container">
            <div class="values-teaser">
                <div class="values-teaser-content">
                    <h2>Our Ethics & Values</h2>
                    <p>Discover the moral teachings, time-honored traditions, and rich history that form the foundation of our cultural identity. These values have guided our community for generations and continue to shape who we are today.</p>
                    <a href="<?php echo APP_URL; ?>/ethics.php" class="btn btn-primary">Explore Our Values</a>
                </div>
            </div>
        </div>
    </section>

<?php include INCLUDES_PATH . 'footer.php'; ?>
