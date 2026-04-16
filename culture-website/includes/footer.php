    <!-- Scroll to Top Button -->
    <button id="scrollTopBtn" class="scroll-top-btn" aria-label="Scroll to top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3><i class="fas fa-landmark"></i> <?php echo APP_NAME; ?></h3>
                <p>Preserving and celebrating our rich cultural heritage for future generations. Explore our traditions, values, and history.</p>
            </div>

            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="<?php echo APP_URL; ?>/index.php"><i class="fas fa-angle-right"></i> Home</a></li>
                    <li><a href="<?php echo APP_URL; ?>/gallery.php"><i class="fas fa-angle-right"></i> Gallery</a></li>
                    <li><a href="<?php echo APP_URL; ?>/videos.php"><i class="fas fa-angle-right"></i> Videos</a></li>
                    <li><a href="<?php echo APP_URL; ?>/ethics.php"><i class="fas fa-angle-right"></i> Ethics & Values</a></li>
                    <li><a href="<?php echo APP_URL; ?>/contact.php"><i class="fas fa-angle-right"></i> Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Connect With Us</h3>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <div class="footer-contact-info">
                    <p><i class="fas fa-map-marker-alt"></i> 123 Heritage Lane, Culture City</p>
                    <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                    <p><i class="fas fa-envelope"></i> info@culturalheritage.org</p>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="<?php echo APP_URL; ?>/assets/js/main.js"></script>
</body>
</html>
