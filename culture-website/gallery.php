<?php
$pageTitle = 'Gallery - Cultural Heritage';
require_once __DIR__ . '/config/app.php';

$filter = isset($_GET['cat']) ? sanitize($_GET['cat']) : null;
$validFilters = ['events', 'traditional_dress', 'activities'];
if ($filter && !in_array($filter, $validFilters)) {
    $filter = null;
}

$galleryItems = getGalleryItems($filter);

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="page-banner-overlay">
        <h1><i class="fas fa-camera"></i> Gallery</h1>
        <p>Explore our cultural events, traditional dress, and community activities.</p>
    </div>
</section>

<!-- Gallery Content -->
<section class="section">
    <div class="container">
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="<?php echo APP_URL; ?>/gallery.php" class="filter-tab <?php echo !$filter ? 'active' : ''; ?>">All</a>
            <a href="<?php echo APP_URL; ?>/gallery.php?cat=events" class="filter-tab <?php echo $filter === 'events' ? 'active' : ''; ?>">Cultural Events</a>
            <a href="<?php echo APP_URL; ?>/gallery.php?cat=traditional_dress" class="filter-tab <?php echo $filter === 'traditional_dress' ? 'active' : ''; ?>">Traditional Dress</a>
            <a href="<?php echo APP_URL; ?>/gallery.php?cat=activities" class="filter-tab <?php echo $filter === 'activities' ? 'active' : ''; ?>">Activities</a>
        </div>

        <?php if (empty($galleryItems)): ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <h3>No gallery items found</h3>
            <p>Check back later for new additions.</p>
        </div>
        <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($galleryItems as $index => $item): ?>
            <div class="gallery-card" data-index="<?php echo $index; ?>">
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
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox Modal -->
<div class="lightbox-modal" id="lightboxModal">
    <button class="lightbox-close" id="lightboxClose">&times;</button>
    <button class="lightbox-prev" id="lightboxPrev"><i class="fas fa-chevron-left"></i></button>
    <button class="lightbox-next" id="lightboxNext"><i class="fas fa-chevron-right"></i></button>
    <div class="lightbox-content">
        <div class="lightbox-image" id="lightboxImage">
            <div class="placeholder-image placeholder-large">
                <i class="fas fa-image"></i>
                <span id="lightboxTitle"></span>
            </div>
        </div>
        <div class="lightbox-caption">
            <h3 id="lightboxCaptionTitle"></h3>
            <p id="lightboxCaptionDesc"></p>
        </div>
    </div>
</div>

<script>
// Pass gallery data to JS for lightbox
var galleryData = <?php echo json_encode(array_map(function($item) {
    return [
        'title' => $item['title'],
        'description' => $item['description'],
        'image' => $item['image'],
        'category' => $item['category_name']
    ];
}, $galleryItems)); ?>;
</script>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
