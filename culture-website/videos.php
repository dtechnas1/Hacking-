<?php
/**
 * Cultural Heritage Website - Videos Page
 */
require_once __DIR__ . '/config/app.php';

// Get filter category from URL
$filterCat = isset($_GET['cat']) ? sanitize($_GET['cat']) : null;

// Validate category
$validCategories = ['dance', 'interviews', 'programs'];
if ($filterCat && !in_array($filterCat, $validCategories)) {
    $filterCat = null;
}

// Get videos
$videoItems = getVideos($conn, $filterCat);

include INCLUDES_PATH . 'header.php';
?>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="page-banner-overlay">
            <h1>Videos</h1>
            <p>Watch cultural performances, interviews, and educational programs</p>
        </div>
    </section>

    <!-- Videos Content -->
    <section class="section videos-section">
        <div class="container">
            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="<?php echo APP_URL; ?>/videos.php" class="filter-tab <?php echo !$filterCat ? 'active' : ''; ?>">All</a>
                <a href="<?php echo APP_URL; ?>/videos.php?cat=dance" class="filter-tab <?php echo $filterCat === 'dance' ? 'active' : ''; ?>">Cultural Dance</a>
                <a href="<?php echo APP_URL; ?>/videos.php?cat=interviews" class="filter-tab <?php echo $filterCat === 'interviews' ? 'active' : ''; ?>">Interviews</a>
                <a href="<?php echo APP_URL; ?>/videos.php?cat=programs" class="filter-tab <?php echo $filterCat === 'programs' ? 'active' : ''; ?>">Programs</a>
            </div>

            <!-- Video Grid -->
            <div class="video-grid video-full">
                <?php if (!empty($videoItems)): ?>
                    <?php foreach ($videoItems as $video): ?>
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
                                <span class="video-category"><?php echo htmlspecialchars($video['category_name']); ?></span>
                                <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                                <p><?php echo htmlspecialchars($video['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content-wrapper">
                        <p class="no-content"><i class="fas fa-video"></i> No videos found for this category.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Video Modal -->
    <div class="video-modal" id="videoModal">
        <div class="video-modal-content">
            <button class="video-modal-close" id="videoModalClose" aria-label="Close video">&times;</button>
            <div class="video-modal-body">
                <iframe id="videoIframe" src="" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
            </div>
        </div>
    </div>

<?php include INCLUDES_PATH . 'footer.php'; ?>
