/**
 * Cultural Heritage Website - Main JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {

    // =============================================
    // Mobile Navigation Hamburger Toggle
    // =============================================
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

        // Close menu when clicking a nav link
        document.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });
    }

    // =============================================
    // Gallery Lightbox
    // =============================================
    const lightboxModal = document.getElementById('lightboxModal');
    const lightboxTitle = document.getElementById('lightboxTitle');
    const lightboxDescription = document.getElementById('lightboxDescription');
    const lightboxClose = document.getElementById('lightboxClose');
    const lightboxPrev = document.getElementById('lightboxPrev');
    const lightboxNext = document.getElementById('lightboxNext');
    const lightboxTriggers = document.querySelectorAll('.lightbox-trigger');

    let currentLightboxIndex = 0;
    let lightboxItems = [];

    if (lightboxTriggers.length > 0) {
        // Collect all lightbox items
        lightboxTriggers.forEach(function (trigger, index) {
            lightboxItems.push({
                title: trigger.getAttribute('data-title') || '',
                description: trigger.getAttribute('data-description') || ''
            });

            trigger.addEventListener('click', function () {
                currentLightboxIndex = index;
                openLightbox();
            });
        });
    }

    function openLightbox() {
        if (!lightboxModal) return;
        updateLightboxContent();
        lightboxModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        if (!lightboxModal) return;
        lightboxModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function updateLightboxContent() {
        if (lightboxItems.length === 0) return;
        var item = lightboxItems[currentLightboxIndex];
        if (lightboxTitle) lightboxTitle.textContent = item.title;
        if (lightboxDescription) lightboxDescription.textContent = item.description;
    }

    function lightboxPrevItem() {
        currentLightboxIndex = (currentLightboxIndex - 1 + lightboxItems.length) % lightboxItems.length;
        updateLightboxContent();
    }

    function lightboxNextItem() {
        currentLightboxIndex = (currentLightboxIndex + 1) % lightboxItems.length;
        updateLightboxContent();
    }

    if (lightboxClose) {
        lightboxClose.addEventListener('click', closeLightbox);
    }

    if (lightboxPrev) {
        lightboxPrev.addEventListener('click', lightboxPrevItem);
    }

    if (lightboxNext) {
        lightboxNext.addEventListener('click', lightboxNextItem);
    }

    if (lightboxModal) {
        lightboxModal.addEventListener('click', function (e) {
            if (e.target === lightboxModal) {
                closeLightbox();
            }
        });
    }

    // Keyboard navigation for lightbox
    document.addEventListener('keydown', function (e) {
        if (lightboxModal && lightboxModal.classList.contains('active')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') lightboxPrevItem();
            if (e.key === 'ArrowRight') lightboxNextItem();
        }
    });

    // =============================================
    // Video Modal
    // =============================================
    const videoModal = document.getElementById('videoModal');
    const videoIframe = document.getElementById('videoIframe');
    const videoModalClose = document.getElementById('videoModalClose');
    const videoCards = document.querySelectorAll('.video-card[data-video-url]');

    videoCards.forEach(function (card) {
        card.addEventListener('click', function () {
            var videoUrl = this.getAttribute('data-video-url');
            if (videoUrl && videoModal && videoIframe) {
                videoIframe.src = videoUrl;
                videoModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    function closeVideoModal() {
        if (!videoModal || !videoIframe) return;
        videoModal.classList.remove('active');
        videoIframe.src = '';
        document.body.style.overflow = '';
    }

    if (videoModalClose) {
        videoModalClose.addEventListener('click', closeVideoModal);
    }

    if (videoModal) {
        videoModal.addEventListener('click', function (e) {
            if (e.target === videoModal) {
                closeVideoModal();
            }
        });
    }

    // Escape key closes video modal too
    document.addEventListener('keydown', function (e) {
        if (videoModal && videoModal.classList.contains('active') && e.key === 'Escape') {
            closeVideoModal();
        }
    });

    // =============================================
    // Accordion Toggle (Ethics Page)
    // =============================================
    const accordionHeaders = document.querySelectorAll('.accordion-header');

    accordionHeaders.forEach(function (header) {
        header.addEventListener('click', function () {
            var body = this.nextElementSibling;
            var isActive = this.classList.contains('active');

            // Close all accordion items in the same accordion
            var accordion = this.closest('.accordion');
            if (accordion) {
                accordion.querySelectorAll('.accordion-header').forEach(function (h) {
                    h.classList.remove('active');
                    h.setAttribute('aria-expanded', 'false');
                    h.nextElementSibling.style.maxHeight = null;
                });
            }

            // Toggle the clicked item
            if (!isActive) {
                this.classList.add('active');
                this.setAttribute('aria-expanded', 'true');
                body.style.maxHeight = body.scrollHeight + 'px';
            }
        });
    });

    // =============================================
    // Contact Form Validation
    // =============================================
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            var isValid = true;

            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(function (el) {
                el.textContent = '';
            });
            document.querySelectorAll('.form-group input, .form-group textarea').forEach(function (el) {
                el.classList.remove('error');
            });

            // Validate name
            var name = document.getElementById('name');
            var nameError = document.getElementById('nameError');
            if (name && name.value.trim() === '') {
                nameError.textContent = 'Please enter your full name.';
                name.classList.add('error');
                isValid = false;
            }

            // Validate email
            var email = document.getElementById('email');
            var emailError = document.getElementById('emailError');
            if (email) {
                if (email.value.trim() === '') {
                    emailError.textContent = 'Please enter your email address.';
                    email.classList.add('error');
                    isValid = false;
                } else if (!isValidEmail(email.value.trim())) {
                    emailError.textContent = 'Please enter a valid email address.';
                    email.classList.add('error');
                    isValid = false;
                }
            }

            // Validate subject
            var subject = document.getElementById('subject');
            var subjectError = document.getElementById('subjectError');
            if (subject && subject.value.trim() === '') {
                subjectError.textContent = 'Please enter a subject.';
                subject.classList.add('error');
                isValid = false;
            }

            // Validate message
            var message = document.getElementById('message');
            var messageError = document.getElementById('messageError');
            if (message && message.value.trim() === '') {
                messageError.textContent = 'Please enter your message.';
                message.classList.add('error');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    function isValidEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // =============================================
    // Smooth Scroll for Anchor Links
    // =============================================
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // =============================================
    // Scroll to Top Button
    // =============================================
    const scrollTopBtn = document.getElementById('scrollTopBtn');

    if (scrollTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });

        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

});
