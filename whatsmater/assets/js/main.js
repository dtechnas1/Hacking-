/**
 * WhatsMater - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Dropdown Menus ----
    const menuToggle = document.getElementById('menuToggle');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (menuToggle && dropdownMenu) {
        menuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
    }

    // Close dropdowns on outside click
    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown-menu.show').forEach(function (menu) {
            menu.classList.remove('show');
        });
    });

    // Post action menus
    document.querySelectorAll('.post-actions-menu .dropdown-toggle').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var menu = this.nextElementSibling;
            document.querySelectorAll('.post-actions-menu .dropdown-menu.show').forEach(function (m) {
                if (m !== menu) m.classList.remove('show');
            });
            menu.classList.toggle('show');
        });
    });

    // ---- Toggle Comments ----
    document.querySelectorAll('.toggle-comments').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var postId = this.getAttribute('data-post');
            var section = document.getElementById('comments-' + postId);
            if (section) {
                section.style.display = section.style.display === 'none' ? 'block' : 'none';
            }
        });
    });

    // ---- File Upload Preview ----
    document.querySelectorAll('.post-option input[type="file"]').forEach(function (input) {
        input.addEventListener('change', function () {
            var label = this.closest('.post-option');
            if (this.files.length > 0) {
                label.querySelector('span').textContent = this.files[0].name;
                label.style.color = '#1877f2';
            }
        });
    });

    // ---- Search Functionality ----
    var searchInput = document.getElementById('searchInput');
    var searchResults = document.getElementById('searchResults');
    var searchTimeout = null;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function () {
            var query = this.value.trim();
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(function () {
                fetch(window.location.origin + '/whatsmater/search.php?q=' + encodeURIComponent(query))
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        searchResults.innerHTML = '';
                        if (data.length === 0) {
                            searchResults.innerHTML = '<div style="padding:12px;color:#65676b;">No results found</div>';
                        } else {
                            data.forEach(function (user) {
                                var item = document.createElement('a');
                                item.href = window.location.origin + '/whatsmater/user/profile.php?id=' + user.id;
                                item.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px 14px;color:#050505;';
                                item.innerHTML = '<img src="' + (user.profile_pic || '/whatsmater/assets/images/default-avatar.png') + '" style="width:36px;height:36px;border-radius:50%;object-fit:cover;"><div><strong>' + user.full_name + '</strong><br><small style="color:#65676b;">@' + user.username + '</small></div>';
                                searchResults.appendChild(item);
                            });
                        }
                        searchResults.style.display = 'block';
                    })
                    .catch(function () {
                        searchResults.style.display = 'none';
                    });
            }, 300);
        });

        // Hide on outside click
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    // ---- Auto-scroll chat ----
    var chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // ---- Confirm Dialogs ----
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

});
