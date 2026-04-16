<?php
$pageTitle = 'Videos - Cultural Heritage';
require_once __DIR__ . '/config/app.php';

$filter = isset($_GET['cat']) ? sanitize($_GET['cat']) : null;
$validFilters = ['dance', 'interviews', 'programs'];
if ($filter && !in_array($filter, $validFilters)) {
    $filter = null;
}

$videos = getVideos($filter);

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="page-banner-overlay">
        <h1><i class="fas fa-video"></i> Videos</h1>
        <p>Watch cultural dances, interviews, and community programs.</p>
    </div>
</section>

<!-- Videos Content -->
<section class="section">
    <div class="container">
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="<?php echo APP_URL; ?>/videos.php" class="filter-tab <?php echo !$filter ? 'active' : ''; ?>">All</a>
            <a href="<?php echo APP_URL; ?>/videos.php?cat=dance" class="filter-tab <?php echo $filter === 'dance' ? 'active' : ''; ?>">Cultural Dance</a>
            <a href="<?php echo APP_URL; ?>/videos.php?cat=interviews" class="filter-tab <?php echo $filter === 'interviews' ? 'active' : ''; ?>">Interviews</a>
            <a href="<?php echo APP_URL; ?>/videos.php?cat=programs" class="filter-tab <?php echo $filter === 'programs' ? 'active' : ''; ?>">Programs</a>
        </div>

        <?php if (empty($videos)): ?>
        <div class="empty-state">
            <i class="fas fa-film"></i>
            <h3>No videos found</h3>
            <p>Check back later for new video content.</p>
        </div>
        <?php else: ?>
        <div class="video-grid">
            <?php foreach ($videos as $video): ?>
            <div class="video-card">
                <div class="video-card-thumb" data-video-url="<?php echo htmlspecialchars($video['video_url']); ?>">
                    <div class="placeholder-image">
                        <i class="fas fa-play-circle"></i>
                        <span><?php echo htmlspecialchars($video['title']); ?></span>
                    </div>
                    <div class="play-overlay"><i class="fas fa-play"></i></div>
                </div>
                <div class="video-card-info">
                    <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                    <p><?php echo htmlspecialchars($video['description']); ?></p>
                    <span class="video-category-badge"><?php echo htmlspecialchars($video['category_name']); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Video Player Modal -->
<div class="video-modal" id="videoModal">
    <div class="video-modal-content">
        <button class="video-modal-close" id="videoModalClose">&times;</button>
        <div class="video-modal-player" id="videoModalPlayer">
            <iframe id="videoIframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
