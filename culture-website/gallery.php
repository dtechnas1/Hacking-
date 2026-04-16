<?php
/**
 * Cultural Heritage Website - Gallery Page
 */
require_once __DIR__ . '/config/app.php';

// Get filter category from URL
$filterCat = isset($_GET['cat']) ? sanitize($_GET['cat']) : null;

// Validate category
$validCategories = ['events', 'traditional_dress', 'activities'];
if ($filterCat && !in_array($filterCat, $validCategories)) {
    $filterCat = null;
}

// Get gallery items
$galleryItems = getGalleryItems($conn, $filterCat);

include INCLUDES_PATH . 'header.php';
?>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="page-banner-overlay">
            <h1>Gallery</h1>
            <p>Explore our collection of cultural photographs and images</p>
        </div>
    </section>

    <!-- Gallery Content -->
    <section class="section gallery-section">
        <div class="container">
            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="<?php echo APP_URL; ?>/gallery.php" class="filter-tab <?php echo !$filterCat ? 'active' : ''; ?>">All</a>
                <a href="<?php echo APP_URL; ?>/gallery.php?cat=events" class="filter-tab <?php echo $filterCat === 'events' ? 'active' : ''; ?>">Cultural Events</a>
                <a href="<?php echo APP_URL; ?>/gallery.php?cat=traditional_dress" class="filter-tab <?php echo $filterCat === 'traditional_dress' ? 'active' : ''; ?>">Traditional Dress</a>
                <a href="<?php echo APP_URL; ?>/gallery.php?cat=activities" class="filter-tab <?php echo $filterCat === 'activities' ? 'active' : ''; ?>">Activities</a>
            </div>

            <!-- Gallery Grid -->
            <div class="gallery-grid gallery-full">
                <?php if (!empty($galleryItems)): ?>
                    <?php foreach ($galleryItems as $index => $item): ?>
                        <div class="gallery-card" data-index="<?php echo $index; ?>" data-category="<?php echo htmlspecialchars($item['category_label']); ?>">
                            <div class="gallery-image lightbox-trigger" data-title="<?php echo htmlspecialchars($item['title']); ?>" data-description="<?php echo htmlspecialchars($item['description']); ?>">
                                <div class="image-placeholder">
                                    <i class="fas fa-image fa-2x"></i>
                                    <p><?php echo htmlspecialchars($item['title']); ?></p>
                                </div>
                                <div class="gallery-overlay">
                                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <p><?php echo htmlspecialchars(substr($item['description'], 0, 80)); ?>...</p>
                                    <span class="gallery-zoom"><i class="fas fa-search-plus"></i></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-content-wrapper">
                        <p class="no-content"><i class="fas fa-images"></i> No gallery items found for this category.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div class="lightbox-modal" id="lightboxModal">
        <div class="lightbox-content">
            <button class="lightbox-close" id="lightboxClose" aria-label="Close lightbox">&times;</button>
            <button class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="Previous image"><i class="fas fa-chevron-left"></i></button>
            <div class="lightbox-image-wrapper">
                <div class="lightbox-placeholder">
                    <i class="fas fa-image fa-4x"></i>
                    <h3 id="lightboxTitle"></h3>
                    <p id="lightboxDescription"></p>
                </div>
            </div>
            <button class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="Next image"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

<?php include INCLUDES_PATH . 'footer.php'; ?>
