# WhatsMater - Social Media Application

A complete social media / chatting application with a Facebook-like interface built with HTML, CSS, PHP, and MySQL.

## Features

### User Features
- **Registration & Login** - Secure authentication with password hashing
- **News Feed** - View posts from friends and public posts, create posts with text and images
- **User Profiles** - View/edit profile information, cover photos, bio, and personal details
- **Friends System** - Send/accept/decline friend requests, view friend suggestions
- **Messenger** - Real-time messaging with conversation list and unread indicators
- **Notifications** - Get notified about likes, comments, friend requests, and messages
- **Comments & Likes** - Interact with posts through comments and likes
- **Privacy Controls** - Set post visibility to public, friends-only, or private
- **Search** - AJAX-powered user search
- **Settings** - Update profile info, change password, upload profile picture

### Admin Features
- **Dashboard** - Overview with stats (total users, posts, messages, online users, etc.)
- **User Management** - Search, filter, activate, suspend, ban, promote/demote users
- **Post Management** - View, hide, restore, delete posts with report indicators
- **Reports System** - Review and resolve/dismiss user reports
- **Admin Activity Log** - Track all admin actions
- **System Settings** - View application info, database stats, configuration reference

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx web server with `mod_rewrite` enabled

## Installation

1. **Clone or download** the project to your web server's document root.

2. **Create the database** by importing the SQL file:
   ```bash
   mysql -u root -p < database/whatsmater.sql
   ```

3. **Configure database connection** in `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'whatsmater');
   ```

4. **Configure application URL** in `config/app.php`:
   ```php
   define('APP_URL', 'http://localhost/whatsmater');
   ```

5. **Set upload directory permissions**:
   ```bash
   chmod -R 755 uploads/
   ```

6. **Access the application** at `http://localhost/whatsmater`

## Default Admin Account

- **Username:** admin
- **Email:** admin@whatsmater.com
- **Password:** password

## Project Structure

```
whatsmater/
├── admin/                  # Admin panel pages
│   ├── dashboard.php       # Admin dashboard with stats
│   ├── users.php           # User management
│   ├── posts.php           # Post moderation
│   ├── reports.php         # Report management
│   └── settings.php        # System settings
├── assets/
│   ├── css/style.css       # Complete stylesheet
│   ├── js/main.js          # Frontend JavaScript
│   └── images/             # Static images
├── config/
│   ├── app.php             # Application config & helpers
│   └── database.php        # Database connection
├── database/
│   └── whatsmater.sql      # Database schema & seed data
├── includes/
│   ├── header.php          # Navigation header
│   ├── footer.php          # Footer
│   └── sidebar.php         # Left sidebar
├── uploads/
│   ├── profiles/           # User profile pictures
│   └── posts/              # Post images
├── user/                   # User pages
│   ├── profile.php         # User profile
│   ├── friends.php         # Friends management
│   ├── messages.php        # Messenger
│   ├── notifications.php   # Notifications
│   └── settings.php        # User settings
├── index.php               # News feed (home page)
├── login.php               # Login page
├── register.php            # Registration page
├── logout.php              # Logout handler
├── search.php              # AJAX search endpoint
└── README.md
```

## Security Features

- Password hashing with `password_hash()` / `password_verify()`
- Prepared statements for all database queries (SQL injection prevention)
- Input sanitization with `htmlspecialchars()` and `mysqli_real_escape_string()`
- Session-based authentication
- Role-based access control (user/admin)
- File upload validation (type and size checks)

## License

This project is for educational purposes.
