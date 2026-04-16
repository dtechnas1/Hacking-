<?php
/**
 * Cultural Heritage Website - Contact Page
 */
require_once __DIR__ . '/config/app.php';

$successMsg = '';
$errorMsg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    // Server-side validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $errorMsg = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Please enter a valid email address.';
    } else {
        // Insert into contact_messages using prepared statement
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $successMsg = 'Thank you for your message! We will get back to you soon.';
        } else {
            $errorMsg = 'An error occurred. Please try again later.';
        }
        $stmt->close();
    }
}

include INCLUDES_PATH . 'header.php';
?>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="page-banner-overlay">
            <h1>Contact Us</h1>
            <p>Get in touch with us to learn more about our cultural heritage</p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="section contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h2>Send Us a Message</h2>

                    <?php if ($successMsg): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $successMsg; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMsg): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errorMsg; ?>
                        </div>
                    <?php endif; ?>

                    <form id="contactForm" method="POST" action="<?php echo APP_URL; ?>/contact.php" novalidate>
                        <div class="form-group">
                            <label for="name"><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                            <span class="error-message" id="nameError"></span>
                        </div>

                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email address" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                            <span class="error-message" id="emailError"></span>
                        </div>

                        <div class="form-group">
                            <label for="subject"><i class="fas fa-tag"></i> Subject</label>
                            <input type="text" id="subject" name="subject" placeholder="Enter message subject" value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" required>
                            <span class="error-message" id="subjectError"></span>
                        </div>

                        <div class="form-group">
                            <label for="message"><i class="fas fa-comment-alt"></i> Message</label>
                            <textarea id="message" name="message" rows="6" placeholder="Type your message here..." required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                            <span class="error-message" id="messageError"></span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="contact-info-wrapper">
                    <h2>Get In Touch</h2>
                    <div class="contact-info-cards">
                        <div class="contact-info-card">
                            <div class="contact-info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4>Our Location</h4>
                                <p>123 Heritage Lane<br>Culture City, CC 12345</p>
                            </div>
                        </div>

                        <div class="contact-info-card">
                            <div class="contact-info-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h4>Phone</h4>
                                <p>+1 (555) 123-4567<br>+1 (555) 987-6543</p>
                            </div>
                        </div>

                        <div class="contact-info-card">
                            <div class="contact-info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4>Email</h4>
                                <p>info@culturalheritage.org<br>contact@culturalheritage.org</p>
                            </div>
                        </div>

                        <div class="contact-info-card">
                            <div class="contact-info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h4>Office Hours</h4>
                                <p>Mon - Fri: 9:00 AM - 5:00 PM<br>Sat: 10:00 AM - 2:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <!-- Google Maps Placeholder -->
                    <div class="map-container">
                        <h3>Find Us</h3>
                        <div class="map-placeholder">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.835434509374!2d144.95373531531978!3d-37.81627977975159!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzfCsDQ4JzU4LjYiUyAxNDTCsDU3JzIyLjQiRQ!5e0!3m2!1sen!2s!4v1600000000000!5m2!1sen!2s" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include INCLUDES_PATH . 'footer.php'; ?>
