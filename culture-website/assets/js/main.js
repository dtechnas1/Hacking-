/**
 * Cultural Heritage Website - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Mobile Navigation Toggle ----
    var hamburger = document.getElementById('navHamburger');
    var navMenu = document.getElementById('navMenu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });

        // Close menu when clicking a link
        var navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });
    }

    // ---- Scroll to Top Button ----
    var scrollTopBtn = document.getElementById('scrollTopBtn');

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

    // ---- Navbar Scroll Effect ----
    var navbar = document.querySelector('.navbar');

    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 50) {
                navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
            } else {
                navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            }
        });
    }

    // ---- Gallery Lightbox ----
    var lightboxModal = document.getElementById('lightboxModal');
    var lightboxClose = document.getElementById('lightboxClose');
    var lightboxPrev = document.getElementById('lightboxPrev');
    var lightboxNext = document.getElementById('lightboxNext');
    var lightboxTitle = document.getElementById('lightboxTitle');
    var lightboxCaptionTitle = document.getElementById('lightboxCaptionTitle');
    var lightboxCaptionDesc = document.getElementById('lightboxCaptionDesc');
    var currentLightboxIndex = 0;

    if (lightboxModal && typeof galleryData !== 'undefined') {
        var galleryCards = document.querySelectorAll('.gallery-card[data-index]');

        galleryCards.forEach(function (card) {
            card.addEventListener('click', function () {
                currentLightboxIndex = parseInt(this.getAttribute('data-index'));
                openLightbox(currentLightboxIndex);
            });
        });

        function openLightbox(index) {
            if (index < 0 || index >= galleryData.length) return;
            currentLightboxIndex = index;

            var item = galleryData[index];
            if (lightboxTitle) lightboxTitle.textContent = item.title;
            if (lightboxCaptionTitle) lightboxCaptionTitle.textContent = item.title;
            if (lightboxCaptionDesc) lightboxCaptionDesc.textContent = item.description;

            lightboxModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightboxModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function navigateLightbox(direction) {
            currentLightboxIndex += direction;
            if (currentLightboxIndex < 0) currentLightboxIndex = galleryData.length - 1;
            if (currentLightboxIndex >= galleryData.length) currentLightboxIndex = 0;
            openLightbox(currentLightboxIndex);
        }

        if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
        if (lightboxPrev) lightboxPrev.addEventListener('click', function () { navigateLightbox(-1); });
        if (lightboxNext) lightboxNext.addEventListener('click', function () { navigateLightbox(1); });

        lightboxModal.addEventListener('click', function (e) {
            if (e.target === lightboxModal) closeLightbox();
        });

        document.addEventListener('keydown', function (e) {
            if (!lightboxModal.classList.contains('active')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') navigateLightbox(-1);
            if (e.key === 'ArrowRight') navigateLightbox(1);
        });
    }

    // ---- Video Modal ----
    var videoModal = document.getElementById('videoModal');
    var videoModalClose = document.getElementById('videoModalClose');
    var videoIframe = document.getElementById('videoIframe');

    if (videoModal) {
        var videoThumbs = document.querySelectorAll('.video-card-thumb[data-video-url], .video-card[data-video-url] .video-card-thumb');

        // Also handle video cards with data-video-url on the card itself
        var videoCardsWithUrl = document.querySelectorAll('.video-card[data-video-url]');

        function openVideoModal(url) {
            if (videoIframe) videoIframe.src = url;
            videoModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeVideoModal() {
            videoModal.classList.remove('active');
            if (videoIframe) videoIframe.src = '';
            document.body.style.overflow = '';
        }

        videoThumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                var url = this.getAttribute('data-video-url');
                if (url) {
                    openVideoModal(url);
                }
            });
        });

        videoCardsWithUrl.forEach(function (card) {
            card.addEventListener('click', function () {
                var url = this.getAttribute('data-video-url');
                if (url) {
                    openVideoModal(url);
                }
            });
        });

        if (videoModalClose) videoModalClose.addEventListener('click', closeVideoModal);

        videoModal.addEventListener('click', function (e) {
            if (e.target === videoModal) closeVideoModal();
        });

        document.addEventListener('keydown', function (e) {
            if (videoModal.classList.contains('active') && e.key === 'Escape') {
                closeVideoModal();
            }
        });
    }

    // ---- Ethics Section Navigation ----
    var ethicsNavLinks = document.querySelectorAll('.ethics-nav-link');

    if (ethicsNavLinks.length > 0) {
        ethicsNavLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                ethicsNavLinks.forEach(function (l) { l.classList.remove('active'); });
                this.classList.add('active');
            });
        });

        // Highlight active section on scroll
        var ethicsSections = document.querySelectorAll('.ethics-section');
        if (ethicsSections.length > 0) {
            window.addEventListener('scroll', function () {
                var scrollPos = window.pageYOffset + 200;

                ethicsSections.forEach(function (section) {
                    var sectionId = section.getAttribute('id');
                    if (!sectionId) return;

                    var sectionTop = section.offsetTop;
                    var sectionHeight = section.offsetHeight;

                    if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                        ethicsNavLinks.forEach(function (link) {
                            link.classList.remove('active');
                            if (link.getAttribute('href') === '#' + sectionId) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            });
        }
    }

    // ---- Contact Form Validation ----
    var contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            var name = document.getElementById('name');
            var email = document.getElementById('email');
            var message = document.getElementById('message');
            var valid = true;

            // Clear previous errors
            clearFieldError(name);
            clearFieldError(email);
            clearFieldError(message);

            if (name && name.value.trim() === '') {
                showFieldError(name, 'Please enter your name.');
                valid = false;
            }

            if (email && email.value.trim() === '') {
                showFieldError(email, 'Please enter your email.');
                valid = false;
            } else if (email && !isValidEmail(email.value)) {
                showFieldError(email, 'Please enter a valid email address.');
                valid = false;
            }

            if (message && message.value.trim() === '') {
                showFieldError(message, 'Please enter your message.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }

    function isValidEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showFieldError(field, message) {
        field.style.borderColor = '#DC3545';
        var errorEl = document.createElement('span');
        errorEl.className = 'field-error';
        errorEl.style.color = '#DC3545';
        errorEl.style.fontSize = '0.8rem';
        errorEl.style.marginTop = '4px';
        errorEl.textContent = message;
        field.parentNode.appendChild(errorEl);
    }

    function clearFieldError(field) {
        if (!field) return;
        field.style.borderColor = '';
        var errors = field.parentNode.querySelectorAll('.field-error');
        errors.forEach(function (el) { el.remove(); });
    }

    // ---- Smooth Scroll for Anchor Links ----
    var anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;

            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                var offset = 90; // Account for fixed navbar
                var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ---- Fade-in Animation on Scroll ----
    var animateElements = document.querySelectorAll('.gallery-card, .video-card, .ethics-card, .contact-info-card, .timeline-item');

    if ('IntersectionObserver' in window && animateElements.length > 0) {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animateElements.forEach(function (el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    }

});
