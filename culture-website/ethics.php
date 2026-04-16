<?php
$pageTitle = 'Ethics & Values - Cultural Heritage';
require_once __DIR__ . '/config/app.php';

$traditions = getEthicsContent('traditions');
$moralTeachings = getEthicsContent('moral_teachings');
$history = getEthicsContent('history');

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="page-banner-overlay">
        <h1><i class="fas fa-scroll"></i> Ethics & Values</h1>
        <p>Discover the traditions, moral teachings, and history that define our cultural identity.</p>
    </div>
</section>

<!-- Ethics Navigation -->
<section class="section ethics-nav-section">
    <div class="container">
        <div class="ethics-nav">
            <a href="#traditions" class="ethics-nav-link active"><i class="fas fa-hands-praying"></i> Traditions</a>
            <a href="#moral-teachings" class="ethics-nav-link"><i class="fas fa-book-open"></i> Moral Teachings</a>
            <a href="#history" class="ethics-nav-link"><i class="fas fa-landmark"></i> History</a>
        </div>
    </div>
</section>

<!-- Traditions Section -->
<section class="section ethics-section" id="traditions">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-hands-praying"></i> Our Traditions</h2>
            <div class="section-divider"></div>
            <p>The sacred customs and practices that have been preserved through generations.</p>
        </div>
        <?php if (!empty($traditions)): ?>
        <div class="ethics-grid">
            <?php foreach ($traditions as $item): ?>
            <div class="ethics-card">
                <div class="ethics-card-icon">
                    <i class="fas fa-feather-pointed"></i>
                </div>
                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                <p><?php echo htmlspecialchars($item['body']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-scroll"></i>
            <h3>Content coming soon</h3>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Moral Teachings Section -->
<section class="section ethics-section" id="moral-teachings">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-book-open"></i> Moral Teachings</h2>
            <div class="section-divider"></div>
            <p>The principles and values that guide our daily lives and interactions.</p>
        </div>
        <?php if (!empty($moralTeachings)): ?>
        <div class="ethics-grid">
            <?php foreach ($moralTeachings as $item): ?>
            <div class="ethics-card">
                <div class="ethics-card-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                <p><?php echo htmlspecialchars($item['body']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-book"></i>
            <h3>Content coming soon</h3>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- History Section -->
<section class="section ethics-section" id="history">
    <div class="container">
        <div class="section-header">
            <h2><i class="fas fa-landmark"></i> Our History</h2>
            <div class="section-divider"></div>
            <p>The journey of our people from ancient times to the present day.</p>
        </div>
        <?php if (!empty($history)): ?>
        <div class="timeline">
            <?php foreach ($history as $index => $item): ?>
            <div class="timeline-item <?php echo $index % 2 === 0 ? 'timeline-left' : 'timeline-right'; ?>">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p><?php echo htmlspecialchars($item['body']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <h3>Content coming soon</h3>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
