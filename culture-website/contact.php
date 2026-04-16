<?php
$pageTitle = 'Contact Us - Cultural Heritage';
require_once __DIR__ . '/config/app.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $success = 'Thank you for your message! We will get back to you soon.';
        } else {
            $error = 'Something went wrong. Please try again later.';
        }
        $stmt->close();
    }
}

require_once INCLUDES_PATH . 'header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="page-banner-overlay">
        <h1><i class="fas fa-envelope"></i> Contact Us</h1>
        <p>Get in touch with us. We would love to hear from you.</p>
    </div>
</section>

<!-- Contact Content -->
<section class="section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <h2>Send Us a Message</h2>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="" class="contact-form" id="contactForm">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Your Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="subject"><i class="fas fa-tag"></i> Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="What is this about?" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="message"><i class="fas fa-comment-dots"></i> Message <span class="required">*</span></label>
                        <textarea id="message" name="message" rows="6" required placeholder="Write your message here..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="contact-info-wrapper">
                <h2>Contact Information</h2>
                <p>Feel free to reach out to us through any of the following channels.</p>

                <div class="contact-info-cards">
                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Our Location</h4>
                        <p>123 Cultural Heritage Lane<br>Community Center, CC 12345</p>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4>Phone Number</h4>
                        <p>+1 (555) 123-4567<br>+1 (555) 765-4321</p>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email Address</h4>
                        <p>info@culturalheritage.com<br>support@culturalheritage.com</p>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4>Working Hours</h4>
                        <p>Monday - Friday: 9AM - 5PM<br>Saturday: 10AM - 2PM</p>
                    </div>
                </div>

                <div class="contact-social">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once INCLUDES_PATH . 'footer.php'; ?>
