<?php
/**
 * Cultural Heritage Website - Ethics & Values Page
 */
require_once __DIR__ . '/config/app.php';

// Get ethics content by section
$traditions = getEthicsContent($conn, 'traditions');
$moralTeachings = getEthicsContent($conn, 'moral_teachings');
$history = getEthicsContent($conn, 'history');

include INCLUDES_PATH . 'header.php';
?>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="page-banner-overlay">
            <h1>Ethics & Values</h1>
            <p>Discover the traditions, moral teachings, and history that define our culture</p>
        </div>
    </section>

    <!-- Ethics Content -->
    <section class="section ethics-section">
        <div class="container">

            <!-- Traditions Section -->
            <div class="ethics-category">
                <div class="ethics-category-header">
                    <h2><i class="fas fa-hands-praying"></i> Traditions</h2>
                    <p>Time-honored customs and practices that connect us to our ancestors</p>
                </div>
                <div class="accordion" id="traditionsAccordion">
                    <?php if (!empty($traditions)): ?>
                        <?php foreach ($traditions as $index => $item): ?>
                            <div class="accordion-item">
                                <button class="accordion-header <?php echo $index === 0 ? 'active' : ''; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                    <span><?php echo htmlspecialchars($item['title']); ?></span>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </button>
                                <div class="accordion-body" <?php echo $index === 0 ? 'style="max-height: 500px;"' : ''; ?>>
                                    <div class="accordion-content">
                                        <p><?php echo nl2br(htmlspecialchars($item['body'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-content">No traditions content available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Moral Teachings Section -->
            <div class="ethics-category">
                <div class="ethics-category-header">
                    <h2><i class="fas fa-balance-scale"></i> Moral Teachings</h2>
                    <p>Ethical principles passed down through generations</p>
                </div>
                <div class="accordion" id="moralAccordion">
                    <?php if (!empty($moralTeachings)): ?>
                        <?php foreach ($moralTeachings as $index => $item): ?>
                            <div class="accordion-item">
                                <button class="accordion-header <?php echo $index === 0 ? 'active' : ''; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                    <span><?php echo htmlspecialchars($item['title']); ?></span>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </button>
                                <div class="accordion-body" <?php echo $index === 0 ? 'style="max-height: 500px;"' : ''; ?>>
                                    <div class="accordion-content">
                                        <p><?php echo nl2br(htmlspecialchars($item['body'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-content">No moral teachings content available.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- History Section -->
            <div class="ethics-category">
                <div class="ethics-category-header">
                    <h2><i class="fas fa-scroll"></i> History</h2>
                    <p>The origins and journey of our cultural heritage</p>
                </div>
                <div class="accordion" id="historyAccordion">
                    <?php if (!empty($history)): ?>
                        <?php foreach ($history as $index => $item): ?>
                            <div class="accordion-item">
                                <button class="accordion-header <?php echo $index === 0 ? 'active' : ''; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                    <span><?php echo htmlspecialchars($item['title']); ?></span>
                                    <i class="fas fa-chevron-down accordion-icon"></i>
                                </button>
                                <div class="accordion-body" <?php echo $index === 0 ? 'style="max-height: 500px;"' : ''; ?>>
                                    <div class="accordion-content">
                                        <p><?php echo nl2br(htmlspecialchars($item['body'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-content">No history content available.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

<?php include INCLUDES_PATH . 'footer.php'; ?>
